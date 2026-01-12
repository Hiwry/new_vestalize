<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar coluna para múltiplos meios de pagamento (JSON)
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->json('payment_methods')->nullable()->after('payment_method');
        });

        // Migrar dados existentes de payment_method para payment_methods
        $transactions = DB::table('cash_transactions')->whereNull('payment_methods')->get();
        foreach ($transactions as $transaction) {
            $paymentMethods = json_encode([[
                'method' => $transaction->payment_method,
                'amount' => (float) $transaction->amount
            ]]);
            DB::table('cash_transactions')
                ->where('id', $transaction->id)
                ->update(['payment_methods' => $paymentMethods]);
        }

        // Alterar enum de payment_method para incluir novos tipos
        // Como não podemos alterar enum diretamente, vamos fazer via raw SQL
        DB::statement("
            ALTER TABLE cash_transactions 
            MODIFY COLUMN payment_method ENUM(
                'dinheiro', 
                'pix', 
                'cartao', 
                'transferencia', 
                'boleto',
                'entrada_dinheiro',
                'debito_conta',
                'credito_conta',
                'multiplo'
            ) DEFAULT 'dinheiro'
        ");
    }

    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropColumn('payment_methods');
        });

        // Reverter enum para valores originais
        DB::statement("
            ALTER TABLE cash_transactions 
            MODIFY COLUMN payment_method ENUM(
                'dinheiro', 
                'pix', 
                'cartao', 
                'transferencia', 
                'boleto'
            ) DEFAULT 'dinheiro'
        ");
    }
};

