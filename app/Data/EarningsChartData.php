<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EarningsChartData extends Data
{
    /**
     * @param EarningsChartHourData[] $day
     * @param EarningsChartDayData[] $week
     * @param EarningsChartDayData[] $month
     */
    public function __construct(
        public array $day,
        public array $week,
        public array $month,
    ) {}

    public static function make(array $day, array $week, array $month): self
    {
        return new self(
            day: $day,
            week: $week,
            month: $month,
        );
    }
}
