<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class GetUsedSpotsData extends Data
{
    public function __construct(
        public readonly int $totalSpots,
        public readonly int $usedSpots,
        public readonly float $percentage,
    ) {
    }

    public static function make($totalSpots, $usedSpots): self
    {
        return new self(
            totalSpots: $totalSpots,
            usedSpots: $usedSpots,
            percentage: $usedSpots / $totalSpots * 100,
        );
    }
}
