<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Organisasi - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-indigo-950 text-white min-h-screen flex items-center justify-center p-6 py-16">
    <div class="relative max-w-xl w-full bg-white text-slate-900 rounded-[2rem] p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl mx-auto mb-4">AH</div>
            <h1 class="text-2xl font-black text-slate-900">Daftarkan Organisasi/HIMA</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola event & terima pembayaran sendiri layaknya marketplace.
                Akun akan aktif setelah diverifikasi Superadmin.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organizer.register.post') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Nama Organisasi/Kepanitiaan</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name') }}" placeholder="HIMA Teknik Informatika"
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Email Organisasi</label>
                        <input type="email" name="organization_email" value="{{ old('organization_email') }}" placeholder="hima@amikom.ac.id"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                        <p class="text-xs text-slate-400 mt-1.5">Email ini juga akan menjadi email login ke dashboard.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">No. Telp/WA</label>
                        <input type="text" name="organization_phone" value="{{ old('organization_phone') }}" placeholder="0812xxxxxxx"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Deskripsi Singkat</label>
                    <textarea name="organization_description" rows="2" placeholder="Ceritakan tentang organisasi Anda..."
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">{{ old('organization_description') }}</textarea>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-500 mb-4 uppercase tracking-widest">Akun Penanggung Jawab (PIC)</p>
                    <div class="space-y-5">
                        <div>
                            <input type="text" name="pic_name" value="{{ old('pic_name') }}" placeholder="Nama Lengkap PIC"
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="password" name="password" placeholder="Password (min. 8 karakter)"
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                            <input type="password" name="password_confirmation" placeholder="Ulangi Password"
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                Kirim Pendaftaran
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Sudah punya akun? <a href="{{ route('organizer.login') }}" class="text-indigo-600 font-bold hover:underline">Login di sini</a>
        </p>
    </div>
</body>
</html>
