<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\FonnteService;
use Illuminate\Console\Command;

/**
 * Abandoned Cart Recovery: jika pembeli menutup tab sebelum bayar,
 * kirim WA berisi link pembayaran Midtrans agar transaksi tidak hangus.
 */
class SendAbandonedCartReminders extends Command
{
    protected $signature = 'app:send-abandoned-cart-reminders';
    protected $description = 'Kirim reminder WhatsApp untuk transaksi Pending yang belum dibayar (abandoned cart)';

    public function handle(FonnteService $fonnte): int
    {
        $transactions = Transaction::with('event')
            ->where('status', 'Pending')
            ->whereNotNull('snap_token')
            ->whereNull('wa_reminder_sent_at')
            ->where('created_at', '<=', now()->subMinutes(15))
            ->where('created_at', '>=', now()->subHours(24)) // jangan spam order lama
            ->get();

        $this->info("Ditemukan {$transactions->count()} transaksi pending untuk direminder.");

        foreach ($transactions as $transaction) {
            $paymentUrl = route('checkout.payment', $transaction->order_id);

            $sent = $fonnte->sendMessage(
                $transaction->customer_phone,
                "Halo {$transaction->customer_name}! 👋\nSepertinya Anda belum menyelesaikan pembayaran tiket *{$transaction->event->title}*.\n\nSelesaikan pembayaran sebelum kehabisan di sini:\n{$paymentUrl}\n\n- AmikomEventHub"
            );

            $transaction->update(['wa_reminder_sent_at' => now()]);

            $this->line(($sent ? '✔' : '✘') . " Order {$transaction->order_id}");
        }

        return self::SUCCESS;
    }
}
