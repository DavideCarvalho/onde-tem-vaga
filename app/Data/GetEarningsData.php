<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use App\Data\EarningsChartData;

#[TypeScript]
class GetEarningsData extends Data
{
    public function __construct(
        public readonly float $today,
        public readonly float $week,
        public readonly float $month,
        public readonly EarningsChartData $chart,
    ) {}

    public static function make($today, $week, $month, $chart): self
    {
        return new self(
            today: $today,
            week: $week,
            month: $month,
            chart: EarningsChartData::from($chart),
        );
    }
}
