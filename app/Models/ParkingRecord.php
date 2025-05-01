<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingRecord extends Model
{
    protected $fillable = [
        'parking_id',
        'vehicle_id',
        'parking_spot_id',
        'entry_time',
        'exit_time',
        'total_amount',
        'is_paid',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function parkingSpot(): BelongsTo
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class);
    }
} 