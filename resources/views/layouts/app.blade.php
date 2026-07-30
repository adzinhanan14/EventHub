<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmikomEventHub - Temukan Event Seru!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav
        class="glass sticky top-8 z-40 mx-4 mt-4 px-6 py-4 rounded-2xl border border-white/20 shadow-lg flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div
                class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                AH</div>
            <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
        </div>
        <div class="hidden md:flex gap-8 font-medium">
            <a href="#events" class="text-indigo-600">Jelajahi</a>
            <a href="#kategori" class="hover:text-indigo-600 transition">Kategori</a>
            <a href="#team" class="hover:text-indigo-600 transition">Tentang Kami</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <span class="hidden md:inline text-sm font-semibold text-slate-600">Hai, {{ Auth::user()->name }}</span>
                <a href="{{ route('ticket') }}" class="px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-200 transition">Tiket Saya</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl font-semibold text-rose-600 hover:bg-rose-50 transition">Keluar</button>
                </form>
            @else
                <a href="{{ route('organizer.login') }}" class="hidden md:inline px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-200 transition">Panitia/HIMA</a>
                <a href="{{ route('login') }}"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">Masuk</a>
            @endauth
        </div>
    </nav>

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-6 px-6">
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl font-semibold shadow-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-3xl mx-auto mt-6 px-6">
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="max-w-3xl mx-auto mt-6 px-6">
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl font-semibold shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')

    <!-- Footer -->
    <footer class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4 col-span-2">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">
                        AH</div>
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-xs text-indigo-300">Platform reservasi tiket event online terbaik untuk mahasiswa dan
                    penyelenggara profesional.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Navigasi</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="hover:text-white transition">Home</a></li>
                    <li><a href="#" class="hover:text-white transition">Semua Event</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Bayar</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li>support@eventtiket.com</li>
                    <li>+62 812 3456 7890</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2024 AmikomEventHub. Built with Laravel & Tailwind CSS.
        </div>
    </footer>

</body>

</html>