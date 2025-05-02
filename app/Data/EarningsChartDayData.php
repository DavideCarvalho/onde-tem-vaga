<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EarningsChartDayData extends Data
{
    public function __construct(
        public string $day,
        public float $value,
    ) {}

    public static function make(string $day, float $value): self
    {
        return new self(
            day: $day,
            value: $value,
        );
    }
}
