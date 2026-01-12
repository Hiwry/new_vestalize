<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CashTransaction;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\StockMovement;
use App\Models\SalesHistory;
use App\Models\OrderStatusTracking;
use App\Models\Status;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutService
{
    /**
     * Registrar pagamentos para um pedido
     * 
     * @param Order $order
     * @param array $paymentMethods Array de métodos de pagamento
     * @param float $total Total do pedido
     * @return Payment
     */
    public static function processPayments(Order $order, array $paymentMethods, float $total): Payment
    {
        // Garantir que cada método tenha um ID
        foreach ($paymentMethods as $index => $method) {
            if (!isset($method['id'])) {
                $paymentMethods[$index]['id'] = time() . rand(1000, 9999) . $index;
            }
        }
        
        $totalPaid = array_sum(array_column($paymentMethods, 'amount'));
        $primaryMethod = count($paymentMethods) === 1 ? $paymentMethods[0]['method'] : 'pix';
        
        return Payment::create([
            'order_id' => $order->id,
            'method' => $primaryMethod,
            'payment_method' => count($paymentMethods) > 1 ? 'multiplo' : $primaryMethod,
            'payment_methods' => $paymentMethods,
            'amount' => $total,
            'entry_amount' => $totalPaid,
            'remaining_amount' => max(0, $total - $totalPaid),
            'entry_date' => Carbon::now('America/Sao_Paulo'),
            'payment_date' => Carbon::now('America/Sao_Paulo'),
            'status' => $totalPaid >= $total ? 'pago' : 'pendente',
        ]);
    }

    /**
     * Registrar transações de caixa para um pedido
     * 
     * @param Order $order
     * @param array $paymentMethods
     * @param int $storeId
     * @param string $status 'pendente' para pedidos, 'confirmado' para PDV
     */
    public static function recordCashTransactions(
        Order $order, 
        array $paymentMethods, 
        int $storeId, 
        string $status = 'confirmado'
    ): void {
        $user = Auth::user();
        $client = $order->client;
        $clientName = $client->name ?? 'N/A';
        $orderNumber = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        
        $categoryPrefix = $order->is_pdv ? 'Venda PDV' : 'Venda';
        
        foreach ($paymentMethods as $method) {
            CashTransaction::create([
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? 'Sistema',
                'order_id' => $order->id,
                'store_id' => $storeId,
                'type' => 'entrada',
                'category' => 'Venda',
                'description' => "{$categoryPrefix} - Pedido #{$orderNumber} - Cliente: {$clientName}",
                'amount' => $method['amount'],
                'payment_method' => $method['method'],
                'payment_methods' => [$method],
                'transaction_date' => Carbon::now('America/Sao_Paulo'),
                'status' => $status,
                'notes' => "{$categoryPrefix} - Pedido #{$orderNumber}",
            ]);
        }
    }

    /**
     * Cancelar transações de caixa de um pedido (criar transações de reversão)
     * 
     * @param Order $sale
     * @param string $reason
     */
    public static function reverseCashTransactions(Order $sale, string $reason): void
    {
        $user = Auth::user();
        $client = $sale->client;
        $clientName = $client->name ?? 'N/A';
        $orderNumber = str_pad($sale->id, 6, '0', STR_PAD_LEFT);
        
        $cashTransactions = $sale->cashTransactions()->where('status', 'confirmado')->get();
        
        foreach ($cashTransactions as $transaction) {
            CashTransaction::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'order_id' => $sale->id,
                'store_id' => $transaction->store_id,
                'type' => 'saida',
                'category' => 'Cancelamento',
                'description' => "Cancelamento de Venda PDV - Pedido #{$orderNumber} - Cliente: {$clientName}",
                'amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'payment_methods' => $transaction->payment_methods,
                'transaction_date' => Carbon::now('America/Sao_Paulo'),
                'status' => 'confirmado',
                'notes' => "Reversão de venda cancelada - Pedido #{$orderNumber} - Motivo: {$reason}",
            ]);
        }
    }

    /**
     * Registrar histórico de venda e tracking de status
     * 
     * @param Order $order
     */
    public static function recordSaleHistory(Order $order): void
    {
        try {
            SalesHistory::recordSale($order);
        } catch (\Exception $e) {
            Log::warning('Erro ao registrar histórico de venda', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }

        try {
            OrderStatusTracking::recordEntry($order->id, $order->status_id, Auth::id());
        } catch (\Exception $e) {
            Log::warning('Erro ao registrar tracking de status', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }
    }

    /**
     * Obter loja atual do usuário ou loja principal
     * 
     * @return int|null
     */
    public static function getCurrentStoreId(): ?int
    {
        $user = Auth::user();
        
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            return !empty($storeIds) ? $storeIds[0] : null;
        } elseif ($user->isVendedor()) {
            $userStores = $user->stores()->get();
            if ($userStores->isNotEmpty()) {
                return $userStores->first()->id;
            }
        }
        
        $mainStore = Store::where('is_main', true)->first();
        return $mainStore ? $mainStore->id : null;
    }

    /**
     * Obter status de pedido entregue para PDV
     * 
     * @return Status|null
     */
    public static function getDeliveredStatus(): ?Status
    {
        $status = Status::where('name', 'Entregue')->first();
        if (!$status) {
            $status = Status::orderBy('position', 'desc')->first();
        }
        return $status;
    }

    /**
     * Calcular totais de um carrinho
     * 
     * @param array $cart
     * @return float
     */
    public static function calculateCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $itemTotal = $item['total_price'] ?? ($item['quantity'] * $item['unit_price']);
            $itemDiscount = $item['item_discount'] ?? 0;
            $total += $itemTotal - $itemDiscount;
        }
        return $total;
    }
}
