<?php

namespace App\Http\Controllers\Api\Parking;

use App\Http\Controllers\Controller;
use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use App\Models\ParkingPricingConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterEntryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'plate' => 'required|string|max:7',
            'model' => 'nullable|string',
            'color' => 'nullable|string',
            'pricing_type' => 'required|in:hourly,daily,monthly',
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

            // Busca a configuração de preços
            $pricingConfig = ParkingPricingConfig::where('type', $request->pricing_type)
                ->where('parking_id', $spot->parking_id)
                ->where('is_active', true)
                ->first();

            if (!$pricingConfig) {
                return response()->json(['message' => 'Configuração de preços não encontrada'], 400);
            }

            // Cria o registro de entrada
            $record = ParkingRecord::create([
                'vehicle_id' => $vehicle->id,
                'parking_spot_id' => $spot->id,
                'parking_id' => $spot->parking_id,
                'parking_pricing_config_id' => $pricingConfig->id,
                'entry_time' => now(),
            ]);

            // Marca a vaga como ocupada
            $spot->update(['is_occupied' => true]);

            return response()->json([
                'message' => 'Entrada registrada com sucesso',
                'data' => $record->load('vehicle', 'parkingSpot', 'pricingConfig'),
            ]);
        });
    }
} 