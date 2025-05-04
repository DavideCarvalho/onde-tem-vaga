<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ParkingRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'parking_id',
        'vehicle_id',
        'parking_spot_id',
        'parking_pricing_config_id',
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

    public function pricingConfig(): BelongsTo
    {
        return $this->belongsTo(ParkingPricingConfig::class, 'parking_pricing_config_id');
    }

    public function calculateTotalAmount(): float
    {
        if (!$this->exit_time) {
            return 0;
        }

        $entryTime = Carbon::parse($this->entry_time);
        $exitTime = Carbon::parse($this->exit_time);
        $hours = $exitTime->diffInHours($entryTime);
        $days = $exitTime->diffInDays($entryTime);
        $months = $exitTime->diffInMonths($entryTime);

        $config = $this->pricingConfig;

        return match ($config->type) {
            'hourly' => $this->calculateHourlyAmount($config, $hours),
            'daily' => $this->calculateDailyAmount($config, $days),
            'monthly' => $this->calculateMonthlyAmount($config, $months),
            default => 0,
        };
    }

    private function calculateHourlyAmount(ParkingPricingConfig $config, int $hours): float
    {
        if ($hours <= $config->base_hours) {
            return $config->base_amount;
        }

        $additionalHours = $hours - $config->base_hours;
        return $config->base_amount + ($additionalHours * $config->additional_hour_amount);
    }

    private function calculateDailyAmount(ParkingPricingConfig $config, int $days): float
    {
        return $days * $config->daily_amount;
    }

    private function calculateMonthlyAmount(ParkingPricingConfig $config, int $months): float
    {
        return $months * $config->monthly_amount;
    }
} 