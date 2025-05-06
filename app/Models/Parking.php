<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Clickbar\Magellan\Data\Geometries\Point;

class Parking extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'address',
        'hourly_rate',
        'latitude',
        'longitude',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'zip_code',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'location' => Point::class,
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