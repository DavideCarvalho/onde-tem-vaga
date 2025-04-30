<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSpot extends Model
{
    protected $fillable = [
        'identification',
        'is_occupied',
    ];

    protected $casts = [
        'is_occupied' => 'boolean',
    ];

    public function parkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class);
    }
}
