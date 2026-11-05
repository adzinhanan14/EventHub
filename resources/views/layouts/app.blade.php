<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Temukan acara kampus, lihat detail tiket, dan pesan tiket di AmikomEventHub.')">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'AmikomEventHub | Agenda acara kampus')</title>
    <script>
        if (localStorage.getItem('amikom-theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            scroll-padding-top: 6rem;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="font-sans bg-slate-50 text-slate-900">
    <a href="#main-content"
        class="absolute left-4 top-4 z-[100] -translate-y-20 rounded-lg bg-white px-4 py-3 font-semibold text-indigo-900 shadow focus:translate-y-0">
        Lewati ke konten
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="min-w-0 truncate rounded text-sm font-bold tracking-tight text-slate-950 sm:text-lg">
                AmikomEventHub
            </a>

            <nav aria-label="Navigasi utama" class="hidden items-center gap-7 xl:flex">
                <a href="{{ route('home') }}#events" class="min-h-11 flex items-center font-semibold text-slate-700 hover:text-indigo-800">Jelajahi</a>
                <a href="{{ route('home') }}#kategori" class="min-h-11 flex items-center font-semibold text-slate-700 hover:text-indigo-800">Kategori</a>
                <a href="{{ route('home') }}#team" class="min-h-11 flex items-center font-semibold text-slate-700 hover:text-indigo-800">Tentang tim</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="hidden items-center gap-2 xl:flex">
                    @auth
                        <span class="max-w-40 truncate text-sm font-semibold text-slate-600">Hai, {{ Auth::user()->name }}</span>
                        <a href="{{ route('ticket') }}" class="inline-flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Tiket saya</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="min-h-11 rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('organizer.login') }}" class="inline-flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Untuk panitia</a>
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-lg bg-indigo-800 px-4 font-semibold text-white hover:bg-indigo-900">Masuk</a>
                    @endauth
                    <button type="button" data-theme-toggle aria-pressed="false" class="min-h-11 rounded-lg border border-slate-500 px-3 font-semibold text-slate-800 hover:bg-slate-100">
                        Mode gelap
                    </button>
                </div>

                <details class="relative xl:hidden">
                    <summary class="flex min-h-11 cursor-pointer list-none items-center rounded-lg border border-slate-500 px-3 font-semibold text-slate-800">
                        Menu
                    </summary>
                    <nav aria-label="Navigasi seluler" class="absolute right-0 top-full mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                        <a href="{{ route('home') }}#events" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Jelajahi event</a>
                        <a href="{{ route('home') }}#kategori" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Kategori</a>
                        <a href="{{ route('home') }}#team" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Tentang tim</a>
                        @auth
                            <a href="{{ route('ticket') }}" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Tiket saya</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="min-h-11 w-full rounded-lg px-3 text-left font-semibold text-slate-700 hover:bg-slate-100">Keluar</button>
                            </form>
                        @else
                            <a href="{{ route('organizer.login') }}" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-slate-700 hover:bg-slate-100">Masuk sebagai panitia</a>
                            <a href="{{ route('login') }}" class="flex min-h-11 items-center rounded-lg px-3 font-semibold text-indigo-800 hover:bg-indigo-50">Masuk</a>
                        @endauth
                        <button type="button" data-theme-toggle aria-pressed="false" class="min-h-11 w-full rounded-lg px-3 text-left font-semibold text-slate-800 hover:bg-slate-100">
                            Mode gelap
                        </button>
                    </nav>
                </details>

                @guest
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-lg bg-indigo-800 px-3 font-semibold text-white hover:bg-indigo-900 xl:hidden">Masuk</a>
                @endguest
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6" role="status" aria-live="polite">
            <div class="rounded-lg border border-green-800 bg-green-50 px-5 py-4 font-semibold text-green-900">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6" role="alert">
            <div class="rounded-lg border border-rose-800 bg-rose-50 px-5 py-4 font-semibold text-rose-900">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6" role="alert">
            <div class="rounded-lg border border-rose-800 bg-rose-50 px-5 py-4 font-semibold text-rose-900">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white px-4 py-10 sm:px-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('home') }}" class="font-bold text-slate-950">AmikomEventHub</a>
                <p class="mt-2 max-w-md text-sm leading-6 text-slate-700">Temukan acara kampus, lihat detail tiket, dan lanjutkan pemesanan.</p>
            </div>
            <nav aria-label="Navigasi footer" class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                <a href="{{ route('home') }}#events" class="min-h-11 inline-flex items-center text-slate-700 hover:text-indigo-800">Jelajahi event</a>
                <a href="{{ route('home') }}#kategori" class="min-h-11 inline-flex items-center text-slate-700 hover:text-indigo-800">Kategori</a>
                <a href="{{ route('home') }}#team" class="min-h-11 inline-flex items-center text-slate-700 hover:text-indigo-800">Tentang tim</a>
            </nav>
        </div>
        <div class="mx-auto mt-6 max-w-7xl border-t border-slate-200 pt-5 text-sm text-slate-600">
            &copy; {{ now()->year }} AmikomEventHub
        </div>
    </footer>

</body>

</html>