@extends('layouts.app')
@section('content')

<div class="max-w-6xl mx-auto px-6 py-16">
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10 mb-10 flex flex-col md:flex-row gap-8 items-start">
        <div class="w-24 h-24 bg-indigo-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl shrink-0">
            {{ strtoupper(substr($organization->name, 0, 2)) }}
        </div>
        <div class="flex-1">
            <h1 class="text-3xl font-black">{{ $organization->name }}</h1>
            <p class="text-slate-500 mt-2">{{ $organization->description ?? 'Belum ada deskripsi.' }}</p>
            <div class="flex items-center gap-6 mt-4">
                <div class="flex items-center gap-1">
                    <span class="text-amber-500 text-xl">⭐</span>
                    <span class="font-black text-lg">{{ $averageRating ?: '-' }}</span>
                    <span class="text-slate-400 text-sm">({{ $totalReviews }} ulasan)</span>
                </div>
                <div class="text-sm text-slate-400">{{ $organization->events()->count() }} event diselenggarakan</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-black mb-6">Event dari {{ $organization->name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($events as $event)
                <a href="{{ route('events.show', $event->id) }}" class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition overflow-hidden">
                    <div class="aspect-[3/2] overflow-hidden">
                        <img src="{{ $event->poster_path ? asset('storage/'.$event->poster_path) : 'https://placehold.co/400x300' }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition">
                    </div>
                    <div class="p-5">
                        <p class="font-bold group-hover:text-indigo-600 transition">{{ $event->title }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $event->date?->translatedFormat('d M Y') }}</p>
                    </div>
                </a>
                @empty
                <p class="text-slate-400 text-sm col-span-2">Belum ada event.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $events->links() }}</div>
        </div>

        <div>
            <h2 class="text-2xl font-black mb-6">Ulasan Peserta</h2>
            <div class="space-y-4">
                @forelse($reviews as $review)
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-sm">{{ $review->user->name ?? 'Peserta' }}</p>
                        <span class="text-amber-500 text-sm">{{ str_repeat('⭐', $review->rating) }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mb-2">{{ $review->event->title ?? '-' }}</p>
                    @if($review->comment)
                    <p class="text-sm text-slate-600">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <p class="text-slate-400 text-sm">Belum ada ulasan untuk organisasi ini.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $reviews->links() }}</div>
        </div>
    </div>
</div>
@endsection
