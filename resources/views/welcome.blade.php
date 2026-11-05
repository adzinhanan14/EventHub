@extends('layouts.app')

@section('title', 'AmikomEventHub | Agenda acara kampus')
@section('meta_description', 'Jelajahi agenda acara kampus, pilih kategori, dan lihat informasi tiket di AmikomEventHub.')

@section('content')
    @php
        $selectedCategory = request('category');
    @endphp

    <section class="bg-green-900 text-white">
        <div class="mx-auto max-w-5xl px-4 py-16 text-center sm:px-6 sm:py-20">
            <p class="text-sm font-bold text-green-200">AGENDA KAMPUS</p>
            <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl">
                Temukan acara kampus yang ingin kamu datangi.
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-green-100 sm:text-lg">
                Cari jadwal acara, lokasi, dan informasi tiket dalam satu halaman.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="#events" class="inline-flex min-h-11 items-center rounded-lg bg-white px-5 font-semibold text-green-900 hover:bg-green-50">
                    Lihat jadwal event
                </a>
                <a href="{{ route('organizer.login') }}" class="inline-flex min-h-11 items-center rounded-lg border border-green-200 px-5 font-semibold text-white hover:bg-green-800">
                    Masuk sebagai panitia
                </a>
            </div>
        </div>
    </section>

    <section id="kategori" class="scroll-mt-24 bg-white px-4 py-10 sm:px-6 sm:py-12">
        <div class="mx-auto max-w-5xl text-center">
            <h2 class="text-2xl font-bold text-slate-950">Pilih kategori acara</h2>
            <p class="mt-2 text-slate-700">Saring agenda sesuai minatmu.</p>

            <nav aria-label="Filter kategori event" class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="{{ route('home') }}#events"
                    @if (!$selectedCategory) aria-current="true" @endif
                    class="inline-flex min-h-11 items-center rounded-lg border px-4 font-semibold {{ !$selectedCategory ? 'border-green-800 bg-green-800 text-white' : 'border-slate-500 bg-white text-slate-800 hover:bg-green-50' }}">
                    Semua event
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('home', ['category' => $category->slug]) }}#events"
                        @if ($selectedCategory === $category->slug) aria-current="true" @endif
                        class="inline-flex min-h-11 items-center rounded-lg border px-4 font-semibold {{ $selectedCategory === $category->slug ? 'border-green-800 bg-green-800 text-white' : 'border-slate-500 bg-white text-slate-800 hover:bg-green-50' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    <section id="events" class="scroll-mt-24 border-y border-green-200 bg-green-50 px-4 py-11 sm:px-6 sm:py-14">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7 text-center">
                <h2 class="text-2xl font-bold text-slate-950">
                    @if ($showingArchive)
                        Arsip acara
                    @else
                        Event mendatang
                    @endif
                </h2>
                <p class="mx-auto mt-2 max-w-2xl text-slate-700">
                    @if ($showingArchive)
                        Jadwal mendatang belum tersedia. Berikut event terakhir yang tercatat.
                    @elseif ($selectedCategory)
                        Menampilkan jadwal untuk kategori {{ $categories->firstWhere('slug', $selectedCategory)?->name ?? 'pilihan' }}.
                    @else
                        Pilih acara untuk melihat jadwal dan informasi tiket.
                    @endif
                </p>
                @if ($selectedCategory)
                    <a href="{{ route('home') }}#events" class="mt-3 inline-flex min-h-11 items-center font-semibold text-green-800 underline underline-offset-4">
                        Hapus filter
                    </a>
                @endif
            </div>

            @if ($events->isNotEmpty())
                <div class="mx-auto flex max-w-6xl flex-wrap justify-center gap-4">
                    @foreach ($events as $event)
                        <article class="flex w-full flex-col overflow-hidden rounded-xl border border-slate-300 bg-white sm:w-[calc((100%_-_1rem)/2)] xl:w-[calc((100%_-_2rem)/3)]">
                            @if ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                                <img src="{{ asset('storage/' . $event->poster_path) }}" alt="Poster {{ $event->title }}" class="aspect-[16/9] w-full object-cover" loading="lazy">
                            @else
                                <div class="flex aspect-[16/9] items-center justify-center bg-green-100 px-5 text-center text-sm font-semibold text-green-900">
                                    <div>
                                        <svg aria-hidden="true" class="mx-auto mb-2 h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <rect x="3.75" y="5.75" width="16.5" height="14.5" rx="2"></rect>
                                            <path d="M7.75 3.75v4M16.25 3.75v4M4 10h16"></path>
                                        </svg>
                                        Poster belum tersedia
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col p-5">
                                @if ($event->category)
                                    <p class="text-sm font-semibold text-green-800">{{ $event->category->name }}</p>
                                @endif
                                <h3 class="mt-2 text-lg font-bold leading-snug text-slate-950">
                                    @if ($showingArchive)
                                        {{ $event->title }}
                                    @else
                                        <a href="{{ route('events.show', $event->id) }}" class="rounded hover:text-green-800">
                                            {{ $event->title }}
                                        </a>
                                    @endif
                                </h3>
                                <p class="mt-3 text-sm font-medium text-slate-700">
                                    <time datetime="{{ $event->date->toIso8601String() }}">{{ $event->date->locale('id')->translatedFormat('l, d F Y, H.i') }}</time>
                                </p>
                                @if ($event->location)
                                    <p class="mt-1 text-sm text-slate-700">{{ $event->location }}</p>
                                @endif
                                @if ($event->organization)
                                    <p class="mt-3 text-sm text-slate-700">{{ $event->organization->name }}</p>
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
                                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-sm font-semibold text-slate-800">Tiket habis</span>
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
            @else
                <div class="mx-auto max-w-2xl rounded-xl border border-slate-300 bg-white p-6 text-center sm:p-8" role="status">
                    <h3 class="text-lg font-bold text-slate-950">Belum ada event mendatang</h3>
                    <p class="mt-2 leading-6 text-slate-700">
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
        <section class="bg-white px-4 py-10 sm:px-6 sm:py-12">
            <div class="mx-auto max-w-6xl">
                <h2 class="text-center text-xl font-bold text-slate-950">Partner</h2>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-4">
                    @foreach ($partners as $partner)
                        <div class="flex min-h-20 min-w-32 items-center justify-center rounded-lg border border-slate-300 bg-white p-4">
                            @if ($partner->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($partner->logo))
                                <img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}" class="max-h-12 max-w-40 object-contain" loading="lazy">
                            @else
                                <span class="text-center font-semibold text-slate-800">{{ $partner->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="team" class="scroll-mt-24 bg-slate-950 px-4 py-11 text-white sm:px-6 sm:py-14">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-xl font-bold">Tim pengembang</h2>
            <p class="mt-2 text-center text-slate-300">Tim yang mengembangkan AmikomEventHub.</p>

            @php
                $ourTeam = [
                    ['name' => 'Allaudya Mutiara Annida', 'nim' => '24.12.3406', 'photo' => 'assets/team/allaudya-mutiara-annida.png'],
                    ['name' => 'Yoana Dovina Fallen', 'nim' => '24.12.3391', 'photo' => 'assets/team/yoana-dovina-fallen.png'],
                    ['name' => 'Abdullah Adzin Hanan', 'nim' => '24.12.3405', 'photo' => 'assets/team/abdullah-adzin-hanan.png'],
                ];
            @endphp

            <div class="mt-6 grid justify-items-center gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($ourTeam as $member)
                    <div class="flex w-full max-w-md items-center gap-4 rounded-lg border border-slate-700 p-4">
                        <img src="{{ asset($member['photo']) }}" alt="" class="h-14 w-14 rounded-full object-cover" loading="lazy">
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
