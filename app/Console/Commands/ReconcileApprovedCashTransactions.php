<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\CashApprovalSyncService;
use Illuminate\Console\Command;

class ReconcileApprovedCashTransactions extends Command
{
    protected $signature = 'cash:reconcile-approved
                            {--order_id= : Reconciliar apenas um pedido}
                            {--dry-run : Apenas exibe os pedidos elegiveis}';

    protected $description = 'Garante que pagamentos aprovados tenham todas as transacoes confirmadas no caixa';

    public function __construct(
        private CashApprovalSyncService $cashApprovalSyncService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = Order::with(['payment', 'client', 'user'])
            ->whereHas('payment', function ($paymentQuery) {
                $paymentQuery->where('cash_approved', true);
            });

        if ($orderId = $this->option('order_id')) {
            $query->where('id', $orderId);
        }

        $orders = $query->orderBy('id')->get();

        if ($orders->isEmpty()) {
            $this->info('Nenhum pedido aprovado encontrado para reconciliar.');
            return self::SUCCESS;
        }

        $this->info("Pedidos aprovados encontrados: {$orders->count()}");

        foreach ($orders as $order) {
            $payment = $order->payment;
            if (!$payment) {
                $this->line("Pulado pedido #{$order->id}: sem payment.");
                continue;
            }

            if ($this->option('dry-run')) {
                $methodsCount = is_array($payment->payment_methods) ? count($payment->payment_methods) : 0;
                $this->warn("Pedido #{$order->id}: {$methodsCount} metodo(s), total pago R$ " . number_format((float) $payment->entry_amount, 2, ',', '.'));
                continue;
            }

            $this->line("Reconciliando pedido #{$order->id}...");
            $this->cashApprovalSyncService->syncApprovedPayment(
                $order,
                $payment,
                'Reconciliado por comando',
                'Comando cash:reconcile-approved'
            );
        }

        $this->info($this->option('dry-run') ? 'Dry-run concluido.' : 'Reconciliacao concluida.');

        return self::SUCCESS;
    }
}
