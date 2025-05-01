<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ParkingSeeder::class,
            UserSeeder::class,
            ParkingPricingConfigSeeder::class,
            ParkingSpotSeeder::class,
            VehicleSeeder::class,
            ParkingRecordSeeder::class,
        ]);
    }
}
