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
        Schema::create('uniforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. "Botina Nobuck"
            $table->string('type')->index(); // e.g. "Calçado", "EPI", "Uniforme"
            $table->string('color')->nullable();
            $table->string('size')->nullable(); // e.g. "38", "P", "GG"
            $table->string('gender')->nullable(); // e.g. "Masculino", "Feminino", "Unissex"
            $table->decimal('quantity', 10, 3)->default(0);
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
        Schema::dropIfExists('uniforms');
    }
};
