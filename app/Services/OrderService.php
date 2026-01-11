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
     * Adicionar um pagamento ao pedido
     * 
     * @param Order $order
     * @param array $data (method, amount, notes)
     * @param int $userId
     * @return void
     */
    public static function addPayment(Order $order, array $data, int $userId): void
    {
        DB::transaction(function () use ($order, $data, $userId) {
            $payment = Payment::where('order_id', $order->id)->first();
            $user = \App\Models\User::find($userId);
            
            if ($payment) {
                $newEntryAmount = $payment->entry_amount + $data['amount'];
                $newRemainingAmount = $payment->remaining_amount - $data['amount'];
                
                $paymentMethods = $payment->payment_methods ?? [];
                $paymentMethods[] = [
                    'id' => time() . rand(1000, 9999),
                    'method' => $data['method'],
                    'amount' => $data['amount'],
                    'date' => now()->format('Y-m-d H:i:s'),
                ];
                
                $payment->update([
                    'entry_amount' => $newEntryAmount,
                    'remaining_amount' => $newRemainingAmount,
                    'payment_methods' => $paymentMethods,
                    'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                ]);
            } else {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'method' => $data['method'],
                    'payment_method' => $data['method'],
                    'payment_methods' => [[
                        'id' => time() . rand(1000, 9999),
                        'method' => $data['method'],
                        'amount' => $data['amount'],
                        'date' => now()->format('Y-m-d H:i:s'),
                    ]],
                    'amount' => $order->total,
                    'entry_amount' => $data['amount'],
                    'remaining_amount' => $order->total - $data['amount'],
                    'status' => $data['amount'] >= $order->total ? 'pago' : 'pendente',
                    'entry_date' => now(),
                    'payment_date' => now(),
                ]);
            }

            CashTransaction::create([
                'type' => 'entrada',
                'category' => 'Venda',
                'description' => "Pagamento do Pedido #" . self::formatOrderNumber($order) . " - " . ($order->client->name ?? 'N/A'),
                'amount' => $data['amount'],
                'payment_method' => $data['method'],
                'status' => 'pendente',
                'transaction_date' => now(),
                'order_id' => $order->id,
                'user_id' => $userId,
                'user_name' => $user->name ?? 'N/A',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Atualizar um pagamento do pedido
     */
    public static function updatePayment(Order $order, array $data, ?string $methodId): void
    {
        $payment = Payment::where('order_id', $order->id)->firstOrFail();
        
        if ($methodId && $payment->payment_methods && is_array($payment->payment_methods)) {
            $paymentMethods = $payment->payment_methods;
            $oldAmount = 0;
            
            foreach ($paymentMethods as $key => $method) {
                if ($method['id'] == $methodId) {
                    $oldAmount = $method['amount'];
                    $paymentMethods[$key]['method'] = $data['method'];
                    $paymentMethods[$key]['amount'] = $data['amount'];
                    break;
                }
            }
            
            $amountDifference = $data['amount'] - $oldAmount;
            $newEntryAmount = $payment->entry_amount + $amountDifference;
            $newRemainingAmount = $payment->remaining_amount - $amountDifference;
            
            $payment->update([
                'entry_amount' => $newEntryAmount,
                'remaining_amount' => $newRemainingAmount,
                'payment_methods' => $paymentMethods,
                'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                'notes' => $data['notes'] ?? null,
            ]);
            
            $cashTransaction = CashTransaction::where('order_id', $order->id)
                ->where('type', 'entrada')
                ->where('amount', $oldAmount)
                ->first();

            if ($cashTransaction) {
                $cashTransaction->update([
                    'amount' => $data['amount'],
                    'payment_method' => $data['method'],
                    'notes' => $data['notes'] ?? null,
                ]);
            }
        }
    }

    /**
     * Remover um pagamento ou todos os pagamentos de um pedido
     */
    public static function deletePayment(Order $order, ?string $methodId = null): void
    {
        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) return;

        if ($methodId && $payment->payment_methods && is_array($payment->payment_methods)) {
            $paymentMethods = $payment->payment_methods;
            $removedAmount = 0;
            
            foreach ($paymentMethods as $key => $method) {
                if ($method['id'] == $methodId) {
                    $removedAmount = $method['amount'];
                    unset($paymentMethods[$key]);
                    break;
                }
            }
            
            $paymentMethods = array_values($paymentMethods);
            
            if (!empty($paymentMethods)) {
                $newEntryAmount = $payment->entry_amount - $removedAmount;
                $newRemainingAmount = $payment->remaining_amount + $removedAmount;
                
                $payment->update([
                    'entry_amount' => $newEntryAmount,
                    'remaining_amount' => $newRemainingAmount,
                    'payment_methods' => $paymentMethods,
                    'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                ]);
                
                $cashTransaction = CashTransaction::where('order_id', $order->id)
                    ->where('type', 'entrada')
                    ->where('amount', $removedAmount)
                    ->first();
                
                if ($cashTransaction) {
                    $cashTransaction->delete();
                }
                return;
            }
        }
        
        CashTransaction::where('order_id', $order->id)->where('type', 'entrada')->delete();
        $payment->delete();
    }

    /**
     * Duplicar um pedido existente
     */
    public static function duplicate(Order $originalOrder, int $userId): Order
    {
        return DB::transaction(function () use ($originalOrder, $userId) {
            $initialStatus = Status::orderBy('position')->first();
            $user = \App\Models\User::find($userId);

            $newOrder = Order::create([
                'client_id' => $originalOrder->client_id,
                'user_id' => $userId,
                'status_id' => $initialStatus?->id ?? $originalOrder->status_id,
                'store_id' => $originalOrder->store_id,
                'seller' => $originalOrder->seller,
                'contract_type' => $originalOrder->contract_type,
                'delivery_date' => now()->addDays(15),
                'subtotal' => $originalOrder->subtotal,
                'discount' => $originalOrder->discount,
                'delivery_fee' => $originalOrder->delivery_fee,
                'total' => $originalOrder->total,
                'notes' => $originalOrder->notes,
                'is_draft' => true,
            ]);

            foreach ($originalOrder->items as $item) {
                $newItem = $newOrder->items()->create([
                    'print_type' => $item->print_type,
                    'art_name' => $item->art_name,
                    'quantity' => $item->quantity,
                    'fabric' => $item->fabric,
                    'color' => $item->color,
                    'collar' => $item->collar,
                    'model' => $item->model,
                    'detail' => $item->detail,
                    'sizes' => $item->sizes,
                    'unit_price' => $item->unit_price,
                    'cover_image' => $item->cover_image,
                ]);

                foreach ($item->sublimations as $sub) {
                    $newItem->sublimations()->create([
                        'size_id' => $sub->size_id,
                        'size_name' => $sub->size_name,
                        'location_id' => $sub->location_id,
                        'location_name' => $sub->location_name,
                        'quantity' => $sub->quantity,
                        'unit_price' => $sub->unit_price,
                        'discount_percent' => $sub->discount_percent,
                        'final_price' => $sub->final_price,
                        'application_type' => $sub->application_type,
                        'color_count' => $sub->color_count,
                        'has_neon' => $sub->has_neon,
                    ]);
                }
            }

            OrderLog::create([
                'order_id' => $newOrder->id,
                'user_id' => $userId,
                'user_name' => $user->name ?? 'Sistema',
                'action' => 'order_created',
                'description' => 'Pedido criado por duplicação do pedido #' . self::formatOrderNumber($originalOrder),
            ]);

            return $newOrder;
        });
    }

    /**
     * Preparar array de alterações detalhadas para auditoria
     */
    public static function prepareChanges(Order $order, array $validated): array
    {
        $changes = [];
        $selectedSteps = $validated['selected_steps'] ?? [];

        // Dados do Cliente
        if (in_array('client', $selectedSteps)) {
            $clientChanges = [];
            $client = $order->client;
            
            if ($client) {
                if (($validated['client_name'] ?? null) && $client->name !== $validated['client_name']) {
                    $clientChanges['name'] = ['old' => $client->name, 'new' => $validated['client_name']];
                }
                if (($validated['client_phone_primary'] ?? null) && $client->phone_primary !== $validated['client_phone_primary']) {
                    $clientChanges['phone_primary'] = ['old' => $client->phone_primary, 'new' => $validated['client_phone_primary']];
                }
                if (($validated['client_email'] ?? null) && $client->email !== $validated['client_email']) {
                    $clientChanges['email'] = ['old' => $client->email, 'new' => $validated['client_email']];
                }
                if (($validated['client_cpf_cnpj'] ?? null) && $client->cpf_cnpj !== $validated['client_cpf_cnpj']) {
                    $clientChanges['cpf_cnpj'] = ['old' => $client->cpf_cnpj, 'new' => $validated['client_cpf_cnpj']];
                }
                if (($validated['client_address'] ?? null) && $client->address !== $validated['client_address']) {
                    $clientChanges['address'] = ['old' => $client->address, 'new' => $validated['client_address']];
                }
            }
            
            if (!empty($clientChanges)) {
                $changes['client'] = $clientChanges;
            }
        }

        // Itens do Pedido
        if (in_array('items', $selectedSteps) && isset($validated['items'])) {
            $itemsChanges = [];
            foreach ($validated['items'] as $itemData) {
                $item = $order->items->find($itemData['id']);
                if ($item) {
                    $itemChanges = [];
                    if ($item->print_type !== ($itemData['print_type'] ?? null)) {
                        $itemChanges['print_type'] = ['old' => $item->print_type, 'new' => $itemData['print_type']];
                    }
                    if ($item->art_name !== ($itemData['art_name'] ?? null)) {
                        $itemChanges['art_name'] = ['old' => $item->art_name, 'new' => $itemData['art_name']];
                    }
                    if ($item->quantity != ($itemData['quantity'] ?? null)) {
                        $itemChanges['quantity'] = ['old' => $item->quantity, 'new' => $itemData['quantity']];
                    }
                    if ($item->fabric !== ($itemData['fabric'] ?? null)) {
                        $itemChanges['fabric'] = ['old' => $item->fabric, 'new' => $itemData['fabric']];
                    }
                    if ($item->color !== ($itemData['color'] ?? null)) {
                        $itemChanges['color'] = ['old' => $item->color, 'new' => $itemData['color']];
                    }
                    if ($item->unit_price != ($itemData['unit_price'] ?? null)) {
                        $itemChanges['unit_price'] = ['old' => $item->unit_price, 'new' => $itemData['unit_price']];
                    }
                    
                    if (!empty($itemChanges)) {
                        $itemsChanges[$item->id] = $itemChanges;
                    }
                }
            }
            if (!empty($itemsChanges)) {
                $changes['items'] = $itemsChanges;
            }
        }

        // Outros campos (Personalização / Pagamento / Metadados)
        $metadataFields = [
            'contract_type' => 'Tipo de Contrato',
            'seller' => 'Vendedor',
            'delivery_date' => 'Data de Entrega',
            'subtotal' => 'Subtotal',
            'discount' => 'Desconto',
            'delivery_fee' => 'Taxa de Entrega',
            'total' => 'Total',
            'notes' => 'Observações',
        ];

        foreach ($metadataFields as $field => $label) {
            if (isset($validated[$field]) && $order->$field != $validated[$field]) {
                $oldValue = $order->$field;
                $newValue = $validated[$field];
                
                if (in_array($field, ['subtotal', 'discount', 'delivery_fee', 'total'])) {
                    $oldValue = 'R$ ' . number_format((float)$oldValue, 2, ',', '.');
                    $newValue = 'R$ ' . number_format((float)$newValue, 2, ',', '.');
                }
                
                $changes[$field] = [
                    'label' => $label,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}
