<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Sistem Aplikasi Penjaga Pintu (Check-in Scanner).
 * Halaman terpisah berbasis HTML5 QR Scanner untuk validasi tiket di hari-H,
 * mencegah kecurangan berupa double-entry.
 */
class CheckinController extends Controller
{
    public function scanner(Request $request)
    {
        $organizationId = Auth::user()->organization_id;
        $events = Event::where('organization_id', $organizationId)->orderBy('date', 'desc')->get();

        // Riwayat check-in yang SUDAH TERSIMPAN di database (bukan cuma sesi browser saat ini),
        // supaya kalau halaman di-refresh atau dibuka lagi nanti, riwayatnya tetap ada.
        $history = Transaction::with('event')
            ->whereHas('event', fn ($q) => $q->where('organization_id', $organizationId))
            ->where('checkin_status', 'used')
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->limit(50)
            ->get();

        return view('organizer.checkin.scanner', compact('events', 'history'));
    }

    public function scan(Request $request, CertificateService $certificateService)
    {
        $request->validate(['ticket_code' => 'required|string']);

        $organizationId = Auth::user()->organization_id;

        $transaction = Transaction::with('event')
            ->where('ticket_code', $request->ticket_code)
            ->first();

        if (!$transaction || !$transaction->event || $transaction->event->organization_id !== $organizationId) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan atau bukan milik organisasi Anda.'], 404);
        }

        if ($transaction->status !== 'Success') {
            return response()->json(['status' => 'error', 'message' => 'Tiket ini belum dibayar / tidak valid.'], 422);
        }

        if ($transaction->isCheckedIn()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket ini SUDAH PERNAH digunakan (' . $transaction->checked_in_at?->format('d M Y H:i') . '). Kemungkinan penyusup / double entry.',
            ], 409);
        }

        $transaction->update([
            'checkin_status' => 'used',
            'checked_in_at' => now(),
        ]);

        // Trigger E-Certificate otomatis begitu peserta divalidasi hadir di lokasi
        $certificateSent = $certificateService->generateAndSend($transaction);
        \Illuminate\Support\Facades\Log::info("[Certificate] Hasil generateAndSend untuk order {$transaction->order_id}: " . ($certificateSent ? 'TERKIRIM' : 'TIDAK terkirim (lihat log [Certificate] di atas untuk alasannya)'));

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil.',
            'data' => [
                'nama' => $transaction->customer_name,
                'event' => $transaction->event->title,
                'order_id' => $transaction->order_id,
            ],
        ]);
    }
}