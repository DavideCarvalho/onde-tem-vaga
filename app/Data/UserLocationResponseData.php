<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserLocationResponseData extends Data
{
    public function __construct(
        public ?float $lat,
        public ?float $lon,
        public ?string $city,
        public ?string $state,
        public ?string $country,
    ) {}

    public static function make(?float $lat, ?float $lon, ?string $city, ?string $state, ?string $country): self
    {
        return new self($lat, $lon, $city, $state, $country);
    }
} 