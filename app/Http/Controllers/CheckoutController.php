<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Event;
use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\TicketReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventTicketMail;

class CheckoutController extends Controller
{
    public function __construct(private TicketReservationService $reservation)
    {
    }

    public function create(Event $event)
    {
        $categories = \App\Models\Category::all();
        $event->load('ticketTiers');
        $activeTier = $event->activeTier();
        $price = $event->currentPrice();

        return view('checkout.create', compact('event', 'categories', 'activeTier', 'price'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'coupon_code'    => 'nullable|string|max:50',
        ]);

        // --- Penjualan Bertahap (Dynamic Pricing): pakai tier aktif jika ada ---
        $activeTier = $event->activeTier();
        $basePrice  = $activeTier ? $activeTier->price : $event->price;

        // --- Kupon / Voucher Diskon (divalidasi SEBELUM menahan stok, supaya
        // kupon yang tidak valid tidak sia-sia menahan slot tiket orang lain) ---
        $coupon = null;
        $discount = 0;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if (!$coupon || !$coupon->isValidFor($event)) {
                return back()->withInput()->with('error', 'Kode kupon tidak valid atau sudah tidak berlaku.');
            }

            $discount = $coupon->calculateDiscount($basePrice);
        }

        // ==========================================================
        // RACE CONDITION HANDLING (Reserve then Release):
        // Stok langsung "ditahan" DI SINI, sesaat setelah klik Checkout —
        // bukan menunggu pembayaran lunas. Dilakukan dengan row-lock
        // (SELECT ... FOR UPDATE) di dalam TicketReservationService supaya
        // dua pengunjung tidak bisa merebut slot terakhir yang sama secara
        // bersamaan. Jika reservasi ini tidak dibayar dalam 15 menit,
        // scheduler `reservations:release-expired` akan mengembalikan stoknya (+1).
        // ==========================================================
        try {
            $this->reservation->reserve($event, $activeTier);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $adminFee   = $basePrice > 0 ? 5000 : 0; // Event gratis tidak dikenakan biaya admin
        $totalPrice = max(0, $basePrice - $discount) + $adminFee;

        $orderId = 'TRX-' . time() . '-' . Str::random(5);

        $transaction = Transaction::create([
            'user_id'         => Auth::id(),
            'event_id'        => $event->id,
            'ticket_tier_id'  => $activeTier?->id,
            'coupon_id'       => $coupon?->id,
            'order_id'        => $orderId,
            'customer_name'   => $request->customer_name,
            'customer_email'  => $request->customer_email,
            'customer_phone'  => $request->customer_phone,
            'total_price'     => $totalPrice,
            'discount_amount' => $discount,
            'status'          => 'Pending',
            // Batas waktu reservasi: kalau tidak dibayar sebelum ini, stok dilepas lagi.
            'expires_at'      => $totalPrice > 0 ? now()->addMinutes(TicketReservationService::RESERVATION_MINUTES) : null,
        ]);

        // ==========================================================
        // Bypass Transaksi untuk Acara Gratis: skip Midtrans sepenuhnya
        // ==========================================================
        if ($totalPrice <= 0) {
            $this->finalizeSuccess($transaction, $coupon);
            return redirect()->route('checkout.success', $transaction->order_id);
        }

        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email'      => $request->customer_email,
                'phone'      => $request->customer_phone,
            ],
            'callbacks' => [
                'finish' => url('/payment-finish'),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return redirect()->route('checkout.payment', $transaction->order_id);
        } catch (\Exception $e) {
            // Gagal minta Snap token = pembeli tidak akan pernah sampai ke halaman
            // bayar. Lepas lagi stok yang sudah ditahan supaya tidak nyangkut.
            $this->reservation->release($transaction, 'Failed');
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
        $categories  = \App\Models\Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        // Kalau transaksi sudah kedaluwarsa (status di DB, atau waktu reservasi
        // sudah lewat), jangan render halaman payment. Kalau dibiarkan, Snap
        // akan otomatis membuka popup "Transaksi sudah kedaluwarsa" milik
        // Midtrans, dan tombol "Kembali ke halaman merchant" di popup itu
        // tidak bisa kita arahkan ke halaman event (di luar kendali kita).
        // Jadi langsung saja arahkan pembeli ke halaman event tiket ini.
        $isExpired = in_array($transaction->status, ['Expired', 'Failed'])
            || ($transaction->status === 'Pending' && $transaction->expires_at && $transaction->expires_at->isPast());

        if ($isExpired) {
            if ($transaction->status === 'Pending') {
                $this->reservation->release($transaction, 'Failed');
            }

            return redirect()->route('events.show', $transaction->event)
                ->with('error', 'Waktu pembayaran untuk transaksi ini sudah habis. Silakan pesan tiket lagi.');
        }

        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Order ID tidak ditemukan.');
        }

        return redirect()->route('checkout.success', $orderId);
    }

    public function success($order_id)
    {
        $categories  = \App\Models\Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        if ($transaction->status === 'Success') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // Reservasi sudah dilepas duluan (kadaluarsa / dibatalkan Midtrans) -> tidak
        // perlu tanya API Midtrans lagi, langsung informasikan ke pembeli.
        if (in_array($transaction->status, ['Expired', 'Failed'])) {
            return redirect()->route('home')
                ->with('error', 'Waktu pembayaran sudah habis atau transaksi dibatalkan. Silakan checkout ulang.');
        }

        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        try {
            $status     = \Midtrans\Transaction::status($order_id);
            $trx_status = is_array($status)
                ? ($status['transaction_status'] ?? '')
                : ($status->transaction_status ?? '');

            if (in_array($trx_status, ['settlement', 'capture'])) {
                if ($transaction->status === 'Pending') {
                    $this->finalizeSuccess($transaction, $transaction->coupon);
                }
            } elseif ($trx_status === 'pending') {
                return redirect()->route('checkout.payment', $order_id)
                    ->with('info', 'Pembayaran Anda masih menunggu konfirmasi transfer.');
            } else {
                // Midtrans bilang batal/expire -> lepas reservasi stok sekarang juga,
                // jangan tunggu scheduler (idempotent, aman walau nanti scheduler ikut jalan).
                $this->reservation->release($transaction, 'Failed');

                return redirect()->route('home')
                    ->with('error', 'Transaksi dibatalkan atau kadaluarsa.');
            }
        } catch (\Exception $e) {
            Log::error('Midtrans API error di success(): ' . $e->getMessage());
        }

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        return view('checkout.success', compact('transaction', 'categories'));
    }

    /**
     * Finalisasi transaksi sukses: generate kode tiket unik (QR check-in),
     * kirim E-Ticket via email + WhatsApp, catat pemakaian kupon.
     * (Stok sudah dipotong sejak reservasi dibuat, lihat TicketReservationService::reserve().)
     * Dipakai baik oleh alur bayar normal maupun bypass event gratis.
     */
    private function finalizeSuccess(Transaction $transaction, ?Coupon $coupon = null): void
    {
        // Stok TIDAK dipotong lagi di sini — sudah ditahan (reserved) sejak klik
        // Checkout. confirmSuccess() hanya mengubah status secara atomic & idempotent,
        // supaya aman walau dipanggil bersamaan dengan webhook Midtrans.
        if (!$this->reservation->confirmSuccess($transaction)) {
            // Sudah diproses lebih dulu (mis. oleh webhook) -> tidak perlu diulang.
            return;
        }

        $transaction->refresh();

        if ($coupon) {
            $coupon->increment('used_count');
        }

        try {
            Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
        } catch (\Exception $e) {
            Log::error('Gagal kirim E-Ticket email: ' . $e->getMessage());
        }

        // Notifikasi WhatsApp (Fonnte) sebagai pelengkap E-Ticket via email
        app(FonnteService::class)->sendMessage(
            $transaction->customer_phone,
            "Halo {$transaction->customer_name}! 🎉\nPembayaran tiket *{$transaction->event->title}* berhasil.\nOrder ID: {$transaction->order_id}\nSilakan cek email Anda untuk E-Ticket lengkap (QR check-in).\n\nSampai jumpa di acara! - AmikomEventHub"
        );
    }
}