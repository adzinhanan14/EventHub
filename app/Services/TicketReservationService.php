<?php

namespace App\Services;

use App\Models\Event;
use App\Models\TicketTier;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Menangani race condition perebutan stok tiket dengan pola "Reserve then Release":
 *
 * 1. reserve()        -> dipanggil TEPAT saat pengunjung klik Checkout. Stok langsung
 *                         dipotong (ditahan) di sini, memakai row-lock (lockForUpdate)
 *                         supaya dua pembeli tidak bisa merebut slot terakhir yang sama
 *                         secara bersamaan.
 * 2. confirmSuccess()  -> dipanggil saat pembayaran lunas. Tidak memotong stok lagi
 *                         (sudah dipotong saat reserve), hanya mengubah status transaksi.
 * 3. release()         -> dipanggil saat reservasi kadaluarsa/batal/gagal. Stok
 *                         dikembalikan (+1) agar bisa direbut pembeli lain.
 *
 * confirmSuccess() & release() memakai atomic conditional UPDATE
 * (`WHERE status = 'Pending'`) supaya idempotent: walau dipanggil bersamaan oleh
 * lebih dari satu proses (misal webhook Midtrans & scheduler expired berjalan di
 * detik yang sama), stok hanya akan diproses SATU KALI.
 */
class TicketReservationService
{
    public const RESERVATION_MINUTES = 15;

    /**
     * Status yang berarti "reservasi masih terbuka" (stok masih ditahan, belum final).
     * 'Challenge' termasuk karena itu hanya status tampilan menunggu fraud-review
     * Midtrans — stoknya tetap tertahan sampai keputusan final masuk.
     */
    public const OPEN_STATUSES = ['Pending', 'Challenge'];

    /**
     * Tahan 1 slot tiket untuk event (dan tier aktif jika ada).
     *
     * @throws RuntimeException jika stok/kuota sudah habis
     */
    public function reserve(Event $event, ?TicketTier $tier = null): void
    {
        DB::transaction(function () use ($event, $tier) {
            // SELECT ... FOR UPDATE: kunci baris event, request lain yang mencoba
            // reserve event yang sama akan menunggu sampai transaksi ini commit.
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

            if (!$lockedEvent || $lockedEvent->stock <= 0) {
                throw new RuntimeException('Mohon maaf, tiket untuk acara ini sudah habis.');
            }

            if ($tier) {
                $lockedTier = TicketTier::where('id', $tier->id)->lockForUpdate()->first();

                if (!$lockedTier || $lockedTier->remainingQuota() <= 0) {
                    throw new RuntimeException('Kuota untuk tingkatan harga ini sudah habis.');
                }

                $lockedTier->increment('sold');
            }

            $lockedEvent->decrement('stock');
        });
    }

    /**
     * Konfirmasi reservasi menjadi transaksi sukses (pembayaran lunas / event gratis).
     * Tidak menyentuh stok karena sudah dipotong saat reserve().
     *
     * @return bool true jika berhasil mengonfirmasi (baru pertama kali diproses),
     *              false jika transaksi sudah pernah diproses sebelumnya (idempotent guard).
     */
    public function confirmSuccess(Transaction $transaction): bool
    {
        $affected = Transaction::where('id', $transaction->id)
            ->whereIn('status', self::OPEN_STATUSES)
            ->update([
                'status'      => 'Success',
                'ticket_code' => (string) Str::uuid(),
            ]);

        return $affected > 0;
    }

    /**
     * Lepaskan reservasi: kembalikan stok (+1) & kuota tier (-1 sold), lalu ubah
     * status transaksi (mis. 'Expired' oleh scheduler, atau 'Failed' oleh webhook
     * cancel/deny/expire Midtrans).
     *
     * @return bool true jika stok berhasil dilepaskan sekarang,
     *              false jika transaksi sudah lebih dulu final (Success/Failed/Expired)
     *              sehingga tidak perlu (dan tidak boleh) dilepas lagi.
     */
    public function release(Transaction $transaction, string $reasonStatus = 'Expired'): bool
    {
        return DB::transaction(function () use ($transaction, $reasonStatus) {
            // Atomic conditional update: hanya baris yang MASIH 'Pending' yang akan
            // ter-update. Jika proses lain sudah lebih dulu mengubahnya, $affected = 0
            // dan kita tidak melepas stok dua kali.
            $affected = Transaction::where('id', $transaction->id)
                ->whereIn('status', self::OPEN_STATUSES)
                ->update(['status' => $reasonStatus]);

            if ($affected === 0) {
                return false;
            }

            if ($transaction->event_id) {
                Event::where('id', $transaction->event_id)->increment('stock');
            }

            if ($transaction->ticket_tier_id) {
                TicketTier::where('id', $transaction->ticket_tier_id)->decrement('sold');
            }

            return true;
        });
    }
}
