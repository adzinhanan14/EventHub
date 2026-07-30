<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_tier_id',
        'coupon_id',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_price',
        'discount_amount',
        'status',
        'expires_at',
        'snap_token',
        'ticket_code',
        'checkin_status',
        'checked_in_at',
        'wa_reminder_sent_at',
        'certificate_sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'wa_reminder_sent_at' => 'datetime',
        'certificate_sent_at' => 'datetime',
    ];

    /**
     * Reservasi stok masih aktif (belum expired) untuk transaksi berstatus Pending.
     */
    public function isReservationActive(): bool
    {
        return in_array($this->status, \App\Services\TicketReservationService::OPEN_STATUSES)
            && $this->expires_at
            && now()->lt($this->expires_at);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketTier()
    {
        return $this->belongsTo(TicketTier::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'Success';
    }

    public function isCheckedIn(): bool
    {
        return $this->checkin_status === 'used';
    }

    /**
     * Boleh direview jika: transaksi sukses, event sudah lewat >= 1 hari, dan belum pernah direview.
     */
    public function canBeReviewed(): bool
    {
        if (! $this->isSuccess() || $this->review) {
            return false;
        }

        return $this->event && $this->event->date && $this->event->date->lte(now()->subDay());
    }

    /**
     * Cari transaksi milik $user (via user_id ATAU customer_email) untuk $event yang
     * benar-benar berhak diulas saat ini. Dipakai bersama oleh EventController (untuk
     * menampilkan/menyembunyikan link "Beri Ulasan") dan ReviewController (validasi submit),
     * supaya keduanya selalu konsisten.
     */
    public static function findEligibleForReview(Event $event, User $user): ?self
    {
        return static::where('event_id', $event->id)
            ->where('status', 'Success')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
            })
            ->whereDoesntHave('review')
            ->get()
            ->first(fn (self $t) => $t->canBeReviewed());
    }
}
