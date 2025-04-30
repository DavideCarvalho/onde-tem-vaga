<?php

namespace App\Http\Controllers;

use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingController extends Controller
{
    public function registerEntry(Request $request)
    {
        $request->validate([
            'plate' => 'required|string|max:7',
            'model' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Busca ou cria o veículo
            $vehicle = Vehicle::firstOrCreate(
                ['plate' => $request->plate],
                [
                    'model' => $request->model,
                    'color' => $request->color,
                ]
            );

            // Encontra uma vaga disponível
            $spot = ParkingSpot::where('is_occupied', false)->first();
            if (!$spot) {
                return response()->json(['message' => 'Não há vagas disponíveis'], 400);
            }

            // Cria o registro de entrada
            $record = ParkingRecord::create([
                'vehicle_id' => $vehicle->id,
                'parking_spot_id' => $spot->id,
                'entry_time' => now(),
            ]);

            // Marca a vaga como ocupada
            $spot->update(['is_occupied' => true]);

            return response()->json([
                'message' => 'Entrada registrada com sucesso',
                'data' => $record->load('vehicle', 'parkingSpot'),
            ]);
        });
    }

    public function registerExit(Request $request, ParkingRecord $record)
    {
        if ($record->exit_time) {
            return response()->json(['message' => 'Saída já registrada'], 400);
        }

        return DB::transaction(function () use ($record) {
            $record->update([
                'exit_time' => now(),
                'total_amount' => $this->calculateAmount($record),
            ]);

            $record->parkingSpot->update(['is_occupied' => false]);

            return response()->json([
                'message' => 'Saída registrada com sucesso',
                'data' => $record->load('vehicle', 'parkingSpot'),
            ]);
        });
    }

    public function getAvailableSpots()
    {
        $available = ParkingSpot::where('is_occupied', false)->count();
        $total = ParkingSpot::count();

        return response()->json([
            'available' => $available,
            'total' => $total,
        ]);
    }

    private function calculateAmount(ParkingRecord $record): float
    {
        $entryTime = $record->entry_time;
        $exitTime = $record->exit_time;
        $hours = $exitTime->diffInHours($entryTime);
        
        // Valor base por hora
        $baseRate = 5.00;
        
        // Primeira hora é gratuita
        if ($hours <= 1) {
            return 0;
        }
        
        // Demais horas são cobradas
        return ($hours - 1) * $baseRate;
    }
} 