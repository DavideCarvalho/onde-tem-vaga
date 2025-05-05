<?php

namespace App\Data\Parking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class GetParkedVehicleResponseData extends Data
{
    public function __construct(
        public string $id,
        public string $plate,
        public string $brand,
        public string $model,
        public string $color,
        public string $entry_time,
    ) {}

    public static function make(string $id, string $plate, string $brand, string $model, string $color, string $entry_time): self
    {
        return new self($id, $plate, $brand, $model, $color, $entry_time);
    }
}
