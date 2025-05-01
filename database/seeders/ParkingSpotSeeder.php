<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\ParkingSpot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parkings = Parking::all();

        foreach ($parkings as $parking) {
            // Criar 20 vagas para cada estacionamento
            for ($i = 1; $i <= 20; $i++) {
                ParkingSpot::create([
                    'parking_id' => $parking->id,
                    'identification' => "V{$i}",
                    'is_occupied' => false,
                ]);
            }
        }
    }
}
