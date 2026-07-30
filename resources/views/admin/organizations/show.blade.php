@extends('layouts.admin')
@section('page_title', $organization->name)
@section('page_subtitle', 'Detail organisasi & riwayat event.')
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-[2rem] border shadow-sm">
        <h3 class="font-black mb-3">Info Organisasi</h3>
        <p class="text-sm text-slate-500 mb-1"><b>Email:</b> {{ $organization->email ?? '-' }}</p>
        <p class="text-sm text-slate-500 mb-1"><b>Telp:</b> {{ $organization->phone ?? '-' }}</p>
        <p class="text-sm text-slate-500 mb-1"><b>Status:</b> {{ ucfirst($organization->status) }}</p>
        <p class="text-sm text-slate-500 mt-3">{{ $organization->description ?? '-' }}</p>
    </div>

    <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] border shadow-sm">
        <h3 class="font-black mb-4">Daftar Event ({{ $organization->events->count() }})</h3>
        <div class="space-y-3">
            @forelse($organization->events as $event)
            <div class="flex justify-between items-center border rounded-xl px-4 py-3 text-sm">
                <div>
                    <p class="font-bold">{{ $event->title }}</p>
                    <p class="text-xs text-slate-400">{{ $event->date }}</p>
                </div>
                <span class="text-indigo-600 font-bold">{{ $event->price > 0 ? 'Rp'.number_format($event->price) : 'Gratis' }}</span>
            </div>
            @empty
            <p class="text-slate-400 text-sm">Belum ada event.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
