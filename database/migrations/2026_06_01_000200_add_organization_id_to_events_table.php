<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Organization;
use App\Models\Event;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')
                ->constrained('organizations')->cascadeOnDelete();
        });

        // Buat organisasi default agar event lama (data seed sebelumnya) tetap punya penyelenggara
        $default = Organization::firstOrCreate(
            ['slug' => 'amikom-event-hub'],
            [
                'name' => 'Amikom Event Hub (Official)',
                'status' => 'approved',
                'approved_at' => now(),
                'description' => 'Penyelenggara resmi bawaan platform untuk event lama.',
            ]
        );

        Event::whereNull('organization_id')->update(['organization_id' => $default->id]);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
