<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingSpot;
use Illuminate\Http\JsonResponse;

class GetAvailableSpotsController extends Controller
{
  public function __invoke(): JsonResponse
  {
    $available = ParkingSpot::where('is_occupied', false)->count();
    $total = ParkingSpot::count();

    return response()->json([
      'available' => $available,
      'total' => $total,
    ]);
  }
}
