<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\OrderEditHistory;
use App\Models\OrderEditRequest;
use App\Models\OrderLog;
use App\Models\OrderSublimation;
use App\Models\OrderSublimationFile;
use App\Models\ProductOption;
use App\Models\PersonalizationPrice;
use App\Models\SublimationLocation;
use App\Models\Payment;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EditOrderService
{
    public function __construct(
        private readonly ImageProcessor $imageProcessor
    ) {}

    /**
     * Iniciar edição de pedido - carrega dados na sessão
     */
    public function startEdit(Order $order): bool
    {
        try {
            // Inicializar sessão com dados básicos
            Session::put('edit_order_id', $order->id);
            Session::put('edit_order_data', [
                'client' => [
                    'id' => $order->client->id,
                    'name' => $order->client->name,
                    'phone_primary' => $order->client->phone_primary,
                    'phone_secondary' => $order->client->phone_secondary,
                    'email' => $order->client->email,
                    'cpf_cnpj' => $order->client->cpf_cnpj,
                    'address' => $order->client->address,
                    'city' => $order->client->city,
                    'state' => $order->client->state,
                    'zip_code' => $order->client->zip_code,
                    'category' => $order->client->category,
                ],
                'items' => $order->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_number' => $item->item_number,
                        'fabric' => $item->fabric,
                        'color' => $item->color,
                        'collar' => $item->collar,
                        'model' => $item->model,
                        'detail' => $item->detail,
                        'print_type' => $item->print_type,
                        'print_desc' => $item->print_desc,
                        'art_name' => $item->art_name,
                        'sizes' => $item->sizes,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'art_notes' => $item->art_notes,
                        'cover_image' => $item->cover_image,
                    ];
                })->toArray(),
                'payment' => [
                    'delivery_date' => $order->delivery_date,
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'delivery_fee' => $order->delivery_fee,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method ?? '',
                    'entry_date' => $order->entry_date ?? $order->created_at->format('Y-m-d'),
                    'notes' => $order->notes,
                ],
                'contract_type' => $order->contract_type,
                'seller' => $order->seller,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error starting edit session', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Obter dados de edição da sessão
     */
    public function getEditData(): array
    {
        return Session::get('edit_order_data', []);
    }

    /**
     * Obter ID do pedido em edição
     */
    public function getEditOrderId(): ?int
    {
        return Session::get('edit_order_id');
    }

    /**
     * Verificar se sessão de edição está ativa
     */
    public function hasActiveSession(): bool
    {
        return Session::has('edit_order_id');
    }

    /**
     * Atualizar dados do cliente na sessão
     */
    public function updateClientData(array $data): void
    {
        $editData = $this->getEditData();
        $editData['client'] = $data;
        Session::put('edit_order_data', $editData);
    }

    /**
     * Obter nome de ProductOption por ID
     */
    public function getProductOptionName($id, ?string $type = null): ?string
    {
        if (!$id || !is_numeric($id)) {
            return $id; // Já é um nome, retornar como está
        }

        $query = ProductOption::where('id', $id);
        if ($type) {
            $query->where('type', $type);
        }
        
        $option = $query->first();
        return $option ? $option->name : null;
    }

    /**
     * Obter nome de personalização
     */
    public function getPersonalizationName($id): ?string
    {
        if (!$id || !is_numeric($id)) {
            return $id;
        }

        $option = ProductOption::where('id', $id)
            ->where('type', 'personalizacao')
            ->first();

        return $option ? $option->name : null;
    }

    /**
     * Converter ID para nome se necessário
     */
    public function convertToName($value, string $type): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return $value; // Já é um nome
        }

        if ($type === 'personalization') {
            return $this->getPersonalizationName($value);
        }

        return $this->getProductOptionName($value, $type);
    }

    /**
     * Criar snapshot do pedido antes da edição
     */
    public function createSnapshotBefore(Order $order): array
    {
        return [
            'order' => [
                'id' => $order->id,
                'seller' => $order->seller,
                'contract_type' => $order->contract_type,
                'delivery_date' => $order->delivery_date,
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'delivery_fee' => $order->delivery_fee,
                'total' => $order->total,
            ],
            'client' => [
                'name' => $order->client->name,
                'phone_primary' => $order->client->phone_primary,
                'email' => $order->client->email,
                'cpf_cnpj' => $order->client->cpf_cnpj,
                'address' => $order->client->address,
                'city' => $order->client->city,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'print_type' => $item->print_type,
                    'quantity' => $item->quantity,
                    'fabric' => $item->fabric,
                    'color' => $item->color,
                    'collar' => $item->collar,
                    'model' => $item->model,
                    'detail' => $item->detail,
                    'sizes' => $item->sizes,
                ];
            })->toArray(),
        ];
    }

    /**
     * Criar snapshot do pedido após a edição
     */
    public function createSnapshotAfter(Order $order): array
    {
        $order->refresh();
        return $this->createSnapshotBefore($order);
    }

    /**
     * Calcular diferenças entre dois snapshots
     */
    public function calculateEditDifferences(array $before, array $after): array
    {
        $differences = [];

        // Comparar dados do pedido
        if (isset($before['order']) && isset($after['order'])) {
            foreach ($before['order'] as $key => $valueBefore) {
                $valueAfter = $after['order'][$key] ?? null;
                if ($valueBefore != $valueAfter) {
                    $differences['order'][$key] = [
                        'before' => $valueBefore,
                        'after' => $valueAfter,
                    ];
                }
            }
        }

        // Comparar dados do cliente
        if (isset($before['client']) && isset($after['client'])) {
            foreach ($before['client'] as $key => $valueBefore) {
                $valueAfter = $after['client'][$key] ?? null;
                if ($valueBefore != $valueAfter) {
                    $differences['client'][$key] = [
                        'before' => $valueBefore,
                        'after' => $valueAfter,
                    ];
                }
            }
        }

        // Comparar itens
        $itemsBefore = collect($before['items'] ?? [])->keyBy('id');
        $itemsAfter = collect($after['items'] ?? [])->keyBy('id');

        foreach ($itemsAfter as $id => $itemAfter) {
            $itemBefore = $itemsBefore->get($id);
            
            if (!$itemBefore) {
                $differences['items']['added'][] = $itemAfter;
            } else {
                $itemDiff = [];
                foreach ($itemAfter as $key => $valueAfter) {
                    $valueBefore = $itemBefore[$key] ?? null;
                    if ($valueBefore != $valueAfter) {
                        $itemDiff[$key] = [
                            'before' => $valueBefore,
                            'after' => $valueAfter,
                        ];
                    }
                }
                if (!empty($itemDiff)) {
                    $differences['items']['modified'][$id] = $itemDiff;
                }
            }
        }

        foreach ($itemsBefore as $id => $itemBefore) {
            if (!$itemsAfter->has($id)) {
                $differences['items']['removed'][] = $itemBefore;
            }
        }

        return $differences;
    }

    /**
     * Resolver loja do usuário para edição
     */
    public function resolveStoreIdForEdit(Order $order): int
    {
        $user = Auth::user();
        $storeId = $order->store_id; // Manter o store_id atual por padrão
        
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            $storeId = !empty($storeIds) ? $storeIds[0] : $storeId;
        } elseif ($user->isVendedor()) {
            $userStores = $user->stores()->get();
            if ($userStores->isNotEmpty()) {
                $storeId = $userStores->first()->id;
            } elseif ($user->store) {
                $store = Store::where('name', 'like', '%' . $user->store . '%')->first();
                if ($store) {
                    $storeId = $store->id;
                }
            }
        }
        
        return $storeId;
    }

    /**
     * Finalizar edição do pedido
     */
    public function finalizeEdit(Order $order, array $editData): Order
    {
        return DB::transaction(function () use ($order, $editData) {
            $snapshotBefore = $this->createSnapshotBefore($order);
            $user = Auth::user();

            // Atualizar cliente
            $order->client->update($editData['client']);

            // Resolver store_id
            $storeId = $this->resolveStoreIdForEdit($order);

            // Determinar delivery_date
            $deliveryDate = $this->resolveDeliveryDate($order, $editData);

            // Atualizar pedido
            $order->update([
                'contract_type' => $editData['contract_type'],
                'seller' => $editData['seller'],
                'store_id' => $storeId,
                'delivery_date' => $deliveryDate,
                'subtotal' => $editData['payment']['subtotal'] ?? $order->subtotal,
                'discount' => $editData['payment']['discount'] ?? 0,
                'delivery_fee' => $editData['payment']['delivery_fee'] ?? 0,
                'total' => $editData['payment']['total'] ?? $order->total,
                'payment_method' => $editData['payment']['payment_method'] ?? null,
                'entry_date' => $editData['payment']['entry_date'] ?? null,
                'notes' => $editData['payment']['notes'] ?? null,
                'is_modified' => true,
            ]);

            // Atualizar itens
            foreach ($editData['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    $item = OrderItem::find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'fabric' => $itemData['fabric'],
                            'color' => $itemData['color'],
                            'collar' => $itemData['collar'],
                            'model' => $itemData['model'],
                            'detail' => $itemData['detail'],
                            'print_type' => $itemData['print_type'],
                            'print_desc' => $itemData['print_desc'] ?? null,
                            'art_name' => $itemData['art_name'],
                            'sizes' => $itemData['sizes'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'total_price' => $itemData['total_price'],
                            'art_notes' => $itemData['art_notes'] ?? null,
                        ]);
                    }
                }
            }

            // Criar snapshot após edição
            $snapshotAfter = $this->createSnapshotAfter($order);
            $differences = $this->calculateEditDifferences($snapshotBefore, $snapshotAfter);

            // Registrar histórico de edição
            OrderEditHistory::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'snapshot_before' => json_encode($snapshotBefore),
                'snapshot_after' => json_encode($snapshotAfter),
                'changes' => json_encode($differences),
            ]);

            // Registrar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'action' => 'edited',
                'description' => 'Pedido editado por ' . $user->name,
            ]);

            // Limpar sessão
            $this->clearSession();

            return $order->fresh();
        });
    }

    /**
     * Resolver data de entrega
     */
    private function resolveDeliveryDate(Order $order, array $editData): string
    {
        if (!empty($editData['payment']['delivery_date'])) {
            return Carbon::parse($editData['payment']['delivery_date'])->format('Y-m-d');
        } elseif ($order->delivery_date) {
            return Carbon::parse($order->delivery_date)->format('Y-m-d');
        } else {
            // Calcular 15 dias úteis
            return \App\Helpers\DateHelper::calculateDeliveryDate(Carbon::now(), 15)->format('Y-m-d');
        }
    }

    /**
     * Salvar personalização
     */
    public function savePersonalization(OrderItem $item, array $data): OrderSublimation
    {
        // Buscar localização
        $locationId = null;
        $locationName = null;
        
        if ($data['personalization_type'] !== 'SUB. TOTAL' && !empty($data['location'])) {
            $location = SublimationLocation::find($data['location']);
            $locationId = $location ? $location->id : null;
            $locationName = $location ? $location->name : $data['location'];
        }

        // Para SUB. TOTAL, usar quantidade total do item
        $quantity = $data['quantity'] ?? 1;
        if ($data['personalization_type'] === 'SUB. TOTAL') {
            $quantity = $item->quantity;
        }

        $artName = $data['art_name'] ?? $item->art_name ?? null;

        // Verificar se está editando
        if (!empty($data['editing_personalization_id'])) {
            $personalization = OrderSublimation::findOrFail($data['editing_personalization_id']);
            $personalization->update([
                'art_name' => $artName,
                'size_name' => $data['personalization_type'] === 'SUB. TOTAL' ? 'CACHARREL' : ($data['size'] ?? null),
                'location_id' => $locationId,
                'location_name' => $locationName,
                'quantity' => $quantity,
                'color_count' => $data['color_count'] ?? 0,
                'unit_price' => $data['unit_price'],
                'final_price' => $data['final_price'],
                'color_details' => $data['color_details'] ?? null,
                'seller_notes' => $data['seller_notes'] ?? null,
            ]);
        } else {
            $personalization = OrderSublimation::create([
                'order_item_id' => $item->id,
                'application_type' => strtolower($data['personalization_type']),
                'art_name' => $artName,
                'size_id' => null,
                'size_name' => $data['personalization_type'] === 'SUB. TOTAL' ? 'CACHARREL' : ($data['size'] ?? null),
                'location_id' => $locationId,
                'location_name' => $locationName,
                'quantity' => $quantity,
                'color_count' => $data['color_count'] ?? 0,
                'has_neon' => false,
                'neon_surcharge' => 0,
                'unit_price' => $data['unit_price'],
                'discount_percent' => 0,
                'final_price' => $data['final_price'],
                'application_image' => null,
                'color_details' => $data['color_details'] ?? null,
                'seller_notes' => $data['seller_notes'] ?? null,
            ]);
        }

        // Atualizar preço total do item
        $this->updateItemTotalPrice($item);

        return $personalization;
    }

    /**
     * Processar arquivos de personalização
     */
    public function processPersonalizationFiles(OrderSublimation $personalization, array $files): void
    {
        foreach ($files as $file) {
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('orders/sublimations', $fileName, 'public');

            OrderSublimationFile::create([
                'order_sublimation_id' => $personalization->id,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Atualizar preço total do item
     */
    public function updateItemTotalPrice(OrderItem $item): void
    {
        $basePrice = $item->unit_price * $item->quantity;
        $totalPersonalizationPrice = $item->sublimations()->sum('final_price');
        $item->update(['total_price' => $basePrice + $totalPersonalizationPrice]);
    }

    /**
     * Limpar sessão de edição
     */
    public function clearSession(): void
    {
        Session::forget([
            'edit_order_id',
            'edit_order_data',
            'size_surcharges',
            'item_personalizations',
        ]);
    }

    /**
     * Verificar se usuário pode editar pedido
     */
    public function canUserEdit(Order $order): bool
    {
        $user = Auth::user();

        // Admin pode editar sempre
        if ($user->isAdmin()) {
            return true;
        }

        // Vendedor precisa de aprovação
        $approvedEditRequest = $order->editRequests()
            ->where('status', 'approved')
            ->first();

        return $approvedEditRequest !== null;
    }

    /**
     * Recalcular subtotal do pedido
     */
    public function recalculateSubtotal(Order $order): float
    {
        $subtotal = 0;
        
        foreach ($order->items as $item) {
            $itemBasePrice = $item->unit_price * $item->quantity;
            $personalizationTotal = $item->sublimations()->sum('final_price');
            $subtotal += ($itemBasePrice + $personalizationTotal);
        }
        
        $order->subtotal = $subtotal;
        $order->save();
        
        return $subtotal;
    }
}
