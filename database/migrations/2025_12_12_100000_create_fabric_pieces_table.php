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
        Schema::create('fabric_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('fabric_id')->nullable()->constrained('product_options')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('product_options')->nullOnDelete();
            
            // Informações do fornecedor e NF
            $table->string('supplier')->nullable()->comment('Fornecedor');
            $table->string('invoice_number')->nullable()->comment('Número da Nota Fiscal');
            $table->string('invoice_key')->nullable()->comment('Chave de Acesso NF-e (44 dígitos)');
            
            // Características físicas
            $table->decimal('weight', 10, 3)->nullable()->comment('Peso em kg');
            $table->decimal('weight_current', 10, 3)->nullable()->comment('Peso atual (após abrir)');
            $table->decimal('meters', 10, 2)->nullable()->comment('Metragem');
            $table->string('barcode')->nullable()->comment('Código de barras/SKU');
            $table->string('shelf')->nullable()->comment('Prateleira/Gôndola');
            
            // Origem e destino
            $table->string('origin')->nullable()->comment('Cidade/Estado de origem');
            $table->string('destination')->nullable()->comment('Destino');
            
            // Status e datas
            $table->enum('status', ['fechada', 'aberta', 'vendida', 'em_transferencia'])->default('fechada');
            $table->datetime('received_at')->nullable()->comment('Data de recebimento');
            $table->datetime('opened_at')->nullable()->comment('Data de abertura');
            $table->datetime('sold_at')->nullable()->comment('Data de venda');
            
            // Preços
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('Preço de compra');
            $table->decimal('sale_price', 10, 2)->nullable()->comment('Preço de venda');
            
            // Vinculações
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('sold_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Observações
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['store_id', 'status']);
            $table->index('invoice_number');
            $table->index('barcode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_pieces');
    }
};
