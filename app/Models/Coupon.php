<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'organization_id', 'event_id', 'code', 'type', 'value',
        'max_uses', 'used_count', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Cek apakah kupon valid dipakai untuk sebuah event pada waktu sekarang.
     */
    public function isValidFor(Event $event): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->event_id && $this->event_id !== $event->id) {
            return false;
        }

        if ($this->organization_id !== $event->organization_id) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Hitung besaran potongan (rupiah) dari sebuah harga.
     */
    public function calculateDiscount(int $price): int
    {
        $discount = $this->type === 'percentage'
            ? (int) round($price * $this->value / 100)
            : $this->value;

        return min($discount, $price);
    }
}
