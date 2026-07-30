<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = Auth::user()->organization_id;
        $search = $request->input('search');

        $events = Event::with('category')
            ->where('organization_id', $organizationId)
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('organizer.events.index', compact('events', 'search'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data['organization_id'] = Auth::user()->organization_id;
        // Checkbox HTML tidak dikirim sama sekali kalau tidak dicentang, jadi
        // ditangani terpisah dari validate() supaya default-nya tetap false.
        $data['issues_certificate'] = $request->boolean('issues_certificate');

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        Event::create($data);

        return redirect()->route('organizer.events.index')->with('success', 'Event baru berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        $this->authorizeOwnership($event);
        $categories = Category::orderBy('name')->get();
        $event->load('ticketTiers', 'coupons');
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeOwnership($event);

        $data = $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data['issues_certificate'] = $request->boolean('issues_certificate');

        if ($request->hasFile('poster')) {
            if ($event->poster_path) {
                Storage::disk('public')->delete($event->poster_path);
            }
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($data);

        return redirect()->route('organizer.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->authorizeOwnership($event);

        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }
        $event->delete();

        return redirect()->route('organizer.events.index')->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Pastikan event yang diakses benar-benar milik organisasi yang sedang login.
     * Ini kunci isolasi data pada arsitektur multi-tenant.
     */
    private function authorizeOwnership(Event $event): void
    {
        abort_unless($event->organization_id === Auth::user()->organization_id, 403, 'Anda tidak memiliki akses ke event ini.');
    }
}
