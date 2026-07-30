<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrganizationProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Organizer\AuthController as OrganizerAuthController;
use App\Http\Controllers\Organizer\RegisterController as OrganizerRegisterController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboardController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\TicketTierController;
use App\Http\Controllers\Organizer\CouponController;
use App\Http\Controllers\Organizer\CheckinController;
use App\Http\Controllers\TicketController;


// ==========================================
// RUTE AUTH & REDIRECT
// ==========================================
Route::get('/login', function () {
    return view('auth.customer-login'); // Login Instan via Google (SSO) untuk pembeli tiket
})->name('login');

// Login Instan via Sosial Media (SSO) - Laravel Socialite
// Dipakai oleh pembeli tiket (customer) MAUPUN Admin Organisasi (organizer)
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// RUTE USER AREA & TIKET
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
Route::get('/my-ticket', [TicketController::class, 'ticket'])->name('ticket')->middleware('auth');

// RUTE SCAN E-TICKET & OTOMATIS KIRIM SERTIFIKAT
Route::post('/ticket/scan', [TicketController::class, 'scanTicket'])->name('ticket.scan');

Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/payment-finish', [CheckoutController::class, 'finish'])->name('checkout.finish');

// Profil publik penyelenggara (Multi-Tenant): menampilkan rekam jejak rating & ulasan
Route::get('/organizations/{organization}', [OrganizationProfileController::class, 'show'])->name('organizations.show');

// Rating & Review pasca-acara (wajib login sebagai pembeli)
Route::middleware('auth')->group(function () {
    Route::get('/events/{event}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/events/{event}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

// ==========================================
// RUTE WEBHOOK MIDTRANS (Server-to-Server, no session/CSRF)
// ==========================================
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');

// ==========================================
// RUTE ORGANIZER AREA (Organisasi/Kepanitiaan/HIMA) - Arsitektur Multi-Tenant
// ==========================================
Route::prefix('organizer')->name('organizer.')->group(function () {

    Route::get('register', [OrganizerRegisterController::class, 'showRegister'])->name('register');
    Route::post('register', [OrganizerRegisterController::class, 'register'])->name('register.post');

    Route::get('login', [OrganizerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [OrganizerAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [OrganizerAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'organizer'])->group(function () {
        Route::get('/', fn () => redirect()->route('organizer.dashboard'));
        Route::get('dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');

        Route::resource('events', OrganizerEventController::class)->except(['show']);
        Route::post('events/{event}/tiers', [TicketTierController::class, 'store'])->name('events.tiers.store');
        Route::put('events/{event}/tiers/{tier}', [TicketTierController::class, 'update'])->name('events.tiers.update');
        Route::delete('events/{event}/tiers/{tier}', [TicketTierController::class, 'destroy'])->name('events.tiers.destroy');

        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::patch('coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

        // Sistem Aplikasi Penjaga Pintu (QR Check-in Scanner)
        Route::get('checkin', [CheckinController::class, 'scanner'])->name('checkin.scanner');
        Route::post('checkin/scan', [CheckinController::class, 'scan'])->name('checkin.scan');
    });
});

// ==========================================
// RUTE ADMIN AREA (Superadmin)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Admin
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Middleware Auth & Admin
    Route::middleware(['auth', 'admin'])->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Transaksi
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

        // Verifikasi kelayakan Organisasi/Kepanitiaan (Multi-Tenant oversight)
        Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('organizations/{organization:id}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::patch('organizations/{organization:id}/approve', [OrganizationController::class, 'approve'])->name('organizations.approve');
        Route::patch('organizations/{organization:id}/reject', [OrganizationController::class, 'reject'])->name('organizations.reject');

        // CRUD dengan Resource
        Route::resource('events', EventAdminController::class);
        Route::resource('partners', PartnerController::class);
        Route::resource('categories', CategoryController::class);
    });
});