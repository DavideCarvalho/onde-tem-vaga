<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterExitController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'record_id' => 'required|exists:parking_records,id',
            'exit_time' => 'required|date',
        ]);

        $record = ParkingRecord::findOrFail($request->record_id);

        if ($record->exit_time) {
            return response()->json([
                'message' => 'Este veículo já saiu do estacionamento.',
            ], 400);
        }

        $record->update([
            'exit_time' => $request->exit_time,
        ]);

        return response()->json([
            'message' => 'Saída registrada com sucesso!',
            'data' => [
                'id' => $record->id,
                'plate' => $record->vehicle->plate,
                'brand' => $record->vehicle->brand,
                'model' => $record->vehicle->model,
                'color' => $record->vehicle->color,
                'entry_time' => $record->entry_time,
                'exit_time' => $record->exit_time,
                'total_time' => $record->exit_time->diffForHumans($record->entry_time),
                'total_price' => $record->calculateTotalAmount(),
            ],
        ]);
    }
}
