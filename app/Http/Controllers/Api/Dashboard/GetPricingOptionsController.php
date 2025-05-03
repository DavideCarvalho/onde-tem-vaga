<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Data\GetPricingOptionsData;
use App\Http\Controllers\Controller;
use App\Models\ParkingPricingConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class GetPricingOptionsController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    $user = $request->user();
    $pricingOptions = ParkingPricingConfig::query()
      ->where('is_active', true)
      ->where('parking_id', $user->parking_id)
      ->get()
      ->map(fn($config) => GetPricingOptionsData::make($config));

    return response()->json($pricingOptions);
  }
}
