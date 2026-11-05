<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Partner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $partners = Partner::latest()->get();

        $query = Event::with(['category', 'organization', 'ticketTiers']);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $events = (clone $query)
            ->where('date', '>=', now())
            ->orderBy('date')
            ->take(5)
            ->get();

        $showingArchive = false;

        if ($events->isEmpty()) {
            $archive = (clone $query)
                ->where('date', '<', now())
                ->orderByDesc('date')
                ->take(5)
                ->get();

            if ($archive->isNotEmpty()) {
                $events = $archive;
                $showingArchive = true;
            }
        }

        return response()
            ->view('welcome', compact('events', 'categories', 'partners', 'showingArchive'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }
}