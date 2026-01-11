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
     * Atualiza um item de costura existente
     */
    public function processUpdateItem(OrderItem $item, array $validated, ?string $coverImagePath = null): void
    {
        $order = $item->order;

        // Buscar opções
        $allOptionIds = collect([
            $validated['tecido'],
            $validated['cor'],
            $validated['tipo_corte'],
            $validated['gola']
        ]);

        if (!empty($validated['detalhe'])) $allOptionIds->push($validated['detalhe']);
        if (!empty($validated['tipo_tecido'])) $allOptionIds->push($validated['tipo_tecido']);
        $allOptionIds = $allOptionIds->merge($validated['personalizacao'])->unique();
        
        $allOptions = \App\Models\ProductOption::whereIn('id', $allOptionIds)->get()->keyBy('id');
        
        $personalizacaoNames = collect($validated['personalizacao'])
            ->map(fn($id) => $allOptions[$id]->name ?? '')
            ->filter()
            ->join(', ');
            
        $tecido = $allOptions[$validated['tecido']];
        $cor = $allOptions[$validated['cor']];
        $tipoCorte = $allOptions[$validated['tipo_corte']];
        $gola = $allOptions[$validated['gola']];
        $detalhe = !empty($validated['detalhe']) ? $allOptions[$validated['detalhe']] : null;
        $tipoTecido = !empty($validated['tipo_tecido']) ? $allOptions[$validated['tipo_tecido']] : null;

        // Preço unitário (se não vier no validated, calcula das opções)
        $unitPrice = $validated['unit_price'] ?? (
            ($tipoCorte->price ?? 0) + ($detalhe->price ?? 0) + ($gola->price ?? 0)
        );

        $item->update([
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'print_type' => $personalizacaoNames,
            'sizes' => $validated['tamanhos'],
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $this->calculateItemTotalPrice($unitPrice, $validated['quantity'], $validated['tamanhos']),
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'print_desc' => json_encode(['apply_surcharge' => (bool)($validated['apply_surcharge'] ?? false)]),
        ]);

        $this->recalculateOrderTotals($order);
    }

    /**
     * Remove um item do pedido e renumera os restantes
     */
    public function processDeleteItem(OrderItem $item): void
    {
        $order = $item->order;
        $item->delete();

        // Renumerar itens
        $items = $order->items()->orderBy('id')->get();
        foreach ($items as $index => $it) {
            $it->update(['item_number' => $index + 1]);
        }

        $this->recalculateOrderTotals($order);
    }

    /**
     * Remove uma personalização e recalcula os totais
     */
    public function processDeletePersonalization(int $personalizationId): void
    {
        $personalization = \App\Models\OrderSublimation::findOrFail($personalizationId);
        $item = $personalization->item;
        $order = $item->order;
        
        $personalization->delete();
        
        // Recalcular total do item
        $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)->sum('final_price');
        $item->update([
            'total_price' => ($item->unit_price * $item->quantity) + $totalPersonalizations
        ]);
        
        $this->recalculateOrderTotals($order);
    }

    /**
     * Processa e salva os dados de pagamento do pedido no wizard
     */
    public function processSavePayment(Order $order, array $validated, ?string $orderCoverImagePath = null): array
    {
        return DB::transaction(function () use ($order, $validated, $orderCoverImagePath) {
            $subtotal = $order->subtotal;
            $delivery = (float)($validated['delivery_fee'] ?? 0);
            
            // Recalcular acréscimos de tamanho no backend
            $sizeSurcharges = [];
            $largeSizes = ['GG', 'EXG', 'G1', 'G2', 'G3'];
            $sizeQuantities = [];
            
            foreach ($order->items as $item) {
                $model = strtoupper($item->model ?? '');
                $detail = strtoupper($item->detail ?? '');
                $isRestricted = str_contains($model, 'INFANTIL') || str_contains($model, 'BABY LOOK') || 
                                str_contains($detail, 'INFANTIL') || str_contains($detail, 'BABY LOOK');
                
                $printDesc = is_string($item->print_desc) ? json_decode($item->print_desc, true) : $item->print_desc;
                $applySurcharge = filter_var($printDesc['apply_surcharge'] ?? false, FILTER_VALIDATE_BOOLEAN);
                
                if ($isRestricted && !$applySurcharge) continue;
                
                $sizes = is_string($item->sizes) ? json_decode($item->sizes, true) : $item->sizes;
                if (is_array($sizes)) {
                    foreach ($sizes as $size => $qty) {
                        if (in_array($size, $largeSizes)) {
                            $sizeQuantities[$size] = ($sizeQuantities[$size] ?? 0) + (int)$qty;
                        }
                    }
                }
            }
            
            foreach ($sizeQuantities as $size => $qty) {
                if ($qty > 0) {
                    $surchargeModel = \App\Models\SizeSurcharge::getSurchargeForSize($size, $subtotal);
                    if ($surchargeModel) {
                        $sizeSurcharges[$size] = (float)$surchargeModel->surcharge * $qty;
                    }
                }
            }
            
            $totalSurcharges = array_sum($sizeSurcharges);
            $subtotalWithFees = $subtotal + $totalSurcharges + $delivery;
            
            // Lógica de Desconto
            $discountType = $validated['discount_type'] ?? 'none';
            $discountValue = (float)($validated['discount_value'] ?? 0);
            $discountAmount = 0.0;
            if ($discountType === 'percentage') {
                $discountValue = max(0, min(100, $discountValue));
                $discountAmount = ($subtotalWithFees * $discountValue) / 100.0;
            } elseif ($discountType === 'fixed') {
                $discountAmount = min($discountValue, $subtotalWithFees);
            }
            
            $total = max(0, $subtotalWithFees - $discountAmount);
            
            $order->update([
                'delivery_fee' => $delivery,
                'discount' => $discountAmount,
                'total' => $total,
                'cover_image' => $orderCoverImagePath,
            ]);
            
            // Pagamentos
            $paymentMethods = json_decode($validated['payment_methods'], true);
            $totalPaid = array_sum(array_column($paymentMethods, 'amount'));
            
            Payment::where('order_id', $order->id)->delete();
            CashTransaction::where('order_id', $order->id)->delete();
            
            $primaryMethod = count($paymentMethods) === 1 ? $paymentMethods[0]['method'] : 'pix';
            
            Payment::create([
                'order_id' => $order->id,
                'method' => $primaryMethod,
                'payment_method' => count($paymentMethods) > 1 ? 'multiplo' : $primaryMethod,
                'payment_methods' => $paymentMethods,
                'amount' => $total,
                'entry_amount' => $totalPaid,
                'remaining_amount' => max(0, $total - $totalPaid),
                'entry_date' => $validated['entry_date'],
                'payment_date' => $validated['entry_date'],
                'status' => $totalPaid >= $total ? 'pago' : 'pendente',
            ]);
            
            $user = \App\Models\User::find(Auth::id());
            foreach ($paymentMethods as $method) {
                CashTransaction::create([
                    'store_id' => $order->store_id,
                    'type' => 'entrada',
                    'category' => 'Venda',
                    'description' => "Pagamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . ($order->client->name ?? 'N/A'),
                    'amount' => $method['amount'],
                    'payment_method' => $method['method'],
                    'status' => 'pendente',
                    'transaction_date' => $validated['entry_date'],
                    'order_id' => $order->id,
                    'user_id' => $user->id ?? null,
                    'user_name' => $user->name ?? 'Sistema',
                    'notes' => count($paymentMethods) > 1 ? 'Pagamento parcial (múltiplas formas)' : null,
                ]);
            }
            
            return $sizeSurcharges;
        });
    }

    /**
     * Salva a arte geral de um item (nome e arquivos)
     */
    public function processSaveOrderArt(OrderItem $item, array $validated, array $files = []): void
    {
        $item->update([
            'art_name' => filled($validated['order_art_name'] ?? null)
                ? trim($validated['order_art_name'])
                : null,
        ]);

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . uniqid() . '_' . $originalName;
            $filePath = $file->storeAs('orders/art_files', $fileName, 'public');

            \App\Models\OrderFile::create([
                'order_item_id' => $item->id,
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Salva uma personalização específica (sublimação) para um item
     */
    public function processSavePersonalization(OrderItem $item, array $validated, ?string $applicationImagePath = null, array $artFiles = []): void
    {
        DB::transaction(function () use ($item, $validated, $applicationImagePath, $artFiles) {
            // Buscar localização
            $locationId = null;
            $locationName = null;
            if ($validated['personalization_type'] !== 'SUB. TOTAL' && isset($validated['location']) && $validated['location']) {
                $location = \App\Models\SublimationLocation::find($validated['location']);
                $locationId = $location ? $location->id : null;
                $locationName = $location ? $location->name : $validated['location'];
            }

            // Quantidade
            $quantity = $validated['quantity'] ?? 1;
            if ($validated['personalization_type'] === 'SUB. TOTAL') {
                $quantity = $item->quantity;
            }

            // Criar a personalização
            $personalization = \App\Models\OrderSublimation::create([
                'order_item_id' => $item->id,
                'application_type' => strtolower($validated['personalization_type']),
                'art_name' => $validated['art_name'] ?? null,
                'size_id' => null,
                'size_name' => $validated['personalization_type'] === 'SUB. TOTAL' ? 'CACHARREL' : ($validated['size'] ?? null),
                'location_id' => $locationId,
                'location_name' => $locationName,
                'quantity' => $quantity,
                'color_count' => $validated['color_count'] ?? 0,
                'has_neon' => false,
                'neon_surcharge' => 0,
                'unit_price' => $validated['unit_price'] ?? 0,
                'discount_percent' => 0,
                'final_price' => $validated['final_price'] ?? 0,
                'application_image' => $applicationImagePath,
                'color_details' => $validated['color_details'] ?? null,
                'seller_notes' => $validated['seller_notes'] ?? null,
            ]);

            // Adicionais se SUB. TOTAL
            if ($validated['personalization_type'] === 'SUB. TOTAL' && isset($validated['addons'])) {
                $addons = $validated['addons'] ?? [];
                $regataDiscount = (bool)($validated['regata_discount'] ?? false);
                
                if ($regataDiscount) $addons[] = 'REGATA_DISCOUNT';
                
                $personalization->update([
                    'addons' => json_encode($addons),
                    'regata_discount' => $regataDiscount,
                ]);
            }

            // Arquivos de arte
            foreach ($artFiles as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('orders/art_files', $fileName, 'public');
                
                \App\Models\OrderSublimationFile::create([
                    'order_sublimation_id' => $personalization->id,
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Aplicar descontos automáticos
            \App\Helpers\PersonalizationDiscountHelper::applyDiscounts($item->id);

            // Recalcular totais do item e pedido
            $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)->sum('final_price');
            $item->update([
                'total_price' => ($item->unit_price * $item->quantity) + $totalPersonalizations
            ]);

            $this->recalculateOrderTotals($item->order);
        });
    }

    /**
     * Inicia um novo pedido no wizard (cria/atualiza cliente e rascunho de pedido)
     */
    public function processStartOrder(array $validated, \App\Models\User $user): Order
    {
        return DB::transaction(function () use ($validated, $user) {
            $storeId = $this->resolveStoreId($user);
            
            // Cliente
            if (!empty($validated['client_id'])) {
                $client = \App\Models\Client::findOrFail($validated['client_id']);
                $client->update($validated);
            } else {
                $validated['store_id'] = $storeId;
                $client = \App\Models\Client::create($validated);
            }

            $status = \App\Models\Status::orderBy('position')->first();
            $deliveryDate = \App\Helpers\DateHelper::calculateDeliveryDate(Carbon::now(), 15);

            $order = Order::create([
                'tenant_id' => $user->tenant_id,
                'client_id' => $client->id,
                'user_id' => $user->id,
                'store_id' => $storeId,
                'status_id' => $status?->id ?? \App\Models\Status::withoutGlobalScopes()->orderBy('id')->first()?->id,
                'order_date' => now()->toDateString(),
                'delivery_date' => $deliveryDate->toDateString(),
                'is_draft' => true,
            ]);

            return $order;
        });
    }

    /**
     * Resolve o store_id baseado no usuário
     */
    private function resolveStoreId(\App\Models\User $user): ?int
    {
        // Primeiro, tentar obter loja do tenant do usuário
        if ($user->tenant_id) {
            $tenantStore = \App\Models\Store::where('tenant_id', $user->tenant_id)
                ->where('is_main', true)
                ->first() ?: \App\Models\Store::where('tenant_id', $user->tenant_id)
                ->first();
            
            if ($tenantStore) return $tenantStore->id;
        }
        
        // Vendedor ou Admin de Loja
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            if (!empty($storeIds)) return $storeIds[0];
        } elseif ($user->isVendedor()) {
            $userStore = $user->stores()->first();
            if ($userStore) return $userStore->id;
        }
        
        // Fallback para loja principal
        $mainStore = \App\Models\Store::where('is_main', true)
            ->where('tenant_id', $user->tenant_id)
            ->first() ?: (\App\Models\Store::where('is_main', true)->whereNull('tenant_id')->first());
        
        return $mainStore ? $mainStore->id : null;
    }

    /**
     * Adiciona um item de costura padrão ao pedido
     */
    public function processAddItem(Order $order, array $validated, ?string $coverImagePath = null): OrderItem
    {
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
        
        $allOptionIds = $allOptionIds->merge($validated['personalizacao'])->unique();
        $allOptions = \App\Models\ProductOption::whereIn('id', $allOptionIds)->get()->keyBy('id');
        
        // Construir string de personalizações
        $personalizacaoNames = collect($validated['personalizacao'])
            ->map(fn($id) => $allOptions[$id]->name ?? '')
            ->filter()
            ->join(', ');
            
        $tecido = $allOptions[$validated['tecido']];
        $cor = $allOptions[$validated['cor']];
        $tipoCorte = $allOptions[$validated['tipo_corte']];
        $gola = $allOptions[$validated['gola']];
        $detalhe = !empty($validated['detalhe']) ? $allOptions[$validated['detalhe']] : null;
        $tipoTecido = !empty($validated['tipo_tecido']) ? $allOptions[$validated['tipo_tecido']] : null;

        $itemNumber = $order->items()->count() + 1;

        $item = new OrderItem([
            'item_number' => $itemNumber,
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'print_type' => $personalizacaoNames,
            'sizes' => $validated['tamanhos'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $this->calculateItemTotalPrice($validated['unit_price'], $validated['quantity'], $validated['tamanhos']),
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'print_desc' => json_encode(['apply_surcharge' => (bool)($validated['apply_surcharge'] ?? false)]),
        ]);

        $order->items()->save($item);
        $this->recalculateOrderTotals($order);

        // Salvar IDs de personalização vinculadas a este item na sessão (mantido no controller por lidar com session())
        
        return $item;
    }

    /**
     * Adiciona um item de sublimação total ao pedido
     */
    public function processAddSublimationItem(Order $order, array $validated, ?string $coverImagePath = null, ?string $corelFilePath = null): OrderItem
    {
        // Buscar tipo para label e tecido padrão
        $typeModel = \App\Models\SublimationProductType::with('tecido')->where('slug', $validated['sublimation_type'])->first();
        $typeLabel = $typeModel ? $typeModel->name : $validated['sublimation_type'];
        $fabricName = ($typeModel && $typeModel->tecido) ? $typeModel->tecido->name : ('SUB. TOTAL - ' . $typeLabel);

        // Buscar adicionais selecionados para descrição
        $addonsLabel = '';
        if (!empty($validated['sublimation_addons'])) {
            $addons = \App\Models\SublimationProductAddon::whereIn('id', $validated['sublimation_addons'])->pluck('name');
            $addonsLabel = $addons->join(', ');
        }

        $itemNumber = $order->items()->count() + 1;

        $item = new OrderItem([
            'item_number' => $itemNumber,
            'fabric' => $fabricName,
            'color' => 'Sublimação Total',
            'collar' => $addonsLabel ?: 'Nenhum adicional',
            'model' => $typeLabel,
            'detail' => null,
            'print_type' => 'SUB. TOTAL',
            'art_name' => $validated['art_name'],
            'sizes' => $validated['tamanhos'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $this->calculateItemTotalPrice($validated['unit_price'], $validated['quantity'], $validated['tamanhos']),
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'print_desc' => json_encode([
                'is_sublimation_total' => true, 
                'type' => $validated['sublimation_type'],
                'corel_file' => $corelFilePath,
                'addons' => $validated['sublimation_addons'] ?? []
            ]),
        ]);

        $order->items()->save($item);
        $this->recalculateOrderTotals($order);

        return $item;
    }

    /**
     * Adiciona itens de sublimação local (produtos prontos) ao pedido
     */
    public function processSaveSubLocalItems(Order $order, array $itemsData): void
    {
        foreach ($itemsData as $itemData) {
            $itemNumber = $order->items()->count() + 1;
            
            $product = \App\Models\SubLocalProduct::find($itemData['id']);
            $unitCost = $product->cost ?? 0;
            
            $item = new OrderItem([
                'item_number' => $itemNumber,
                'fabric' => 'Produto Pronto',
                'color' => '-', 
                'collar' => '-',
                'model' => $itemData['name'],
                'detail' => null,
                'print_type' => 'Sublimação Local',
                'sizes' => ['UN' => $itemData['quantity']],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['price'],
                'total_price' => $itemData['price'] * $itemData['quantity'],
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost * $itemData['quantity'],
                'print_desc' => json_encode(['is_sub_local' => true, 'product_id' => $itemData['id']]),
            ]);
            
            $order->items()->save($item);
        }

        $this->recalculateOrderTotals($order);
    }

    /**
     * Calcula o preço total do item considerando acréscimos por tamanho
     */
    private function calculateItemTotalPrice(float $basePrice, int $totalQuantity, array $tamanhos): float
    {
        $totalSurcharge = 0;
        foreach ($tamanhos as $size => $quantity) {
            if ($quantity > 0) {
                $surchargeModel = \App\Models\SizeSurcharge::getSurchargeForSize($size, $basePrice);
                if ($surchargeModel) {
                    $totalSurcharge += $surchargeModel->surcharge * $quantity;
                }
            }
        }
        return ($basePrice * $totalQuantity) + $totalSurcharge;
    }

    /**
     * Finaliza o pedido com toda a lógica de negócio (termos, logs, estoque)
     */
    public function processFinalizeOrder(Order $order, array $data, int $userId): Order
    {
        return DB::transaction(function () use ($order, $data, $userId) {
            // Termos e Condições
            $activeTerms = \App\Models\TermsCondition::getActiveForOrder($order);
            $termsVersion = '1.0';
            if ($activeTerms->isNotEmpty()) {
                $termsVersion = $activeTerms->first()->version ?? '1.0';
            } else {
                $generalTerms = \App\Models\TermsCondition::getActive();
                if ($generalTerms) {
                    $termsVersion = $generalTerms->version ?? '1.0';
                }
            }
            
            // Status Pendente
            $pendenteStatus = Status::where('name', 'Pendente')->first();
            
            // Data de Entrega
            $deliveryDate = $order->delivery_date;
            if (!$deliveryDate) {
                $deliveryDate = DateHelper::calculateDeliveryDate($order->created_at, 15)->format('Y-m-d');
            } else {
                $deliveryDate = Carbon::parse($deliveryDate)->format('Y-m-d');
            }
            
            // Dados de Atualização
            $updateData = [
                'is_draft' => false,
                'status_id' => $pendenteStatus ? $pendenteStatus->id : $order->status_id,
                'delivery_date' => $deliveryDate,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'terms_version' => $termsVersion,
                'is_event' => $data['is_event'] ?? false,
            ];
            
            if ($updateData['is_event']) {
                $updateData['contract_type'] = 'EVENTO';
            }

            // Atualizar Itens (Nome da Arte e Imagem de Capa se fornecidos)
            // Nota: Upload da imagem deve ser feito no controller, aqui apenas salvamos o path
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemId => $itemData) {
                    $item = $order->items()->find($itemId);
                    if ($item) {
                        $itemUpdate = [];
                        if (isset($itemData['art_name'])) $itemUpdate['art_name'] = $itemData['art_name'];
                        if (isset($itemData['cover_image_path'])) $itemUpdate['cover_image'] = $itemData['cover_image_path'];
                        
                        if (!empty($itemUpdate)) {
                            $item->update($itemUpdate);
                        }
                    }
                }
            }
            
            $order->update($updateData);
            
            // Registrar históricos e logs
            \App\Models\SalesHistory::recordSale($order);
            \App\Models\OrderStatusTracking::recordEntry($order->id, $order->status_id, $userId);
            
            $user = \App\Models\User::find($userId);
            \App\Models\OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'user_name' => $user->name ?? 'Sistema',
                'action' => 'PEDIDO_CONFIRMADO',
                'description' => 'Pedido confirmado e enviado para produção.',
            ]);
            
            // Verificar Estoque
            try {
                $stockResult = \App\Services\StockService::checkAndReserveForOrder($order);
                $order->update(['stock_status' => $stockResult['status']]);
                
                \App\Models\OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'user_name' => $user->name ?? 'Sistema',
                    'action' => 'ESTOQUE_VERIFICADO',
                    'description' => 'Estoque verificado: ' . strtoupper($stockResult['status']) . 
                                   '. Solicitações criadas: ' . $stockResult['requests_created'],
                ]);
            } catch (\Exception $e) {
                Log::warning('Erro ao verificar estoque na finalização do pedido', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id,
                ]);
                $order->update(['stock_status' => 'pending']);
            }
            
            return $order->fresh();
        });
    }

    /**
     * Recalcula os totais do pedido
     */
    public function recalculateOrderTotals(Order $order): void
    {
        $order->refresh();
        
        $subtotal = $order->items->sum('total_price');
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
