<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CashService
{
    /**
     * Registrar transação de entrada no caixa
     * 
     * @param int $storeId
     * @param string $category
     * @param string $description
     * @param float $amount
     * @param string $paymentMethod
     * @param string $status
     * @param int|null $orderId
     * @param string|null $notes
     * @return CashTransaction
     */
    public static function recordEntry(
        int $storeId,
        string $category,
        string $description,
        float $amount,
        string $paymentMethod = 'dinheiro',
        string $status = 'confirmado',
        ?int $orderId = null,
        ?string $notes = null
    ): CashTransaction {
        $user = Auth::user();
        
        return CashTransaction::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'order_id' => $orderId,
            'store_id' => $storeId,
            'type' => 'entrada',
            'category' => $category,
            'description' => $description,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_methods' => [['method' => $paymentMethod, 'amount' => $amount]],
            'transaction_date' => Carbon::now('America/Sao_Paulo'),
            'status' => $status,
            'notes' => $notes,
        ]);
    }

    /**
     * Registrar transação de saída no caixa
     * 
     * @param int $storeId
     * @param string $category
     * @param string $description
     * @param float $amount
     * @param string $paymentMethod
     * @param int|null $orderId
     * @param string|null $notes
     * @return CashTransaction
     */
    public static function recordExit(
        int $storeId,
        string $category,
        string $description,
        float $amount,
        string $paymentMethod = 'dinheiro',
        ?int $orderId = null,
        ?string $notes = null
    ): CashTransaction {
        $user = Auth::user();
        
        return CashTransaction::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'order_id' => $orderId,
            'store_id' => $storeId,
            'type' => 'saida',
            'category' => $category,
            'description' => $description,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_methods' => [['method' => $paymentMethod, 'amount' => $amount]],
            'transaction_date' => Carbon::now('America/Sao_Paulo'),
            'status' => 'confirmado',
            'notes' => $notes,
        ]);
    }

    /**
     * Obter saldo total por loja
     * 
     * @param int $storeId
     * @param string|null $date Filtrar por data (Y-m-d)
     * @return array ['entradas' => float, 'saidas' => float, 'saldo' => float]
     */
    public static function getBalance(int $storeId, ?string $date = null): array
    {
        $query = CashTransaction::where('store_id', $storeId)
            ->where('status', 'confirmado');
        
        if ($date) {
            $query->whereDate('transaction_date', $date);
        }
        
        $transactions = $query->get();
        
        $entradas = $transactions->where('type', 'entrada')->sum('amount');
        $saidas = $transactions->where('type', 'saida')->sum('amount');
        
        return [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $entradas - $saidas,
        ];
    }

    /**
     * Obter resumo de transações por método de pagamento
     * 
     * @param int $storeId
     * @param string|null $date
     * @return array
     */
    public static function getSummaryByPaymentMethod(int $storeId, ?string $date = null): array
    {
        $query = CashTransaction::where('store_id', $storeId)
            ->where('status', 'confirmado')
            ->where('type', 'entrada');
        
        if ($date) {
            $query->whereDate('transaction_date', $date);
        }
        
        return $query->get()
            ->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                ];
            })
            ->toArray();
    }
}
