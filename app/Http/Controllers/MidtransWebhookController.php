<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\TicketReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventTicketMail;

class MidtransWebhookController extends Controller
{
    public function __construct(private TicketReservationService $reservation)
    {
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $transaction = Transaction::with('event', 'ticketTier', 'coupon')->where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Idempotent guard: kalau statusnya sudah final (Success/Failed/Expired),
        // reservasinya sudah pernah diselesaikan oleh proses lain -> jangan diproses ulang.
        if (!in_array($transaction->status, TicketReservationService::OPEN_STATUSES)) {
            return response()->json(['message' => 'Already processed']);
        }

        if ($transactionStatus === 'capture' && $fraudStatus === 'challenge') {
            // Masih menunggu keputusan manual fraud-review Midtrans. Ubah label status
            // saja (dipakai UI ticket-card), reservasi stok TETAP tertahan seperti biasa
            // sampai keputusan berikutnya masuk atau expires_at lewat.
            Transaction::where('id', $transaction->id)
                ->whereIn('status', TicketReservationService::OPEN_STATUSES)
                ->update(['status' => 'Challenge']);

            return response()->json(['message' => 'Challenge, awaiting review']);
        }

        if (
            $transactionStatus === 'settlement'
            || ($transactionStatus === 'capture' && $fraudStatus === 'accept')
        ) {
            // Stok TIDAK dipotong lagi di sini — sudah ditahan sejak reservasi dibuat.
            // confirmSuccess() atomic & idempotent: aman walau race dengan halaman success().
            if ($this->reservation->confirmSuccess($transaction)) {
                $transaction->refresh();
                $this->sendSuccessNotifications($transaction);
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            // Lepas reservasi stok sekarang juga (+1), jangan tunggu scheduler.
            // release() idempotent: aman walau race dengan scheduler expired.
            $this->reservation->release($transaction, 'Failed');
        }
        // 'pending' -> tidak melakukan apa-apa, transaksi memang masih menunggu bayar.

        return response()->json(['message' => 'OK']);
    }

    private function sendSuccessNotifications(Transaction $transaction): void
    {
        if ($transaction->coupon) {
            $transaction->coupon->increment('used_count');
        }

        try {
            Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email E-Ticket: ' . $e->getMessage());
        }

        app(FonnteService::class)->sendMessage(
            $transaction->customer_phone,
            "Halo {$transaction->customer_name}! 🎉\nPembayaran tiket *{$transaction->event->title}* berhasil.\nOrder ID: {$transaction->order_id}\nSilakan cek email Anda untuk E-Ticket lengkap (QR check-in).\n\nSampai jumpa di acara! - AmikomEventHub"
        );
    }
}
