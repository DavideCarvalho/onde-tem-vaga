<?php

namespace Database\Seeders;

use App\Models\Parking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Parking::create([
            'name' => 'Estacionamento Central',
            'address' => 'Rua Principal, 123 - Centro',
            'hourly_rate' => 5.00,
        ]);

        Parking::create([
            'name' => 'Estacionamento Shopping',
            'address' => 'Av. Comercial, 456 - Shopping Center',
            'hourly_rate' => 8.00,
        ]);

        Parking::create([
            'name' => 'Estacionamento Aeroporto',
            'address' => 'Av. Aeroporto, 789 - Aeroporto',
            'hourly_rate' => 10.00,
        ]);
    }
}
