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
        Schema::create('parking_pricing_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parking_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Avulso, Diária, Mensalista, etc
            $table->string('type'); // hourly, daily, monthly, custom
            $table->decimal('base_amount', 8, 2)->default(0);
            $table->integer('base_hours')->nullable(); // Para cobrança por hora
            $table->decimal('additional_hour_amount', 8, 2)->nullable(); // Para cobrança por hora
            $table->decimal('daily_amount', 8, 2)->nullable(); // Para cobrança diária
            $table->decimal('monthly_amount', 8, 2)->nullable(); // Para cobrança mensal
            $table->json('custom_rules')->nullable(); // Para regras personalizadas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_pricing_configs');
    }
};
