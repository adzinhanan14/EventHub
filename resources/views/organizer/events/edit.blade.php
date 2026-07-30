@extends('layouts.organizer')
@section('page_title', 'Edit Event')
@section('page_subtitle', $event->title)
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm h-fit">
        <h3 class="font-black text-lg mb-6">Detail Event</h3>
        <form action="{{ route('organizer.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Judul Event</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}"
                    class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kategori</label>
                <select name="category_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal & Waktu</label>
                    <input type="datetime-local" name="date" value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Harga Dasar (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $event->price) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="0">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kapasitas (Stok)</label>
                    <input type="number" name="stock" value="{{ old('stock', $event->stock) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required min="0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Poster Event</label>
                @if($event->poster_path)
                <img src="{{ asset('storage/' . $event->poster_path) }}" class="w-24 h-32 object-cover rounded-xl mb-3">
                @endif
                <input type="file" name="poster" accept="image/*" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
            </div>
            <label class="flex items-start gap-3 p-5 bg-indigo-50/60 border-2 border-indigo-100 rounded-2xl cursor-pointer">
                <input type="checkbox" name="issues_certificate" value="1" {{ old('issues_certificate', $event->issues_certificate) ? 'checked' : '' }}
                    class="mt-1 w-5 h-5 rounded accent-indigo-600">
                <span>
                    <span class="block font-bold text-slate-800">🎓 Ini event Seminar / Workshop</span>
                    <span class="block text-sm text-slate-500 mt-1">Aktifkan untuk menerbitkan <b>E-Sertifikat Kehadiran otomatis</b> ke email peserta yang lolos scan check-in di lokasi.</span>
                </span>
            </label>
            <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">Simpan Perubahan</button>
        </form>
    </div>

    <div class="space-y-8">
        <!-- Dynamic Pricing: Tiers -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="font-black text-lg mb-1">Tingkatan Harga (Dynamic Pricing)</h3>
            <p class="text-xs text-slate-400 mb-4">Early Bird &rarr; Presale &rarr; Regular. Harga aktif otomatis mengikuti tanggal berjalan.</p>

            <div class="space-y-3 mb-5">
                @forelse($event->ticketTiers as $tier)
                <div class="border rounded-2xl p-4 text-sm {{ $tier->isActiveNow() ? 'border-indigo-300 bg-indigo-50/50' : 'border-slate-100' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold">{{ $tier->name }} {{ $tier->isActiveNow() ? '· Aktif' : '' }}</p>
                            <p class="text-indigo-600 font-bold">Rp {{ number_format($tier->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">Terjual {{ $tier->sold }}/{{ $tier->quota }}</p>
                            <p class="text-xs text-slate-400">{{ $tier->starts_at?->format('d/m/y H:i') ?? '-' }} s/d {{ $tier->ends_at?->format('d/m/y H:i') ?? '-' }}</p>
                        </div>
                        <form action="{{ route('organizer.events.tiers.destroy', [$event->id, $tier->id]) }}" method="POST" onsubmit="return confirm('Hapus tingkatan harga ini?');">
                            @csrf @method('DELETE')
                            <button class="text-rose-500 text-xs font-bold hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-xs">Belum ada tier. Event memakai harga dasar.</p>
                @endforelse
            </div>

            <details class="group">
                <summary class="cursor-pointer text-indigo-600 font-bold text-sm">+ Tambah Tingkatan Harga</summary>
                <form action="{{ route('organizer.events.tiers.store', $event->id) }}" method="POST" class="space-y-3 mt-4">
                    @csrf
                    <input type="text" name="name" placeholder="Nama (Early Bird / Presale 1 / Regular)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm" required>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="price" placeholder="Harga" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm" required min="0">
                        <input type="number" name="quota" placeholder="Kuota" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm" required min="1">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="datetime-local" name="starts_at" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
                        <input type="datetime-local" name="ends_at" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm">Tambahkan</button>
                </form>
            </details>
        </div>

        <!-- Coupons for this event -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <h3 class="font-black text-lg mb-1">Kupon Khusus Event Ini</h3>
            <p class="text-xs text-slate-400 mb-4">Untuk kupon berlaku di semua event, kelola di menu <a href="{{ route('organizer.coupons.index') }}" class="text-indigo-600 hover:underline">Kupon & Voucher</a>.</p>
            <div class="space-y-2 mb-4">
                @forelse($event->coupons as $coupon)
                <div class="flex justify-between items-center text-sm border rounded-xl px-4 py-3">
                    <span class="font-mono font-bold">{{ $coupon->code }}</span>
                    <span class="text-slate-400">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : 'Rp'.number_format($coupon->value) }}</span>
                </div>
                @empty
                <p class="text-slate-400 text-xs">Belum ada kupon khusus untuk event ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
