@extends('layouts.organizer')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan performa organisasi Anda')

@section('content')

@if($organization->isPending())
<div class="bg-amber-100 border border-amber-300 text-amber-800 p-4 rounded-xl mb-6 font-semibold text-sm">
    Organisasi Anda masih berstatus <b>menunggu verifikasi</b> Superadmin.
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-3xl shadow-sm border">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Total Pendapatan</p>
        <p class="text-2xl font-black text-indigo-600">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Tiket Terjual</p>
        <p class="text-2xl font-black">{{ $totalTiketTerjual }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Total Event</p>
        <p class="text-2xl font-black">{{ $totalEvent }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Rating Rata-rata</p>
        <p class="text-2xl font-black text-amber-500">⭐ {{ $ratingRataRata ?: '-' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-sm border">
        <h3 class="font-bold text-lg mb-4">Tren Pendapatan (6 Bulan Terakhir)</h3>
        <canvas id="revenueChart" height="110"></canvas>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border">
        <h3 class="font-bold text-lg mb-4">Transaksi Terbaru</h3>
        <div class="space-y-4">
            @forelse($transaksiTerakhir as $trx)
            <div class="flex justify-between items-center text-sm">
                <div>
                    <p class="font-bold">{{ Str::limit($trx->customer_name, 18) }}</p>
                    <p class="text-slate-400 text-xs">{{ Str::limit($trx->event->title ?? '-', 20) }}</p>
                </div>
                <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $trx->status === 'Success' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }}">
                    {{ $trx->status }}
                </span>
            </div>
            @empty
            <p class="text-slate-400 text-sm">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm border">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-lg">Event Terbaru</h3>
        <a href="{{ route('organizer.events.create') }}" class="text-sm font-bold text-indigo-600 hover:underline">+ Buat Event</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($events as $event)
        <div class="border rounded-2xl p-4">
            <p class="font-bold">{{ $event->title }}</p>
            <p class="text-xs text-slate-400">{{ $event->category->name ?? '-' }} &middot; {{ $event->date?->translatedFormat('d M Y') }}</p>
            <p class="text-xs text-slate-500 mt-2">Stok: {{ $event->stock }}</p>
        </div>
        @empty
        <p class="text-slate-400 text-sm">Belum ada event. Yuk buat event pertama Anda!</p>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: @json($revenueChart['labels']),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($revenueChart['data']),
                borderColor: '#166534',
                backgroundColor: 'rgba(22,101,52,0.1)',
                fill: true,
                tension: 0.4,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
