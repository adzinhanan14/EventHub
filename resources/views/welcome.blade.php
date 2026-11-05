@extends('layouts.app')

@section('title', 'AmikomEventHub | Agenda acara kampus')
@section('meta_description', 'Jelajahi agenda acara kampus, pilih kategori, dan lihat informasi tiket di AmikomEventHub.')

@section('content')
    @php
        $selectedCategory = request('category');
        $featuredEvent = $events->first();
        $featuredPosterExists = $featuredEvent
            && $featuredEvent->poster_path
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($featuredEvent->poster_path);
        $stockPhotos = [
            'assets/landing/event-audience.jpg',
            'assets/landing/workshop.jpg',
            'assets/landing/conference-room.jpg',
            'assets/landing/event-crowd.jpg',
        ];
    @endphp

    {{-- Design read: campus event discovery for students, photo-led editorial style, ENERGY 2 / RHYTHM 3 / MOTION 1. The dark scrim keeps hero text readable; stock photos stay labeled until organizers upload event posters. --}}
    <section class="relative isolate flex min-h-[34rem] items-center justify-center overflow-hidden bg-slate-950 px-4 py-16 text-center text-white sm:min-h-[38rem] sm:px-6 lg:min-h-[42rem]">
        @if ($featuredPosterExists)
            <img src="{{ asset('storage/' . $featuredEvent->poster_path) }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" fetchpriority="high">
        @else
            <img src="{{ asset($stockPhotos[0]) }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" fetchpriority="high">
        @endif
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-t from-black/75 via-black/60 to-black/35"></div>
        <div class="mx-auto max-w-4xl">
            <p class="text-sm font-bold tracking-[0.12em] text-green-200">AGENDA KAMPUS AMIKOM</p>
            <h1 class="mx-auto mt-5 max-w-4xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                Temukan acara kampus yang ingin kamu datangi.
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-white/95 sm:text-lg">
                Lihat jadwal, lokasi, dan informasi tiket acara kampus.
            </p>
            <a href="#events" class="mt-8 inline-flex min-h-12 items-center justify-center rounded-lg bg-green-700 px-6 font-semibold text-white transition hover:bg-green-800">
                Lihat jadwal acara
            </a>
        </div>
        @unless ($featuredPosterExists)
            <span class="absolute bottom-4 right-4 rounded bg-black/70 px-3 py-2 text-xs font-medium text-white sm:bottom-6 sm:right-6">
                Foto ilustrasi sementara
            </span>
        @endunless
    </section>

    <section id="kategori" class="scroll-mt-24 border-b border-slate-200 bg-white px-4 py-8 sm:px-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="shrink-0 text-lg font-bold text-slate-950">Jelajahi kategori</h2>
            <nav aria-label="Filter kategori event" class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}#events"
                    @if (!$selectedCategory) aria-current="true" @endif
                    class="inline-flex min-h-11 items-center rounded-md border px-4 font-semibold transition {{ !$selectedCategory ? 'border-green-800 bg-green-800 text-white' : 'border-slate-300 bg-white text-slate-800 hover:border-green-800 hover:bg-green-50' }}">
                    Semua event
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('home', ['category' => $category->slug]) }}#events"
                        @if ($selectedCategory === $category->slug) aria-current="true" @endif
                        class="inline-flex min-h-11 items-center rounded-md border px-4 font-semibold transition {{ $selectedCategory === $category->slug ? 'border-green-800 bg-green-800 text-white' : 'border-slate-300 bg-white text-slate-800 hover:border-green-800 hover:bg-green-50' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    <section id="events" class="scroll-mt-24 bg-slate-50 px-4 py-12 sm:px-6 sm:py-16">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-green-800">JADWAL ACARA</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">
                        @if ($showingArchive)
                            Arsip acara
                        @else
                            Event mendatang
                        @endif
                    </h2>
                    <p class="mt-2 max-w-2xl leading-6 text-slate-700">
                        @if ($showingArchive)
                            Jadwal mendatang belum tersedia. Berikut event terakhir yang tercatat.
                        @elseif ($selectedCategory)
                            Menampilkan jadwal untuk kategori {{ $categories->firstWhere('slug', $selectedCategory)?->name ?? 'pilihan' }}.
                        @else
                            Pilih acara untuk melihat jadwal dan informasi tiket.
                        @endif
                    </p>
                </div>
                @if ($selectedCategory)
                    <a href="{{ route('home') }}#events" class="inline-flex min-h-11 items-center font-semibold text-green-800 underline underline-offset-4">
                        Hapus filter
                    </a>
                @endif
            </div>

            @if ($events->isNotEmpty())
                @php
                    $featuredEventPoster = $featuredEvent->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($featuredEvent->poster_path);
                    $featuredStockPhoto = $stockPhotos[1];
                @endphp
                <article class="mb-6 grid overflow-hidden rounded-2xl border border-slate-200 bg-white lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="relative aspect-[16/10] overflow-hidden bg-green-950 lg:aspect-auto lg:min-h-[28rem]">
                        @if ($featuredEventPoster)
                            <img src="{{ asset('storage/' . $featuredEvent->poster_path) }}" alt="Poster {{ $featuredEvent->title }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        @else
                            <img src="{{ asset($featuredStockPhoto) }}" alt="Foto ilustrasi sementara untuk acara kampus" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            <span class="absolute bottom-3 left-3 rounded bg-black/70 px-3 py-2 text-xs font-medium text-white">Foto ilustrasi sementara</span>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-10">
                        <p class="text-sm font-bold text-green-800">
                            @if ($featuredEvent->category)
                                {{ $featuredEvent->category->name }}
                            @else
                                {{ $showingArchive ? 'ARSIP ACARA' : 'EVENT PILIHAN' }}
                            @endif
                        </p>
                        <h3 class="mt-3 text-2xl font-bold leading-snug text-slate-950 sm:text-3xl">
                            @if ($showingArchive)
                                {{ $featuredEvent->title }}
                            @else
                                <a href="{{ route('events.show', $featuredEvent->id) }}" class="rounded hover:text-green-800">
                                    {{ $featuredEvent->title }}
                                </a>
                            @endif
                        </h3>
                        <p class="mt-4 font-semibold text-slate-800">
                            <time datetime="{{ $featuredEvent->date->toIso8601String() }}">{{ $featuredEvent->date->locale('id')->translatedFormat('l, d F Y, H.i') }}</time>
                        </p>
                        @if ($featuredEvent->location)
                            <p class="mt-1 text-slate-700">{{ $featuredEvent->location }}</p>
                        @endif
                        @if ($featuredEvent->organization)
                            <p class="mt-4 text-sm text-slate-600">{{ $featuredEvent->organization->name }}</p>
                        @endif
                        <div class="mt-6 flex flex-wrap items-center gap-4">
                            @if ($showingArchive)
                                <span class="font-semibold text-slate-700">Event selesai</span>
                            @else
                                <p class="font-bold text-slate-950">
                                    @if ($featuredEvent->isFree())
                                        Gratis
                                    @else
                                        Rp{{ number_format($featuredEvent->currentPrice(), 0, ',', '.') }}
                                    @endif
                                </p>
                                @if ($featuredEvent->isSoldOut())
                                    <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-800">Tiket habis</span>
                                @else
                                    <a href="{{ route('events.show', $featuredEvent->id) }}" class="inline-flex min-h-11 items-center rounded-md bg-green-800 px-4 font-semibold text-white hover:bg-green-900">
                                        Lihat detail
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </article>

                @if ($events->count() > 1)
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($events->skip(1) as $event)
                            @php
                                $eventPosterExists = $event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path);
                                $stockPhoto = $stockPhotos[($loop->index + 2) % count($stockPhotos)];
                            @endphp
                            <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:shadow-md">
                                <div class="relative aspect-[16/10] overflow-hidden bg-green-950">
                                    @if ($eventPosterExists)
                                        <img src="{{ asset('storage/' . $event->poster_path) }}" alt="Poster {{ $event->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                                    @else
                                        <img src="{{ asset($stockPhoto) }}" alt="Foto ilustrasi sementara untuk acara kampus" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                                        <span class="absolute bottom-3 left-3 rounded bg-black/70 px-3 py-2 text-xs font-medium text-white">Foto ilustrasi sementara</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    @if ($event->category)
                                        <p class="text-sm font-bold text-green-800">{{ $event->category->name }}</p>
                                    @endif
                                    <h3 class="mt-2 text-xl font-bold leading-snug text-slate-950">
                                        @if ($showingArchive)
                                            {{ $event->title }}
                                        @else
                                            <a href="{{ route('events.show', $event->id) }}" class="rounded hover:text-green-800">{{ $event->title }}</a>
                                        @endif
                                    </h3>
                                    <p class="mt-3 text-sm font-medium text-slate-700">
                                        <time datetime="{{ $event->date->toIso8601String() }}">{{ $event->date->locale('id')->translatedFormat('l, d F Y, H.i') }}</time>
                                    </p>
                                    @if ($event->location)
                                        <p class="mt-1 text-sm text-slate-700">{{ $event->location }}</p>
                                    @endif
                                    @if ($event->organization)
                                        <p class="mt-3 text-sm text-slate-600">{{ $event->organization->name }}</p>
                                    @endif
                                    <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                                        @if ($showingArchive)
                                            <span class="font-semibold text-slate-700">Event selesai</span>
                                        @else
                                            <p class="font-bold text-slate-950">
                                                @if ($event->isFree())
                                                    Gratis
                                                @else
                                                    Rp{{ number_format($event->currentPrice(), 0, ',', '.') }}
                                                @endif
                                            </p>
                                            @if ($event->isSoldOut())
                                                <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-800">Tiket habis</span>
                                            @else
                                                <a href="{{ route('events.show', $event->id) }}" class="inline-flex min-h-11 items-center font-semibold text-green-800 hover:text-green-900">
                                                    Lihat detail
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center sm:px-10" role="status">
                    <h3 class="text-xl font-bold text-slate-950">Belum ada event mendatang</h3>
                    <p class="mx-auto mt-2 max-w-lg leading-6 text-slate-700">
                        @if ($selectedCategory)
                            Belum ada jadwal untuk kategori ini. Hapus filter untuk melihat kategori lain.
                        @else
                            Jadwal akan muncul di sini setelah panitia menambahkan acara.
                        @endif
                    </p>
                    @if ($selectedCategory)
                        <a href="{{ route('home') }}#events" class="mt-4 inline-flex min-h-11 items-center font-semibold text-green-800 underline underline-offset-4">
                            Tampilkan semua event
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($partners->isNotEmpty())
        <section class="bg-white px-4 py-11 sm:px-6">
            <div class="mx-auto max-w-7xl border-t border-slate-200 pt-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-bold text-slate-950">Partner acara</h2>
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($partners as $partner)
                            <div class="flex min-h-16 min-w-32 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-3">
                                @if ($partner->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($partner->logo))
                                    <img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}" class="max-h-10 max-w-36 object-contain" loading="lazy">
                                @else
                                    <span class="text-center text-sm font-semibold text-slate-800">{{ $partner->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section id="team" class="scroll-mt-24 bg-slate-950 px-4 py-12 text-white sm:px-6 sm:py-14">
        <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
            <div>
                <p class="text-sm font-bold text-green-300">TENTANG</p>
                <h2 class="mt-2 text-2xl font-bold">Tim AmikomEventHub</h2>
                <p class="mt-2 max-w-md leading-6 text-slate-300">Tim yang mengembangkan AmikomEventHub.</p>
            </div>

            @php
                $ourTeam = [
                    ['name' => 'Allaudya Mutiara Annida', 'nim' => '24.12.3406', 'photo' => 'assets/team/allaudya-mutiara-annida.png'],
                    ['name' => 'Yoana Dovina Fallen', 'nim' => '24.12.3391', 'photo' => 'assets/team/yoana-dovina-fallen.png'],
                    ['name' => 'Abdullah Adzin Hanan', 'nim' => '24.12.3405', 'photo' => 'assets/team/abdullah-adzin-hanan.png'],
                ];
            @endphp

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($ourTeam as $member)
                    <div class="flex items-center gap-4 border-b border-slate-700 py-3">
                        <img src="{{ asset($member['photo']) }}" alt="" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                        <div class="min-w-0">
                            <h3 class="font-bold">{{ $member['name'] }}</h3>
                            <p class="mt-1 text-sm text-slate-300">{{ $member['nim'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
