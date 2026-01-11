<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Status;
use App\Models\CashTransaction;
use App\Models\Store;
use App\Models\ProductOption;
use App\Models\SublimationSize;
use App\Models\PersonalizationPrice;
use App\Helpers\StoreHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderWizardService
{
    public function __construct(
        private readonly ImageProcessor $imageProcessor
    ) {}

    /**
     * Resolve a store_id para o pedido baseado no usuário
     */
    public function resolveStoreId(): ?int
    {
        $user = Auth::user();
        $storeId = null;
        
        // Primeiro, tentar obter loja do tenant do usuário
        if ($user->tenant_id) {
            // Buscar loja principal do tenant do usuário
            $tenantStore = Store::where('tenant_id', $user->tenant_id)
                ->where('is_main', true)
                ->first();
            
            if (!$tenantStore) {
                // Se não tem loja principal, pegar a primeira loja do tenant
                $tenantStore = Store::where('tenant_id', $user->tenant_id)->first();
            }
            
            if ($tenantStore) {
                $storeId = $tenantStore->id;
            }
        }
        
        // Fallbacks para casos específicos de role (se ainda não encontrou)
        if (!$storeId) {
            if ($user->isAdminLoja()) {
                $storeIds = $user->getStoreIds();
                $storeId = !empty($storeIds) ? $storeIds[0] : null;
            } elseif ($user->isVendedor()) {
                // Vendedor: buscar loja associada através da tabela store_user
                $userStores = $user->stores()->get();
                if ($userStores->isNotEmpty()) {
                    $storeId = $userStores->first()->id;
                }
            }
        }
        
        // Se ainda não encontrou (super admin ou usuário sem tenant), usar loja principal
        if (!$storeId) {
            $mainStore = Store::where('is_main', true)
                ->where('tenant_id', $user->tenant_id)
                ->first();
            
            if (!$mainStore && !$user->tenant_id) {
                $mainStore = Store::where('is_main', true)->first();
            }
            
            $storeId = $mainStore ? $mainStore->id : null;
        }
        
        Log::info('Store ID resolvido para pedido', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'tenant_id' => $user->tenant_id,
            'store_id' => $storeId,
        ]);
        
        return $storeId;
    }

    /**
     * Cria ou atualiza o cliente e inicia o pedido
     */
    public function storeClientAndStartOrder(array $validated): Order
    {
        $user = Auth::user();
        $storeId = $this->resolveStoreId();
        
        // Se client_id foi enviado, atualiza o cliente existente
        if (!empty($validated['client_id'])) {
            $client = Client::findOrFail($validated['client_id']);
            $client->update([
                'name' => $validated['name'],
                'phone_primary' => $validated['phone_primary'],
                'phone_secondary' => $validated['phone_secondary'] ?? null,
                'email' => $validated['email'] ?? null,
                'cpf_cnpj' => $validated['cpf_cnpj'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
                'category' => $validated['category'] ?? null,
            ]);
        } else {
            // Cria novo cliente
            $client = Client::create([
                'name' => $validated['name'],
                'phone_primary' => $validated['phone_primary'],
                'phone_secondary' => $validated['phone_secondary'] ?? null,
                'email' => $validated['email'] ?? null,
                'cpf_cnpj' => $validated['cpf_cnpj'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
                'category' => $validated['category'] ?? null,
                'store_id' => $storeId,
                'tenant_id' => $user->tenant_id,
            ]);
        }
        
        // Verificar se existe um pedido draft na sessão
        $existingOrderId = session('current_order_id');
        
        if ($existingOrderId) {
            $order = Order::where('id', $existingOrderId)
                ->where('is_draft', true)
                ->first();
            
            if ($order) {
                // Atualizar o pedido existente com o novo cliente
                $order->update([
                    'client_id' => $client->id,
                    'store_id' => $storeId,
                ]);
                
                return $order;
            }
        }
        
        // Criar novo pedido em draft
        $status = Status::where('tenant_id', $user->tenant_id)
            ->orderBy('position')
            ->first();
        
        $order = Order::create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'store_id' => $storeId,
            'status_id' => $status?->id,
            'order_date' => now(),
            'is_draft' => true,
            'total' => 0,
            'subtotal' => 0,
            'total_items' => 0,
            'tenant_id' => $user->tenant_id,
        ]);
        
        // Salvar na sessão
        session(['current_order_id' => $order->id]);
        session(['current_client_id' => $client->id]);
        
        return $order;
    }

    /**
     * Adiciona um item ao pedido
     */
    public function addItemToOrder(array $validated, ?string $coverImagePath = null): OrderItem
    {
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        // Buscar todas as opções em uma única query
        $allOptionIds = collect([
            $validated['tecido'],
            $validated['cor'],
            $validated['tipo_corte'],
            $validated['gola']
        ]);

        if (!empty($validated['detalhe'])) {
            $allOptionIds->push($validated['detalhe']);
        }
        if (!empty($validated['tipo_tecido'])) {
            $allOptionIds->push($validated['tipo_tecido']);
        }
        
        // Adicionar IDs de personalização
        $allOptionIds = $allOptionIds->merge($validated['personalizacao'])->unique();
        
        // Buscar todas as opções de uma vez
        $options = ProductOption::whereIn('id', $allOptionIds)->get()->keyBy('id');
        
        // Montar os nomes
        $tecidoName = $options->get($validated['tecido'])->name ?? 'N/A';
        $tipoTecidoName = isset($validated['tipo_tecido']) ? ($options->get($validated['tipo_tecido'])->name ?? null) : null;
        $corName = $options->get($validated['cor'])->name ?? 'N/A';
        $tipoCorteName = $options->get($validated['tipo_corte'])->name ?? 'N/A';
        $detalheName = isset($validated['detalhe']) ? ($options->get($validated['detalhe'])->name ?? null) : null;
        $golaName = $options->get($validated['gola'])->name ?? 'N/A';
        
        // Personalização - array de nomes
        $personalizacaoNames = collect($validated['personalizacao'])
            ->map(fn($id) => $options->get($id)->name ?? '')
            ->filter()
            ->join(', ');
        
        // Processar tamanhos
        $tamanhos = $validated['tamanhos'];
        $totalQuantity = array_sum(array_map('intval', $tamanhos));
        
        // Calcular acréscimo por tamanho se aplicável
        $surchargeTotal = 0;
        if (!empty($validated['apply_surcharge'])) {
            $surchargeTotal = $this->calculateSizeSurcharge($tamanhos, $order);
        }
        
        $unitPrice = floatval(str_replace(',', '.', $validated['unit_price']));
        $unitCost = isset($validated['unit_cost']) ? floatval(str_replace(',', '.', $validated['unit_cost'])) : 0;
        
        $itemTotal = ($unitPrice * $totalQuantity) + $surchargeTotal;
        
        // Criar o item
        $item = OrderItem::create([
            'order_id' => $order->id,
            'personalizacao' => $personalizacaoNames,
            'tecido' => $tecidoName,
            'tipo_tecido' => $tipoTecidoName,
            'cor' => $corName,
            'tipo_corte' => $tipoCorteName,
            'detalhe' => $detalheName,
            'gola' => $golaName,
            'tamanhos' => json_encode($tamanhos),
            'quantity' => $totalQuantity,
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
            'total' => $itemTotal,
            'size_surcharge' => $surchargeTotal,
            'art_notes' => $validated['art_notes'] ?? null,
            'cover_image' => $coverImagePath,
        ]);
        
        // Atualizar totais do pedido
        $this->recalculateOrderTotals($order);
        
        return $item;
    }

    /**
     * Recalcula os totais do pedido
     */
    public function recalculateOrderTotals(Order $order): void
    {
        $order->refresh();
        
        $subtotal = $order->items->sum('total');
        $totalItems = $order->items->sum('quantity');
        
        $discount = floatval($order->discount ?? 0);
        $deliveryFee = floatval($order->delivery_fee ?? 0);
        
        $total = $subtotal - $discount + $deliveryFee;
        
        $order->update([
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
            'total' => max(0, $total),
        ]);
    }

    /**
     * Calcula acréscimo por tamanho especial
     */
    private function calculateSizeSurcharge(array $tamanhos, Order $order): float
    {
        $surchargeTotal = 0;
        $user = Auth::user();
        
        $surcharges = \App\Models\SizeSurcharge::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->get()
            ->keyBy('size');
        
        foreach ($tamanhos as $size => $qty) {
            $qty = intval($qty);
            if ($qty > 0 && $surcharges->has(strtoupper($size))) {
                $surchargeTotal += $surcharges->get(strtoupper($size))->amount * $qty;
            }
        }
        
        return $surchargeTotal;
    }

    /**
     * Finaliza o pagamento do pedido
     */
    public function processPayment(array $paymentData, Order $order): Payment
    {
        $user = Auth::user();
        
        // Criar registro de pagamento
        $payment = Payment::create([
            'order_id' => $order->id,
            'total_amount' => $order->total,
            'paid_amount' => $paymentData['paid_amount'] ?? 0,
            'remaining_amount' => $order->total - ($paymentData['paid_amount'] ?? 0),
            'payment_methods' => json_encode($paymentData['payment_methods'] ?? []),
            'payment_date' => now(),
            'notes' => $paymentData['notes'] ?? null,
        ]);
        
        // Registrar transação de caixa se houver pagamento
        if (($paymentData['paid_amount'] ?? 0) > 0) {
            $storeId = $order->store_id ?? $this->resolveStoreId();
            
            CashTransaction::create([
                'store_id' => $storeId,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'entrada',
                'sale_type' => 'pedido',
                'amount' => $paymentData['paid_amount'],
                'payment_method' => $paymentData['payment_methods'][0]['method'] ?? 'dinheiro',
                'description' => "Pagamento do Pedido #{$order->id}",
                'transaction_date' => now(),
                'status' => 'approved',
                'tenant_id' => $user->tenant_id,
            ]);
        }
        
        return $payment;
    }

    /**
     * Finaliza o pedido (remove draft e atualiza dados finais)
     */
    public function finalizeOrder(Order $order, array $finalData): Order
    {
        $order->update([
            'is_draft' => false,
            'delivery_date' => $finalData['delivery_date'] ?? null,
            'notes' => $finalData['notes'] ?? null,
            'discount' => $finalData['discount'] ?? 0,
            'delivery_fee' => $finalData['delivery_fee'] ?? 0,
            'is_event' => $finalData['is_event'] ?? false,
        ]);
        
        // Recalcular totais finais
        $this->recalculateOrderTotals($order);
        
        // Limpar sessão
        session()->forget(['current_order_id', 'current_client_id', 'personalization_type']);
        
        // Criar log
        \App\Models\OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Pedido criado',
        ]);
        
        return $order->fresh();
    }

    /**
     * Deleta um item do pedido
     */
    public function deleteItem(int $itemId): bool
    {
        $item = OrderItem::findOrFail($itemId);
        $order = $item->order;
        
        // Verificar se o pedido ainda é draft
        if (!$order->is_draft) {
            throw new \Exception('Não é possível excluir itens de pedidos finalizados.');
        }
        
        $item->delete();
        
        // Recalcular totais
        $this->recalculateOrderTotals($order);
        
        return true;
    }
}
