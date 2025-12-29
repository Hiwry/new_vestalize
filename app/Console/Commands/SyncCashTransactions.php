<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CashTransaction;

class SyncCashTransactions extends Command
{
    protected $signature = 'cash:sync';
    protected $description = 'Sincroniza pedidos existentes com o caixa';

    public function handle()
    {
        $this->info('Sincronizando pedidos com o caixa...');

        // Buscar todos os pedidos que têm pagamento mas não têm transação no caixa
        $payments = Payment::with('order.client')
            ->whereDoesntHave('order.cashTransactions')
            ->get();

        $count = 0;

        foreach ($payments as $payment) {
            $order = $payment->order;
            
            if (!$order) {
                continue;
            }

            // Verificar se há múltiplos métodos de pagamento
            $paymentMethods = is_array($payment->payment_methods) 
                ? $payment->payment_methods 
                : json_decode($payment->payment_methods ?? '[]', true);

            if (empty($paymentMethods)) {
                // Se não houver métodos múltiplos, criar uma única transação
                $paymentMethods = [[
                    'method' => $payment->method ?? $payment->payment_method ?? 'pix',
                    'amount' => $payment->entry_amount ?? $payment->amount ?? 0
                ]];
            }

            // Criar transação para cada método de pagamento
            foreach ($paymentMethods as $method) {
                if (isset($method['amount']) && $method['amount'] > 0) {
                    CashTransaction::create([
                        'type' => 'entrada',
                        'category' => 'Venda',
                        'description' => "Pagamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . $order->client->name,
                        'amount' => $method['amount'],
                        'payment_method' => $method['method'],
                        'transaction_date' => $payment->entry_date ?? $payment->payment_date ?? $order->created_at,
                        'order_id' => $order->id,
                        'user_id' => null,
                        'user_name' => 'Sistema (Sincronização)',
                        'notes' => 'Transação sincronizada automaticamente',
                    ]);
                    
                    $count++;
                }
            }
        }

        $this->info("✅ Sincronização concluída! {$count} transação(ões) criada(s).");
        
        return Command::SUCCESS;
    }
}
