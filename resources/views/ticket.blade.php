@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto px-6 py-16">
    <div class="mb-10 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-3xl font-black">Tiket Saya</h1>
            <p class="text-slate-500 mt-1">Semua tiket yang pernah Anda beli — tiket yang acaranya sudah lewat tetap tersimpan di sini.</p>
        </div>
        <a href="{{ route('home') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 border border-slate-200 hover:bg-slate-100 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    @if ($upcomingTickets->isEmpty() && $pastTickets->isEmpty())
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-slate-500 mb-6">Anda belum memiliki tiket. Yuk jelajahi event menarik!</p>
            <a href="{{ route('home') }}"
                class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition">
                Jelajahi Event
            </a>
        </div>
    @else
        @if ($upcomingTickets->isNotEmpty())
            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Tiket Mendatang</h2>
            @foreach ($upcomingTickets as $trx)
                @include('partials.ticket-card', ['trx' => $trx])
            @endforeach
        @endif

        @if ($pastTickets->isNotEmpty())
            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 {{ $upcomingTickets->isNotEmpty() ? 'mt-10' : '' }}">Riwayat Tiket</h2>
            @foreach ($pastTickets as $trx)
                @include('partials.ticket-card', ['trx' => $trx])
            @endforeach
        @endif
    @endif
</div>
@endsection
