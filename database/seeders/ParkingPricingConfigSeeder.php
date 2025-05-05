<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\ParkingPricingConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParkingPricingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parkings = Parking::all();

        foreach ($parkings as $parking) {
            // Configuração para Avulso (por hora)
            ParkingPricingConfig::create([
                'parking_id' => $parking->id,
                'name' => 'Avulso',
                'type' => 'hourly',
                'base_amount' => $parking->hourly_rate,
                'base_hours' => 1,
                'additional_hour_amount' => $parking->hourly_rate,
                'is_active' => true,
            ]);

            // Configuração para Diária
            ParkingPricingConfig::create([
                'parking_id' => $parking->id,
                'name' => 'Diária',
                'type' => 'daily',
                'daily_amount' => $parking->hourly_rate * 24 * 0.8, // 20% de desconto
                'is_active' => true,
            ]);

            // Configuração para Mensalista
            ParkingPricingConfig::create([
                'parking_id' => $parking->id,
                'name' => 'Mensalista',
                'type' => 'monthly',
                'monthly_amount' => $parking->hourly_rate * 24 * 30 * 0.5, // 50% de desconto
                'is_active' => true,
            ]);

            // Configuração para Semanal
            ParkingPricingConfig::create([
                'parking_id' => $parking->id,
                'name' => 'Semanal',
                'type' => 'weekly',
                'weekly_amount' => $parking->hourly_rate * 24 * 7 * 0.6, // 40% de desconto
                'is_active' => true,
            ]);

            // Configuração para Quinzenal
            ParkingPricingConfig::create([
                'parking_id' => $parking->id,
                'name' => 'Quinzenal',
                'type' => 'biweekly',
                'biweekly_amount' => $parking->hourly_rate * 24 * 15 * 0.55, // 45% de desconto
                'is_active' => true,
            ]);
        }
    }
}
