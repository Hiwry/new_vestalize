<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublimation_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('size_id');
            $table->integer('quantity_from'); // Quantidade mínima
            $table->integer('quantity_to')->nullable(); // Quantidade máxima (null = infinito)
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('size_id')->references('id')->on('sublimation_sizes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sublimation_prices');
    }
};
