<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'plate' => 'ABC1234',
                'model' => 'Toyota Corolla',
                'color' => 'Prata',
            ],
            [
                'plate' => 'DEF5678',
                'model' => 'Honda Civic',
                'color' => 'Preto',
            ],
            [
                'plate' => 'GHI9012',
                'model' => 'Volkswagen Gol',
                'color' => 'Branco',
            ],
            [
                'plate' => 'JKL3456',
                'model' => 'Fiat Uno',
                'color' => 'Vermelho',
            ],
            [
                'plate' => 'MNO7890',
                'model' => 'Chevrolet Onix',
                'color' => 'Azul',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
