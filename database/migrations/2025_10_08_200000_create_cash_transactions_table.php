<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entrada', 'saida']); // Tipo de transação
            $table->string('category'); // Categoria (venda, compra, despesa, etc)
            $table->text('description'); // Descrição
            $table->decimal('amount', 12, 2); // Valor
            $table->enum('payment_method', ['dinheiro', 'pix', 'cartao', 'transferencia', 'boleto']); // Forma de pagamento
            $table->date('transaction_date'); // Data da transação
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Pedido relacionado (se houver)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Usuário que registrou
            $table->string('user_name'); // Nome do usuário
            $table->text('notes')->nullable(); // Observações
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
