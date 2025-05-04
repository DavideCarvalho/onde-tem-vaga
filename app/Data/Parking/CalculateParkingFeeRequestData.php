<?php

namespace App\Data\Parking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CalculateParkingFeeRequestData extends Data
{
    public function __construct(
        public string $record_id,
        public string $exit_time,
    ) {}

    public static function make(string $record_id, string $exit_time): self
    {
        return new self($record_id, $exit_time);
    }
}
