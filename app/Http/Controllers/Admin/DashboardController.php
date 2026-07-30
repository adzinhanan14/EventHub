<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendapatan = Transaction::where('status', 'Success')->sum('total_price');
        $totalTiketTerjual = Transaction::where('status', 'Success')->count();
        $totalEvent = Event::count();
        $transaksiPending = Transaction::where('status', 'Pending')->count();
        $transaksiTerakhir = Transaction::with('event')->latest()->take(5)->get();
        $events = Event::with('category', 'organization')->latest()->take(6)->get();

        $totalOrganisasi = Organization::count();
        $organisasiPending = Organization::where('status', 'pending')->count();
        $totalPembeli = User::where('role', 'user')->count();

        $revenueChart = $this->monthlyChart(fn ($q) => $q->where('status', 'Success'), 'total_price', 'sum');
        $userGrowthChart = $this->userGrowthChart();

        return view('admin.dashboard', compact(
            'totalPendapatan', 'totalTiketTerjual', 'totalEvent',
            'transaksiPending', 'transaksiTerakhir', 'events',
            'totalOrganisasi', 'organisasiPending', 'totalPembeli',
            'revenueChart', 'userGrowthChart'
        ));
    }

    private function monthlyChart(callable $filter, string $column, string $aggregate): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $query = $filter(Transaction::query())
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            $data[] = $aggregate === 'sum' ? (int) $query->sum($column) : $query->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function userGrowthChart(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $data[] = User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
