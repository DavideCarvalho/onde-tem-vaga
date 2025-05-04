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
        Schema::create('parking_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parking_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('parking_spot_id')->constrained()->onDelete('cascade');
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->decimal('total_amount', 8, 2)->default(0);
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
    }
};
