<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Criar notificação de solicitação de edição de orçamento
     */
    public static function createBudgetEditRequest($userId, $budgetId, $budgetNumber, $requesterName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'budget_edit_request',
            'title' => 'Solicitação de Edição de Orçamento',
            'message' => "{$requesterName} solicitou edição no orçamento {$budgetNumber}",
            'link' => route('budget.show', $budgetId),
            'data' => [
                'budget_id' => $budgetId,
                'budget_number' => $budgetNumber,
                'requester_name' => $requesterName,
            ],
        ]);
    }

    /**
     * Marcar como lida
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Criar notificação de pedido movido
     */
    public static function createOrderMoved($userId, $orderId, $orderNumber, $fromStatus, $toStatus)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'order_moved',
            'title' => 'Pedido Movido',
            'message' => "O pedido #{$orderNumber} foi movido de '{$fromStatus}' para '{$toStatus}'",
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
            ],
        ]);
    }

    /**
     * Criar notificação de orçamento com 1 semana
     */
    public static function createBudgetWeekOld($userId, $budgetId, $budgetNumber, $clientName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'budget_week_old',
            'title' => 'Orçamento Pendente',
            'message' => "O orçamento {$budgetNumber} para {$clientName} está há 1 semana sem resposta",
            'link' => route('budget.show', $budgetId),
            'data' => [
                'budget_id' => $budgetId,
                'budget_number' => $budgetNumber,
                'client_name' => $clientName,
            ],
        ]);
    }

    /**
     * Criar notificação de solicitação de edição
     */
    public static function createEditRequest($userId, $orderId, $orderNumber, $requesterName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'edit_request',
            'title' => 'Solicitação de Edição',
            'message' => "{$requesterName} solicitou uma edição no pedido #{$orderNumber}",
            'link' => route('admin.edit-requests.index'),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'requester_name' => $requesterName,
            ],
        ]);
    }

    /**
     * Criar notificação de edição aprovada
     */
    public static function createEditApproved($userId, $orderId, $orderNumber, $approverName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'edit_approved',
            'title' => 'Edição Aprovada',
            'message' => "{$approverName} aprovou sua solicitação de edição para o pedido #{$orderNumber}",
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'approver_name' => $approverName,
            ],
        ]);
    }

    /**
     * Criar notificação de edição rejeitada
     */
    public static function createEditRejected($userId, $orderId, $orderNumber, $reviewerName, $reason = null)
    {
        $message = "{$reviewerName} rejeitou sua solicitação de edição para o pedido #{$orderNumber}";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return self::create([
            'user_id' => $userId,
            'type' => 'edit_rejected',
            'title' => 'Edição Rejeitada',
            'message' => $message,
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'reviewer_name' => $reviewerName,
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Criar notificação de solicitação de cancelamento
     */
    public static function createCancellationRequest($userId, $orderId, $orderNumber, $requesterName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'cancellation_request',
            'title' => 'Solicitação de Cancelamento',
            'message' => "{$requesterName} solicitou o cancelamento do pedido #{$orderNumber}",
            'link' => route('admin.cancellations.index'),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'requester_name' => $requesterName,
            ],
        ]);
    }

    /**
     * Criar notificação de cancelamento aprovado
     */
    public static function createCancellationApproved($userId, $orderId, $orderNumber, $approverName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'cancellation_approved',
            'title' => 'Cancelamento Aprovado',
            'message' => "{$approverName} aprovou o cancelamento do pedido #{$orderNumber}",
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'approver_name' => $approverName,
            ],
        ]);
    }

    /**
     * Criar notificação de cancelamento rejeitado
     */
    public static function createCancellationRejected($userId, $orderId, $orderNumber, $reviewerName, $reason = null)
    {
        $message = "{$reviewerName} rejeitou o cancelamento do pedido #{$orderNumber}";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return self::create([
            'user_id' => $userId,
            'type' => 'cancellation_rejected',
            'title' => 'Cancelamento Rejeitado',
            'message' => $message,
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'reviewer_name' => $reviewerName,
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Criar notificação de orçamento aprovado
     */
    public static function createBudgetApproved($userId, $budgetId, $budgetNumber, $approverName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'budget_approved',
            'title' => 'Orçamento Aprovado',
            'message' => "{$approverName} aprovou o orçamento {$budgetNumber}",
            'link' => route('budget.show', $budgetId),
            'data' => [
                'budget_id' => $budgetId,
                'budget_number' => $budgetNumber,
                'approver_name' => $approverName,
            ],
        ]);
    }

    /**
     * Criar notificação de antecipação de entrega solicitada
     */
    public static function createDeliveryRequestCreated($userId, $orderId, $orderNumber, $requesterName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'delivery_request',
            'title' => 'Solicitação de Antecipação',
            'message' => "{$requesterName} solicitou antecipação de entrega para o pedido #{$orderNumber}",
            'link' => route('delivery-requests.index'),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'requester_name' => $requesterName,
            ],
        ]);
    }

    /**
     * Criar notificação de antecipação aprovada
     */
    public static function createDeliveryRequestApproved($userId, $orderId, $orderNumber, $newDate, $approverName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'delivery_approved',
            'title' => 'Antecipação Aprovada',
            'message' => "{$approverName} aprovou a antecipação de entrega para {$newDate} no pedido #{$orderNumber}",
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'new_date' => $newDate,
                'approver_name' => $approverName,
            ],
        ]);
    }

    /**
     * Criar notificação de solicitação de estoque criada
     */
    public static function createStockRequestCreated($userId, $stockRequestId, $requestingStoreName, $productInfo)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'stock_request_created',
            'title' => 'Nova Solicitação de Estoque',
            'message' => "Nova solicitação de estoque de {$productInfo} da loja {$requestingStoreName}",
            'link' => route('stock-requests.index', ['id' => $stockRequestId]),
            'data' => [
                'stock_request_id' => $stockRequestId,
                'requesting_store_name' => $requestingStoreName,
                'product_info' => $productInfo,
            ],
        ]);
    }

    /**
     * Criar notificação de solicitação de estoque aprovada
     */
    public static function createStockRequestApproved($userId, $stockRequestId, $approverName, $productInfo, $approvedQuantity)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'stock_request_approved',
            'title' => 'Solicitação de Estoque Aprovada',
            'message' => "{$approverName} aprovou sua solicitação de estoque: {$productInfo} (Quantidade: {$approvedQuantity})",
            'link' => route('stock-requests.index', ['id' => $stockRequestId]),
            'data' => [
                'stock_request_id' => $stockRequestId,
                'approver_name' => $approverName,
                'product_info' => $productInfo,
                'approved_quantity' => $approvedQuantity,
            ],
        ]);
    }

    /**
     * Criar notificação de estoque baixo
     */
    public static function createLowStock($userId, $storeName, $productInfo, $size, $availableQuantity)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'low_stock',
            'title' => 'Estoque Baixo',
            'message' => "Estoque baixo: {$productInfo} - Tamanho {$size} na loja {$storeName}. Disponível: {$availableQuantity} peças",
            'link' => route('stocks.index'),
            'data' => [
                'store_name' => $storeName,
                'product_info' => $productInfo,
                'size' => $size,
                'available_quantity' => $availableQuantity,
            ],
        ]);
    }

    /**
     * Criar notificação de antecipação rejeitada
     */
    public static function createDeliveryRequestRejected($userId, $orderId, $orderNumber, $reviewerName, $reason = null)
    {
        $message = "{$reviewerName} rejeitou a antecipação de entrega do pedido #{$orderNumber}";
        if ($reason) {
            $message .= ": {$reason}";
        }

        return self::create([
            'user_id' => $userId,
            'type' => 'delivery_rejected',
            'title' => 'Antecipação Rejeitada',
            'message' => $message,
            'link' => route('orders.show', $orderId),
            'data' => [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'reviewer_name' => $reviewerName,
                'reason' => $reason,
            ],
        ]);
    }
}
