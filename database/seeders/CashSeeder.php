<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashTransaction;

class CashSeeder extends Seeder
{
    public function run(): void
    {
        // Exemplos de transações para demonstração
        // Você pode remover isso se não quiser dados de exemplo
        
        /*
        CashTransaction::create([
            'type' => 'entrada',
            'category' => 'Venda',
            'description' => 'Venda de produto exemplo',
            'amount' => 500.00,
            'payment_method' => 'pix',
            'transaction_date' => now(),
            'user_id' => 1,
            'user_name' => 'Admin',
        ]);

        CashTransaction::create([
            'type' => 'saida',
            'category' => 'Despesa',
            'description' => 'Compra de material',
            'amount' => 150.00,
            'payment_method' => 'dinheiro',
            'transaction_date' => now(),
            'user_id' => 1,
            'user_name' => 'Admin',
        ]);
        */
    }
}
