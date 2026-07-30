# Setup Mail Provider Production (Resend) — AmikomEventHub

Panduan ini untuk mengganti Mailtrap sandbox jadi provider mail beneran
(Resend) supaya e-tiket & e-sertifikat bisa sampai ke email peserta asli,
kompatibel dengan hosting gratis InfinityFree.

## Yang SUDAH disiapkan di project ini
- `composer.json` — sudah ditambahkan dependency `resend/resend-laravel`.
- `config/mail.php` — driver `resend` sudah tersedia bawaan Laravel 12 (tidak perlu diubah).
- `config/services.php` — sudah ada `'resend' => ['key' => env('RESEND_API_KEY')]` (tidak perlu diubah).
- `.env.example` — sudah ada template & catatan untuk kedua environment (local vs production).

## Yang HARUS kamu lakukan sendiri

### 1. Download package Resend (butuh koneksi ke Packagist, tidak bisa dilakukan di sisi saya)
Di terminal lokal kamu (folder project, XAMPP):
```bash
composer update
```
Ini akan mengunduh `resend/resend-laravel` yang sudah didaftarkan di `composer.json`
dan otomatis update `composer.lock` serta folder `vendor/`.

### 2. Siapkan domain pengirim
Kamu butuh domain sendiri (bukan subdomain gratis InfinityFree) untuk verifikasi
pengirim email. Kalau belum punya, beli domain murah (`.my.id` paling hemat,
sekitar Rp15.000–55.000/tahun) di Niagahoster / Domainesia / Rumahweb.

### 3. Daftar Resend & verifikasi domain
1. Buat akun gratis di https://resend.com (tanpa kartu kredit).
2. Masuk ke menu **Domains** → tambahkan domain kamu.
3. Resend akan kasih beberapa DNS record (TXT untuk SPF & DKIM). Pasang
   record-record itu di panel DNS tempat kamu beli domain.
4. Tunggu status berubah jadi **Verified** (biasanya cepat, kadang butuh
   beberapa jam untuk propagasi DNS).
5. Di menu **API Keys**, buat key baru dan simpan (format: `re_xxxxxxx`).
   Resend hanya menampilkan key lengkapnya SEKALI saat dibuat — langsung copy.

### 4. Isi `.env` PRODUCTION (yang di-upload ke InfinityFree, BUKAN `.env` lokal)
```
MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS="sertifikat@domainkamu.my.id"
MAIL_FROM_NAME="AmikomEventHub"
```
Pastikan `MAIL_FROM_ADDRESS` pakai domain yang statusnya sudah **Verified** di Resend.
Baris `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` (punya Mailtrap lama)
boleh dibiarkan/dihapus, sudah tidak dipakai kalau `MAIL_MAILER=resend`.

`.env` lokal (XAMPP) TIDAK perlu diubah — tetap boleh pakai Mailtrap/`log` seperti biasa,
karena SMTP tidak diblokir di local, hanya di server InfinityFree.

### 5. Upload & clear cache di server
Setelah `.env` production diupdate lewat FileZilla:
```bash
php artisan config:clear
php artisan config:cache
```
(jalankan lewat Cron Job / Terminal di panel InfinityFree kalau tersedia)

### 6. Test kirim
Lakukan test check-in scan tiket asli, atau via `php artisan tinker`:
```php
Mail::raw('Test email dari AmikomEventHub', function ($m) {
    $m->to('emailkamu@gmail.com')->subject('Test Resend');
});
```
Cek apakah email betulan masuk ke inbox (bukan cuma masuk log).

---
**Free tier Resend:** 3.000 email/bulan, 100 email/hari — cukup untuk skala event kampus.
