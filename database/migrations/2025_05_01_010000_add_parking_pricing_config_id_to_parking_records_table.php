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
        Schema::table('parking_records', function (Blueprint $table) {
            $table->foreignUuid('parking_pricing_config_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_records', function (Blueprint $table) {
            $table->dropForeign(['parking_pricing_config_id']);
            $table->dropColumn('parking_pricing_config_id');
        });
    }
}; 