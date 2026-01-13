<?php

namespace App\Services;

use App\Models\SalesHistory;
use App\Models\OrderStatusTracking;
use App\Models\StockMovement;
use App\Models\FabricPiece;
use App\Models\SewingMachine;
use App\Models\ProductionSupply;
use App\Models\Uniform;
use App\Models\OrderSublimation;
use App\Models\Store;
use App\Models\SizeSurcharge;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status;
use App\Models\Client;
use App\Models\CashTransaction;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PDVService
{
    /**
     * Obter ID da loja atual do usuário
     */
    public function getCurrentStoreId(): ?int
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
     * Obter carrinho da sessão
     */
    public function getCart(): array
    {
        return Session::get('pdv_cart', []);
    }

    /**
     * Salvar carrinho na sessão
     */
    public function saveCart(array $cart): void
    {
        Session::put('pdv_cart', $cart);
    }

    /**
     * Limpar carrinho da sessão
     */
    public function clearCart(): void
    {
        Session::forget('pdv_cart');
    }

    /**
     * Calcular total do carrinho
     */
    public function calculateCartTotal(array $cart): float
    {
        return collect($cart)->sum(function ($item) {
            return $item['total_price'] ?? 0;
        });
    }

    /**
     * Criar item do tipo producto para o carrinho
     */
    public function createProductCartItem(Product $product, array $data): array
    {
        $unitPrice = $data['unit_price'] ?? $product->price ?? 0;
        $quantity = $data['quantity'];
        $baseTotal = $unitPrice * $quantity;

        // Surcharges para tamanhos especiais
        $sizeQuantities = $data['size_quantities'] ?? [];
        $sizeSurcharges = [];
        $totalSurcharges = 0;

        if (!empty($sizeQuantities) && Schema::hasTable('size_surcharges')) {
            foreach (['GG', 'EXG', 'G1', 'G2', 'G3'] as $size) {
                $qty = $sizeQuantities[$size] ?? 0;
                if ($qty > 0 && $unitPrice > 0) {
                    $surchargeData = SizeSurcharge::getSurchargeForSize($size, $unitPrice);
                    if ($surchargeData) {
                        $total = $surchargeData->surcharge * $qty;
                        $sizeSurcharges[$size] = [
                            'quantity' => $qty,
                            'surcharge_per_unit' => $surchargeData->surcharge,
                            'total' => $total
                        ];
                        $totalSurcharges += $total;
                    }
                }
            }
        }

        return [
            'id' => uniqid(),
            'type' => 'product',
            'product_id' => $product->id,
            'item_id' => $product->id,
            'product_title' => $product->title,
            'category' => $product->category?->name,
            'subcategory' => $product->subcategory?->name,
            'sale_type' => $product->sale_type ?? 'unidade',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $baseTotal + $totalSurcharges,
            'base_price' => $baseTotal,
            'size_surcharges' => $sizeSurcharges,
            'total_surcharges' => $totalSurcharges,
            'application_type' => $data['application_type'] ?? null,
        ];
    }

    /**
     * Criar item do tipo product_option (tipo de corte) para o carrinho
     */
    public function createProductOptionCartItem(ProductOption $option, array $data): array
    {
        $fabricName = 'Tipo de Corte';
        $fabricId = $data['fabric_id'] ?? null;
        
        if ($fabricId && $fabric = ProductOption::find($fabricId)) {
            $fabricName = $fabric->name;
        }

        $unitPrice = $option->price ?? 0;
        $quantity = $data['quantity'];
        $baseTotal = $unitPrice * $quantity;

        // Surcharges
        $sizeSurcharges = [];
        $totalSurcharges = 0;

        // Sublocal personalizations
        $sublocalPersonalizations = [];
        $sublocalTotal = 0;
        if (isset($data['sublocal_personalizations']) && is_array($data['sublocal_personalizations'])) {
            foreach ($data['sublocal_personalizations'] as $p) {
                if (!empty($p['location_id']) || !empty($p['location_name'])) {
                    $pFinal = $p['final_price'] ?? ($p['unit_price'] ?? 0) * ($p['quantity'] ?? 1);
                    $sublocalPersonalizations[] = $p;
                    $sublocalTotal += $pFinal;
                }
            }
        }

        return [
            'id' => uniqid(),
            'type' => 'product_option',
            'product_option_id' => $option->id,
            'item_id' => $option->id,
            'product_title' => $option->name,
            'category' => $fabricName,
            'fabric_id' => $fabricId,
            'subcategory' => null,
            'sale_type' => 'unidade',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $baseTotal + $totalSurcharges + $sublocalTotal,
            'base_price' => $baseTotal,
            'size_surcharges' => $sizeSurcharges,
            'total_surcharges' => $totalSurcharges,
            'application_type' => null,
            'sublocal_personalizations' => $sublocalPersonalizations,
            'size' => $data['size'] ?? null,
            'color_id' => $data['color_id'] ?? null,
            'cut_type_id' => $data['cut_type_id'] ?? $option->id,
        ];
    }

    /**
     * Criar item do tipo fabric_piece para o carrinho
     */
    public function createFabricPieceCartItem($piece, array $data): array
    {
        $weightSold = (float)$data['quantity'];
        $totalCalculatedPrice = (float)$data['unit_price'];
        $pricePerKg = $piece->sale_price ?? ($weightSold > 0 ? $totalCalculatedPrice / $weightSold : 0);

        return [
            'id' => uniqid(),
            'type' => 'fabric_piece',
            'item_id' => $piece->id,
            'product_title' => ($piece->fabric->name ?? 'Tecido') . ' - ' . ($piece->color->name ?? 'Cor'),
            'category' => 'Peça de Tecido',
            'subcategory' => null,
            'sale_type' => 'kg',
            'quantity' => $weightSold,
            'unit_price' => $pricePerKg,
            'total_price' => $totalCalculatedPrice,
            'supplier_name' => $piece->supplier,
            'fabric_type_name' => $piece->fabricType->name ?? ($piece->fabric->name ?? 'Tecido'),
        ];
    }

    /**
     * Criar item do tipo machine para o carrinho
     */
    public function createMachineCartItem($machine, array $data): array
    {
        $unitPrice = $data['unit_price'] ?? 0;

        return [
            'id' => uniqid(),
            'type' => 'machine',
            'item_id' => $machine->id,
            'product_title' => $machine->name . ' (' . $machine->internal_code . ')',
            'category' => 'Máquina',
            'subcategory' => $machine->brand,
            'sale_type' => 'unidade',
            'quantity' => 1,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice,
        ];
    }

    /**
     * Criar item do tipo supply para o carrinho
     */
    public function createSupplyCartItem($supply, array $data): array
    {
        $unitPrice = $data['unit_price'] ?? 0;
        $quantity = $data['quantity'];

        return [
            'id' => uniqid(),
            'type' => 'supply',
            'item_id' => $supply->id,
            'product_title' => $supply->name . ' - ' . $supply->type,
            'category' => 'Suprimento',
            'subcategory' => null,
            'sale_type' => $supply->unit ?? 'unidade',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
        ];
    }

    /**
     * Criar item do tipo uniform para o carrinho
     */
    public function createUniformCartItem($uniform, array $data): array
    {
        $unitPrice = $data['unit_price'] ?? 0;
        $quantity = $data['quantity'];

        return [
            'id' => uniqid(),
            'type' => 'uniform',
            'item_id' => $uniform->id,
            'product_title' => $uniform->name . ' (' . $uniform->size . ')',
            'category' => 'Uniforme/EPI',
            'subcategory' => $uniform->gender,
            'sale_type' => 'unidade',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
        ];
    }

    /**
     * Descontar estoque após venda
     */
    public function deductStockFromSale($storeId, $fabricId, $colorId, $cutTypeId, $size, $quantity): bool
    {
        if (!Schema::hasTable('stocks') || !$storeId) {
            return false;
        }
        
        try {
            $stock = Stock::findByParams($storeId, $fabricId, null, $colorId, $cutTypeId, $size);
            
            if ($stock && $stock->available_quantity >= $quantity) {
                $stock->use($quantity);
                Log::info('Estoque descontado com sucesso (Service)', [
                    'store_id' => $storeId,
                    'size' => $size,
                    'quantity' => $quantity,
                ]);
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Erro ao descontar estoque (Service)', ['error' => $e->getMessage()]);
        }
        
        return false;
    }

    /**
     * Verificar estoque e criar solicitação se necessário
     */
    public function checkStockAndCreateRequest(
        int $storeId,
        ?int $fabricId,
        ?int $colorId,
        ?int $cutTypeId,
        ?string $size,
        int $quantity,
        ?int $orderId = null
    ): bool {
        if (!Schema::hasTable('stocks') || !Schema::hasTable('stock_requests')) {
            Log::warning('Tabelas de estoque não existem');
            return false;
        }

        $validSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        if (!in_array($size, $validSizes)) {
            Log::warning('Tamanho inválido', ['size' => $size]);
            return false;
        }

        if (!$storeId || !$colorId || !$cutTypeId || !$size || !$quantity) {
            Log::error('Campos obrigatórios faltando para criar solicitação de estoque');
            return false;
        }

        try {
            // Verificar estoque
            $stock = Stock::findByParams($storeId, $fabricId, null, $colorId, $cutTypeId, $size);
            $hasStock = $stock && $stock->available_quantity >= $quantity;
            $availableQuantity = $stock ? $stock->available_quantity : 0;

            $requestNotes = $hasStock
                ? "Solicitação automática gerada pelo PDV - Estoque disponível: {$availableQuantity}"
                : "Solicitação automática gerada pelo PDV - Estoque insuficiente (disponível: {$availableQuantity}, solicitado: {$quantity})";

            $stockRequest = StockRequest::create([
                'order_id' => $orderId,
                'requesting_store_id' => $storeId,
                'target_store_id' => null,
                'fabric_id' => $fabricId,
                'color_id' => $colorId,
                'cut_type_id' => $cutTypeId,
                'size' => $size,
                'requested_quantity' => $quantity,
                'status' => 'pendente',
                'requested_by' => Auth::id(),
                'request_notes' => $requestNotes,
            ]);

            // Tentar reservar o estoque se ele existir
            if ($stock && $hasStock) {
                $stock->reserve($quantity, Auth::id(), $orderId, $stockRequest->id, 'Reserva automática PDV (Venda)');
            }

            // Notificar usuários do estoque
            $this->notifyStockUsersForRequest($stockRequest, $orderId, $fabricId, $colorId, $cutTypeId, $size, $quantity, $storeId);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao criar solicitação de estoque', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Notificar usuários do estoque sobre nova solicitação
     */
    private function notifyStockUsersForRequest(
        StockRequest $stockRequest,
        ?int $orderId,
        ?int $fabricId,
        ?int $colorId,
        ?int $cutTypeId,
        string $size,
        int $quantity,
        int $storeId
    ): void {
        try {
            $stockUsers = User::whereIn('role', ['estoque', 'admin', 'admin_geral'])->get();

            if ($stockUsers->isEmpty()) {
                return;
            }

            $fabric = ProductOption::find($fabricId);
            $color = ProductOption::find($colorId);
            $cutType = ProductOption::find($cutTypeId);
            $store = Store::find($storeId);

            $productInfo = sprintf(
                '%s - %s - %s',
                $fabric->name ?? 'N/A',
                $color->name ?? 'N/A',
                $cutType->name ?? 'N/A'
            );

            $storeName = $store->name ?? 'Loja';
            $orderNumber = str_pad($orderId ?? 0, 6, '0', STR_PAD_LEFT);

            foreach ($stockUsers as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'stock_request_created',
                    'title' => 'Nova Solicitação de Estoque',
                    'message' => "Pedido #{$orderNumber} precisa de estoque: {$productInfo} - Tam: {$size} - Qtd: {$quantity}",
                    'link' => route('stock-requests.index'),
                    'data' => [
                        'stock_request_id' => $stockRequest->id,
                        'order_id' => $orderId,
                        'size' => $size,
                        'quantity' => $quantity,
                        'product_info' => $productInfo,
                        'store_name' => $storeName,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao notificar usuários de estoque', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Processar checkout - criar pedido e pagamento com toda lógica de negócio
     */
    public function processCheckout(array $validated): array
    {
        $user = Auth::user();
        $storeId = $this->getCurrentStoreId();
        $cart = $this->getCart();

        if (empty($cart)) {
            throw new \Exception('Carrinho vazio');
        }

        return DB::transaction(function () use ($cart, $validated, $user, $storeId) {
            // Calcular totais
            $subtotal = $this->calculateCartTotal($cart);
            $discount = floatval($validated['discount'] ?? 0);
            $deliveryFee = floatval($validated['delivery_fee'] ?? 0);
            $total = $subtotal - $discount + $deliveryFee;

            // Status finalizado - Tentar 'Entregue', depois 'Pronto', senão o primeiro da fila (geralmente Pendente)
            $status = Status::where('name', 'Entregue')->first() ?? Status::where('name', 'Pronto')->first() ?? Status::orderBy('position', 'asc')->first();

            // Criar pedido
            $order = Order::create([
                'client_id' => $validated['client_id'] ?? null,
                'user_id' => $user->id,
                'store_id' => $storeId,
                'status_id' => $status?->id,
                'order_date' => now(),
                'delivery_date' => now()->addDays(15),
                'is_pdv' => true,
                'is_draft' => false,
                'client_confirmed' => true,
                'client_confirmed_at' => now(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => max(0, $total),
                'total_items' => array_sum(array_column($cart, 'quantity')),
                'tenant_id' => $user->tenant_id,
                'notes' => $validated['notes'] ?? null,
            ]);

            $itemNumber = 1;
            $movementItems = [];

            foreach ($cart as $cartItem) {
                $itemType = $cartItem['type'] ?? 'product';
                $orderItemData = [
                    'order_id' => $order->id,
                    'item_number' => $itemNumber++,
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['unit_price'],
                    'total_price' => $cartItem['total_price'],
                    'print_type' => $cartItem['product_title'] ?? '',
                    'fabric' => '',
                    'color' => '',
                ];

                // Lógica por tipo de item
                if ($itemType == 'fabric_piece') {
                    $piece = FabricPiece::find($cartItem['item_id']);
                    if ($piece) {
                        $orderItemData['fabric'] = $piece->fabric->name ?? 'Tecido';
                        $orderItemData['color'] = $piece->color->name ?? 'Cor';
                        $piece->recordSale($cartItem['quantity'], $order->id);
                    }
                } elseif ($itemType == 'machine') {
                    $machine = SewingMachine::find($cartItem['item_id']);
                    if ($machine) {
                        $orderItemData['fabric'] = $machine->brand ?? '';
                        $orderItemData['color'] = $machine->model ?? '';
                        $machine->update(['status' => 'disposed']);
                    }
                } elseif ($itemType == 'supply') {
                    $supply = ProductionSupply::find($cartItem['item_id']);
                    if ($supply) {
                        $orderItemData['fabric'] = $supply->name ?? '';
                        $orderItemData['color'] = $supply->color ?? '';
                        if ($supply->quantity < $cartItem['quantity']) throw new \Exception("Estoque insuficiente para suprimento: {$supply->name}");
                        $supply->decrement('quantity', $cartItem['quantity']);
                    }
                } elseif ($itemType == 'uniform') {
                    $uniform = Uniform::find($cartItem['item_id']);
                    if ($uniform) {
                        $orderItemData['fabric'] = $uniform->name ?? '';
                        $orderItemData['color'] = ($uniform->size ?? '') . ' - ' . ($uniform->gender ?? '');
                        if ($uniform->quantity < $cartItem['quantity']) throw new \Exception("Estoque insuficiente para uniforme: {$uniform->name}");
                        $uniform->decrement('quantity', $cartItem['quantity']);
                    }
                } else {
                    $orderItemData['fabric'] = $cartItem['fabric_id'] ?? ($cartItem['category'] ?? '');
                    $orderItemData['color'] = $cartItem['subcategory'] ?? '';
                    if ($itemType === 'product_option' && isset($cartItem['product_option_id'])) {
                        $orderItemData['model'] = $cartItem['product_option_id'];
                    }
                    if (!empty($cartItem['size_surcharges'])) {
                        $orderItemData['sizes'] = collect($cartItem['size_surcharges'])->filter(fn($d) => ($d['quantity'] ?? 0) > 0)->map(fn($d) => $d['quantity'])->toArray();
                    }
                }

                // Gerar solicitação de estoque (que agora também reserva o estoque se disponível)
                if (isset($cartItem['size']) && isset($cartItem['color_id']) && isset($cartItem['cut_type_id'])) {
                    // SEMPRE criar solicitação de estoque para rastreamento (conforme lógica original do PDV)
                    // Isso agora também reserva o estoque se houver disponível
                    $this->checkStockAndCreateRequest(
                        $storeId,
                        $cartItem['fabric_id'] ?? null,
                        $cartItem['color_id'],
                        $cartItem['cut_type_id'],
                        $cartItem['size'],
                        (int)$cartItem['quantity'],
                        $order->id
                    );

                    $movementItems[] = [
                        'stock_id' => null,
                        'fabric_type_id' => $cartItem['fabric_id'] ?? null,
                        'color_id' => $cartItem['color_id'],
                        'cut_type_id' => $cartItem['cut_type_id'],
                        'size' => $cartItem['size'],
                        'quantity' => (int)$cartItem['quantity'],
                    ];
                }

                $orderItem = OrderItem::create($orderItemData);

                // Sublimações
                if (!empty($cartItem['sublocal_personalizations'])) {
                    foreach ($cartItem['sublocal_personalizations'] as $p) {
                        OrderSublimation::create([
                            'order_item_id' => $orderItem->id,
                            'application_type' => 'sublimacao_local',
                            'location_id' => $p['location_id'] ?? null,
                            'location_name' => $p['location_name'] ?? null,
                            'quantity' => $p['quantity'] ?? 1,
                            'unit_price' => $p['unit_price'] ?? 0,
                            'final_price' => $p['final_price'] ?? ($p['unit_price'] * $p['quantity']),
                        ]);
                    }
                }
            }

            // Pagamentos
            $paymentMethods = $validated['payment_methods'];
            $totalPaid = collect($paymentMethods)->sum('amount');
            if (round($totalPaid, 2) > round($total, 2)) throw new \Exception("Valor pago excede o total");

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => count($paymentMethods) > 1 ? 'multiplo' : ($paymentMethods[0]['method'] ?? 'dinheiro'),
                'payment_methods' => $paymentMethods,
                'amount' => $total,
                'entry_amount' => $totalPaid,
                'remaining_amount' => max(0, $total - $totalPaid),
                'payment_date' => now(),
                'status' => $totalPaid >= $total ? 'pago' : 'pendente',
            ]);

            // Transações de Caixa
            foreach ($paymentMethods as $method) {
                CashTransaction::create([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'order_id' => $order->id,
                    'store_id' => $storeId,
                    'type' => 'entrada',
                    'category' => 'Venda',
                    'description' => "Venda PDV - Pedido #{$order->id}",
                    'amount' => $method['amount'],
                    'payment_method' => $method['method'],
                    'transaction_date' => now(),
                    'status' => 'confirmado',
                    'tenant_id' => $user->tenant_id,
                ]);
            }

            // Registros adicionais
            SalesHistory::recordSale($order);
            OrderStatusTracking::recordEntry($order->id, $order->status_id, $user->id);

            // Movimentação de estoque
            $movementId = null;
            if (!empty($movementItems)) {
                $movementId = StockMovement::createOrderMovement($storeId, $order->id, $movementItems, "Venda PDV #{$order->id}")->id;
            }

            $this->clearCart();

            return ['order' => $order, 'movementId' => $movementId];
        });
    }

    /**
     * Remover item do carrinho
     */
    public function removeFromCart(string $itemId): array
    {
        $cart = $this->getCart();
        $cart = array_filter($cart, fn($item) => $item['id'] !== $itemId);
        $cart = array_values($cart); // Reindexar
        $this->saveCart($cart);
        
        return $cart;
    }

    /**
     * Atualizar item do carrinho
     */
    public function updateCartItem(string $itemId, array $updates): array
    {
        $cart = $this->getCart();
        
        foreach ($cart as &$item) {
            if ($item['id'] === $itemId) {
                if (isset($updates['quantity'])) {
                    $item['quantity'] = $updates['quantity'];
                    $item['total_price'] = $item['unit_price'] * $updates['quantity'];
                }
                if (isset($updates['unit_price'])) {
                    $item['unit_price'] = $updates['unit_price'];
                    $item['total_price'] = $updates['unit_price'] * $item['quantity'];
                }
                break;
            }
        }
        
        $this->saveCart($cart);
        return $cart;
    }
}
