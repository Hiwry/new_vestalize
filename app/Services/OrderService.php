<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Status;
use App\Models\Store;
use App\Helpers\DateHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderService
{
    /**
     * Confirmar um pedido (tirar do modo rascunho)
     * 
     * @param Order $order
     * @param array $options Opções adicionais (is_event, terms_version, etc)
     * @return Order
     */
    public static function confirmOrder(Order $order, array $options = []): Order
    {
        $pendenteStatus = Status::where('name', 'Pendente')->first();
        
        // Calcular data de entrega se não existir
        $deliveryDate = $order->delivery_date;
        if (!$deliveryDate) {
            $deliveryDate = DateHelper::calculateDeliveryDate($order->created_at, 15)->format('Y-m-d');
        } else {
            $deliveryDate = Carbon::parse($deliveryDate)->format('Y-m-d');
        }
        
        $updateData = [
            'is_draft' => false,
            'status_id' => $pendenteStatus ? $pendenteStatus->id : $order->status_id,
            'delivery_date' => $deliveryDate,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
            'terms_version' => $options['terms_version'] ?? '1.0',
        ];
        
        // Processar evento
        if (isset($options['is_event']) && $options['is_event']) {
            $updateData['contract_type'] = 'EVENTO';
            $updateData['is_event'] = true;
        } else {
            $updateData['is_event'] = false;
        }
        
        $order->update($updateData);
        
        // Registrar log de confirmação
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Sistema',
            'action' => 'PEDIDO_CONFIRMADO',
            'description' => 'Pedido confirmado e enviado para produção.',
        ]);
        
        return $order->fresh();
    }

    /**
     * Cancelar um pedido
     * 
     * @param Order $order
     * @param string $reason
     * @return Order
     */
    public static function cancelOrder(Order $order, string $reason): Order
    {
        $order->update([
            'is_cancelled' => true,
            'cancelled_at' => Carbon::now('America/Sao_Paulo'),
            'cancellation_reason' => $reason,
        ]);
        
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Sistema',
            'action' => 'PEDIDO_CANCELADO',
            'description' => 'Pedido cancelado. Motivo: ' . $reason,
        ]);
        
        return $order->fresh();
    }

    /**
     * Atualizar status de um pedido
     * 
     * @param Order $order
     * @param Status $newStatus
     * @param string|null $notes
     * @return Order
     */
    public static function updateStatus(Order $order, Status $newStatus, ?string $notes = null): Order
    {
        $oldStatus = $order->status;
        
        $order->update([
            'status_id' => $newStatus->id,
        ]);
        
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Sistema',
            'action' => 'STATUS_ALTERADO',
            'description' => sprintf(
                'Status alterado de "%s" para "%s"' . ($notes ? '. Obs: ' . $notes : ''),
                $oldStatus->name ?? 'N/A',
                $newStatus->name
            ),
        ]);
        
        return $order->fresh();
    }

    /**
     * Recalcular totais de um pedido
     * 
     * @param Order $order
     * @return Order
     */
    public static function recalculateTotals(Order $order): Order
    {
        $subtotal = $order->items()->sum('total_price');
        $totalItems = $order->items()->sum('quantity');
        $discount = $order->discount ?? 0;
        $deliveryFee = $order->delivery_fee ?? 0;
        
        $total = max(0, $subtotal - $discount + $deliveryFee);
        
        $order->update([
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
            'total' => $total,
        ]);
        
        return $order->fresh();
    }

    /**
     * Obter número do pedido formatado
     * 
     * @param Order|int $order
     * @return string
     */
    public static function formatOrderNumber($order): string
    {
        $id = $order instanceof Order ? $order->id : $order;
        return str_pad($id, 6, '0', STR_PAD_LEFT);
    }
}
