<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Data\Parking\GetNearbyParkingResponseData;
use App\Models\Parking;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST;

class GetNearbyParkingsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 5000); // Default radius is 5000 meters

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Latitude e longitude são obrigatórios'], 422);
        }

        $point = Point::makeGeodetic((float)$lat, (float)$lng);

        $parkings = Parking::query()
            ->select([
                'id',
                'name',
                'address',
                'latitude',
                'longitude',
                ST::distanceSphere($point, 'location')->as('distance')
            ])
            ->whereNotNull('location')
            ->where(ST::distanceSphere($point, 'location'), '<=', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get()
            ->map(function ($parking) {
                $totalSpaces = $parking->parkingSpots()->count();
                $occupiedSpaces = $parking->parkingSpots()->whereHas('parkingRecords', function ($query) {
                    $query->whereNull('exit_time');
                })->count();
                return GetNearbyParkingResponseData::make(
                    (string) $parking->id,
                    $parking->name,
                    $parking->address,
                    round($parking->distance) . 'm',
                    $totalSpaces - $occupiedSpaces,
                    $totalSpaces > 0 ? round(($occupiedSpaces / $totalSpaces) * 100, 2) : 0
                );
            });

        return response()->json($parkings);
    }
}
