<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use App\Models\ParkingPricingConfig;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ParkingRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $parkings = Parking::all();

        foreach ($parkings as $parking) {
            $spots = $parking->parkingSpots()->take(3)->get();
            $pricingConfigs = $parking->pricingConfigs()->where('is_active', true)->get();

            foreach ($spots as $spot) {
                $vehicle = $vehicles->random();
                $pricingConfig = $pricingConfigs->random();

                ParkingRecord::create([
                    'parking_id' => $parking->id,
                    'vehicle_id' => $vehicle->id,
                    'parking_spot_id' => $spot->id,
                    'parking_pricing_config_id' => $pricingConfig->id,
                    'entry_time' => Carbon::now()->subHours(rand(1, 5)),
                    'exit_time' => null,
                    'total_amount' => 0,
                    'is_paid' => false,
                ]);

                // Atualizar o status da vaga
                $spot->update(['is_occupied' => true]);
            }

            // Criar alguns registros com saída para testar o cálculo de valores
            $spots = $parking->parkingSpots()->skip(3)->take(2)->get();
            foreach ($spots as $spot) {
                $vehicle = $vehicles->random();
                $pricingConfig = $pricingConfigs->random();
                $entryTime = Carbon::now()->subHours(rand(1, 5));
                $exitTime = $entryTime->copy()->addHours(rand(1, 24));

                $record = ParkingRecord::create([
                    'parking_id' => $parking->id,
                    'vehicle_id' => $vehicle->id,
                    'parking_spot_id' => $spot->id,
                    'parking_pricing_config_id' => $pricingConfig->id,
                    'entry_time' => $entryTime,
                    'exit_time' => $exitTime,
                    'total_amount' => 0,
                    'is_paid' => true,
                ]);

                // Calcular e atualizar o valor total
                $record->update([
                    'total_amount' => $record->calculateTotalAmount(),
                ]);
            }
        }
    }
}
