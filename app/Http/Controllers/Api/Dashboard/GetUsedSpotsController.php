<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Data\GetUsedSpotsData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUsedSpotsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $totalSpots = $user->parking->parkingSpots()->count();
        $usedSpots = $user->parking->parkingSpots()->where('is_occupied', true)->count();

        return response()->json(GetUsedSpotsData::make($totalSpots, $usedSpots));
    }
}
