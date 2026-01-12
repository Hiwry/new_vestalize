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
        Schema::create('stock_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('stock_request_id')->nullable()->constrained('stock_requests')->onDelete('set null');
            
            // Tipo de movimentação
            $table->enum('action_type', [
                'entrada',      // Entrada de estoque
                'saida',        // Saída de estoque
                'reserva',      // Reserva de estoque
                'liberacao',    // Liberação de reserva
                'transferencia', // Transferência entre lojas
                'ajuste',       // Ajuste manual
                'devolucao',    // Devolução
                'perda',        // Perda/dano
            ]);
            
            // Dados do estoque
            $table->foreignId('fabric_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('cut_type_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->string('size', 10); // PP, P, M, G, GG, EXG, G1, G2, G3
            
            // Quantidades
            $table->integer('quantity_before')->default(0);
            $table->integer('quantity_after')->default(0);
            $table->integer('quantity_change'); // Pode ser positivo ou negativo
            $table->integer('reserved_quantity_before')->default(0);
            $table->integer('reserved_quantity_after')->default(0);
            
            // Informações adicionais
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Dados extras em JSON
            
            // Timestamps
            $table->timestamp('action_date');
            $table->timestamps();
            
            // Indexes
            $table->index('stock_id');
            $table->index('store_id');
            $table->index('action_type');
            $table->index('action_date');
            $table->index(['fabric_id', 'color_id', 'cut_type_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_history');
    }
};
