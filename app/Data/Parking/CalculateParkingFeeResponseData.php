<?php

namespace App\Data\Parking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CalculateParkingFeeResponseData extends Data
{
    public function __construct(
        public float $total_amount,
        public string $formatted_amount,
    ) {}

    public static function make(float $total_amount, string $formatted_amount): self
    {
        return new self($total_amount, $formatted_amount);
    }
}
