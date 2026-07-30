<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function show($id = null)
    {
        $event = $id ? Event::with('category')->findOrFail($id) : Event::with('category')->first();

        // Dipakai view untuk menampilkan link "Beri Ulasan" hanya jika user benar-benar
        // berhak (sudah beli tiket, transaksi sukses, acara sudah lewat >= 1 hari, belum pernah direview).
        $eligibleTransaction = Auth::check()
            ? Transaction::findEligibleForReview($event, Auth::user())
            : null;

        return view('event-detail', compact('event', 'eligibleTransaction'));
    }

    public function checkout()
    {
        return view('checkout');
    }
}