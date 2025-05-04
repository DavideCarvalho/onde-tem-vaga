<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vehicle extends Model
{
    use HasUuids;

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
