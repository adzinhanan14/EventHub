@extends('layouts.app')

@section('content')

    <!-- 1. Hero Banner Carousel -->
    <section class="max-w-[90rem] mx-auto px-4 sm:px-6 py-6" id="heroCarousel">
        <div class="relative w-full h-[300px] md:h-[450px] rounded-3xl overflow-hidden shadow-xl group">
            <!-- Slides Container -->
            <div id="carouselSlides" class="w-full h-full relative">
                <!-- Slide 1 -->
                <img src="{{ asset('assets/concert.png') }}" alt="Hero Banner 1" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-100" data-index="0">
                <!-- Slide 2 -->
                <img src="{{ asset('assets/hackathon.png') }}" alt="Hero Banner 2" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-0" data-index="1">
                <!-- Slide 3 -->
                <img src="{{ asset('assets/workshop.png') }}" alt="Hero Banner 3" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 opacity-0" data-index="2">
            </div>
            
            <!-- Controls -->
            <button onclick="prevSlide()" class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-md text-slate-800 font-bold opacity-0 group-hover:opacity-100 transition focus:outline-none z-10" aria-label="Previous">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button onclick="nextSlide()" class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-md text-slate-800 font-bold opacity-0 group-hover:opacity-100 transition focus:outline-none z-10" aria-label="Next">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
            
            <!-- Dots -->
            <div id="carouselDots" class="absolute bottom-6 left-0 right-0 flex justify-center gap-2 z-10">
                <button onclick="goToSlide(0)" class="w-8 h-2 bg-indigo-600 rounded-full transition-all focus:outline-none" data-dot="0"></button>
                <button onclick="goToSlide(1)" class="w-2 h-2 bg-slate-300 rounded-full transition-all focus:outline-none" data-dot="1"></button>
                <button onclick="goToSlide(2)" class="w-2 h-2 bg-slate-300 rounded-full transition-all focus:outline-none" data-dot="2"></button>
            </div>
        </div>

        <!-- Vanilla JS for Carousel -->
        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('#carouselSlides img');
            const dots = document.querySelectorAll('#carouselDots button');
            const totalSlides = slides.length;
            let slideInterval;

            function updateCarousel() {
                slides.forEach((slide, index) => {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0');
                    if (index === currentSlide) {
                        slide.classList.remove('opacity-0');
                        slide.classList.add('opacity-100');
                    }
                });
                dots.forEach((dot, index) => {
                    dot.className = index === currentSlide 
                        ? 'w-8 h-2 bg-indigo-600 rounded-full transition-all focus:outline-none'
                        : 'w-2 h-2 bg-slate-300 rounded-full transition-all focus:outline-none';
                });
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
                resetInterval();
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
                resetInterval();
            }

            function goToSlide(index) {
                currentSlide = index;
                updateCarousel();
                resetInterval();
            }

            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            }

            // Start autobased slide
            resetInterval();
        </script>
    </section>

    <!-- 2. Rekomendasi Event -->
    <section class="max-w-[90rem] mx-auto px-4 sm:px-6 py-10">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Rekomendasi Event</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($events->take(4) as $event)
                @php
                    $isPastEvent = $event->isPastEvent();
                    $isSoldOut = $event->isSoldOut();
                    $isUnavailable = $isPastEvent || $isSoldOut;
                @endphp
                <a href="{{ route('events.show', $event->id) }}" class="block bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-lg transition overflow-hidden {{ $isUnavailable ? 'opacity-70' : '' }}">
                    <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                        @if($event->poster_path)
                            <img src="{{ asset('storage/' . $event->poster_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('assets/concert.png') }}" class="w-full h-full object-cover">
                        @endif
                        
                        @if($isPastEvent)
                        <div class="absolute top-2 right-2 px-2 py-1 bg-slate-800/90 rounded text-[10px] font-bold uppercase text-white">Telah Berakhir</div>
                        @elseif($isSoldOut)
                        <div class="absolute top-2 right-2 px-2 py-1 bg-red-600/90 rounded text-[10px] font-bold uppercase text-white">Sold Out</div>
                        @endif
                    </div>
                    <div class="p-4 border-b border-dashed border-slate-200">
                        <h3 class="text-[17px] font-bold text-slate-900 line-clamp-1 mb-2">{{ $event->title }}</h3>
                        <div class="flex items-center gap-2 text-indigo-500 font-semibold text-sm mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ $event->date->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="text-xl font-black text-slate-900">
                            Rp{{ number_format($event->price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="px-4 py-3 flex items-center gap-3 bg-slate-50/50">
                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs text-slate-500">
                            AO
                        </div>
                        <span class="text-sm font-semibold text-slate-600 line-clamp-1">Amikom Organizer</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 3. Laris Manis -->
    <section class="max-w-[90rem] mx-auto py-16 px-4 sm:px-6 bg-gradient-to-r from-[#173BB0] via-indigo-600 to-[#7828C8] my-8 relative overflow-hidden" style="box-shadow: inset 0 0 100px rgba(0,0,0,0.1);">
        <div class="relative z-10 text-center text-white mb-10">
            <h2 class="text-3xl font-extrabold mb-2 tracking-wide">Laris Manis</h2>
            <p class="text-blue-100 text-sm font-medium">Kumpulan event-event laris manis di AmikomEventHub yang mungkin kamu sukai</p>
        </div>
        
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @foreach($events->take(3) as $event)
            <a href="{{ route('events.show', $event->id) }}" class="block rounded-[1.25rem] overflow-hidden shadow-2xl hover:scale-105 transition-transform duration-300 ring-4 ring-white/10 hover:ring-white/30 aspect-[21/9] bg-slate-800">
                @if($event->poster_path)
                    <img src="{{ asset('storage/' . $event->poster_path) }}" class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('assets/concert.png') }}" class="w-full h-full object-cover">
                @endif
            </a>
            @endforeach
        </div>
        <!-- (Removed the Neverland Bandung statically hardcoded text from here) -->
    </section>

    <!-- 4. Kategori Event -->
    <section id="kategori" class="max-w-[90rem] mx-auto px-4 sm:px-6 py-12">
        <h2 class="text-2xl font-bold text-slate-900 mb-8">Kategori Event</h2>
        <div class="flex gap-4 md:gap-8 overflow-x-auto pb-4 scrollbar-hide justify-start md:justify-center">
            @php
                // Map category name keywords to SVG paths for custom dynamic icons
                $catIcons = [
                    'musik' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>',
                    'pameran' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                    'teater' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>',
                    'olahraga' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'wisata' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
                    'wahana' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'workshop' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>',
                    'talkshow' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>',
                    'kompetisi' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>',
                ];
            @endphp
            
            @foreach($categories as $index => $cat)
             @php
                $lowerName = strtolower($cat->name);
                $svgPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>'; // default
                foreach($catIcons as $key => $path) {
                    if (str_contains($lowerName, $key)) {
                        $svgPath = $path;
                        break;
                    }
                }
             @endphp
             <a href="/?category={{ $cat->slug }}#events" class="flex flex-col items-center gap-3 min-w-[80px] group shrink-0">
                 <div class="w-[72px] h-[72px] rounded-full bg-white shadow-md flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 border border-slate-100 group-hover:shadow-lg group-hover:-translate-y-1">
                     <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $svgPath !!}</svg>
                 </div>
                 <span class="text-[13px] font-bold text-slate-700 group-hover:text-blue-600">{{ $cat->name }}</span>
             </a>
            @endforeach
            <!-- Fallback static if no categories exist -->
            @if($categories->isEmpty())
             <div class="flex flex-col items-center gap-3 w-[80px]">
                 <div class="w-[72px] h-[72px] rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                 </div>
                 <span class="text-[13px] font-bold text-slate-500">Lainnya</span>
             </div>
            @endif
        </div>
    </section>

    <!-- 5. Event Terdekat -->
    <section id="events" class="bg-slate-50">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-bold text-slate-900 mb-8">Event Terdekat</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($events as $event)
                    @php
                        $isPastEvent = $event->isPastEvent();
                        $isSoldOut = $event->isSoldOut();
                        $isUnavailable = $isPastEvent || $isSoldOut;
                    @endphp
                    <a href="{{ route('events.show', $event->id) }}" class="block bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-lg transition hover:-translate-y-1 overflow-hidden {{ $isUnavailable ? 'opacity-70' : '' }}">
                        <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                            @if($event->poster_path)
                                <img src="{{ asset('storage/' . $event->poster_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('assets/concert.png') }}" class="w-full h-full object-cover">
                            @endif
                            
                            @if($isPastEvent)
                            <div class="absolute top-2 right-2 px-2 py-1 bg-slate-800/90 rounded text-[10px] font-bold uppercase text-white">Telah Berakhir</div>
                            @elseif($isSoldOut)
                            <div class="absolute top-2 right-2 px-2 py-1 bg-red-600/90 rounded text-[10px] font-bold uppercase text-white">Sold Out</div>
                            @endif
                        </div>
                        <div class="p-4 border-b border-dashed border-slate-200">
                            <h3 class="text-[17px] font-bold text-slate-900 line-clamp-1 mb-2">{{ $event->title }}</h3>
                            <div class="flex items-center gap-2 text-indigo-500 font-semibold text-sm mb-3">
                                <svg class="w-4 h-4" transform="translate(0, -1)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $event->date->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="text-[19px] font-black text-slate-900">
                                Rp{{ number_format($event->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="px-4 py-3 flex items-center gap-3">
                            <div class="w-[26px] h-[26px] rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white uppercase overflow-hidden">
                                @if(isset($event->organizer) && $event->organizer->id)
                                    {{ substr($event->organizer->name, 0, 2) }}
                                @else
                                    AM
                                @endif
                            </div>
                            <span class="text-[13px] font-semibold text-slate-600 line-clamp-1">
                                {{ $event->organizer->name ?? 'Amikom Organizer' }}
                            </span>
                        </div>
                    </a>
                @empty
                <div class="col-span-1 md:col-span-4 text-center py-20 flex flex-col items-center">
                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-slate-500 text-lg font-semibold">Tidak ada event terdekat saat ini.</p>
                </div>
                @endforelse
            </div>
            
            @if($events->count() > 0)
            <div class="text-center mt-10">
                <a href="/#events" class="inline-block px-8 py-2.5 bg-indigo-50 text-indigo-600 font-bold rounded hover:bg-indigo-100 transition shadow-sm text-sm">Lihat Lainnya</a>
            </div>
            @endif
        </div>
    </section>

    <!-- 6. Partner Kami -->
    <section class="max-w-[90rem] mx-auto px-4 sm:px-6 py-16">
        <div class="flex flex-col items-start md:items-center md:text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Partner Kami</h2>
        </div>
        @if($partners->count() > 0)
        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-6 justify-center">
            @foreach($partners as $partner)
            <div class="rounded-xl border border-slate-100 shadow-sm p-4 flex items-center justify-center hover:shadow-md transition bg-white h-20">
                @if($partner->logo)
                <img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}" class="max-w-full max-h-full object-contain filter grayscale hover:filter-none transition">
                @else
                <span class="text-slate-400 text-xs font-bold text-center">{{ $partner->name }}</span>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
            <p class="text-slate-500 font-medium">Belum ada partner yang ditambahkan.</p>
        </div>
        @endif
    </section>

    <!-- 7. Our Team -->
    <section id="team" class="max-w-[90rem] mx-auto px-4 sm:px-6 py-10 mb-16">
        <div class="flex flex-col items-start md:items-center md:text-center mb-10">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Our Team</h2>
            <p class="text-slate-500 text-sm font-medium">Tim Kreatif Pengembang AmikomEventHub</p>
        </div>
        @php
            $ourTeam = [
                ['name' => 'Allaudya Mutiara Annida', 'nim' => '24.12.3406', 'photo' => 'assets/team/allaudya-mutiara-annida.png'],
                ['name' => 'Yoana Dovina Fallen',      'nim' => '24.12.3391', 'photo' => 'assets/team/yoana-dovina-fallen.png'],
                ['name' => 'Abdullah Adzin Hanan',     'nim' => '24.12.3405', 'photo' => 'assets/team/abdullah-adzin-hanan.png'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center justify-items-center max-w-4xl mx-auto">
            @foreach($ourTeam as $member)
            <div class="flex flex-col items-center text-center group">
                <div class="w-32 h-32 rounded-full ring-4 ring-slate-100 overflow-hidden mb-4 shadow-sm group-hover:ring-indigo-100 group-hover:shadow-lg transition-all duration-300">
                    <img src="{{ asset($member['photo']) }}"
                         alt="{{ $member['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <h3 class="font-bold text-slate-900 text-lg">{{ $member['name'] }}</h3>
                <p class="text-indigo-600 font-semibold text-sm">{{ $member['nim'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

@endsection