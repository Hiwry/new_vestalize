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
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null')->comment('Pedido que gerou a solicitação');
            $table->foreignId('requesting_store_id')->constrained('stores')->onDelete('cascade')->comment('Loja que está solicitando');
            $table->foreignId('target_store_id')->nullable()->constrained('stores')->onDelete('set null')->comment('Loja de destino da solicitação');
            $table->foreignId('fabric_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Tipo de tecido');
            $table->foreignId('color_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Cor');
            $table->foreignId('cut_type_id')->nullable()->constrained('product_options')->onDelete('set null')->comment('Tipo de corte');
            $table->string('size', 10)->comment('PP, P, M, G, GG, EXG, G1, G2, G3');
            $table->integer('requested_quantity')->default(0)->comment('Quantidade solicitada');
            $table->integer('approved_quantity')->default(0)->comment('Quantidade aprovada');
            $table->integer('transferred_quantity')->default(0)->comment('Quantidade transferida');
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado', 'em_transferencia', 'concluido', 'cancelado'])->default('pendente');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->text('request_notes')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['requesting_store_id', 'status']);
            $table->index(['target_store_id', 'status']);
            $table->index(['order_id']);
            $table->index(['status', 'created_at']);
            $table->index(['fabric_id', 'color_id', 'cut_type_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
