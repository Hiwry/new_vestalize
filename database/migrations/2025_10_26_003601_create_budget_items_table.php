<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->integer('item_number');
            $table->string('fabric')->nullable();
            $table->string('fabric_type')->nullable();
            $table->string('color')->nullable();
            $table->integer('quantity'); // Apenas quantidade total, sem tamanhos
            $table->string('personalization_types')->nullable(); // JSON com tipos de personalização
            $table->decimal('item_total', 10, 2)->default(0);
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
