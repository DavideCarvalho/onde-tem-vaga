<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ParkingSpotData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $identification,
        public readonly ?string $entry_time,
        public readonly ?VehicleData $vehicle,
    ) {
    }

    public static function make(ParkingRecord $parkingRecord): self
    {
        $vehicle = $parkingRecord->vehicle;

        return new self(
            id: $parkingRecord->id,
            identification: $parkingRecord->parkingSpot->identification,
            entry_time: $parkingRecord->entry_time,
            vehicle: $vehicle ? VehicleData::make($vehicle) : null,
        );
    }
}
