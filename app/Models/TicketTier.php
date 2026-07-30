<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTier extends Model
{
    protected $fillable = [
        'event_id', 'name', 'price', 'quota', 'sold',
        'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->sold);
    }

    /**
     * Sedang berjalan berdasarkan tanggal (dipakai untuk penjualan bertahap:
     * Early Bird -> Presale -> Regular) dan masih ada kuota.
     */
    public function isActiveNow(): bool
    {
        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return $this->remainingQuota() > 0;
    }
}
