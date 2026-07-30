<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Halaman "Tiket Saya": seluruh riwayat tiket yang dibeli user TIDAK PERNAH
 * disembunyikan meski acaranya sudah lewat — dipisah jadi "Tiket Mendatang"
 * dan "Riwayat Tiket" saja supaya tetap mudah ditemukan, sekaligus jadi pintu
 * masuk untuk memberi rating & ulasan setelah acara selesai (H+1) dan transaksi sukses.
 */
class TicketController extends Controller
{
    public function ticket()
    {
        $transactions = Auth::user()->transactions()
            ->with(['event.organization', 'review'])
            ->get()
            ->sortByDesc(fn ($trx) => $trx->event->date ?? $trx->created_at)
            ->values();

        // Event belum berlangsung (atau tanpa tanggal) dianggap "mendatang".
        $upcomingTickets = $transactions->filter(
            fn ($trx) => !$trx->event || !$trx->event->date || $trx->event->date->isFuture()
        )->values();

        // Event sudah lewat -> tetap ditampilkan, hanya dipindah ke bagian "Riwayat".
        $pastTickets = $transactions->filter(
            fn ($trx) => $trx->event && $trx->event->date && $trx->event->date->isPast()
        )->values();

        return view('ticket', compact('upcomingTickets', 'pastTickets'));
    }
}
