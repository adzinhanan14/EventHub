<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index()
    {
        $organizationId = Auth::user()->organization_id;
        $coupons = Coupon::with('event')->where('organization_id', $organizationId)->latest()->paginate(10);
        $events = Event::where('organization_id', $organizationId)->orderBy('title')->get();

        return view('organizer.coupons.index', compact('coupons', 'events'));
    }

    public function store(Request $request)
    {
        $organizationId = Auth::user()->organization_id;

        $data = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:1',
            'max_uses' => 'nullable|numeric|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Pastikan event (jika dipilih) benar-benar milik organisasi ini
        if (!empty($data['event_id'])) {
            $event = Event::findOrFail($data['event_id']);
            abort_unless($event->organization_id === $organizationId, 403);
        }

        $data['organization_id'] = $organizationId;
        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = true;

        Coupon::create($data);

        return back()->with('success', 'Kupon "' . $data['code'] . '" berhasil dibuat.');
    }

    public function toggle(Coupon $coupon)
    {
        abort_unless($coupon->organization_id === Auth::user()->organization_id, 403);
        $coupon->update(['is_active' => !$coupon->is_active]);
        return back()->with('success', 'Status kupon diperbarui.');
    }

    public function destroy(Coupon $coupon)
    {
        abort_unless($coupon->organization_id === Auth::user()->organization_id, 403);
        $coupon->delete();
        return back()->with('success', 'Kupon dihapus.');
    }
}
