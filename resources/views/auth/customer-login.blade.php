<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - AmikomEventHub</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-indigo-950 text-white min-h-screen flex items-center justify-center p-6">
    <div class="relative max-w-md w-full bg-white text-slate-900 rounded-[2rem] p-10 shadow-2xl text-center">
        <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl mx-auto mb-6">AH</div>
        <h1 class="text-2xl font-black text-slate-900 mb-2">Selamat Datang!</h1>
        <p class="text-slate-500 text-sm mb-8">Masuk sekali klik untuk pesan tiket lebih cepat, lihat riwayat, dan memberi ulasan event.</p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('auth.google.redirect') }}{{ request('redirect') ? '?redirect=' . urlencode(request('redirect')) : '' }}"
           class="w-full flex items-center justify-center gap-3 py-4 bg-white border-2 border-slate-200 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition shadow-sm">
            <svg class="w-5 h-5" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Continue with Google
        </a>

        <p class="text-xs text-slate-400 mt-6">Panitia/HIMA yang ingin mendaftarkan organisasi?
            <a href="{{ route('organizer.register') }}" class="text-indigo-600 font-bold hover:underline">Daftar di sini</a>
        </p>
        <a href="{{ route('home') }}" class="block mt-4 text-xs text-slate-400 hover:text-slate-600">&larr; Kembali ke Beranda</a>
    </div>
</body>
</html>
