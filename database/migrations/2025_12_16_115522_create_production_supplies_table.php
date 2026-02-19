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
        Schema::create('production_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('name'); // ex: Linha Branca
            $table->string('type'); // aviamento, tinta, embalagem, outros
            $table->string('color')->nullable();
            $table->decimal('quantity', 10, 3)->default(0);
            $table->string('unit', 10); // un, kg, lt, mt, pct
            $table->decimal('min_stock', 10, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_supplies');
    }
};
