# Panduan Setup Fitur Baru (Soal 1 & Soal 2)

Kode sudah diimplementasikan dan **sudah diuji jalan** di sandbox (migrasi, seeder, dan alur
utama sudah dicoba end-to-end: login organizer, checkout event gratis, QR check-in +
anti double-entry, approve organisasi oleh superadmin). Karena sandbox pengembangan tidak
punya akses ke Packagist, dua package berikut perlu di-install di komputer Anda:

```bash
composer require laravel/socialite barryvdh/laravel-dompdf
composer install   # untuk memastikan semua dependency lain konsisten
```

## 1. Jalankan migrasi & seeder
```bash
php artisan migrate
php artisan db:seed
```
Akun contoh yang dibuat seeder:
- Superadmin: `admin@amikom.ac.id` / `password`
- Organizer (sudah approved): `organizer@amikom.ac.id` / `password` (organisasi: HIMA Teknik Informatika)
- Organizer (masih pending, untuk demo verifikasi): `organizer.si@amikom.ac.id` / `password`
- Kupon contoh: `MAHASISWA50` (diskon 50%, berlaku semua event HIMA TI)
- Event "Web Development Bootcamp 2026" sudah punya 3 tier: Early Bird, Presale 1, Regular
- Event "Digital Illustration Workshop (Gratis)" untuk uji fitur bypass event gratis

## 2. Konfigurasi `.env`
Tambahkan/isi variabel berikut (contoh ada di `.env.example`):

```
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

FONNTE_TOKEN=...
FONNTE_ENABLED=true
```

- **Google OAuth**: buat kredensial di Google Cloud Console → OAuth Client ID (Web application),
  authorized redirect URI harus persis sama dengan `GOOGLE_REDIRECT_URI` di atas.
- **Fonnte**: daftar di https://fonnte.com, ambil token device, isi `FONNTE_TOKEN`.
  Jika `FONNTE_ENABLED=false`, sistem tetap jalan normal tapi hanya mencatat log
  (tidak benar-benar mengirim WA) — aman untuk development tanpa kuota Fonnte.

## 3. Jadwalkan Abandoned Cart Recovery
Command `app:send-abandoned-cart-reminders` sudah didaftarkan berjalan tiap 5 menit di
`routes/console.php`. Pastikan cron job Laravel scheduler aktif di server:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 4. Ringkasan fitur & rute penting
| Fitur | Rute utama |
|---|---|
| SSO Google | `/login`, `/auth/google/redirect`, `/auth/google/callback` |
| Rating & Review | `/events/{event}/review`, halaman profil organisasi `/organizations/{slug}` |
| Multi-Tenant (daftar HIMA) | `/organizer/register`, `/organizer/login`, `/organizer/dashboard` |
| Verifikasi organisasi (Superadmin) | `/admin/organizations` |
| Dashboard grafik | `/admin/dashboard`, `/organizer/dashboard` |
| WhatsApp & Abandoned Cart | otomatis lewat `App\Services\FonnteService` + command terjadwal |
| Reserved-stok tidak diimplementasikan | (tidak dipilih di Soal 2) |
| Check-in QR Scanner | `/organizer/checkin` (kamera HP via html5-qrcode) |
| E-Certificate otomatis | otomatis terpicu saat check-in berhasil (`CertificateService`) |
| Kupon & Dynamic Pricing | `/organizer/coupons`, tier harga di halaman edit event organizer |
| Bypass event gratis | otomatis di `CheckoutController` saat harga akhir = Rp 0 |

## 5. Catatan implementasi
- Kolom `role` di tabel `users` sekarang punya 3 nilai: `user` (pembeli), `organizer`
  (panitia/HIMA), `superadmin`.
- Setiap `Event` sekarang wajib punya `organization_id` — event lama otomatis dipindah ke
  organisasi default "Amikom Event Hub (Official)" oleh migrasi.
- `ticket_code` (UUID unik per transaksi sukses) dipakai untuk QR check-in, terpisah dari
  `order_id` supaya lebih sulit ditebak.
- `CertificateService` akan mencatat log peringatan (bukan error fatal) jika
  `barryvdh/laravel-dompdf` belum ter-install — jalankan `composer require` di atas untuk
  mengaktifkannya sepenuhnya.
