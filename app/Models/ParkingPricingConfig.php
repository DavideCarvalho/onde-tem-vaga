<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ParkingPricingConfig extends Model
{
    use HasUuids;

    protected $fillable = [
        'parking_id',
        'name',
        'type',
        'base_amount',
        'base_hours',
        'additional_hour_amount',
        'daily_amount',
        'monthly_amount',
        'custom_rules',
        'is_active',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'additional_hour_amount' => 'decimal:2',
        'daily_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'custom_rules' => 'array',
        'is_active' => 'boolean',
    ];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class);
    }
}
