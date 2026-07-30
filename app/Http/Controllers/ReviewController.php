<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Sistem Ulasan & Penilaian Bintang (Rating & Review).
 * Hanya bisa diisi oleh pembeli tiket, minimal sehari setelah acara selesai.
 */
class ReviewController extends Controller
{
    public function create(Event $event)
    {
        $transaction = $this->findEligibleTransaction($event);

        if (!$transaction) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'Anda belum bisa memberi ulasan untuk event ini (pastikan Anda sudah membeli tiket & acara sudah selesai minimal 1 hari).');
        }

        return view('reviews.create', compact('event', 'transaction'));
    }

    public function store(Request $request, Event $event)
    {
        $transaction = $this->findEligibleTransaction($event);

        if (!$transaction) {
            return redirect()->route('events.show', $event->id)->with('error', 'Anda tidak berhak memberi ulasan untuk event ini.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'event_id' => $event->id,
            'organization_id' => $event->organization_id,
            'transaction_id' => $transaction->id,
            'user_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return redirect()->route('events.show', $event->id)->with('success', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * Cari transaksi milik user (via email login / customer_email) yang berhak mengulas event ini.
     */
    private function findEligibleTransaction(Event $event): ?Transaction
    {
        if (!Auth::check()) {
            return null;
        }

        return Transaction::findEligibleForReview($event, Auth::user());
    }
}
