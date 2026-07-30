<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Strategi Dynamic Pricing: Early Bird -> Presale -> Regular.
 * Harga naik otomatis mengikuti tanggal berjalan (starts_at / ends_at).
 */
class TicketTierController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $this->authorizeOwnership($event);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|numeric|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|numeric',
        ]);
        $data['event_id'] = $event->id;

        TicketTier::create($data);

        return back()->with('success', 'Tingkatan harga tiket berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event, TicketTier $tier)
    {
        $this->authorizeOwnership($event);
        abort_unless($tier->event_id === $event->id, 404);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|numeric|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|numeric',
        ]);

        $tier->update($data);

        return back()->with('success', 'Tingkatan harga tiket berhasil diperbarui.');
    }

    public function destroy(Event $event, TicketTier $tier)
    {
        $this->authorizeOwnership($event);
        abort_unless($tier->event_id === $event->id, 404);

        $tier->delete();

        return back()->with('success', 'Tingkatan harga tiket dihapus.');
    }

    private function authorizeOwnership(Event $event): void
    {
        abort_unless($event->organization_id === Auth::user()->organization_id, 403);
    }
}
