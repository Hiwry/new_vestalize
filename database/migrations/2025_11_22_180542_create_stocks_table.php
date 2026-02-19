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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('fabric_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Tipo de tecido');
            $table->foreignId('color_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Cor');
            $table->foreignId('cut_type_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Tipo de corte');
            $table->string('size', 10)->comment('PP, P, M, G, GG, EXG, G1, G2, G3');
            $table->integer('quantity')->default(0)->comment('Quantidade em estoque');
            $table->integer('reserved_quantity')->default(0)->comment('Quantidade reservada (em pedidos)');
            $table->decimal('min_stock', 10, 2)->default(0)->comment('Estoque mínimo');
            $table->decimal('max_stock', 10, 2)->nullable()->comment('Estoque máximo');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índices únicos para garantir que não haja duplicatas
            $table->unique(['store_id', 'fabric_id', 'color_id', 'cut_type_id', 'size'], 'stock_unique');
            
            // Índices para buscas rápidas
            $table->index(['store_id', 'fabric_id', 'color_id', 'cut_type_id']);
            $table->index(['store_id', 'size']);
            $table->index(['store_id', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
