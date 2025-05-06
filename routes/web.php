<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\Dashboard\GetUsedSpotsController;
use App\Http\Controllers\Api\Dashboard\GetEarningsController;
use App\Http\Controllers\Api\Dashboard\GetPricingOptionsController;
use App\Http\Controllers\Api\Parking\RegisterEntryController;
use App\Http\Controllers\Api\Parking\RegisterExitController;
use App\Http\Controllers\Api\Parking\GetAvailableSpotsController;
use App\Http\Controllers\Api\Parking\GetParkedVehiclesController;
use App\Http\Controllers\Api\Parking\CalculateParkingFeeController;
use App\Http\Controllers\Api\Parking\GetNearbyParkingsController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\UserLocationController;

Route::get('/', function () {
    return Inertia::render('find-parkings');
})->name('home');

Route::middleware(['auth', 'verified'])->name('web.')->group(function () {
    Route::get('/estacionamento/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->name('api.')->group(function () {
    Route::get('/used-spots', GetUsedSpotsController::class)->name('used-spots');
    Route::get('/earnings', GetEarningsController::class)->name('earnings');
    Route::get('/pricing-options', GetPricingOptionsController::class)->name('pricing-options');
    Route::get('/parking/parked-vehicles', GetParkedVehiclesController::class)->name('parking.parked-vehicles');

    Route::post('/parking/entry', RegisterEntryController::class)->name('parking.entry');
    Route::post('/parking/exit/{record}', RegisterExitController::class)->name('parking.exit');
    Route::get('/parking/available-spots', GetAvailableSpotsController::class)->name('parking.available-spots');
    Route::post('/parking/calculate-fee', CalculateParkingFeeController::class)->name('parking.calculate-fee');
});

Route::get('/api/parking/nearby', GetNearbyParkingsController::class)->name('api.parking.nearby');

Route::get('/api/user-location', UserLocationController::class);

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
