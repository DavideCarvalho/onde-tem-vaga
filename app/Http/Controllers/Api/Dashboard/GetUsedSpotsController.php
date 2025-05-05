<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Data\GetUsedSpotsData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ParkingSpot;
use App\Models\User;

class GetUsedSpotsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        /** @var int $totalSpots */
        $totalSpots = ParkingSpot::where('parking_id', $user->parking_id)->count();
        /** @var int $usedSpots */
        $usedSpots = ParkingSpot::where('parking_id', $user->parking_id)
            ->whereHas('parkingRecords', function ($query) {
                $query->whereNull('exit_time');
            })
            ->count();

        return response()->json(GetUsedSpotsData::make($totalSpots, $usedSpots));
    }
}
