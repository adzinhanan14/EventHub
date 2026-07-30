<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Race Condition Handling (Reserve-then-Release):
 * Stok tiket kini ditahan (reserved) SAAT transaksi dibuat (klik Checkout),
 * bukan menunggu pembayaran lunas. Kolom `expires_at` menandai batas waktu
 * reservasi tsb (± 15 menit). Jika lewat & belum dibayar, scheduler akan
 * melepaskan kembali stoknya (+1) supaya bisa direbut pembeli lain.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn('expires_at');
        });
    }
};
