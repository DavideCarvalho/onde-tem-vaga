<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\JsonResponse;

class GetParkedVehiclesController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $vehicles = ParkingRecord::whereNull('exit_time')
            ->with('vehicle')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'plate' => $record->vehicle->plate,
                    'brand' => $record->vehicle->brand,
                    'model' => $record->vehicle->model,
                    'color' => $record->vehicle->color,
                    'entry_time' => $record->entry_time,
                ];
            });

        return response()->json($vehicles);
    }
}
