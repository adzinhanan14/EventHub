<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $organization = Auth::user()->organization;

        $eventIds = Event::where('organization_id', $organization->id)->pluck('id');

        $totalPendapatan = Transaction::whereIn('event_id', $eventIds)->where('status', 'Success')->sum('total_price');
        $totalTiketTerjual = Transaction::whereIn('event_id', $eventIds)->where('status', 'Success')->count();
        $totalEvent = $eventIds->count();
        $transaksiPending = Transaction::whereIn('event_id', $eventIds)->where('status', 'Pending')->count();
        $transaksiTerakhir = Transaction::with('event')->whereIn('event_id', $eventIds)->latest()->take(5)->get();
        $events = Event::with('category')->where('organization_id', $organization->id)->latest()->take(6)->get();
        $ratingRataRata = $organization->averageRating();

        // Data grafik: pendapatan 6 bulan terakhir
        $revenueChart = $this->monthlyRevenue($eventIds);

        return view('organizer.dashboard', compact(
            'organization', 'totalPendapatan', 'totalTiketTerjual', 'totalEvent',
            'transaksiPending', 'transaksiTerakhir', 'events', 'ratingRataRata', 'revenueChart'
        ));
    }

    private function monthlyRevenue($eventIds): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $data[] = (int) Transaction::whereIn('event_id', $eventIds)
                ->where('status', 'Success')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
