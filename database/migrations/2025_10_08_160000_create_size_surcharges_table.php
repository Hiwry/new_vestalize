<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_surcharges', function (Blueprint $table) {
            $table->id();
            $table->string('size'); // GG, EXG, G1, G2, G3
            $table->decimal('price_from', 10, 2); // Valor mínimo
            $table->decimal('price_to', 10, 2)->nullable(); // Valor máximo (null = infinito)
            $table->decimal('surcharge', 10, 2); // Acréscimo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_surcharges');
    }
};
