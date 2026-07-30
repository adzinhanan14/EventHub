<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Abandoned Cart Recovery: cek tiap 5 menit apakah ada transaksi Pending
// yang perlu diingatkan lewat WhatsApp agar tidak hangus.
Schedule::command('app:send-abandoned-cart-reminders')->everyFiveMinutes();

// Race Condition Handling: lepaskan reservasi stok tiket yang sudah kadaluarsa
// (dibuat saat klik Checkout, ditahan 15 menit) supaya stok bisa direbut
// pembeli lain kalau tidak kunjung dibayar. Dijalankan tiap menit agar
// pelepasan stok terasa (hampir) real-time.
Schedule::command('reservations:release-expired')->everyMinute();

// E-Certificate Otomatis: jaring pengaman untuk event seminar/workshop yang
// baru berakhir, mengirim sertifikat ke peserta tervalidasi hadir yang entah
// kenapa belum menerimanya secara real-time saat scan check-in.
Schedule::command('certificates:send-post-event')->everyThirtyMinutes();
