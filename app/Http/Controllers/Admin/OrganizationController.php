<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

/**
 * Superadmin bertugas mengawasi kelayakan seluruh penyelenggara (Organisasi/HIMA).
 */
class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $organizations = Organization::withCount('events')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('admin.organizations.index', compact('organizations', 'status'));
    }

    public function show(Organization $organization)
    {
        $organization->load(['events' => fn ($q) => $q->latest(), 'users']);
        return view('admin.organizations.show', compact('organization'));
    }

    public function approve(Organization $organization)
    {
        $organization->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Organisasi "' . $organization->name . '" telah disetujui dan dapat mulai mengelola event.');
    }

    public function reject(Request $request, Organization $organization)
    {
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $organization->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Organisasi "' . $organization->name . '" ditolak.');
    }
}
