<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Data\Parking\GetParkedVehicleResponseData;

class GetParkedVehiclesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $vehicles = ParkingRecord::whereNull('exit_time')
            ->with('vehicle')
            ->where('parking_id', $user->parking_id)
            ->get()
            ->map(function ($record) {
                return GetParkedVehicleResponseData::make(
                    $record->id,
                    $record->vehicle->plate ?? '',
                    $record->vehicle->brand ?? '',
                    $record->vehicle->model ?? '',
                    $record->vehicle->color ?? '',
                    $record->entry_time
                );
            });

        return response()->json($vehicles);
    }
}
