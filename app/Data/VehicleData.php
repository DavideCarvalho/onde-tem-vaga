<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
#[TypeScript]
class VehicleData extends Data
{
    public function __construct(
        public readonly string $plate,
        public readonly string $model,
        public readonly string $color,
    ) {
    }

    public static function make(Vehicle $vehicle): self
    {
        return new self(
            plate: $vehicle->plate,
            model: $vehicle->model,
            color: $vehicle->color,
        );
    }
}
