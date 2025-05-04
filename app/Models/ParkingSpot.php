<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ParkingSpot extends Model
{
    use HasUuids;

    protected $fillable = [
        'parking_id',
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

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class);
    }
}
