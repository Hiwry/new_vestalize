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
        Schema::create('sublimation_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do adicional (ex: REGATA, M. LONGA, POLO)
            $table->string('description')->nullable(); // Descrição opcional
            $table->decimal('price_adjustment', 10, 2); // Ajuste de preço (+ ou -)
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0); // Para ordenação
            $table->timestamps();
            
            $table->index(['active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sublimation_addons');
    }
};