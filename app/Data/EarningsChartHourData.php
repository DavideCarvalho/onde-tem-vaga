<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EarningsChartHourData extends Data
{
    public function __construct(
        public int $hour,
        public float $value,
    ) {}

    public static function make(int $hour, float $value): self
    {
        return new self(
            hour: $hour,
            value: $value,
        );
    }
}
