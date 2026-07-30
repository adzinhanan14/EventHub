<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\TicketReservationService;
use Illuminate\Console\Command;

/**
 * Race Condition Handling: melepas reservasi stok tiket yang sudah lewat batas
 * waktu pembayaran (expires_at) tapi belum kunjung dibayar, supaya stoknya
 * dikembalikan (+1) dan bisa direbut pembeli lain.
 */
class ReleaseExpiredReservations extends Command
{
    protected $signature = 'reservations:release-expired';

    protected $description = 'Lepaskan reservasi stok tiket (Pending/Challenge) yang sudah melewati batas waktu pembayaran';

    public function handle(TicketReservationService $reservation): int
    {
        $candidates = Transaction::whereIn('status', TicketReservationService::OPEN_STATUSES)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $released = 0;

        foreach ($candidates as $transaction) {
            if ($reservation->release($transaction, 'Expired')) {
                $released++;
                $this->line("✔ Order {$transaction->order_id} dilepaskan (stok +1).");
            }
        }

        $this->info("Selesai. {$released} dari {$candidates->count()} reservasi kadaluarsa berhasil dilepaskan.");

        return self::SUCCESS;
    }
}
