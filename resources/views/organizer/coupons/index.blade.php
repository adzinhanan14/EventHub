@extends('layouts.organizer')
@section('page_title', 'Kupon & Voucher')
@section('page_subtitle', 'Strategi diskon fleksibel untuk menarik pembeli.')
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden h-fit">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Diskon</th>
                    <th class="px-6 py-4">Berlaku Untuk</th>
                    <th class="px-6 py-4">Terpakai</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y border-t">
                @forelse($coupons as $coupon)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-5 font-mono font-black">{{ $coupon->code }}</td>
                    <td class="px-6 py-5 font-bold text-indigo-600">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : 'Rp'.number_format($coupon->value,0,',','.') }}</td>
                    <td class="px-6 py-5 text-sm text-slate-500">{{ $coupon->event->title ?? 'Semua Event' }}</td>
                    <td class="px-6 py-5 text-sm">{{ $coupon->used_count }}{{ $coupon->max_uses ? '/'.$coupon->max_uses : '' }}</td>
                    <td class="px-6 py-5">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $coupon->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                            {{ $coupon->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex gap-2">
                            <form action="{{ route('organizer.coupons.toggle', $coupon->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs font-bold text-indigo-600 hover:underline">{{ $coupon->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                            <form action="{{ route('organizer.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Hapus kupon ini?');">
                                @csrf @method('DELETE')
                                <button class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada kupon.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-6 bg-slate-50/50 border-t">{{ $coupons->links() }}</div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm h-fit">
        <h3 class="font-black text-lg mb-4">Buat Kupon Baru</h3>
        <form action="{{ route('organizer.coupons.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Kode Kupon</label>
                <input type="text" name="code" placeholder="MAHASISWA50" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-mono uppercase" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Berlaku untuk Event</label>
                <select name="event_id" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Tipe</label>
                    <select name="type" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
                        <option value="percentage">Persen (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Nilai</label>
                    <input type="number" name="value" placeholder="50" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm" required min="1">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Maks. Pemakaian (Opsional)</label>
                <input type="number" name="max_uses" placeholder="100" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm" min="1">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="starts_at" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
                <input type="date" name="ends_at" class="px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm">
            </div>
            <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-black">Buat Kupon</button>
        </form>
    </div>
</div>
@endsection
