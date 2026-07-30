<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('ticket_tier_id')->nullable()->after('event_id')
                ->constrained('ticket_tiers')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->after('ticket_tier_id')
                ->constrained('coupons')->nullOnDelete();
            $table->integer('discount_amount')->default(0)->after('total_price');

            // Sistem Check-in QR Scanner
            $table->string('ticket_code')->nullable()->unique()->after('snap_token');
            $table->enum('checkin_status', ['valid', 'used'])->default('valid')->after('ticket_code');
            $table->timestamp('checked_in_at')->nullable()->after('checkin_status');

            // WhatsApp notification & abandoned cart recovery
            $table->timestamp('wa_reminder_sent_at')->nullable()->after('checked_in_at');

            // E-Certificate
            $table->timestamp('certificate_sent_at')->nullable()->after('wa_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('ticket_tier_id');
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'discount_amount', 'ticket_code', 'checkin_status',
                'checked_in_at', 'wa_reminder_sent_at', 'certificate_sent_at',
            ]);
        });
    }
};
