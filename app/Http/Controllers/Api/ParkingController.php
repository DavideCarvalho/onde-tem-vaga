<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function parkedVehicles()
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