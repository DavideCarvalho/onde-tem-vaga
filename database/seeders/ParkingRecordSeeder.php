<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
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
            
            foreach ($spots as $spot) {
                $vehicle = $vehicles->random();
                
                ParkingRecord::create([
                    'parking_id' => $parking->id,
                    'vehicle_id' => $vehicle->id,
                    'parking_spot_id' => $spot->id,
                    'entry_time' => Carbon::now()->subHours(rand(1, 5)),
                    'exit_time' => null,
                    'total_amount' => 0,
                    'is_paid' => false,
                ]);

                // Atualizar o status da vaga
                $spot->update(['is_occupied' => true]);
            }
        }
    }
}
