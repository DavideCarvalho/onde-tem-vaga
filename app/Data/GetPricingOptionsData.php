<?php

namespace App\Data;

use App\Models\ParkingPricingConfig;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class GetPricingOptionsData extends Data
{
  public function __construct(
    public readonly string $value,
    public readonly string $label,
  ) {}

  public static function make(ParkingPricingConfig $pricingOption): self
  {
    return new self(
      value: $pricingOption->type,
      label: $pricingOption->name,
    );
  }
}
