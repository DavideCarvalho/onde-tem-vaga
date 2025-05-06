<?php

namespace Database\Seeders;

use App\Models\Parking;
use Illuminate\Database\Seeder;
use Clickbar\Magellan\Data\Geometries\Point;

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
            'street' => 'Rua Principal',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01000-000',
            'hourly_rate' => 5.00,
            'latitude' => -23.550520,
            'longitude' => -46.633308,
            'location' => Point::makeGeodetic(-23.550520, -46.633308),
        ]);

        Parking::create([
            'name' => 'Estacionamento Shopping',
            'address' => 'Av. Comercial, 456 - Shopping Center',
            'street' => 'Av. Comercial',
            'number' => '456',
            'neighborhood' => 'Shopping Center',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01100-000',
            'hourly_rate' => 8.00,
            'latitude' => -23.561414,
            'longitude' => -46.655881,
            'location' => Point::makeGeodetic(-23.561414, -46.655881),
        ]);

        Parking::create([
            'name' => 'Estacionamento Aeroporto',
            'address' => 'Av. Aeroporto, 789 - Aeroporto',
            'street' => 'Av. Aeroporto',
            'number' => '789',
            'neighborhood' => 'Aeroporto',
            'city' => 'Guarulhos',
            'state' => 'SP',
            'zip_code' => '07190-100',
            'hourly_rate' => 10.00,
            'latitude' => -23.626634,
            'longitude' => -46.656111,
            'location' => Point::makeGeodetic(-23.626634, -46.656111),
        ]);

        Parking::create([
            'name' => 'Estacionamento Gonzaga',
            'address' => 'Av. Ana Costa, 500 - Gonzaga',
            'street' => 'Av. Ana Costa',
            'number' => '500',
            'neighborhood' => 'Gonzaga',
            'city' => 'Santos',
            'state' => 'SP',
            'zip_code' => '11060-001',
            'hourly_rate' => 7.00,
            'latitude' => -23.967539,
            'longitude' => -46.328869,
            'location' => Point::makeGeodetic(-23.967539, -46.328869),
        ]);

        Parking::create([
            'name' => 'Estacionamento Ponta da Praia',
            'address' => 'Av. Saldanha da Gama, 1000 - Ponta da Praia',
            'street' => 'Av. Saldanha da Gama',
            'number' => '1000',
            'neighborhood' => 'Ponta da Praia',
            'city' => 'Santos',
            'state' => 'SP',
            'zip_code' => '11030-401',
            'hourly_rate' => 9.00,
            'latitude' => -23.981531,
            'longitude' => -46.304882,
            'location' => Point::makeGeodetic(-23.981531, -46.304882),
        ]);

        Parking::create([
            'name' => 'Estacionamento Boqueirão',
            'address' => 'Rua Oswaldo Cruz, 200 - Boqueirão',
            'street' => 'Rua Oswaldo Cruz',
            'number' => '200',
            'neighborhood' => 'Boqueirão',
            'city' => 'Santos',
            'state' => 'SP',
            'zip_code' => '11045-101',
            'hourly_rate' => 6.50,
            'latitude' => -23.960832,
            'longitude' => -46.333477,
            'location' => Point::makeGeodetic(-23.960832, -46.333477),
        ]);
    }
}
