@extends('layouts.admin')
@section('page_title', 'Organisasi/HIMA')
@section('page_subtitle', 'Awasi kelayakan seluruh penyelenggara di platform.')
@section('content')

<div class="flex gap-2 mb-6">
    @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
    <a href="{{ route('admin.organizations.index', ['status' => $key]) }}"
       class="px-5 py-2.5 rounded-xl font-bold text-sm {{ ($status ?? 'pending') === $key ? 'bg-indigo-600 text-white' : 'bg-white border text-slate-600 hover:bg-slate-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
            <tr>
                <th class="px-8 py-4">Organisasi</th>
                <th class="px-8 py-4">Kontak</th>
                <th class="px-8 py-4">Jumlah Event</th>
                <th class="px-8 py-4">Status</th>
                <th class="px-8 py-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y border-t">
            @forelse($organizations as $org)
            <tr class="hover:bg-slate-50/50 transition">
                <td class="px-8 py-6">
                    <p class="font-black">{{ $org->name }}</p>
                    <p class="text-xs text-slate-400">{{ $org->slug }}</p>
                </td>
                <td class="px-8 py-6 text-sm text-slate-500">
                    <p>{{ $org->email ?? '-' }}</p>
                    <p>{{ $org->phone ?? '-' }}</p>
                </td>
                <td class="px-8 py-6 font-bold">{{ $org->events_count }}</td>
                <td class="px-8 py-6">
                    @php
                        $badge = ['pending' => 'bg-amber-100 text-amber-600', 'approved' => 'bg-green-100 text-green-600', 'rejected' => 'bg-rose-100 text-rose-600'];
                    @endphp
                    <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $badge[$org->status] }}">{{ ucfirst($org->status) }}</span>
                </td>
                <td class="px-8 py-6">
                    <div class="flex gap-2">
                        @if($org->status !== 'approved')
                        <form action="{{ route('admin.organizations.approve', $org) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="px-3 py-2 bg-green-600 text-white rounded-xl text-xs font-bold hover:bg-green-700">Setujui</button>
                        </form>
                        @endif
                        @if($org->status !== 'rejected')
                        <button type="button" onclick="document.getElementById('reject-modal-{{ $org->id }}').classList.remove('hidden')"
                            class="px-3 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700">Tolak</button>
                        @endif
                    </div>

                    <div id="reject-modal-{{ $org->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-6">
                        <div class="bg-white rounded-3xl p-6 max-w-sm w-full">
                            <h4 class="font-black mb-3">Tolak "{{ $org->name }}"?</h4>
                            <form action="{{ route('admin.organizations.reject', $org) }}" method="POST">
                                @csrf @method('PATCH')
                                <textarea name="rejection_reason" rows="3" placeholder="Alasan penolakan (opsional)" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm mb-4"></textarea>
                                <div class="flex gap-2">
                                    <button type="button" onclick="document.getElementById('reject-modal-{{ $org->id }}').classList.add('hidden')" class="flex-1 py-3 border rounded-xl font-bold text-sm">Batal</button>
                                    <button type="submit" class="flex-1 py-3 bg-rose-600 text-white rounded-xl font-bold text-sm">Tolak</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-8 py-10 text-center text-slate-500">Tidak ada organisasi pada status ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-8 py-6 bg-slate-50/50 border-t">{{ $organizations->links() }}</div>
</div>
@endsection
