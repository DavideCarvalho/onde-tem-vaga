<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingSpot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class GetAvailableSpotsController extends Controller
{
  public function __invoke(Request $request): JsonResponse
  {
    /** @var User $user */
    $user = $request->user();
    $available = ParkingSpot::whereNull('exit_time')->andWhere('parking_id', $user->parking_id)->count();
    $total = ParkingSpot::where('parking_id', $user->parking_id)->count();

    return response()->json([
      'available' => $available,
      'total' => $total,
    ]);
  }
}
