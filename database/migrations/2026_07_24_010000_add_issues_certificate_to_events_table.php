<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * E-Certificate Otomatis: kategori event di sistem ini (Teknologi, Desain, dst)
 * menandakan TOPIK, bukan FORMAT acara. Kolom `issues_certificate` ini yang
 * dipakai organizer untuk menandai "event ini seminar/workshop, terbitkan
 * E-Sertifikat kehadiran otomatis untuk peserta yang hadir".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('issues_certificate')->default(false)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('issues_certificate');
        });
    }
};
