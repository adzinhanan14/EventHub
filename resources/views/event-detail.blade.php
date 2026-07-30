@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Left: Poster -->
    <div class="lg:col-span-1">
        <div class="sticky top-32">
            <img src="{{ $event->poster_path ? asset('storage/' . $event->poster_path) : 'assets/concert.png' }}"
                alt="{{ $event->title }}"
                class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white">
            <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="font-bold mb-4">Penyelenggara</h4>
                <a href="{{ $event->organization ? route('organizations.show', $event->organization->slug) : '#' }}" class="flex items-center gap-4 hover:opacity-80 transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                        {{ strtoupper(substr($event->organization->name ?? 'AH', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $event->organization->name ?? 'AmikomEventHub' }}</p>
                        <p class="text-xs text-slate-500">⭐ {{ $event->organization->averageRating() ?: '-' }} rating &middot; Lihat profil</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Right: Details -->
    <div class="lg:col-span-2 space-y-12">
        <div class="mb-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:text-indigo-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Halaman Beranda
            </a>
        </div>
        <div class="space-y-4">
            <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">
                {{ $event->category->name ?? 'Umum' }}
            </span>
            <h1 class="text-4xl md:text-5xl font-black leading-tight">{{ $event->title }}</h1>
            <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>{{ $event->date->format('l, d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $event->location }}</span>
                </div>
            </div>
        </div>

        <div class="prose prose-slate max-w-none">
            <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
            <p class="text-lg text-slate-600 leading-relaxed">
                {{ $event->description ?? 'Deskripsi event belum tersedia.' }}
            </p>
        </div>

        <!-- Harga & Tombol Pesan -->
        <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div>
                    <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                    <h2 class="text-5xl font-black">
                        @if($event->currentPrice() > 0)
                            Rp {{ number_format($event->currentPrice(), 0, ',', '.') }}
                            <span class="text-lg font-medium text-indigo-200">/ orang</span>
                        @else
                            GRATIS
                        @endif
                    </h2>
                    @if($event->activeTier())
                    <p class="mt-1 text-indigo-200 text-sm font-bold uppercase tracking-wide">{{ $event->activeTier()->name }}</p>
                    @endif
                    <p class="mt-4 text-indigo-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                    </p>
                </div>
                <div>
                    {{-- ✅ PERBAIKAN: pakai route() dengan ID event --}}
                    <a href="{{ route('checkout.create', $event->id) }}"
                        class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
            <!-- Decoration -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
            <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Ulasan Peserta ⭐ {{ $event->averageRating() ?: '-' }} ({{ $event->reviews()->count() }})</h3>
                @if ($eligibleTransaction)
                <a href="{{ route('reviews.create', $event->id) }}" class="text-sm font-bold text-indigo-600 hover:underline">Beri Ulasan &rarr;</a>
                @elseif (auth()->check())
                <span class="text-xs text-slate-400 font-semibold text-right">Ulasan bisa diberikan setelah Anda membeli tiket &amp; acara selesai (H+1)</span>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($event->reviews()->latest()->take(4)->get() as $review)
                <div class="bg-white rounded-2xl border p-5">
                    <div class="flex justify-between items-center mb-2">
                        <p class="font-bold text-sm">{{ $review->user->name ?? 'Peserta' }}</p>
                        <span class="text-amber-500 text-sm">{{ str_repeat('⭐', $review->rating) }}</span>
                    </div>
                    @if($review->comment)<p class="text-sm text-slate-600">{{ $review->comment }}</p>@endif
                </div>
                @empty
                <p class="text-slate-400 text-sm">Belum ada ulasan untuk event ini.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
            <ul class="space-y-3 text-slate-500">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Tiket dapat discan di pintu masuk (Check-in).
                </li>
                <li class="flex items-start gap-2 text-rose-500">
                    <svg class="w-5 h-5 text-rose-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tiket yang sudah dibeli tidak dapat direfund.
                </li>
            </ul>
        </div>
    </div>
</main>
@endsection