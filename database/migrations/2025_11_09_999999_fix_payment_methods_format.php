<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corrigir pagamentos antigos que não têm payment_methods como array
        $paymentsToFix = DB::table('payments')
            ->whereNull('payment_methods')
            ->orWhere('payment_methods', '')
            ->get();

        foreach ($paymentsToFix as $payment) {
            // Se tem amount mas não tem entry_amount, usar amount
            $entryAmount = $payment->entry_amount > 0 ? $payment->entry_amount : $payment->amount;
            
            // Criar array de payment_methods
            $method = $payment->method ?: $payment->payment_method ?: 'pix';
            $paymentMethods = [
                [
                    'id' => uniqid(),
                    'method' => $method,
                    'amount' => floatval($entryAmount),
                ]
            ];

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'entry_amount' => $entryAmount,
                    'payment_methods' => json_encode($paymentMethods),
                    'method' => $method,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não é necessário reverter
    }
};

