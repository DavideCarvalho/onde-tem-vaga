<?php

namespace App\Data\Parking;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class GetNearbyParkingResponseData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public string $distance,
        public int $available_spaces,
        public float $occupancy_percentage,
    ) {}

    public static function make(string $id, string $name, string $address, string $distance, int $available_spaces, float $occupancy_percentage): self
    {
        return new self($id, $name, $address, $distance, $available_spaces, $occupancy_percentage);
    }
} 