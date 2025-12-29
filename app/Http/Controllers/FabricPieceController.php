<?php

namespace App\Http\Controllers;

use App\Models\FabricPiece;
use App\Models\FabricPieceSale;
use App\Models\FabricPieceTransfer;
use App\Models\Order;
use App\Models\ProductOption;
use App\Models\Store;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FabricPieceController extends Controller
{
    /**
     * Listar peças de tecido
     */
    public function index(Request $request): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();
        
        $query = FabricPiece::with(['store', 'fabricType', 'color'])
            ->whereIn('store_id', $userStoreIds);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Por padrão, mostrar peças ativas (incluindo em transferência)
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $pieces = $query->latest()->paginate(20)->withQueryString();

        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        $stats = [
            'total' => FabricPiece::whereIn('store_id', $userStoreIds)->whereIn('status', ['fechada', 'aberta', 'em_transferencia'])->count(),
            'fechadas' => FabricPiece::whereIn('store_id', $userStoreIds)->where('status', 'fechada')->count(),
            'abertas' => FabricPiece::whereIn('store_id', $userStoreIds)->where('status', 'aberta')->count(),
            'vendidas' => FabricPiece::whereIn('store_id', $userStoreIds)->where('status', 'vendida')->count(),
            'em_transferencia' => FabricPiece::whereIn('store_id', $userStoreIds)->where('status', 'em_transferencia')->count(),
        ];

        // Stats por loja
        $storeStats = [];
        foreach ($stores as $store) {
            $count = FabricPiece::where('store_id', $store->id)->whereIn('status', ['fechada', 'aberta', 'em_transferencia'])->count();
            $storeStats[$store->id] = [
                'name' => $store->name,
                'count' => $count,
            ];
        }

        // Vendas recentes para associar na venda parcial (últimas 50 vendas PDV e pedidos)
        $recentOrders = Order::whereIn('store_id', $userStoreIds)
            ->where('is_cancelled', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function($order) {
                $prefix = $order->is_pdv ? 'PDV' : 'PED';
                $number = str_pad($order->id, 6, '0', STR_PAD_LEFT);
                $clientName = $order->client?->name ?? 'Sem cliente';
                return [
                    'id' => $order->id,
                    'label' => "[{$prefix}] #{$number} - {$clientName} - R$ " . number_format($order->total, 2, ',', '.')
                ];
            });

        return view('fabric-pieces.index', compact('pieces', 'stores', 'fabricTypes', 'colors', 'stats', 'storeStats', 'recentOrders'));
    }

    /**
     * Formulário de cadastro
     */
    public function create(): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        return view('fabric-pieces.create', compact('stores', 'fabricTypes', 'colors'));
    }

    /**
     * Salvar nova peça
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:50',
            'invoice_key' => 'nullable|string|max:44',
            'weight' => 'nullable|numeric|min:0',
            'meters' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'origin_store_id' => 'nullable|exists:stores,id',
            'destination_store_id' => 'nullable|exists:stores,id',
            'between_stores' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0', // Preço por Kg
            'received_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Se é transferência entre lojas, usar nomes das lojas como origem/destino
        if ($request->input('between_stores')) {
            $validated['between_stores'] = true;
            if ($validated['origin_store_id']) {
                $originStore = Store::find($validated['origin_store_id']);
                $validated['origin'] = $originStore ? $originStore->name : null;
            }
            if ($validated['destination_store_id']) {
                $destStore = Store::find($validated['destination_store_id']);
                $validated['destination'] = $destStore ? $destStore->name : null;
            }
        } else {
            $validated['between_stores'] = false;
            $validated['origin_store_id'] = null;
            $validated['destination_store_id'] = null;
        }

        $validated['status'] = 'fechada';
        $validated['received_at'] = $validated['received_at'] ?? now();
        $validated['purchase_price'] = 0; // Removido o suporte ao preço de compra

        FabricPiece::create($validated);

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça cadastrada com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id): View
    {
        $piece = FabricPiece::findOrFail($id);
        
        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->orderBy('name')->get();

        return view('fabric-pieces.edit', compact('piece', 'stores', 'fabricTypes', 'colors'));
    }

    /**
     * Atualizar peça
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $piece = FabricPiece::findOrFail($id);

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:50',
            'invoice_key' => 'nullable|string|max:44',
            'weight' => 'nullable|numeric|min:0',
            'weight_current' => 'nullable|numeric|min:0',
            'meters' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'sale_price' => 'nullable|numeric|min:0',
            'received_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['purchase_price'] = 0; // Removido
        $piece->update($validated);

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça atualizada com sucesso!');
    }

    /**
     * Excluir peça
     */
    public function destroy($id): RedirectResponse
    {
        $piece = FabricPiece::findOrFail($id);
        $piece->delete();

        return redirect()->route('fabric-pieces.index')
            ->with('success', 'Peça excluída com sucesso!');
    }

    /**
     * Marcar peça como aberta
     */
    public function open(Request $request, $id): JsonResponse
    {
        $piece = FabricPiece::findOrFail($id);

        if ($piece->status !== 'fechada') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas peças fechadas podem ser abertas.',
            ], 400);
        }

        $weightCurrent = $request->input('weight_current');
        $piece->markAsOpened($weightCurrent);

        return response()->json([
            'success' => true,
            'message' => 'Peça marcada como aberta!',
            'piece' => $piece->fresh(),
        ]);
    }

    /**
     * Marcar peça como vendida (inteira)
     */
    public function sell(Request $request, $id): JsonResponse
    {
        $piece = FabricPiece::findOrFail($id);

        if ($piece->status === 'vendida') {
            return response()->json([
                'success' => false,
                'message' => 'Esta peça já foi vendida.',
            ], 400);
        }

        $orderId = $request->input('order_id');
        $piece->markAsSold($orderId);

        return response()->json([
            'success' => true,
            'message' => 'Peça vendida com sucesso!',
            'piece' => $piece->fresh(),
        ]);
    }

    /**
     * Vender parte da peça (venda parcial)
     */
    public function sellPartial(Request $request, $id): JsonResponse
    {
        $piece = FabricPiece::findOrFail($id);

        if ($piece->status !== 'aberta') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas peças abertas podem ter vendas parciais.',
            ], 400);
        }

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'nullable|in:kg,metros',
            'unit_price' => 'nullable|numeric|min:0',
            'order_id' => 'nullable|exists:orders,id',
            'notes' => 'nullable|string',
        ]);

        $unit = $validated['unit'] ?? 'kg';
        $quantity = $validated['quantity'];

        // Verificar se há quantidade suficiente
        $currentQuantity = $unit === 'kg' ? $piece->weight_current : $piece->meters;
        if ($quantity > $currentQuantity) {
            return response()->json([
                'success' => false,
                'message' => "Quantidade insuficiente. Disponível: {$currentQuantity} {$unit}",
            ], 400);
        }

        // Registrar a venda
        FabricPieceSale::create([
            'fabric_piece_id' => $piece->id,
            'store_id' => $piece->store_id,
            'order_id' => $validated['order_id'] ?? null,
            'sold_by' => auth()->id(),
            'quantity' => $quantity,
            'unit' => $unit,
            'unit_price' => $validated['unit_price'] ?? $piece->sale_price,
            'total_price' => ($validated['unit_price'] ?? $piece->sale_price ?? 0) * $quantity,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Atualizar quantidade restante
        if ($unit === 'kg') {
            $piece->weight_current = $piece->weight_current - $quantity;
        } else {
            $piece->meters = $piece->meters - $quantity;
        }

        // Se acabou o estoque, marcar como vendida
        $remaining = $unit === 'kg' ? $piece->weight_current : $piece->meters;
        if ($remaining <= 0) {
            $piece->status = 'vendida';
            $piece->sold_at = now();
            $piece->sold_by = auth()->id();
        }

        $piece->save();

        return response()->json([
            'success' => true,
            'message' => "Vendido {$quantity} {$unit} com sucesso!",
            'remaining' => $remaining,
            'piece' => $piece->fresh(),
        ]);
    }

    /**
     * Transferir peça para outra loja (automático)
     */
    public function transfer(Request $request, $id): JsonResponse
    {
        $piece = FabricPiece::findOrFail($id);

        if (!in_array($piece->status, ['fechada', 'aberta'])) {
            return response()->json([
                'success' => false,
                'message' => 'Esta peça não pode ser transferida no momento.',
            ], 400);
        }

        $validated = $request->validate([
            'to_store_id' => 'required|exists:stores,id|different:' . $piece->store_id,
            'notes' => 'nullable|string',
        ]);

        $fromStoreId = $piece->store_id;
        $toStoreId = $validated['to_store_id'];

        // Criar registro de transferência
        $transfer = FabricPieceTransfer::create([
            'fabric_piece_id' => $piece->id,
            'from_store_id' => $fromStoreId,
            'to_store_id' => $toStoreId,
            'requested_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'received_by' => auth()->id(),
            'status' => 'recebida', // Transferência automática já completa
            'requested_at' => now(),
            'approved_at' => now(),
            'shipped_at' => now(),
            'received_at' => now(),
            'request_notes' => $validated['notes'] ?? null,
        ]);

        // Atualizar peça para nova loja imediatamente
        $piece->update([
            'store_id' => $toStoreId,
            // Mantém o status atual (fechada/aberta)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peça transferida com sucesso!',
            'transfer_id' => $transfer->id,
            'transfer' => $transfer->load(['fromStore', 'toStore']),
        ]);
    }

    /**
     * Confirmar recebimento de transferência
     */
    public function receiveTransfer($transferId): JsonResponse
    {
        $transfer = FabricPieceTransfer::findOrFail($transferId);

        if (!in_array($transfer->status, ['aprovada', 'em_transito'])) {
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

    /**
     * Cancelar transferência
     */
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

    /**
     * Imprimir nota de transferência
     */
    public function printTransfer($transferId): View
    {
        $transfer = FabricPieceTransfer::with(['fabricPiece.fabricType', 'fabricPiece.color', 'fromStore', 'toStore', 'requestedBy'])
            ->findOrFail($transferId);

        return view('fabric-pieces.transfer-print', compact('transfer'));
    }

    /**
     * Relatório de peças de tecido
     */
    public function report(Request $request): View
    {
        $userStoreIds = StoreHelper::getUserStoreIds();
        $stores = Store::whereIn('id', $userStoreIds)->get();

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $storeId = $request->input('store_id');

        $query = FabricPiece::whereIn('store_id', $userStoreIds);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Totais
        $totals = [
            'total' => (clone $query)->count(),
            'ativas' => (clone $query)->whereIn('status', ['fechada', 'aberta'])->count(),
            'vendidas' => (clone $query)->where('status', 'vendida')
                ->whereBetween('sold_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count(),
            'peso_total' => (clone $query)->whereIn('status', ['fechada', 'aberta'])->sum('weight') ?? 0,
            'custo_total' => (clone $query)->whereIn('status', ['fechada', 'aberta'])->get()->sum(fn($p) => $p->weight * (float)$p->sale_price),
            'valor_vendas' => (clone $query)->where('status', 'vendida')
                ->whereBetween('sold_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum('sale_price') ?? 0,
        ];

        // Por Loja
        $byStore = [];
        foreach ($stores as $store) {
            $storeQuery = FabricPiece::where('store_id', $store->id);
            $byStore[] = [
                'name' => $store->name,
                'ativas' => (clone $storeQuery)->whereIn('status', ['fechada', 'aberta'])->count(),
                'vendidas' => (clone $storeQuery)->where('status', 'vendida')->count(),
                'peso' => (clone $storeQuery)->whereIn('status', ['fechada', 'aberta'])->sum('weight') ?? 0,
            ];
        }

        // Por Tipo de Tecido
        $byFabricType = FabricPiece::whereIn('store_id', $userStoreIds)
            ->selectRaw('fabric_type_id, COUNT(*) as count, SUM(weight) as peso')
            ->groupBy('fabric_type_id')
            ->get()
            ->map(function ($item) {
                $fabricType = $item->fabric_type_id ? ProductOption::find($item->fabric_type_id) : null;
                return [
                    'name' => $fabricType?->name,
                    'count' => $item->count,
                    'peso' => $item->peso ?? 0,
                ];
            });

        // Por Fornecedor
        $bySupplier = FabricPiece::whereIn('store_id', $userStoreIds)
            ->selectRaw('supplier, COUNT(*) as count, SUM(sale_price * weight) as custo_estimado')
            ->groupBy('supplier')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->supplier,
                    'count' => $item->count,
                    'custo' => $item->custo_estimado ?? 0,
                ];
            });

        // Histórico de Vendas (Individual)
        $salesHistory = FabricPieceSale::with(['fabricPiece.fabricType', 'fabricPiece.color', 'store', 'soldBy', 'order'])
            ->whereIn('store_id', $storeId ? [$storeId] : $userStoreIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Vendas Agrupadas por Tipo de Tecido
        $salesByFabricType = FabricPieceSale::join('fabric_pieces', 'fabric_piece_sales.fabric_piece_id', '=', 'fabric_pieces.id')
            ->whereIn('fabric_piece_sales.store_id', $storeId ? [$storeId] : $userStoreIds)
            ->whereBetween('fabric_piece_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('fabric_pieces.fabric_type_id, SUM(fabric_piece_sales.quantity) as total_kg, SUM(fabric_piece_sales.total_price) as total_value')
            ->groupBy('fabric_pieces.fabric_type_id')
            ->get()
            ->map(function ($item) {
                $fabricType = $item->fabric_type_id ? ProductOption::find($item->fabric_type_id) : null;
                return [
                    'name' => $fabricType?->name ?? 'NÃO INFORMADO',
                    'kg' => $item->total_kg,
                    'value' => $item->total_value
                ];
            });

        // Vendas Agrupadas por Peça
        $salesByPiece = FabricPieceSale::join('fabric_pieces', 'fabric_piece_sales.fabric_piece_id', '=', 'fabric_pieces.id')
            ->whereIn('fabric_piece_sales.store_id', $storeId ? [$storeId] : $userStoreIds)
            ->whereBetween('fabric_piece_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('fabric_pieces.id, fabric_pieces.fabric_type_id, fabric_pieces.color_id, fabric_pieces.invoice_number, fabric_pieces.supplier, SUM(fabric_piece_sales.quantity) as total_kg, SUM(fabric_piece_sales.total_price) as total_value, MAX(fabric_piece_sales.created_at) as last_sale')
            ->groupBy('fabric_pieces.id', 'fabric_pieces.fabric_type_id', 'fabric_pieces.color_id', 'fabric_pieces.invoice_number', 'fabric_pieces.supplier')
            ->orderByDesc('total_kg')
            ->get()
            ->map(function ($item) {
                $fabricType = $item->fabric_type_id ? ProductOption::find($item->fabric_type_id) : null;
                $color = $item->color_id ? ProductOption::find($item->color_id) : null;
                return [
                    'id' => $item->id,
                    'fabric' => $fabricType?->name ?? 'N/A',
                    'color' => $color?->name ?? 'N/A',
                    'nf' => $item->invoice_number,
                    'supplier' => $item->supplier,
                    'kg' => $item->total_kg,
                    'value' => $item->total_value,
                    'last_sale' => $item->last_sale
                ];
            });

        return view('fabric-pieces.report', compact(
            'stores', 'totals', 'byStore', 'byFabricType', 'bySupplier', 'salesHistory', 'salesByFabricType', 'salesByPiece'
        ));
    }
    /**
     * Importar via Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\FabricPiecesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Importação realizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro na importação: ' . $e->getMessage());
        }
    }
}
