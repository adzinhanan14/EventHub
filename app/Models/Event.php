<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organization_id', 'category_id', 'title', 'description', 'date',
        'location', 'price', 'stock', 'poster_path', 'issues_certificate',
    ];

    protected $casts = [
        'date' => 'datetime',
        'issues_certificate' => 'boolean',
    ];

    // Menandakan atribut: 1 Event harus terpaut pada satu wujud Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Arsitektur Multi-Tenant: setiap event dimiliki oleh 1 organisasi/panitia
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function ticketTiers()
    {
        return $this->hasMany(TicketTier::class)->orderBy('sort_order');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Harga aktif saat ini: jika event memakai skema tier bertahap
     * (Early Bird / Presale / Regular) maka pakai tier yang sedang berjalan,
     * kalau tidak fallback ke harga dasar event.
     */
    public function activeTier(): ?TicketTier
    {
        return $this->ticketTiers
            ->filter(fn (TicketTier $tier) => $tier->isActiveNow())
            ->first();
    }

    public function currentPrice(): int
    {
        $tier = $this->activeTier();
        return $tier ? $tier->price : $this->price;
    }

    public function isFree(): bool
    {
        return $this->currentPrice() <= 0;
    }

    public function isPastEvent(): bool
    {
        return $this->date && $this->date->isPast();
    }

    public function isSoldOut(): bool
    {
        return $this->stock <= 0;
    }
}
