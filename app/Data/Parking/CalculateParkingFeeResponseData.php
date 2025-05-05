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
        public bool $already_paid,
    ) {}

    public static function make(float $total_amount, string $formatted_amount, bool $already_paid): self
    {
        return new self($total_amount, $formatted_amount, $already_paid);
    }
}
