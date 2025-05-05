<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Parking extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'address',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    public function parkingSpots(): HasMany
    {
        return $this->hasMany(ParkingSpot::class);
    }

    public function parkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pricingConfigs(): HasMany
    {
        return $this->hasMany(ParkingPricingConfig::class);
    }

    public function getTotalSpotsAttribute(): int
    {
        return $this->parkingSpots()->count();
    }
} 