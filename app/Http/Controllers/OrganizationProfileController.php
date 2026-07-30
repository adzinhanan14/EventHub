<?php

namespace App\Http\Controllers;

use App\Models\Organization;

/**
 * Halaman profil publik penyelenggara: menampilkan rekam jejak rating & ulasan
 * untuk menarik kepercayaan calon pembeli pada event mereka selanjutnya.
 */
class OrganizationProfileController extends Controller
{
    public function show(Organization $organization)
    {
        abort_unless($organization->isApproved(), 404);

        $events = $organization->events()->with('category')->orderBy('date', 'desc')->paginate(6);
        $reviews = $organization->reviews()->with('event', 'user')->latest()->paginate(10, ['*'], 'reviews_page');
        $averageRating = $organization->averageRating();
        $totalReviews = $organization->reviews()->count();

        return view('organizations.show', compact('organization', 'events', 'reviews', 'averageRating', 'totalReviews'));
    }
}
