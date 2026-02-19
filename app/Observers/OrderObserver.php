<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Libera estoque reservado quando pedido é cancelado.
     */
    public function updated(Order $order): void
    {
        // Verificar se o pedido foi cancelado
        if ($order->isDirty('is_cancelled') && $order->is_cancelled) {
            $this->releaseReservedStock($order);
        }
    }

    /**
     * Handle the Order "deleted" event.
     * Libera estoque reservado quando pedido é excluído.
     */
    public function deleted(Order $order): void
    {
        $this->releaseReservedStock($order);
    }

    /**
     * Libera todo o estoque reservado para um pedido
     */
    private function releaseReservedStock(Order $order): void
    {
        Log::info(' OrderObserver: Liberando estoque reservado para pedido cancelado/excluído', [
            'order_id' => $order->id,
        ]);

        // Buscar todas as solicitações de estoque do pedido que ainda estão pendentes
        $stockRequests = StockRequest::where('order_id', $order->id)
            ->whereIn('status', ['pendente', 'aprovado', 'em_transferencia'])
            ->get();

        $releasedCount = 0;
        $errors = [];

        foreach ($stockRequests as $stockRequest) {
            try {
                // Buscar o estoque correspondente
                $stock = Stock::findByParams(
                    $stockRequest->target_store_id,
                    $stockRequest->fabric_id,
                    $stockRequest->fabric_type_id,
                    $stockRequest->color_id,
                    $stockRequest->cut_type_id,
                    $stockRequest->size
                );

                if ($stock) {
                    // Quantidade a liberar: se foi aprovado, usa approved_quantity, senão usa requested_quantity
                    $quantityToRelease = $stockRequest->status === 'aprovado' 
                        ? $stockRequest->approved_quantity 
                        : $stockRequest->requested_quantity;

                    // Liberar a reserva
                    $stock->release(
                        $quantityToRelease,
                        auth()->id(),
                        $order->id,
                        $stockRequest->id,
                        "Liberação por cancelamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT)
                    );

                    $releasedCount++;

                    Log::info(' Estoque liberado', [
                        'stock_id' => $stock->id,
                        'stock_request_id' => $stockRequest->id,
                        'quantity_released' => $quantityToRelease,
                        'size' => $stockRequest->size,
                    ]);
                }

                // Atualizar status da solicitação para cancelado
                $stockRequest->update([
                    'status' => 'cancelado',
                    'approval_notes' => ($stockRequest->approval_notes ?? '') . "\n[CANCELADO] Pedido cancelado em " . now()->format('d/m/Y H:i'),
                ]);

            } catch (\Exception $e) {
                $errors[] = [
                    'stock_request_id' => $stockRequest->id,
                    'error' => $e->getMessage(),
                ];
                Log::error('Erro ao liberar estoque', [
                    'stock_request_id' => $stockRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info(' OrderObserver: Liberação de estoque concluída', [
            'order_id' => $order->id,
            'total_requests' => $stockRequests->count(),
            'released' => $releasedCount,
            'errors' => count($errors),
        ]);
    }
}
