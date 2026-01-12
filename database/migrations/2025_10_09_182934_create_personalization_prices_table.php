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
        Schema::create('personalization_prices', function (Blueprint $table) {
            $table->id();
            $table->string('personalization_type'); // DTF, SERIGRAFIA, BORDADO, SUBLIMACAO
            $table->string('size_name'); // A4, A3, 10x15cm, etc.
            $table->string('size_dimensions')->nullable(); // 21x29.7cm, etc.
            $table->integer('quantity_from'); // Quantidade mínima
            $table->integer('quantity_to')->nullable(); // Quantidade máxima (null = infinito)
            $table->decimal('price', 10, 2); // Preço unitário
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0); // Para ordenação
            $table->timestamps();
            
            $table->index(['personalization_type', 'active']);
            $table->index(['personalization_type', 'size_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalization_prices');
    }
};
