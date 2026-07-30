@extends('layouts.organizer')
@section('page_title', 'Check-in Scanner')
@section('page_subtitle', 'Scan QR tiket peserta di lokasi acara untuk mencegah kecurangan (double entry).')
@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <h3 class="font-black text-lg mb-4">Kamera Scanner</h3>
        <div id="qr-reader" class="rounded-2xl overflow-hidden border-2 border-slate-100"></div>
        <p class="text-xs text-slate-400 mt-4 text-center">Arahkan kamera ke QR Code pada E-Ticket peserta.</p>
    </div>

    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <h3 class="font-black text-lg mb-4">Hasil Scan</h3>
        <div id="result-box" class="p-6 rounded-2xl border-2 border-dashed border-slate-200 text-center text-slate-400 min-h-[160px] flex items-center justify-center">
            Menunggu hasil scan...
        </div>

        <h4 class="font-bold text-sm mt-8 mb-3 text-slate-500 uppercase tracking-widest">Riwayat Check-in</h4>
        <div id="history-list" class="space-y-2 text-sm max-h-64 overflow-y-auto">
            @forelse ($history as $trx)
                <div class="px-4 py-2 rounded-xl border border-green-200 bg-green-50">
                    <p class="font-bold text-xs">✔ {{ $trx->customer_name }} — {{ $trx->event->title ?? '-' }}</p>
                    <p class="text-xs text-slate-500">{{ $trx->checked_in_at->format('d M Y H:i') }}</p>
                </div>
            @empty
                <p id="history-empty" class="text-slate-400 text-xs">Belum ada riwayat check-in.</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result-box');
    const historyList = document.getElementById('history-list');
    let isProcessing = false;

    function renderResult(type, title, subtitle) {
        const colors = {
            success: 'bg-green-50 border-green-300 text-green-700',
            error: 'bg-rose-50 border-rose-300 text-rose-700',
        };
        resultBox.className = 'p-6 rounded-2xl border-2 text-center min-h-[160px] flex flex-col items-center justify-center gap-1 ' + colors[type];
        resultBox.innerHTML = `<p class="font-black text-lg">${title}</p><p class="text-sm">${subtitle}</p>`;

        const emptyPlaceholder = document.getElementById('history-empty');
        if (emptyPlaceholder) emptyPlaceholder.remove();

        const item = document.createElement('div');
        item.className = 'px-4 py-2 rounded-xl border ' + (type === 'success' ? 'border-green-200 bg-green-50' : 'border-rose-200 bg-rose-50');
        item.innerHTML = `<p class="font-bold text-xs">${title}</p><p class="text-xs text-slate-500">${subtitle}</p>`;
        historyList.prepend(item);
    }

    async function onScanSuccess(ticketCode) {
        if (isProcessing) return;
        isProcessing = true;

        try {
            const response = await fetch("{{ route('organizer.checkin.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ ticket_code: ticketCode }),
            });
            const data = await response.json();

            if (response.ok && data.status === 'success') {
                renderResult('success', '✔ Check-in Berhasil', `${data.data.nama} — ${data.data.event}`);
            } else {
                renderResult('error', '✘ Ditolak', data.message || 'Tiket tidak valid.');
            }
        } catch (e) {
            renderResult('error', '✘ Error', 'Gagal menghubungi server.');
        }

        setTimeout(() => { isProcessing = false; }, 2000);
    }

    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => onScanSuccess(decodedText),
        () => {}
    ).catch((err) => {
        document.getElementById('qr-reader').innerHTML =
            '<p class="p-6 text-sm text-rose-500">Gagal mengakses kamera: ' + err + '</p>';
    });
</script>
@endsection
