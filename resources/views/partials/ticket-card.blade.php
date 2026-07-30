@php
    $statusStyle = match($trx->status) {
        'Success' => 'bg-green-100 text-green-700',
        'Pending' => 'bg-amber-100 text-amber-700',
        'Challenge' => 'bg-amber-100 text-amber-700',
        default => 'bg-rose-100 text-rose-700',
    };
    $statusLabel = match($trx->status) {
        'Success' => 'Berhasil',
        'Pending' => 'Menunggu Pembayaran',
        'Challenge' => 'Menunggu Verifikasi',
        default => 'Gagal / Kadaluarsa',
    };
@endphp
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 mb-6">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $trx->order_id }}</p>
            <h2 class="text-xl font-black">{{ $trx->event->title ?? 'Event tidak tersedia' }}</h2>
            @if ($trx->event)
                <p class="text-slate-500 text-sm mt-1">
                    {{ $trx->event->organization->name ?? '' }}
                    &middot; {{ optional($trx->event->date)->translatedFormat('d M Y') }}
                    &middot; {{ $trx->event->location }}
                </p>
            @endif
        </div>
        <span class="px-4 py-1.5 rounded-full text-xs font-bold {{ $statusStyle }}">{{ $statusLabel }}</span>
    </div>

    @if ($trx->status === 'Success')
        <div class="mt-4">
            <button type="button" onclick="document.getElementById('ticket-modal-{{ $trx->order_id }}').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4h-4m4 0v4m-4-4v4m-4-11a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm0 0v4m0-4H4m4 4H6a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2zm10-10a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2h2z" />
                </svg>
                Tampilkan QR Tiket
            </button>
        </div>

        {{-- Modal E-Ticket dengan QR untuk check-in di lokasi acara --}}
        <div id="ticket-modal-{{ $trx->order_id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                onclick="document.getElementById('ticket-modal-{{ $trx->order_id }}').classList.add('hidden')"></div>

            <div class="relative w-full max-w-sm bg-white rounded-[2rem] shadow-2xl overflow-hidden">
                <button type="button" onclick="document.getElementById('ticket-modal-{{ $trx->order_id }}').classList.add('hidden')"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/90 text-slate-500 flex items-center justify-center hover:bg-white transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="bg-indigo-50 px-8 pt-8 pb-6 text-center border-b-2 border-dashed border-indigo-200">
                    <p class="text-indigo-600 text-xs font-bold uppercase tracking-widest mb-2">E-Ticket Resmi</p>
                    <h2 class="text-xl font-black">{{ $trx->event->title ?? 'Event tidak tersedia' }}</h2>
                </div>

                <div class="px-8 py-6">
                    <div class="grid grid-cols-2 gap-y-4 mb-2 text-sm">
                        <div>
                            <p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Nama Pembeli</p>
                            <p class="font-bold">{{ $trx->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Tanggal & Waktu</p>
                            <p class="font-bold">{{ $trx->event && $trx->event->date ? $trx->event->date->translatedFormat('d M, H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Order ID</p>
                            <p class="font-bold text-xs">{{ $trx->order_id }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Lokasi</p>
                            <p class="font-bold">{{ $trx->event->location ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6 text-center mt-4">
                        <p class="text-slate-400 text-[11px] font-bold uppercase mb-3">Scan QR untuk Check-in</p>
                        <div class="inline-block bg-white p-3 rounded-xl shadow-inner">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($trx->ticket_code ?? $trx->order_id) }}"
                                alt="QR Code" width="180" height="180" class="block">
                        </div>
                        <p class="font-mono font-bold text-sm mt-3">{{ $trx->order_id }}</p>
                        <p class="text-slate-400 text-[10px] mt-1">Kode ini hanya berlaku sekali (anti double-entry).</p>
                    </div>
                </div>

                <div class="px-8 pb-6 text-center">
                    <p class="text-slate-400 text-xs">Mohon tunjukkan E-Ticket ini saat memasuki area acara.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-100 text-sm">
        <div>
            <p class="text-slate-400 text-xs font-bold uppercase mb-1">Total Bayar</p>
            <p class="font-bold">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-xs font-bold uppercase mb-1">Kode Tiket</p>
            <p class="font-bold font-mono">{{ $trx->ticket_code ? substr($trx->ticket_code, 0, 8) . '…' : '-' }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-xs font-bold uppercase mb-1">Tanggal Beli</p>
            <p class="font-bold">{{ $trx->created_at->translatedFormat('d M Y') }}</p>
        </div>
    </div>

    {{-- Aksi lanjutan tergantung status transaksi --}}
    @if ($trx->status === 'Pending')
        <div class="mt-6 pt-6 border-t border-slate-100">
            <a href="{{ route('checkout.payment', $trx->order_id) }}"
                class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition">
                Lanjutkan Pembayaran
            </a>
        </div>
    @elseif ($trx->status === 'Success')
        <div class="mt-6 pt-6 border-t border-slate-100">
            @if ($trx->review)
                {{-- Sudah pernah memberi ulasan --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ulasan Anda</p>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-amber-400">{{ str_repeat('★', $trx->review->rating) }}{{ str_repeat('☆', 5 - $trx->review->rating) }}</span>
                </div>
                @if ($trx->review->comment)
                    <p class="text-slate-600 text-sm">{{ $trx->review->comment }}</p>
                @endif
            @elseif ($trx->canBeReviewed())
                {{-- Boleh mengulas: sukses, event lewat >=1 hari, belum pernah direview --}}
                <a href="{{ route('reviews.create', $trx->event->id) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 text-slate-900 rounded-xl font-bold text-sm hover:bg-amber-300 transition">
                    ⭐ Beri Rating & Ulasan
                </a>
            @elseif ($trx->event && $trx->event->date && $trx->event->date->isFuture())
                <p class="text-slate-400 text-sm">Ulasan dapat diberikan setelah acara berlangsung.</p>
            @else
                <p class="text-slate-400 text-sm">Ulasan dapat diberikan mulai H+1 setelah acara selesai.</p>
            @endif
        </div>
    @endif
</div>
