<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\Dashboard\GetUsedSpotsController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->name('web.')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->name('api.')->group(function () {
    Route::get('/used-spots', GetUsedSpotsController::class)->name('used-spots');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
