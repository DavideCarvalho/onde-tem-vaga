<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'plate',
        'model',
        'color',
    ];

    public function parkingRecords(): HasMany
    {
        return $this->hasMany(ParkingRecord::class);
    }
}
