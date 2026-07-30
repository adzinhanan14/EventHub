<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\TicketTier;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================================
        // 0. Injeksi Akun Super Admin (mengawasi seluruh penyelenggara)
        // ============================================================
        User::firstOrCreate(
            ['email' => 'admin@amikom.ac.id'],
            [
                'name'     => 'Super Admin Amikom',
                'password' => bcrypt('password'),
                'role'     => 'superadmin',
            ]
        );

        // ============================================================
        // 0.1 Organisasi/Kepanitiaan contoh (Arsitektur Multi-Tenant)
        // ============================================================
        $hima = Organization::firstOrCreate(
            ['slug' => 'hima-informatika'],
            [
                'name'        => 'HIMA Teknik Informatika',
                'email'       => 'hima.ti@amikom.ac.id',
                'phone'       => '081234567890',
                'description' => 'Himpunan Mahasiswa Teknik Informatika Universitas Amikom Yogyakarta.',
                'status'      => 'approved',
                'approved_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'organizer@amikom.ac.id'],
            [
                'name'            => 'Panitia HIMA TI',
                'password'        => bcrypt('password'),
                'role'            => 'organizer',
                'organization_id' => $hima->id,
            ]
        );

        // Organisasi contoh kedua yang masih menunggu verifikasi Superadmin
        $pendingOrg = Organization::firstOrCreate(
            ['slug' => 'hima-sistem-informasi'],
            [
                'name'        => 'HIMA Sistem Informasi',
                'email'       => 'hima.si@amikom.ac.id',
                'status'      => 'pending',
            ]
        );

        User::firstOrCreate(
            ['email' => 'organizer.si@amikom.ac.id'],
            [
                'name'            => 'Panitia HIMA SI',
                'password'        => bcrypt('password'),
                'role'            => 'organizer',
                'organization_id' => $pendingOrg->id,
            ]
        );

        // 1. Menyiapkan 3 Kategori
        $cat1 = \App\Models\Category::firstOrCreate([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $cat2 = \App\Models\Category::firstOrCreate([
            'name' => 'E-Sports',
            'slug' => 'e-sports',
        ]);

        $cat3 = \App\Models\Category::firstOrCreate([
            'name' => 'Desain',
            'slug' => 'desain',
        ]);

        // 2. Menanamkan 6 Data Event secara logis dan bervariatif, dimiliki oleh HIMA TI
        $event1 = \App\Models\Event::firstOrCreate(
            ['title' => 'Web Development Bootcamp 2026'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat1->id,
                'description' => 'Pelajari cara membangun aplikasi web modern dari nol menggunakan Laravel dan Tailwind CSS.',
                'date'        => '2026-08-15 09:00:00',
                'location'    => 'Lab Komputer 1 Amikom',
                'price'       => 75000,
                'stock'       => 50,
                'poster_path' => 'posters/web-bootcamp.png',
            ]
        );

        \App\Models\Event::firstOrCreate(
            ['title' => 'AI & Data Science Summit'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat1->id,
                'description' => 'Eksplorasi masa depan Artificial Intelligence bersama para ahli dari industri teknologi terkini.',
                'date'        => '2026-09-01 08:00:00',
                'location'    => 'Amikom Convention Hall',
                'price'       => 150000,
                'stock'       => 200,
                'poster_path' => 'posters/ai-summit.png',
            ]
        );

        \App\Models\Event::firstOrCreate(
            ['title' => 'Amikom E-Sport U-Champ: Mobile Legends'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat2->id,
                'description' => 'Turnamen Mobile Legends terbesar antar mahasiswa dengan prize pool jutaan rupiah.',
                'date'        => '2026-07-20 13:00:00',
                'location'    => 'Student Center Amikom',
                'price'       => 50000,
                'stock'       => 64,
                'poster_path' => 'posters/mlbb-champ.png',
            ]
        );

        \App\Models\Event::firstOrCreate(
            ['title' => 'Valorant Campus League'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat2->id,
                'description' => 'Ajang unjuk kemampuan taktis dalam turnamen Valorant bergengsi tingkat kampus.',
                'date'        => '2026-07-25 10:00:00',
                'location'    => 'Amikom Esports Arena',
                'price'       => 60000,
                'stock'       => 32,
                'poster_path' => 'posters/valo-league.png',
            ]
        );

        \App\Models\Event::firstOrCreate(
            ['title' => 'UI/UX Masterclass: Designing for Humans'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat3->id,
                'description' => 'Tingkatkan skill desain produk digital Anda melalui pendekatan user-centered design secara mendalam.',
                'date'        => '2026-10-10 10:00:00',
                'location'    => 'Ruang Citra 2 Amikom',
                'price'       => 85000,
                'stock'       => 100,
                'poster_path' => 'posters/uiux-masterclass.png',
            ]
        );

        // Event gratis contoh, untuk menguji fitur "Bypass Transaksi untuk Acara Gratis"
        \App\Models\Event::firstOrCreate(
            ['title' => 'Digital Illustration Workshop (Gratis)'],
            [
                'organization_id' => $hima->id,
                'category_id' => $cat3->id,
                'description' => 'Workshop intensif untuk mengasah teknik ilustrasi digital menggunakan berbagai software grafis. Gratis untuk mahasiswa Amikom!',
                'date'        => '2026-11-05 09:00:00',
                'location'    => 'Creative Studio Amikom',
                'price'       => 0,
                'stock'       => 60,
                'poster_path' => 'posters/digital-illustration.png',
            ]
        );

        // 3. Contoh skema harga bertahap (Dynamic Pricing) untuk event Bootcamp
        TicketTier::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Early Bird'],
            [
                'price' => 50000, 'quota' => 20, 'sort_order' => 1,
                'starts_at' => now()->subDays(10), 'ends_at' => now()->addDays(3),
            ]
        );
        TicketTier::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Presale 1'],
            [
                'price' => 65000, 'quota' => 20, 'sort_order' => 2,
                'starts_at' => now()->addDays(3)->addHour(), 'ends_at' => now()->addDays(20),
            ]
        );
        TicketTier::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Regular'],
            [
                'price' => 75000, 'quota' => 10, 'sort_order' => 3,
                'starts_at' => now()->addDays(20)->addHour(), 'ends_at' => null,
            ]
        );

        // 4. Contoh kupon diskon
        Coupon::firstOrCreate(
            ['code' => 'MAHASISWA50'],
            [
                'organization_id' => $hima->id,
                'event_id' => null, // berlaku untuk semua event milik HIMA TI
                'type' => 'percentage',
                'value' => 50,
                'max_uses' => 100,
                'is_active' => true,
            ]
        );
    }
}
