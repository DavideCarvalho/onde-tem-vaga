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

    public function calculateTotalAmount(?string $exitTime = null): array
    {
        $config = $this->pricingConfig;
        $exitTime = $exitTime ? Carbon::parse($exitTime) : $this->exit_time;
        $entryTime = Carbon::parse($this->entry_time);
        $alreadyPaid = false;
        $amount = 0;

        switch ($config->type) {
            case 'hourly':
                $amount = $this->calculateHourlyAmount($config, $entryTime->diffInHours($exitTime));
                break;
            case 'daily':
                $amount = $this->calculateDailyAmount($config, $entryTime->diffInDays($exitTime));
                break;
            case 'weekly':
                [$amount, $alreadyPaid] = $this->calculatePeriodicAmount($config, $exitTime, 'week');
                break;
            case 'biweekly':
                [$amount, $alreadyPaid] = $this->calculatePeriodicAmount($config, $exitTime, 'biweek');
                break;
            case 'monthly':
                [$amount, $alreadyPaid] = $this->calculatePeriodicAmount($config, $exitTime, 'month');
                break;
            default:
                $amount = 0;
        }

        return [
            'amount' => $amount,
            'already_paid' => $alreadyPaid,
        ];
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

    private function calculatePeriodicAmount(ParkingPricingConfig $config, Carbon $exitTime, string $period): array
    {
        $amount = 0;
        $alreadyPaid = false;
        $query = self::where('vehicle_id', $this->vehicle_id)
            ->where('is_paid', true);

        switch ($period) {
            case 'week':
                $query->whereBetween('exit_time', [
                    $exitTime->copy()->startOfWeek(),
                    $exitTime->copy()->endOfWeek()
                ]);
                $amount = $config->weekly_amount ?? 0;
                break;
            case 'biweek':
                $start = $exitTime->copy()->day <= 15
                    ? $exitTime->copy()->startOfMonth()
                    : $exitTime->copy()->startOfMonth()->addDays(15);
                $end = $start->copy()->addDays(14);
                $query->whereBetween('exit_time', [$start, $end]);
                $amount = $config->biweekly_amount ?? 0;
                break;
            case 'month':
                $query->whereMonth('exit_time', $exitTime->month)
                      ->whereYear('exit_time', $exitTime->year);
                $amount = $config->monthly_amount ?? 0;
                break;
        }

        if ($query->exists()) {
            $alreadyPaid = true;
            $amount = 0;
        }

        return [$amount, $alreadyPaid];
    }
} 