<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->string('identification')->unique();
            $table->boolean('is_occupied')->default(false);
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('parking_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_spot_id')->constrained()->onDelete('cascade');
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_records');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('parking_spots');
    }
};
