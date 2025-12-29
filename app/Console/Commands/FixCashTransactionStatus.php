<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\Status;

class FixCashTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cash:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige o status das transações de caixa baseado no status do pedido';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando transações de caixa...');

        // Buscar IDs dos status "Pronto" e "Entregue"
        $statusPronto = Status::where('name', 'Pronto')->first();
        $statusEntregue = Status::where('name', 'Entregue')->first();

        if (!$statusPronto || !$statusEntregue) {
            $this->error('❌ Status "Pronto" ou "Entregue" não encontrados!');
            return 1;
        }

        // Buscar transações de entrada (pagamentos) que estão como "confirmado"
        $transactions = CashTransaction::where('type', 'entrada')
            ->where('status', 'confirmado')
            ->whereNotNull('order_id')
            ->get();

        $this->info("📊 Encontradas {$transactions->count()} transações confirmadas.");

        $updated = 0;

        foreach ($transactions as $transaction) {
            $order = Order::with('status')->find($transaction->order_id);

            if (!$order) {
                $this->warn("⚠️  Pedido #{$transaction->order_id} não encontrado (Transação ID: {$transaction->id})");
                continue;
            }

            // Se o pedido NÃO está em "Pronto" ou "Entregue", mudar para "pendente"
            if (!in_array($order->status_id, [$statusPronto->id, $statusEntregue->id])) {
                $transaction->update(['status' => 'pendente']);
                $updated++;
                $this->line("✅ Transação ID {$transaction->id} (Pedido #{$order->id} - {$order->status->name}) → Pendente");
            }
        }

        $this->newLine();
        $this->info("✨ Concluído! {$updated} transações atualizadas para 'pendente'.");

        return 0;
    }
}
