<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_sublimations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('quantity'); // Quantidade de aplicações neste local
            $table->decimal('unit_price', 10, 2); // Preço unitário (antes do desconto)
            $table->decimal('discount_percent', 5, 2)->default(0); // Percentual de desconto aplicado
            $table->decimal('final_price', 10, 2); // Preço final (com desconto)
            $table->timestamps();

            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sublimation_sizes')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('sublimation_locations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_sublimations');
    }
};
