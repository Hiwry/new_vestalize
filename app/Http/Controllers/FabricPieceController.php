<?php

namespace App\Http\Controllers;

use App\Helpers\StoreHelper;
use App\Models\FabricPiece;
use App\Models\FabricPieceSale;
use App\Models\FabricPieceTransfer;
use App\Models\Order;
use App\Models\ProductOption;
use App\Models\Store;
use App\Services\FabricPieceInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FabricPieceController extends Controller
{
    public function __construct(
        private readonly FabricPieceInventoryService $inventoryService
    ) {
    }

    public function index(Request $request): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();

        $query = FabricPiece::with(['store', 'fabric', 'fabricType', 'color'])
            ->whereIn('store_id', $userStoreIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['fechada', 'aberta', 'em_transferencia']);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('fabric_type_id')) {
            $query->where('fabric_type_id', $request->fabric_type_id);
        }

        if ($request->filled('color_id')) {
            $query->where('color_id', $request->color_id);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', 'like', '%' . $request->supplier . '%');
        }

        if ($request->boolean('alert_only')) {
            $query->where('min_quantity_alert', '>', 0)
                ->where(function ($builder) {
                    $builder->where(function ($subQuery) {
                        $subQuery->where('control_unit', 'kg')
                            ->whereRaw('COALESCE(weight_current, weight, 0) <= min_quantity_alert');
                    })->orWhere(function ($subQuery) {
                        $subQuery->where('control_unit', 'metros')
                            ->whereRaw('COALESCE(meters_current, meters, 0) <= min_quantity_alert');
                    });
                });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('fabricType', fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('color', fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $pieces = $query->latest()->paginate(20)->withQueryString();

        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        $baseStatsQuery = FabricPiece::whereIn('store_id', $userStoreIds);

        $stats = [
            'total' => (clone $baseStatsQuery)->whereIn('status', ['fechada', 'aberta', 'em_transferencia'])->count(),
            'fechadas' => (clone $baseStatsQuery)->where('status', 'fechada')->count(),
            'abertas' => (clone $baseStatsQuery)->where('status', 'aberta')->count(),
            'vendidas' => (clone $baseStatsQuery)->where('status', 'vendida')->count(),
            'em_transferencia' => (clone $baseStatsQuery)->where('status', 'em_transferencia')->count(),
            'estoque_baixo' => (clone $baseStatsQuery)->where('min_quantity_alert', '>', 0)
                ->where('status', '!=', 'vendida')
                ->where(function ($builder) {
                    $builder->where(function ($subQuery) {
                        $subQuery->where('control_unit', 'kg')
                            ->whereRaw('COALESCE(weight_current, weight, 0) <= min_quantity_alert');
                    })->orWhere(function ($subQuery) {
                        $subQuery->where('control_unit', 'metros')
                            ->whereRaw('COALESCE(meters_current, meters, 0) <= min_quantity_alert');
                    });
                })->count(),
        ];

        $storeStats = [];
        foreach ($stores as $store) {
            $activeQuery = FabricPiece::where('store_id', $store->id)
                ->whereIn('status', ['fechada', 'aberta', 'em_transferencia']);

            $storeStats[$store->id] = [
                'name' => $store->name,
                'count' => (clone $activeQuery)->count(),
                'low_stock' => (clone $activeQuery)->where('min_quantity_alert', '>', 0)
                    ->where(function ($builder) {
                        $builder->where(function ($subQuery) {
                            $subQuery->where('control_unit', 'kg')
                                ->whereRaw('COALESCE(weight_current, weight, 0) <= min_quantity_alert');
                        })->orWhere(function ($subQuery) {
                            $subQuery->where('control_unit', 'metros')
                                ->whereRaw('COALESCE(meters_current, meters, 0) <= min_quantity_alert');
                        });
                    })->count(),
            ];
        }

        $recentOrders = Order::whereIn('store_id', $userStoreIds)
            ->where('is_cancelled', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function (Order $order) {
                $prefix = $order->is_pdv ? 'PDV' : 'PED';
                $number = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
                $clientName = $order->client?->name ?? 'Sem cliente';

                return [
                    'id' => $order->id,
                    'label' => "[{$prefix}] #{$number} - {$clientName} - R$ " . number_format((float) $order->total, 2, ',', '.'),
                ];
            });

        return view('fabric-pieces.index', compact(
            'pieces',
            'stores',
            'fabricTypes',
            'colors',
            'stats',
            'storeStats',
            'recentOrders'
        ));
    }

    public function create(): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        return view('fabric-pieces.create', compact('stores', 'fabricTypes', 'colors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:50',
            'invoice_key' => 'nullable|string|max:44',
            'control_unit' => 'required|in:kg,metros',
            'weight' => 'nullable|numeric|min:0',
            'weight_current' => 'nullable|numeric|min:0',
            'meters' => 'nullable|numeric|min:0',
            'meters_current' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'origin_store_id' => 'nullable|exists:stores,id',
            'destination_store_id' => 'nullable|exists:stores,id',
            'between_stores' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0',
            'min_quantity_alert' => 'nullable|numeric|min:0',
            'available_in_pdv' => 'nullable|boolean',
            'available_in_catalog' => 'nullable|boolean',
            'available_in_orders' => 'nullable|boolean',
            'received_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $this->assertUnitHasInitialQuantity($validated);
        $this->fillTransferOrigins($validated, $request);
        $this->fillChannelFlags($validated, $request);
        $this->fillFabricMetadata($validated);
        $this->fillCurrentQuantities($validated);

        $validated['status'] = 'fechada';
        $validated['received_at'] = $validated['received_at'] ?? now();
        $validated['purchase_price'] = 0;

        FabricPiece::create($validated);

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça cadastrada com sucesso!');
    }

    public function edit($id): View
    {
        $piece = FabricPiece::findOrFail($id);

        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        return view('fabric-pieces.edit', compact('piece', 'stores', 'fabricTypes', 'colors'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $piece = FabricPiece::findOrFail($id);

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:50',
            'invoice_key' => 'nullable|string|max:44',
            'control_unit' => 'required|in:kg,metros',
            'weight' => 'nullable|numeric|min:0',
            'weight_current' => 'nullable|numeric|min:0',
            'meters' => 'nullable|numeric|min:0',
            'meters_current' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'origin_store_id' => 'nullable|exists:stores,id',
            'destination_store_id' => 'nullable|exists:stores,id',
            'between_stores' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0',
            'min_quantity_alert' => 'nullable|numeric|min:0',
            'available_in_pdv' => 'nullable|boolean',
            'available_in_catalog' => 'nullable|boolean',
            'available_in_orders' => 'nullable|boolean',
            'received_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $this->assertUnitHasInitialQuantity($validated);
        $this->fillTransferOrigins($validated, $request);
        $this->fillChannelFlags($validated, $request);
        $this->fillFabricMetadata($validated);
        $this->fillCurrentQuantities($validated, $piece);

        $validated['purchase_price'] = 0;

        $piece->update($validated);

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça atualizada com sucesso!');
    }

    public function destroy($id): RedirectResponse
    {
        $piece = FabricPiece::findOrFail($id);
        $piece->delete();

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça excluída com sucesso!');
    }

    public function open(Request $request, $id): JsonResponse
    {
        try {
            $piece = FabricPiece::findOrFail($id);
            $currentQuantity = $request->input('current_quantity', $request->input('weight_current', $request->input('meters_current')));

            $piece = $this->inventoryService->openPiece(
                $piece,
                $currentQuantity !== null && $currentQuantity !== '' ? (float) $currentQuantity : null
            );

            return response()->json([
                'success' => true,
                'message' => 'Peça marcada como aberta!',
                'piece' => $piece,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function sell(Request $request, $id): JsonResponse
    {
        try {
            $piece = FabricPiece::findOrFail($id);

            $result = $this->inventoryService->sellEntirePiece($piece, [
                'order_id' => $request->input('order_id'),
                'unit_price' => $request->input('unit_price', $piece->sale_price),
                'channel' => $request->input('channel', 'manual'),
                'notes' => $request->input('notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Peça vendida com sucesso!',
                'piece' => $result['piece'],
                'remaining' => $result['remaining'],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function sellPartial(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|in:kg,metros',
            'unit_price' => 'nullable|numeric|min:0',
            'order_id' => 'nullable|exists:orders,id',
            'channel' => 'nullable|in:manual,pdv,catalog,order',
            'notes' => 'nullable|string',
        ]);

        try {
            $piece = FabricPiece::findOrFail($id);

            $result = $this->inventoryService->sell($piece, [
                'quantity' => (float) $validated['quantity'],
                'unit' => $validated['unit'] ?? $piece->control_unit,
                'unit_price' => $validated['unit_price'] ?? $piece->sale_price,
                'order_id' => $validated['order_id'] ?? null,
                'channel' => $validated['channel'] ?? 'manual',
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'remaining' => $result['remaining'],
                'piece' => $result['piece'],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function transfer(Request $request, $id): JsonResponse
    {
        $piece = FabricPiece::findOrFail($id);

        if (!in_array($piece->status, ['fechada', 'aberta'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Esta peça não pode ser transferida no momento.',
            ], 400);
        }

        $validated = $request->validate([
            'to_store_id' => 'required|exists:stores,id|different:' . $piece->store_id,
            'notes' => 'nullable|string',
        ]);

        $transfer = FabricPieceTransfer::create([
            'fabric_piece_id' => $piece->id,
            'from_store_id' => $piece->store_id,
            'to_store_id' => $validated['to_store_id'],
            'requested_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'received_by' => auth()->id(),
            'status' => 'recebida',
            'requested_at' => now(),
            'approved_at' => now(),
            'shipped_at' => now(),
            'received_at' => now(),
            'request_notes' => $validated['notes'] ?? null,
        ]);

        $piece->update([
            'store_id' => $validated['to_store_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peça transferida com sucesso!',
            'transfer_id' => $transfer->id,
            'transfer' => $transfer->load(['fromStore', 'toStore']),
        ]);
    }

    public function receiveTransfer($transferId): JsonResponse
    {
        $transfer = FabricPieceTransfer::findOrFail($transferId);

        if (!in_array($transfer->status, ['aprovada', 'em_transito'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Esta transferência não pode ser recebida.',
            ], 400);
        }

        $transfer->receive();

        return response()->json([
            'success' => true,
            'message' => 'Transferência recebida com sucesso!',
        ]);
    }

    public function cancelTransfer(Request $request, $transferId): JsonResponse
    {
        $transfer = FabricPieceTransfer::findOrFail($transferId);

        if ($transfer->status === 'recebida') {
            return response()->json([
                'success' => false,
                'message' => 'Transferências recebidas não podem ser canceladas.',
            ], 400);
        }

        $transfer->cancel($request->input('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Transferência cancelada.',
        ]);
    }

    public function printTransfer($transferId): View
    {
        $transfer = FabricPieceTransfer::with(['fabricPiece.fabricType', 'fabricPiece.color', 'fromStore', 'toStore', 'requestedBy'])
            ->findOrFail($transferId);

        return view('fabric-pieces.transfer-print', compact('transfer'));
    }

    public function report(Request $request): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $storeId = $request->input('store_id');

        $pieceQuery = FabricPiece::with(['store', 'fabricType', 'color'])
            ->whereIn('store_id', $userStoreIds);

        if ($storeId) {
            $pieceQuery->where('store_id', $storeId);
        }

        $activePieces = (clone $pieceQuery)
            ->whereIn('status', ['fechada', 'aberta', 'em_transferencia'])
            ->get();

        $salesBaseQuery = FabricPieceSale::active()
            ->with(['fabricPiece.fabricType', 'fabricPiece.color', 'store', 'soldBy', 'order', 'catalogOrder'])
            ->whereIn('store_id', $storeId ? [$storeId] : $userStoreIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $salesHistory = (clone $salesBaseQuery)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $totals = [
            'total' => (clone $pieceQuery)->count(),
            'ativas' => $activePieces->count(),
            'vendidas' => (clone $pieceQuery)->where('status', 'vendida')
                ->whereBetween('sold_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'kg_ativo' => $activePieces->where('control_unit', 'kg')->sum(fn (FabricPiece $piece) => $piece->available_quantity),
            'metros_ativo' => $activePieces->where('control_unit', 'metros')->sum(fn (FabricPiece $piece) => $piece->available_quantity),
            'custo_total' => $activePieces->sum(fn (FabricPiece $piece) => $piece->available_quantity * (float) $piece->sale_price),
            'valor_vendas' => (clone $salesBaseQuery)->sum('total_price'),
            'estoque_baixo' => $activePieces->filter(fn (FabricPiece $piece) => $piece->is_below_alert)->count(),
        ];

        $byStore = [];
        foreach ($stores as $store) {
            $storePieces = $activePieces->where('store_id', $store->id);

            $byStore[] = [
                'name' => $store->name,
                'ativas' => $storePieces->count(),
                'vendidas' => (clone $pieceQuery)->where('store_id', $store->id)
                    ->where('status', 'vendida')
                    ->count(),
                'kg' => $storePieces->where('control_unit', 'kg')->sum(fn (FabricPiece $piece) => $piece->available_quantity),
                'metros' => $storePieces->where('control_unit', 'metros')->sum(fn (FabricPiece $piece) => $piece->available_quantity),
            ];
        }

        $byFabricType = $activePieces
            ->groupBy('fabric_type_id')
            ->map(function ($group) {
                $piece = $group->first();

                return [
                    'name' => $piece?->fabricType?->name ?? 'Não definido',
                    'count' => $group->count(),
                    'kg' => $group->where('control_unit', 'kg')->sum(fn (FabricPiece $row) => $row->available_quantity),
                    'metros' => $group->where('control_unit', 'metros')->sum(fn (FabricPiece $row) => $row->available_quantity),
                ];
            })
            ->values()
            ->sortByDesc('count');

        $bySupplier = $activePieces
            ->groupBy(fn (FabricPiece $piece) => $piece->supplier ?: 'Não informado')
            ->map(function ($group, $supplier) {
                return [
                    'name' => $supplier,
                    'count' => $group->count(),
                    'custo' => $group->sum(fn (FabricPiece $piece) => $piece->available_quantity * (float) $piece->sale_price),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();

        $salesByFabricType = $salesHistory
            ->groupBy(fn (FabricPieceSale $sale) => $sale->fabricPiece?->fabric_type_id ?: 'sem-tipo')
            ->map(function ($group) {
                $firstSale = $group->first();

                return [
                    'name' => $firstSale?->fabricPiece?->fabricType?->name ?? 'Não informado',
                    'quantity_label' => $this->formatSalesBreakdown($group),
                    'value' => $group->sum('total_price'),
                ];
            })
            ->values()
            ->sortByDesc('value');

        $salesByPiece = $salesHistory
            ->groupBy('fabric_piece_id')
            ->map(function ($group) {
                /** @var FabricPieceSale $firstSale */
                $firstSale = $group->first();
                $piece = $firstSale?->fabricPiece;

                return [
                    'id' => $piece?->id,
                    'fabric' => $piece?->fabricType?->name ?? 'N/A',
                    'color' => $piece?->color?->name ?? 'N/A',
                    'nf' => $piece?->invoice_number,
                    'supplier' => $piece?->supplier,
                    'quantity' => $group->sum('quantity'),
                    'unit' => $firstSale?->unit ?? ($piece?->control_unit ?? 'kg'),
                    'value' => $group->sum('total_price'),
                    'last_sale' => $group->max('created_at'),
                ];
            })
            ->sortByDesc('value')
            ->take(10)
            ->values();

        $lowStockPieces = $activePieces
            ->filter(fn (FabricPiece $piece) => $piece->is_below_alert)
            ->sortBy(fn (FabricPiece $piece) => $piece->available_quantity)
            ->values()
            ->take(12);

        $lowStockChart = [
            'labels' => $lowStockPieces->map(fn (FabricPiece $piece) => Str::limit($piece->display_name, 32))->values(),
            'current' => $lowStockPieces->map(fn (FabricPiece $piece) => round($piece->available_quantity, 3))->values(),
            'minimum' => $lowStockPieces->map(fn (FabricPiece $piece) => round((float) $piece->min_quantity_alert, 3))->values(),
        ];

        return view('fabric-pieces.report', compact(
            'stores',
            'totals',
            'byStore',
            'byFabricType',
            'bySupplier',
            'salesHistory',
            'salesByFabricType',
            'salesByPiece',
            'lowStockPieces',
            'lowStockChart'
        ));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\FabricPiecesImport(), $request->file('file'));

            return redirect()->back()->with('success', 'Importação realizada com sucesso!');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', 'Erro na importação: ' . $exception->getMessage());
        }
    }

    private function assertUnitHasInitialQuantity(array $validated): void
    {
        $controlUnit = $validated['control_unit'] ?? 'kg';

        if ($controlUnit === 'kg' && empty($validated['weight'])) {
            throw ValidationException::withMessages([
                'weight' => 'Informe o peso inicial da peça.',
            ]);
        }

        if ($controlUnit === 'metros' && empty($validated['meters'])) {
            throw ValidationException::withMessages([
                'meters' => 'Informe a metragem inicial da peça.',
            ]);
        }
    }

    private function fillTransferOrigins(array &$validated, Request $request): void
    {
        if ($request->boolean('between_stores')) {
            $validated['between_stores'] = true;

            if (!empty($validated['origin_store_id'])) {
                $validated['origin'] = Store::find($validated['origin_store_id'])?->name;
            }

            if (!empty($validated['destination_store_id'])) {
                $validated['destination'] = Store::find($validated['destination_store_id'])?->name;
            }

            return;
        }

        $validated['between_stores'] = false;
        $validated['origin_store_id'] = null;
        $validated['destination_store_id'] = null;
    }

    private function fillChannelFlags(array &$validated, Request $request): void
    {
        $validated['available_in_pdv'] = $request->boolean('available_in_pdv');
        $validated['available_in_catalog'] = $request->boolean('available_in_catalog');
        $validated['available_in_orders'] = $request->boolean('available_in_orders');
        $validated['min_quantity_alert'] = $validated['min_quantity_alert'] ?? 0;
    }

    private function fillFabricMetadata(array &$validated): void
    {
        $validated['fabric_id'] = $this->resolveFabricId($validated['fabric_type_id'] ?? null);
    }

    private function fillCurrentQuantities(array &$validated, ?FabricPiece $piece = null): void
    {
        $controlUnit = $validated['control_unit'] ?? $piece?->control_unit ?? 'kg';

        $validated['weight_current'] = $validated['weight_current']
            ?? ($controlUnit === 'kg'
                ? ($piece && $piece->control_unit === 'kg' ? $piece->weight_current : ($validated['weight'] ?? $piece?->weight ?? 0))
                : ($piece?->weight_current ?? ($validated['weight'] ?? null)));

        $validated['meters_current'] = $validated['meters_current']
            ?? ($controlUnit === 'metros'
                ? ($piece && $piece->control_unit === 'metros' ? $piece->meters_current : ($validated['meters'] ?? $piece?->meters ?? 0))
                : ($piece?->meters_current ?? ($validated['meters'] ?? null)));
    }

    private function resolveFabricId(?int $fabricTypeId): ?int
    {
        if (!$fabricTypeId) {
            return null;
        }

        $fabricType = ProductOption::find($fabricTypeId);

        if (!$fabricType) {
            return null;
        }

        if ($fabricType->type === 'tecido') {
            return (int) $fabricType->id;
        }

        $parent = $fabricType->parent;

        if (!$parent || $parent->type !== 'tecido') {
            return null;
        }

        return (int) $parent->id;
    }

    private function formatSalesBreakdown($sales): string
    {
        return $sales->groupBy('unit')
            ->map(function ($group, $unit) {
                $label = $unit === 'metros' ? 'm' : 'kg';
                return number_format((float) $group->sum('quantity'), 2, ',', '.') . ' ' . $label;
            })
            ->implode(' | ');
    }
}
