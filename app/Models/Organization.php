<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'logo', 'description',
        'status', 'rejection_reason', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Rata-rata rating dari seluruh ulasan event yang pernah diselenggarakan.
     */
    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function totalRevenue(): int
    {
        return (int) Transaction::whereHas('event', function ($q) {
            $q->where('organization_id', $this->id);
        })->where('status', 'Success')->sum('total_price');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
