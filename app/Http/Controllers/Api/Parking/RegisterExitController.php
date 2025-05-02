<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegisterExitController extends Controller
{
    public function __invoke(ParkingRecord $record): JsonResponse
    {
        if ($record->exit_time) {
            return response()->json(['message' => 'Saída já registrada'], 400);
        }

        return DB::transaction(function () use ($record) {
            $record->update([
                'exit_time' => now(),
                'total_amount' => $record->calculateTotalAmount(),
            ]);

            $record->parkingSpot->update(['is_occupied' => false]);

            return response()->json([
                'message' => 'Saída registrada com sucesso',
                'data' => $record->load('vehicle', 'parkingSpot', 'pricingConfig'),
            ]);
        });
    }
} 