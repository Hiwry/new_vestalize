<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogOrder;
use App\Models\FabricPieceSale;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockRequest;
use App\Models\Stock;
use App\Models\ProductOption;
use App\Models\Product;
use App\Services\FabricPieceInventoryService;
use App\Services\CatalogGatewaySettingsService;
use Illuminate\Support\Str;

class CatalogOrderController extends Controller
{
    /**
     * List all catalog orders for the current tenant
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CatalogOrder::class);

        $query = CatalogOrder::where('tenant_id', Auth::user()->tenant_id)
            ->with('store')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('order_code', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        // Stats
        $stats = CatalogOrder::where('tenant_id', Auth::user()->tenant_id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as converted,
                SUM(total) as total_revenue
            ")
            ->first();

        $requirePaidBeforeOrder = $this->requiresPaidBeforeOrder((int) Auth::user()->tenant_id);

        return view('admin.catalog-orders.index', compact('orders', 'stats', 'requirePaidBeforeOrder'));
    }

    /**
     * Show catalog order details
     */
    public function show(CatalogOrder $catalogOrder)
    {
        $this->authorize('view', $catalogOrder);

        $catalogOrder->loadMissing(['store', 'order']);

        $requirePaidBeforeOrder = $this->requiresPaidBeforeOrder((int) $catalogOrder->tenant_id);
        $stockSeparation = $this->buildStockSeparationSummary($catalogOrder);

        return view('admin.catalog-orders.show', compact('catalogOrder', 'requirePaidBeforeOrder', 'stockSeparation'));
    }

    /**
     * Update catalog order status
     */
    public function updateStatus(Request $request, CatalogOrder $catalogOrder)
    {
        $this->authorize('update', $catalogOrder);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $catalogOrder->status;
        $newStatus = $request->status;
        $requirePaidBeforeOrder = $this->requiresPaidBeforeOrder((int) $catalogOrder->tenant_id);

        if (
            $newStatus === 'approved'
            && $requirePaidBeforeOrder
            && $catalogOrder->payment_status !== 'paid'
        ) {
            return back()->with('error', 'Este pedido só pode ser aprovado após pagamento confirmado.');
        }

        try {
            DB::transaction(function () use ($catalogOrder, $oldStatus, $newStatus, $request) {
                if ($newStatus === 'approved' && $oldStatus !== 'approved') {
                    $this->createStockRequests($catalogOrder);
                    $this->reserveFabricPieces($catalogOrder);
                } elseif ($oldStatus === 'approved' && $newStatus !== 'approved' && $newStatus !== 'converted') {
                    $this->releaseStockRequests($catalogOrder);
                    $this->restoreFabricPieceSales($catalogOrder);
                }

                $catalogOrder->update([
                    'status' => $newStatus,
                    'admin_notes' => $request->admin_notes,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Não foi possível atualizar o pedido: ' . $e->getMessage());
        }

        $statusMessages = [
            'approved' => 'Pedido aprovado e solicitações de estoque geradas!',
            'rejected' => 'Pedido rejeitado.',
            'cancelled' => 'Pedido cancelado.',
            'pending' => 'Pedido retornado para pendente.',
        ];

        return back()->with('success', $statusMessages[$request->status] ?? 'Status atualizado.');
    }

    /**
     * Update catalog order payment data (admin)
     */
    public function updatePayment(Request $request, CatalogOrder $catalogOrder)
    {
        $this->authorize('update', $catalogOrder);

        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_gateway_id' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        $paymentData = is_array($catalogOrder->payment_data) ? $catalogOrder->payment_data : [];

        if (!empty($validated['payment_notes'])) {
            $paymentData['admin_payment_notes'] = trim($validated['payment_notes']);
        } else {
            unset($paymentData['admin_payment_notes']);
        }

        if ($validated['payment_status'] === 'paid') {
            $paymentData['paid_at'] = $paymentData['paid_at'] ?? now()->toDateTimeString();
        } else {
            unset($paymentData['paid_at']);
        }

        $catalogOrder->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'payment_gateway_id' => $validated['payment_gateway_id'] ?? null,
            'payment_data' => empty($paymentData) ? null : $paymentData,
        ]);

        return back()->with('success', 'Pagamento do pedido atualizado com sucesso.');
    }

    /**
     * Create stock requests for catalog order items
     */
    private function createStockRequests(CatalogOrder $catalogOrder)
    {
        $tenantId = $catalogOrder->tenant_id;
        $storeId = $catalogOrder->store_id;

        // Fetch options once to use for mapping
        $options = ProductOption::where('active', true)
            ->where(function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            })
            ->get();
        $optionsById = $options->keyBy('id');

        $fabrics = $options->where('type', 'tecido');
        $colors = $options->where('type', 'cor');
        $cutTypes = $options->where('type', 'tipo_corte');

        $productIds = collect($catalogOrder->items ?? [])
            ->pluck('product_id')
            ->filter()
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values();

        $productsById = Product::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('id', $productIds)
            ->get(['id', 'tecido_id', 'cut_type_id'])
            ->keyBy('id');

        // Group items by unique product combination to decide which store to request from
        $groupedItems = [];
        foreach ($catalogOrder->items as $item) {
            if (($item['item_type'] ?? 'product') === 'fabric_piece') {
                continue;
            }

            $productId = isset($item['product_id']) ? (int) $item['product_id'] : null;
            $product = $productId ? $productsById->get($productId) : null;

            $fabricId = ($product && $product->tecido_id)
                ? (int) $product->tecido_id
                : $this->resolveOptionId($item['fabric'] ?? $item['title'] ?? null, $fabrics);
            $colorId = $this->resolveOptionId($item['color'] ?? null, $colors);
            $cutTypeId = ($product && $product->cut_type_id)
                ? (int) $product->cut_type_id
                : $this->resolveOptionId($item['model'] ?? null, $cutTypes);
            $fabricTypeId = $this->resolveFabricTypeId($cutTypeId, $optionsById);

            if (!$fabricId || !$cutTypeId) {
                \Log::warning('Pedido de catálogo com item sem mapeamento completo de tecido/corte', [
                    'catalog_order_id' => $catalogOrder->id,
                    'order_code' => $catalogOrder->order_code,
                    'product_id' => $productId,
                    'item' => $item,
                ]);
            }
            
            $key = "{$fabricId}-{$fabricTypeId}-{$colorId}-{$cutTypeId}";
            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'fabric_id' => $fabricId,
                    'fabric_type_id' => $fabricTypeId,
                    'color_id' => $colorId,
                    'cut_type_id' => $cutTypeId,
                    'sizes' => []
                ];
            }
            $size = strtoupper($item['size'] ?? 'M');
            $quantity = $item['quantity'] ?? 1;
            $groupedItems[$key]['sizes'][$size] = ($groupedItems[$key]['sizes'][$size] ?? 0) + $quantity;
        }

        foreach ($groupedItems as $group) {
            // Find best store with complete stock
            $targetStoreId = Stock::findBestStoreWithCompleteStock(
                $group['fabric_id'],
                $group['fabric_type_id'],
                $group['color_id'],
                $group['cut_type_id'],
                $group['sizes'],
                $storeId
            );

            foreach ($group['sizes'] as $size => $quantity) {
                $stockRequest = StockRequest::create([
                    'tenant_id' => $tenantId,
                    'requesting_store_id' => $storeId,
                    'target_store_id' => $targetStoreId,
                    'requested_by' => Auth::id(),
                    'status' => 'pendente',
                    'fabric_id' => $group['fabric_id'],
                    'fabric_type_id' => $group['fabric_type_id'],
                    'color_id' => $group['color_id'],
                    'cut_type_id' => $group['cut_type_id'],
                    'size' => $size,
                    'requested_quantity' => $quantity,
                    'request_notes' => "Pedido Catálogo #{$catalogOrder->order_code}" . 
                                      ($targetStoreId ? " (Estoque reservado)" : " (Sem estoque imediato)"),
                ]);
                
                // Notificar lojas
                $storeName = $catalogOrder->store->name ?? 'N/A';
                $productInfo = sprintf('%s - %s', $stockRequest->fabric->name ?? 'N/A', $stockRequest->color->name ?? 'N/A');
                
                $estoqueUsers = \App\Models\User::where('tenant_id', $tenantId)->where(function($q) {
                    $q->where('role', 'estoque')->orWhere('role', 'admin')->orWhere('role', 'admin_geral');
                })->get();

                foreach ($estoqueUsers as $user) {
                    \App\Models\Notification::createStockRequestCreated($user->id, $stockRequest->id, $storeName, $productInfo);
                }

                // Reserve stock if found
                if ($targetStoreId) {
                    $stock = Stock::findByParams(
                        $targetStoreId,
                        $group['fabric_id'],
                        $group['fabric_type_id'],
                        $group['color_id'],
                        $group['cut_type_id'],
                        $size
                    );

                    if (!$stock && $group['fabric_type_id']) {
                        // Fallback para registros legados sem fabric_type_id
                        $stock = Stock::findByParams(
                            $targetStoreId,
                            $group['fabric_id'],
                            null,
                            $group['color_id'],
                            $group['cut_type_id'],
                            $size
                        );
                    }

                    if ($stock) {
                        $stock->reserve($quantity, Auth::id(), null, $stockRequest->id, "Reserva automática para Pedido #{$catalogOrder->order_code}");
                    }
                }
            }
        }
    }

    /**
     * Release stock reservations and reject pending stock requests for a catalog order
     */
    private function releaseStockRequests(CatalogOrder $catalogOrder): void
    {
        $stockRequests = StockRequest::where('tenant_id', $catalogOrder->tenant_id)
            ->where(function ($query) use ($catalogOrder) {
                $query->where('request_notes', 'like', "%Pedido Catálogo #{$catalogOrder->order_code}%")
                    ->orWhere('request_notes', 'like', "%Pedido Catálogo: {$catalogOrder->order_code}%");
            })
            ->get();

        foreach ($stockRequests as $stockRequest) {
            if ($stockRequest->target_store_id) {
                $stock = Stock::findByParams(
                    (int) $stockRequest->target_store_id,
                    $stockRequest->fabric_id ? (int) $stockRequest->fabric_id : null,
                    $stockRequest->fabric_type_id ? (int) $stockRequest->fabric_type_id : null,
                    $stockRequest->color_id ? (int) $stockRequest->color_id : null,
                    $stockRequest->cut_type_id ? (int) $stockRequest->cut_type_id : null,
                    (string) $stockRequest->size
                );

                if (!$stock && $stockRequest->fabric_type_id) {
                    $stock = Stock::findByParams(
                        (int) $stockRequest->target_store_id,
                        $stockRequest->fabric_id ? (int) $stockRequest->fabric_id : null,
                        null,
                        $stockRequest->color_id ? (int) $stockRequest->color_id : null,
                        $stockRequest->cut_type_id ? (int) $stockRequest->cut_type_id : null,
                        (string) $stockRequest->size
                    );
                }

                if ($stock && $stock->reserved_quantity > 0) {
                    $stock->release(
                        (int) $stockRequest->requested_quantity,
                        Auth::id(),
                        $stockRequest->order_id,
                        $stockRequest->id,
                        "Liberação automática - Pedido catálogo #{$catalogOrder->order_code}"
                    );
                }
            }

            if ($stockRequest->status === 'pendente') {
                $stockRequest->update([
                    'status' => 'rejeitado',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'rejection_reason' => 'Pedido de catálogo não aprovado.',
                ]);
            }
        }
    }

    /**
     * Helper to map string names to option IDs
     */
    private function resolveOptionId(?string $name, $options): ?int
    {
        if (!$name) return null;

        $normalizedSearch = Str::slug($name);
        
        // Try exact slug match
        foreach ($options as $option) {
            if (Str::slug($option->name) === $normalizedSearch) {
                return $option->id;
            }
        }

        // Try loose contains match
        foreach ($options as $option) {
            if (str_contains(Str::slug($option->name), $normalizedSearch) || str_contains($normalizedSearch, Str::slug($option->name))) {
                return $option->id;
            }
        }

        return null;
    }

    /**
     * Resolve fabric_type_id from cut type parent option.
     */
    private function resolveFabricTypeId(?int $cutTypeId, $optionsById): ?int
    {
        if (!$cutTypeId) {
            return null;
        }

        $cutType = $optionsById->get($cutTypeId);
        if (!$cutType || !$cutType->parent_id) {
            return null;
        }

        $parent = $optionsById->get($cutType->parent_id);
        if (!$parent || $parent->type !== 'tipo_tecido') {
            return null;
        }

        return (int) $parent->id;
    }

    private function reserveFabricPieces(CatalogOrder $catalogOrder): void
    {
        /** @var FabricPieceInventoryService $inventory */
        $inventory = app(FabricPieceInventoryService::class);

        foreach (($catalogOrder->items ?? []) as $item) {
            if (($item['item_type'] ?? 'product') !== 'fabric_piece') {
                continue;
            }

            $pieceId = (int) ($item['fabric_piece_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);
            $unit = (string) ($item['control_unit'] ?? 'kg');

            if ($pieceId <= 0 || $quantity <= 0) {
                continue;
            }

            $existingSale = FabricPieceSale::active()
                ->where('catalog_order_id', $catalogOrder->id)
                ->where('fabric_piece_id', $pieceId)
                ->first();

            if ($existingSale) {
                continue;
            }

            $piece = \App\Models\FabricPiece::find($pieceId);
            if (!$piece) {
                continue;
            }

            $inventory->sell($piece, [
                'quantity' => $quantity,
                'unit' => $unit,
                'unit_price' => (float) ($item['unit_price'] ?? 0),
                'catalog_order_id' => $catalogOrder->id,
                'channel' => 'catalog',
                'sold_by' => Auth::id(),
                'notes' => 'Pedido catálogo ' . $catalogOrder->order_code,
            ]);
        }
    }

    private function restoreFabricPieceSales(CatalogOrder $catalogOrder): void
    {
        /** @var FabricPieceInventoryService $inventory */
        $inventory = app(FabricPieceInventoryService::class);

        $sales = FabricPieceSale::active()
            ->where('catalog_order_id', $catalogOrder->id)
            ->get();

        foreach ($sales as $sale) {
            $inventory->restoreSale(
                $sale,
                'Reversão do pedido catálogo ' . $catalogOrder->order_code,
                Auth::id()
            );
        }
    }

    /**
     * Convert catalog order to internal Order
     */
    public function convertToOrder(CatalogOrder $catalogOrder)
    {
        $this->authorize('update', $catalogOrder);

        if ($catalogOrder->status !== 'approved') {
            return back()->with('error', 'Somente pedidos aprovados podem ser convertidos.');
        }

        if (
            $this->requiresPaidBeforeOrder((int) $catalogOrder->tenant_id)
            && $catalogOrder->payment_status !== 'paid'
        ) {
            return back()->with('error', 'Somente pedidos com pagamento aprovado podem ser convertidos.');
        }

        if ($catalogOrder->order_id) {
            return back()->with('error', 'Este pedido já foi convertido.');
        }

        try {
            DB::beginTransaction();

            $status = Status::withoutGlobalScopes()
                ->where('tenant_id', $catalogOrder->tenant_id)
                ->orderBy('position')
                ->first()
                ?? Status::withoutGlobalScopes()->orderBy('position')->first();

            // Create internal order
            $order = Order::create([
                'tenant_id' => $catalogOrder->tenant_id,
                'store_id' => $catalogOrder->store_id,
                'client_id' => null,
                'user_id' => Auth::id(),
                'status_id' => $status?->id,
                'order_date' => now(),
                'delivery_date' => now()->addDays(15),
                'is_draft' => false,
                'origin' => 'catalogo',
                'notes' => "Pedido do catálogo: {$catalogOrder->order_code}\n"
                         . "Cliente: {$catalogOrder->customer_name}\n"
                         . "Telefone: {$catalogOrder->customer_phone}\n"
                         . ($catalogOrder->notes ? "\nObs: {$catalogOrder->notes}" : ''),
                'subtotal' => $catalogOrder->subtotal,
                'discount' => $catalogOrder->discount,
                'delivery_fee' => $catalogOrder->delivery_fee,
                'total' => $catalogOrder->total,
                'total_items' => $catalogOrder->total_items,
            ]);

            // Create order items
            $itemNumber = 1;
            foreach ($catalogOrder->items as $item) {
                $itemType = $item['item_type'] ?? 'product';
                $quantity = (float) ($item['quantity'] ?? 0);
                $lineTotal = (float) ($item['total'] ?? 0);
                $metadata = [
                    'catalog_order_item' => [
                        'catalog_order_id' => $catalogOrder->id,
                        'catalog_order_code' => $catalogOrder->order_code,
                        'item_type' => $itemType,
                        'product_id' => $item['product_id'] ?? null,
                        'fabric_piece_id' => $item['fabric_piece_id'] ?? null,
                        'quantity' => $quantity,
                        'quantity_label' => $item['quantity_label'] ?? null,
                        'control_unit' => $item['control_unit'] ?? null,
                    ],
                ];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_number' => $itemNumber++,
                    'fabric' => ($itemType === 'fabric_piece')
                        ? ($item['fabric_type_name'] ?? $item['title'] ?? 'Peça de tecido')
                        : ($item['sku'] ?? $item['title'] ?? 'Produto catálogo'),
                    'color' => $item['color'] ?? '',
                    'collar' => '-',
                    'model' => $itemType === 'fabric_piece' ? 'Peça de tecido' : ($item['title'] ?? 'Produto catálogo'),
                    'detail' => implode(' | ', array_filter([
                        !empty($item['size']) ? 'Tam: ' . $item['size'] : null,
                        !empty($item['supplier_name']) ? 'Fornecedor: ' . $item['supplier_name'] : null,
                    ])) ?: null,
                    'print_type' => $itemType === 'fabric_piece' ? 'Venda de tecido' : 'Catálogo',
                    'print_desc' => json_encode($metadata),
                    'sizes' => $itemType === 'fabric_piece' ? [] : array_filter([
                        $item['size'] ?? null => ($item['size'] ?? null) ? (int) $quantity : null,
                    ]),
                    'quantity' => $itemType === 'fabric_piece' ? 1 : (int) $quantity,
                    'unit_price' => $itemType === 'fabric_piece' ? $lineTotal : (float) ($item['unit_price'] ?? 0),
                    'total_price' => $lineTotal,
                    'unit_cost' => 0,
                    'total_cost' => 0,
                ]);

                if ($itemType === 'fabric_piece' && !empty($item['fabric_piece_id'])) {
                    FabricPieceSale::active()
                        ->where('catalog_order_id', $catalogOrder->id)
                        ->where('fabric_piece_id', (int) $item['fabric_piece_id'])
                        ->update([
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                        ]);
                }
            }

            // Link and update status
            $catalogOrder->update([
                'order_id' => $order->id,
                'status' => 'converted',
            ]);

            // Link existing stock requests to the internal order
            StockRequest::where('tenant_id', $catalogOrder->tenant_id)
                ->where(function ($query) use ($catalogOrder) {
                    $query->where('request_notes', 'like', "%Pedido Catálogo #{$catalogOrder->order_code}%")
                        ->orWhere('request_notes', 'like', "%Pedido Catálogo: {$catalogOrder->order_code}%");
                })
                ->whereNull('order_id')
                ->update(['order_id' => $order->id]);

            DB::commit();

            return back()->with('success', "Pedido convertido com sucesso! Pedido interno #{$order->id} criado.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao converter pedido de catálogo.', [
                'catalog_order_id' => $catalogOrder->id,
                'order_code' => $catalogOrder->order_code,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erro ao converter pedido. Tente novamente.');
        }
    }

    private function requiresPaidBeforeOrder(int $tenantId): bool
    {
        if ($tenantId <= 0) {
            return true;
        }

        return CatalogGatewaySettingsService::forTenant($tenantId)['require_paid_before_order'];
    }

    private function buildStockSeparationSummary(CatalogOrder $catalogOrder): array
    {
        $counts = [
            'pendente' => 0,
            'aprovado' => 0,
            'em_transferencia' => 0,
            'concluido' => 0,
            'rejeitado' => 0,
            'cancelado' => 0,
        ];

        $statusRows = $this->catalogStockRequestsQuery($catalogOrder)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        foreach ($statusRows as $statusRow) {
            $status = (string) $statusRow->status;
            if (!array_key_exists($status, $counts)) {
                $counts[$status] = 0;
            }
            $counts[$status] = (int) $statusRow->total;
        }

        $total = array_sum($counts);
        $separatedCount = $counts['aprovado'] + $counts['em_transferencia'] + $counts['concluido'];
        $isOrderApproved = in_array($catalogOrder->status, ['approved', 'converted'], true);
        $fabricPieceSalesCount = FabricPieceSale::active()
            ->where('catalog_order_id', $catalogOrder->id)
            ->count();

        $summary = [
            'label' => 'Aguardando envio ao estoque',
            'description' => 'O pedido ainda não foi enviado para separação.',
            'tone' => 'slate',
            'total_requests' => $total,
            'counts' => $counts,
            'is_fully_separated' => false,
        ];

        if (!$isOrderApproved && $total === 0) {
            return $summary;
        }

        if ($total === 0 && $fabricPieceSalesCount > 0) {
            $summary['label'] = 'Tecido reservado';
            $summary['description'] = 'As peças de tecido deste pedido já foram vinculadas e baixadas no estoque.';
            $summary['tone'] = 'emerald';
            $summary['is_fully_separated'] = true;
            return $summary;
        }

        if ($total === 0) {
            $summary['label'] = 'Sem solicitações';
            $summary['description'] = 'Não há solicitações de estoque registradas para este pedido.';
            $summary['tone'] = 'slate';
            return $summary;
        }

        if ($counts['rejeitado'] > 0) {
            $summary['label'] = $separatedCount > 0 ? 'Separação parcial com problema' : 'Separação com problema';
            $summary['description'] = 'Existe solicitação rejeitada. Verifique o estoque para concluir o pedido.';
            $summary['tone'] = 'rose';
            return $summary;
        }

        if ($counts['pendente'] > 0 && $separatedCount === 0) {
            $summary['label'] = 'Aguardando separação';
            $summary['description'] = 'O estoque ainda não iniciou a separação deste pedido.';
            $summary['tone'] = 'amber';
            return $summary;
        }

        if ($counts['pendente'] > 0 && $separatedCount > 0) {
            $summary['label'] = 'Separação parcial';
            $summary['description'] = 'Parte dos itens já foi separada, mas ainda há pendências no estoque.';
            $summary['tone'] = 'blue';
            return $summary;
        }

        if ($counts['concluido'] === $total) {
            $summary['label'] = 'Pedido separado';
            $summary['description'] = 'Todos os itens foram separados pelo estoque.';
            $summary['tone'] = 'emerald';
            $summary['is_fully_separated'] = true;
            return $summary;
        }

        if ($separatedCount === $total) {
            $summary['label'] = 'Separado / em transferência';
            $summary['description'] = 'Todos os itens já estão em etapa final de separação.';
            $summary['tone'] = 'emerald';
            $summary['is_fully_separated'] = true;
            return $summary;
        }

        $summary['label'] = 'Em separação';
        $summary['description'] = 'O estoque está processando a separação deste pedido.';
        $summary['tone'] = 'blue';
        return $summary;
    }

    private function catalogStockRequestsQuery(CatalogOrder $catalogOrder)
    {
        return StockRequest::where('tenant_id', $catalogOrder->tenant_id)
            ->where(function ($query) use ($catalogOrder) {
                $query->where('request_notes', 'like', "%Pedido Catálogo #{$catalogOrder->order_code}%")
                    ->orWhere('request_notes', 'like', "%Pedido Catálogo: {$catalogOrder->order_code}%");
            });
    }
}
