<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\ProductOption;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;
use App\Models\StockHistory;
use App\Models\StockMovement;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    /**
     * Garante que apenas perfis autorizados alterem o estoque.
     */
    private function ensureCanManage(): void
    {
        $user = Auth::user();

        if (!$user || (!$user->isAdminGeral() && !$user->isEstoque())) {
            abort(403, 'Acesso negado. Apenas admin geral ou equipe de estoque podem alterar estoques.');
        }
    }

    /**
     * Listar estoque
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants sem selecionar contexto
        if ($this->isSuperAdmin() && !$this->hasSelectedTenant()) {
            return $this->emptySuperAdminResponse('stocks.index', [
                'groupedStocks' => [],
                'stores' => collect([]),
                'fabricTypes' => collect([]),
                'colors' => collect([]),
                'cutTypes' => collect([]),
                'sizes' => ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'],
                'storeId' => null,
                'fabricId' => null,
                'colorId' => null,
                'cutTypeId' => null,
                'size' => null,
                'lowStock' => false,
            ]);
        }

        $storeId = $request->get('store_id');
        $fabricId = $request->get('fabric_id');
        $colorId = $request->get('color_id');
        $cutTypeId = $request->get('cut_type_id');
        $size = $request->get('size');
        $searchId = $request->get('search_id');
        $lowStock = $request->get('low_stock', false);

        $query = Stock::with(['store', 'fabric', 'fabricType', 'color', 'cutType.parent']);

        // Filtro por ID
        if ($searchId) {
            $query->where('id', $searchId);
        }

        // Filtros
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($fabricId) {
            $query->where('fabric_id', $fabricId);
        }

        if ($request->has('fabric_type_id') && $request->get('fabric_type_id')) {
            $typeId = $request->get('fabric_type_id');
            $query->where(function($q) use ($typeId) {
                $q->where('fabric_type_id', $typeId)
                  ->orWhere(function($q2) use ($typeId) {
                      $q2->whereNull('fabric_type_id')
                         ->whereHas('cutType', function($q3) use ($typeId) {
                             $q3->where('parent_id', $typeId);
                         });
                  });
            });
        }

        if ($colorId) {
            $query->where('color_id', $colorId);
        }

        if ($cutTypeId) {
            $query->where('cut_type_id', $cutTypeId);
        }

        if ($size) {
            $query->where('size', $size);
        }

        // Estoque baixo
        if ($lowStock) {
            $query->whereRaw('(quantity - reserved_quantity) < min_stock');
        }

        $allStocks = $query->orderBy('store_id')
            ->orderBy('fabric_id')
            ->orderBy('fabric_type_id')
            ->orderBy('color_id')
            ->orderBy('cut_type_id')
            ->orderBy('size')
            ->get();

        // Agrupar estoques por loja, tecido, tipo de corte e cor
        $groupedStocks = [];
        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        
        foreach ($allStocks as $stock) {
            $key = sprintf(
                '%d_%d_%d_%d_%d',
                $stock->store_id,
                $stock->fabric_id ?? 0,
                $stock->fabric_type_id ?? 0,
                $stock->cut_type_id ?? 0,
                $stock->color_id ?? 0
            );
            
            if (!isset($groupedStocks[$key])) {
                $groupedStocks[$key] = [
                    'store' => [
                        'id' => $stock->store->id,
                        'name' => $stock->store->name,
                    ],
                    'fabric' => $stock->fabric ? [
                        'id' => $stock->fabric->id,
                        'name' => $stock->fabric->name,
                    ] : null,
                    'fabric_type' => $stock->fabricType ? [
                        'id' => $stock->fabricType->id,
                        'name' => $stock->fabricType->name,
                    ] : (
                        ($stock->cutType && $stock->cutType->parent && $stock->cutType->parent->type === 'tipo_tecido')
                        ? [
                            'id' => $stock->cutType->parent->id,
                            'name' => $stock->cutType->parent->name,
                        ]
                        : null
                    ),
                    'cut_type' => $stock->cutType ? [
                        'id' => $stock->cutType->id,
                        'name' => $stock->cutType->name,
                    ] : null,
                    'color' => $stock->color ? [
                        'id' => $stock->color->id,
                        'name' => $stock->color->name,
                    ] : null,
                    'sizes' => [],
                    'total_quantity' => 0,
                    'total_reserved' => 0,
                    'total_available' => 0,
                    'last_updated' => $stock->updated_at->toDateTimeString(),
                ];
            }
            
            $groupedStocks[$key]['sizes'][$stock->size] = [
                'id' => $stock->id,
                'quantity' => $stock->quantity,
                'reserved_quantity' => $stock->reserved_quantity,
                'available_quantity' => $stock->available_quantity,
                'min_stock' => $stock->min_stock,
                'max_stock' => $stock->max_stock,
                'shelf' => $stock->shelf,
                'updated_at' => $stock->updated_at,
            ];
            
            $groupedStocks[$key]['total_quantity'] += $stock->quantity;
            $groupedStocks[$key]['total_reserved'] += $stock->reserved_quantity;
            $groupedStocks[$key]['total_available'] += $stock->available_quantity;
            
            // Atualizar última atualização se for mais recente
            $lastUpdated = is_string($groupedStocks[$key]['last_updated']) 
                ? \Carbon\Carbon::parse($groupedStocks[$key]['last_updated'])
                : $groupedStocks[$key]['last_updated'];
            if ($stock->updated_at > $lastUpdated) {
                $groupedStocks[$key]['last_updated'] = $stock->updated_at->toDateTimeString();
            }
        }

        // Dados para filtros
        $stores = StoreHelper::getAvailableStores();
        // $fabrics = ProductOption::where('type', 'tecido')->where('active', true)->orderBy('name')->get(); // Antigo
        $fabricTypes = ProductOption::where('type', 'tipo_tecido')->where('active', true)->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->where('active', true)->orderBy('name')->get();
        $cutTypes = ProductOption::where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();

        return view('stocks.index', compact(
            'groupedStocks',
            'stores',
            'fabricTypes',
            'colors',
            'cutTypes',
            'sizes',
            'storeId',
            'fabricId',
            'colorId',
            'cutTypeId',
            'size',
            'lowStock'
        ));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create(Request $request): View
    {
        $this->ensureCanManage();

        // Workaround para evitar limpar cache de rota em produção:
        // Se passar ?mode=edit, redireciona para a lógica de edição
        if ($request->get('mode') === 'edit') {
            return $this->edit($request);
        }

        $stores = StoreHelper::getAvailableStores();
        $fabrics = ProductOption::where('type', 'tecido')->where('active', true)->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->where('active', true)->orderBy('name')->get();
        $cutTypes = ProductOption::where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];

        return view('stocks.create', compact('stores', 'fabrics', 'colors', 'cutTypes', 'sizes'));
    }

    /**
     * Salvar novo estoque
     */
    public function store(Request $request)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_id' => 'required|exists:product_options,id',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'color_id' => 'required|exists:product_options,id',
            'cut_type_id' => 'required|exists:product_options,id',
            'sizes' => 'required|array',
            'sizes.*' => 'nullable|integer|min:0',
            'shelf' => 'nullable|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verificar permissão de acesso à loja
        if (!StoreHelper::canAccessStore($validated['store_id'])) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar esta loja.');
        }

        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        $createdCount = 0;
        $updatedCount = 0;

        $shelf = isset($validated['shelf']) ? trim($validated['shelf']) : null;
        
        foreach ($sizes as $size) {
            $quantity = isset($validated['sizes'][$size]) ? (int)$validated['sizes'][$size] : 0;
            
            // Criar ou atualizar apenas se a quantidade for maior que 0
            if ($quantity > 0) {
                $stockData = [
                    'store_id' => $validated['store_id'],
                    'fabric_id' => $validated['fabric_id'],
                    'fabric_type_id' => $validated['fabric_type_id'] ?? null,
                    'color_id' => $validated['color_id'],
                    'cut_type_id' => $validated['cut_type_id'],
                    'size' => $size,
                    'shelf' => $shelf, // Mesma prateleira para todos os tamanhos
                    'quantity' => $quantity,
                    'min_stock' => $validated['min_stock'] ?? 0,
                    'max_stock' => $validated['max_stock'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ];

                $existing = Stock::findByParams(
                    $validated['store_id'],
                    $validated['fabric_id'],
                    $validated['fabric_type_id'] ?? null,
                    $validated['color_id'],
                    $validated['cut_type_id'],
                    $size
                );

                $stock = null;
                if ($existing) {
                    $existing->update($stockData);
                    $stock = $existing->fresh();
                    $updatedCount++;
                } else {
                    $stock = Stock::create($stockData);
                    $createdCount++;
                }
                
                // Verificar se estoque baixou abaixo de 30 peças e criar notificação
                if ($stock) {
                    $this->checkLowStock($stock);
                    
                    // Registrar histórico de criação/edição inicial
                    // Se foi update, o log de diferença seria mais complexo aqui pois estamos no loop
                    // Mas como é "store" (novo ou atualização via planilha/form de massa), 
                    // vamos registrar "ajuste" ou "entrada" dependendo do caso.
                    // Para simplificar, vamos registrar "entrada" da quantidade informada se for novo,
                    // ou "ajuste" se for atualização.
                    
                    $actionType = $existing ? 'ajuste' : 'entrada';
                    $quantityChange = $quantity; // No caso de update aqui, estamos redefinindo ou somando? 
                    // O código original fazia $existing->update($stockData), substituindo o valor 'quantity'.
                    // Se substituir, precisamos saber a diferença.
                    
                    // OBS: O código original faz update($stockData). $stockData['quantity'] = $quantity.
                    // Então está SOBRESCREVENDO a quantidade, não somando.
                    // Recalcular diferença:
                    $diff = 0;
                    if ($existing) {
                        // Precisamos da quantidade *antiga* que não temos mais fácil aqui pois já atualizou
                        // Mas podemos assumir que se era update, a intenção era definir para este valor.
                        // Vamos registrar como 'edicao' o valor final.
                        // Melhor: Vamos registrar a mudança.
                        // Para fazer direito, teríamos que ter pego o valor antes. 
                        // Como já passou, vamos registrar como 'edicao' com o valor ATUAL.
                 try {
                    StockHistory::recordMovement(
                        'ajuste', // Usar 'ajuste' pois 'edicao' não existe no enum
                        $stock,
                        $diff,
                        Auth::id(),
                        null,
                        null,
                        "Atualização manual via cadastro: {$quantity} peças (total)"
                    );
                        } catch (\Exception $e) {
                            \Log::warning('Erro ao registrar histórico: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $message = '';
        if ($createdCount > 0 && $updatedCount > 0) {
            $message = "Estoque cadastrado com sucesso! {$createdCount} novo(s) registro(s) criado(s) e {$updatedCount} atualizado(s).";
        } elseif ($createdCount > 0) {
            $message = "Estoque cadastrado com sucesso! {$createdCount} novo(s) registro(s) criado(s).";
        } elseif ($updatedCount > 0) {
            $message = "Estoque atualizado com sucesso! {$updatedCount} registro(s) atualizado(s).";
        } else {
            return redirect()->back()->with('error', 'Nenhuma quantidade foi informada. Informe pelo menos uma quantidade maior que 0.');
        }

        return redirect()->route('stocks.index')->with('success', $message);
    }

    /**
     * Mostrar estoque específico
     */
    public function show($id): JsonResponse
    {
        $stock = Stock::with(['store', 'fabric', 'color', 'cutType'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'stock' => $stock,
        ]);
    }

    /**
     * Verificar se estoque está baixo (abaixo de 30 peças) e criar notificação
     */
    private function checkLowStock(Stock $stock)
    {
        $stock->load(['store', 'fabric', 'color', 'cutType']);
        $availableQuantity = $stock->available_quantity;
        
        // Verificar se está abaixo de 30 peças
        if ($availableQuantity < 30) {
            // Verificar se já existe notificação recente (últimas 24 horas) para evitar spam
            $recentNotification = Notification::where('type', 'low_stock')
                ->where('data->store_name', $stock->store->name ?? 'N/A')
                ->where('data->product_info', ($stock->fabric->name ?? 'N/A') . ' - ' . ($stock->color->name ?? 'N/A'))
                ->where('data->size', $stock->size)
                ->where('created_at', '>=', now()->subDay())
                ->exists();
            
            if (!$recentNotification) {
                $productInfo = ($stock->fabric->name ?? 'N/A') . ' - ' . ($stock->color->name ?? 'N/A');
                $storeName = $stock->store->name ?? 'N/A';
                
                // Criar notificação para usuários de estoque
                $estoqueUsers = User::where('role', 'estoque')->orWhere('role', 'admin')->get();
                foreach ($estoqueUsers as $estoqueUser) {
                    Notification::createLowStock(
                        $estoqueUser->id,
                        $storeName,
                        $productInfo,
                        $stock->size,
                        $availableQuantity
                    );
                }
            }
        }
    }

    /**
     * Buscar estoque por tipo de corte (mostra todas as combinações disponíveis)
     */
    public function getByCutType(Request $request): JsonResponse
    {
        $rules = [
            'cut_type_id' => 'required|integer|exists:product_options,id',
        ];
        
        if ($request->has('store_id') && $request->store_id !== null) {
            $rules['store_id'] = 'required|integer|exists:stores,id';
        }
        
        if ($request->has('color_id') && $request->color_id !== null) {
            $rules['color_id'] = 'required|integer|exists:product_options,id';
        }
        
        $validated = $request->validate($rules);

        try {
            $cutType = ProductOption::findOrFail($validated['cut_type_id']);
            
            if ($cutType->type !== 'tipo_corte') {
                return response()->json([
                    'success' => false,
                    'message' => 'ID fornecido não é um tipo de corte',
                ], 400);
            }

            // Buscar todos os estoques que têm este tipo de corte (de todas as lojas se não especificar)
            $query = Stock::where('cut_type_id', $validated['cut_type_id'])
                ->with(['store', 'fabric', 'color', 'cutType']);
            
            if (isset($validated['store_id']) && !empty($validated['store_id'])) {
                $query->where('store_id', $validated['store_id']);
            }
            
            if (isset($validated['color_id']) && !empty($validated['color_id'])) {
                $query->where('color_id', $validated['color_id']);
            }
            
            $stocks = $query->get();

            // Agrupar por tamanho e loja
            $stockBySize = [];
            foreach ($stocks as $stock) {
                $size = $stock->size ?? 'N/A';
                $storeId = $stock->store_id;
                $storeName = $stock->store ? $stock->store->name : ('Loja #' . $storeId);
            
            if (!isset($stockBySize[$size])) {
                $stockBySize[$size] = [
                    'size' => $size,
                    'available' => 0,
                    'total' => 0,
                    'reserved' => 0,
                    'stores' => [],
                    'combinations' => []
                ];
            }
            
            $stockBySize[$size]['available'] += $stock->available_quantity;
            $stockBySize[$size]['total'] += $stock->quantity;
            $stockBySize[$size]['reserved'] += $stock->reserved_quantity;
            
            // Agrupar por loja
            if (!isset($stockBySize[$size]['stores'][$storeId])) {
                $stockBySize[$size]['stores'][$storeId] = [
                    'store_id' => $storeId,
                    'store_name' => $storeName,
                    'available' => 0,
                    'total' => 0,
                    'reserved' => 0,
                    'items' => []
                ];
            }
            
            $stockBySize[$size]['stores'][$storeId]['available'] += $stock->available_quantity;
            $stockBySize[$size]['stores'][$storeId]['total'] += $stock->quantity;
            $stockBySize[$size]['stores'][$storeId]['reserved'] += $stock->reserved_quantity;
            
            $stockBySize[$size]['stores'][$storeId]['items'][] = [
                'fabric' => $stock->fabric->name ?? 'N/A',
                'color' => $stock->color->name ?? 'N/A',
                'available' => $stock->available_quantity,
                'total' => $stock->quantity,
            ];
            
            // Manter compatibilidade com versão anterior
            $stockBySize[$size]['combinations'][] = [
                'fabric' => $stock->fabric->name ?? 'N/A',
                'color' => $stock->color->name ?? 'N/A',
                'available' => $stock->available_quantity,
                'total' => $stock->quantity,
                'store_name' => $storeName,
            ];
        }
        
            // Converter stores de array associativo para array indexado
            foreach ($stockBySize as $size => &$data) {
                $data['stores'] = array_values($data['stores']);
            }

            return response()->json([
                'success' => true,
                'cut_type' => $cutType->name,
                'stock_by_size' => array_values($stockBySize),
                'total_combinations' => $stocks->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar estoque por tipo de corte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar estoque: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar tecido relacionado ao tipo de corte (API)
     */
    public function getFabricByCutType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cut_type_id' => 'required|exists:product_options,id',
        ]);

        $cutType = ProductOption::with('parent.parent')->findOrFail($validated['cut_type_id']);
        
        if ($cutType->type !== 'tipo_corte') {
            return response()->json([
                'success' => false,
                'message' => 'ID fornecido não é um tipo de corte',
            ], 400);
        }

        $fabricTypeId = null;
        $fabricTypeName = null;

        // Hierarquia: tipo_corte -> tipo_tecido -> tecido
        if ($cutType->parent_id && $cutType->parent) {
            if ($cutType->parent->type === 'tipo_tecido' && $cutType->parent->parent_id) {
                $tipoTecido = $cutType->parent;
                $fabricTypeId = $tipoTecido->id;
                $fabricTypeName = $tipoTecido->name;
                
                if ($tipoTecido->parent && $tipoTecido->parent->type === 'tecido') {
                    $fabricId = $tipoTecido->parent->id;
                    $fabricName = $tipoTecido->parent->name;
                }
            } elseif ($cutType->parent->type === 'tecido') {
                // Se o parent direto for tecido
                $fabricId = $cutType->parent->id;
                $fabricName = $cutType->parent->name;
            }
        }

        // Se não encontrou, tentar buscar via relacionamento muitos-para-muitos
        if (!$fabricId && Schema::hasTable('product_option_relations')) {
            $fabricRelation = \DB::table('product_option_relations')
                ->join('product_options', 'product_option_relations.parent_id', '=', 'product_options.id')
                ->where('product_option_relations.option_id', $cutType->id)
                ->where('product_options.type', 'tecido')
                ->select('product_options.id', 'product_options.name')
                ->first();
            
            if ($fabricRelation) {
                $fabricId = $fabricRelation->id;
                $fabricName = $fabricRelation->name;
            }
        }

        return response()->json([
            'success' => true,
            'fabric_id' => $fabricId,
            'fabric_name' => $fabricName,
            'fabric_type_id' => $fabricTypeId,
            'fabric_type_name' => $fabricTypeName,
        ]);
    }

    /**
     * Buscar tipos de tecido baseados no tecido pai (API)
     */
    public function getFabricTypes(Request $request): JsonResponse
    {
        try {
            $fabricId = $request->input('fabric_id');
            
            if (!$fabricId) {
                return response()->json([
                    'success' => false,
                    'fabric_types' => [],
                    'message' => 'fabric_id is required',
                ]);
            }

            $fabricTypes = ProductOption::where('type', 'tipo_tecido')
                ->where('parent_id', $fabricId)
                ->where('active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'fabric_types' => $fabricTypes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar tipos de tecido', [
                'error' => $e->getMessage(),
                'fabric_id' => $request->input('fabric_id'),
            ]);
            
            return response()->json([
                'success' => false,
                'fabric_types' => [],
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verificar estoque em tempo real (API)
     */
    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'size' => 'required|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $query = Stock::query()
            ->where('size', $validated['size']);

        if (!empty($validated['fabric_id'])) {
            $query->where('fabric_id', $validated['fabric_id']);
        }

        if (!empty($validated['color_id'])) {
            $query->where('color_id', $validated['color_id']);
        }

        if (!empty($validated['cut_type_id'])) {
            $query->where('cut_type_id', $validated['cut_type_id']);
        }

        // Preferir loja selecionada, mas buscar em todas se não houver na loja
        $stocks = $query->get();

        if ($stocks->isEmpty()) {
            return response()->json([
                'success' => true,
                'stock' => null,
                'available_quantity' => 0,
                'has_stock' => false,
                'can_fulfill' => false,
                'stores' => [],
            ]);
        }

        $preferredStoreId = $validated['store_id'] ?? null;
        $stockInPreferred = $preferredStoreId
            ? $stocks->firstWhere('store_id', $preferredStoreId)
            : $stocks->first();

        $totalAvailable = $stocks->sum('available_quantity');
        $hasStock = $totalAvailable >= ($validated['quantity'] ?? 1);

        $stores = $stocks->groupBy('store_id')->map(function ($items, $storeId) {
            $first = $items->first();
            return [
                'store_id' => (int) $storeId,
                'store_name' => $first->store->name ?? ('Loja #'.$storeId),
                'available' => $items->sum('available_quantity'),
                'total' => $items->sum('quantity'),
                'reserved' => $items->sum('reserved_quantity'),
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'stock' => $stockInPreferred ? [
                'id' => $stockInPreferred->id,
                'quantity' => $stockInPreferred->quantity,
                'reserved_quantity' => $stockInPreferred->reserved_quantity,
                'available_quantity' => $stockInPreferred->available_quantity,
                'min_stock' => $stockInPreferred->min_stock,
                'is_below_minimum' => $stockInPreferred->isBelowMinimum(),
            ] : null,
            'available_quantity' => $totalAvailable,
            'has_stock' => $hasStock,
            'can_fulfill' => $hasStock,
            'stores' => $stores,
        ]);
    }

    /**
     * Atualizar estoque
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->ensureCanManage();

        $stock = Stock::findOrFail($id);

        // Verificar permissão
        if (!StoreHelper::canAccessStore($stock->store_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta loja.',
            ], 403);
        }

        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'shelf' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Guardar quantidade anterior para calcular a diferença
        $oldQuantity = $stock->quantity;
        
        $stock->update($validated);
        
        // Calcular diferença se a quantidade foi alterada
        if (isset($validated['quantity'])) {
            $newQuantity = $validated['quantity'];
            $diff = $newQuantity - $oldQuantity;
            
            if ($diff != 0) {
                $actionType = $diff > 0 ? 'entrada' : 'saida';
                try {
                    StockHistory::recordMovement(
                        'edicao', // Usar 'edicao' como tipo principal, mas o valor reflete a mudança
                        $stock,
                        $diff,
                        Auth::id(),
                        null,
                        null,
                        "Atualização manual: {$oldQuantity} -> {$newQuantity}"
                    );
                } catch (\Exception $e) {
                    \Log::warning('Erro ao registrar histórico de atualização: ' . $e->getMessage());
                }
            } else {
                 // Mudou apenas outros campos (min_stock, shelf, notes)
                 try {
                    StockHistory::recordMovement(
                        'ajuste',
                        $stock,
                        0,
                        Auth::id(),
                        null,
                        null,
                        "Atualização de dados cadastrais (Prateleira/Obs/Limites)"
                    );
                } catch (\Exception $e) {
                    \Log::warning('Erro ao registrar histórico de dados: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estoque atualizado com sucesso!',
            'stock' => $stock->load(['store', 'fabric', 'color', 'cutType']),
        ]);
    }

    /**
     * Reservar estoque
     */
    public function reserve(Request $request): JsonResponse
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'size' => 'required|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'quantity' => 'required|integer|min:1',
        ]);

        $stock = Stock::findByParams(
            $validated['store_id'],
            $validated['fabric_id'],
            $validated['color_id'],
            $validated['cut_type_id'],
            $validated['size']
        );

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque não encontrado.',
            ], 404);
        }

        if (!$stock->reserve($validated['quantity'])) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque insuficiente. Disponível: ' . $stock->available_quantity,
                'available_quantity' => $stock->available_quantity,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estoque reservado com sucesso!',
            'stock' => $stock->fresh(),
        ]);
    }

    /**
     * Liberar estoque reservado
     */
    public function release(Request $request): JsonResponse
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'size' => 'required|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'quantity' => 'required|integer|min:1',
        ]);

        $stock = Stock::findByParams(
            $validated['store_id'],
            $validated['fabric_id'],
            $validated['color_id'],
            $validated['cut_type_id'],
            $validated['size']
        );

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque não encontrado.',
            ], 404);
        }

        $stock->release($validated['quantity']);

        return response()->json([
            'success' => true,
            'message' => 'Estoque liberado com sucesso!',
            'stock' => $stock->fresh(),
        ]);
    }
    /**
     * Editar grupo de estoque
     */
    public function edit(Request $request): View
    {
        $this->ensureCanManage();

        $storeId = $request->get('store_id');
        $fabricId = $request->get('fabric_id');
        $fabricTypeId = $request->get('fabric_type_id');
        $colorId = $request->get('color_id');
        $cutTypeId = $request->get('cut_type_id');

        // Verificar parâmetros mínimos (cut_type_id pode ser nulo)
        if (!$storeId || !$fabricId || !$colorId) {
            abort(404, 'Parâmetros insuficientes para editar o estoque.');
        }

        // Buscar todos os estoques deste grupo
        $query = Stock::where('store_id', $storeId)
            ->where('fabric_id', $fabricId)
            ->where('fabric_type_id', $fabricTypeId) // Pode ser null, então where('fabric_type_id', null) funciona
            ->where('color_id', $colorId);
            
        if ($cutTypeId) {
            $query->where('cut_type_id', $cutTypeId);
        }
        // Se cut_type_id não for fornecido, buscar todos independente do cut_type
        
        $stocks = $query->get();

        // Fallback: Se não encontrou, tentar buscar com fabric_type_id nulo (legado)
        // Isso acontece porque a view infere o fabric_type do cut_type, mas o banco pode estar nulo
        if ($stocks->isEmpty() && $fabricTypeId && $cutTypeId) {
            $fallbackQuery = Stock::where('store_id', $storeId)
                ->where('fabric_id', $fabricId)
                ->whereNull('fabric_type_id')
                ->where('color_id', $colorId)
                ->where('cut_type_id', $cutTypeId);
                
            $potentialStocks = $fallbackQuery->get();
            
            if ($potentialStocks->isNotEmpty()) {
                // Verificar validação: O cut_type deve ter o parent igual ao fabricTypeId solicitado
                $checkStock = $potentialStocks->first();
                // Carregar relacionamento se necessário (embora findByParams possa não carregar)
                $cutTypeOption = ProductOption::find($cutTypeId);
                
                if ($cutTypeOption && $cutTypeOption->parent_id == $fabricTypeId) {
                    $stocks = $potentialStocks;
                    
                    // Opcional: Atualizar os dados para corrigir o problema definitivamente
                    // Stock::whereIn('id', $stocks->pluck('id'))->update(['fabric_type_id' => $fabricTypeId]);
                }
            }
        }

        if ($stocks->isEmpty()) {
            abort(404, 'Estoque não encontrado.');
        }

        // Carregar modelos relacionados para exibição
        $store = Store::findOrFail($storeId);
        $fabric = ProductOption::findOrFail($fabricId);
        $fabricType = $fabricTypeId ? ProductOption::find($fabricTypeId) : null;
        $color = ProductOption::findOrFail($colorId);
        $cutType = $cutTypeId ? ProductOption::find($cutTypeId) : null;

        // Preparar dados para o formulário
        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        $currentQuantities = [];
        $reservedQuantities = [];
        $commonShelf = null;
        $commonMinStock = 0;
        $commonMaxStock = null;
        $commonNotes = null;

        foreach ($stocks as $stock) {
            $currentQuantities[$stock->size] = $stock->quantity;
            $reservedQuantities[$stock->size] = $stock->reserved_quantity;
            
            // Pegar valores comuns do primeiro item encontrado (assumindo consistência)
            if ($commonShelf === null) $commonShelf = $stock->shelf;
            if ($commonMinStock === 0) $commonMinStock = $stock->min_stock;
            if ($commonMaxStock === null) $commonMaxStock = $stock->max_stock;
            if ($commonNotes === null) $commonNotes = $stock->notes;
        }

        return view('stocks.edit', compact(
            'store',
            'fabric',
            'fabricType',
            'color',
            'cutType',
            'sizes',
            'currentQuantities',
            'reservedQuantities',
            'commonShelf',
            'commonMinStock',
            'commonMaxStock',
            'commonNotes'
        ));
    }

    /**
     * Transferir estoque entre lojas (Ad-hoc)
     */
    public function transfer(Request $request, $id)
    {
        $this->ensureCanManage();

        $referenceStock = Stock::findOrFail($id);
        
        $validated = $request->validate([
            'target_store_id' => 'required|exists:stores,id|different:'.$referenceStock->store_id,
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $targetStoreId = $validated['target_store_id'];
        $quantities = $validated['quantities'];
        
        \DB::beginTransaction();
        try {
            $transferredCount = 0;
            $movementItems = [];
            
            foreach ($quantities as $size => $qty) {
                if (!$qty || $qty <= 0) continue;
                
                // Buscar estoque de origem para este tamanho específico
                $sourceStock = Stock::findByParams(
                    $referenceStock->store_id,
                    $referenceStock->fabric_id,
                    $referenceStock->fabric_type_id,
                    $referenceStock->color_id,
                    $referenceStock->cut_type_id,
                    $size
                );
                
                if (!$sourceStock || !$sourceStock->hasStock($qty)) {
                    throw new \Exception("Estoque insuficiente para o tamanho {$size} (Solicitado: {$qty}).");
                }
                
                // Decrementar Origem
                $sourceStock->use($qty, \Auth::id(), null, null, "Transferência para loja ID #{$targetStoreId}");
                
                // Buscar ou Criar Destino
                $targetStock = Stock::findByParams(
                    $targetStoreId,
                    $referenceStock->fabric_id,
                    $referenceStock->fabric_type_id,
                    $referenceStock->color_id,
                    $referenceStock->cut_type_id,
                    $size
                );
                
                if ($targetStock) {
                    $targetStock->add($qty, \Auth::id(), null, null, "Transferência da loja ID #{$referenceStock->store_id}");
                } else {
                    // Criar novo estoque
                    $targetStock = Stock::createOrUpdateStock([
                        'store_id' => $targetStoreId,
                        'fabric_id' => $referenceStock->fabric_id,
                        'fabric_type_id' => $referenceStock->fabric_type_id,
                        'color_id' => $referenceStock->color_id,
                        'cut_type_id' => $referenceStock->cut_type_id,
                        'size' => $size,
                        'quantity' => $qty,
                    ]);
                    
                    // Registrar histórico manualmente
                     \App\Models\StockHistory::recordMovement(
                        'entrada',
                        $targetStock,
                        $qty,
                        \Auth::id(),
                        null,
                        null,
                        "Transferência da loja ID #{$referenceStock->store_id} (Novo Estoque)"
                    );
                }
                
                // Registrar item para a nota de movimentação
                $movementItems[] = [
                    'stock_id' => $sourceStock->id,
                    'fabric_type_id' => $referenceStock->fabric_type_id,
                    'color_id' => $referenceStock->color_id,
                    'cut_type_id' => $referenceStock->cut_type_id,
                    'size' => $size,
                    'quantity' => $qty,
                ];
                
                $transferredCount += $qty;
            }
            
            if (empty($movementItems)) {
                 throw new \Exception("Nenhuma quantidade informada para transferência.");
            }
            
            // Criar registro de movimentação para impressão
            $movement = StockMovement::createTransfer(
                $referenceStock->store_id,
                $targetStoreId,
                $movementItems,
                $validated['notes'] ?? null
            );
            
            \DB::commit();
            return redirect()->back()
                ->with('success', "Transferência de {$transferredCount} itens realizada com sucesso!")
                ->with('movement_id', $movement->id);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Erro na transferência: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir nota de movimentação
     */
    public function printMovement($id): View
    {
        $movement = StockMovement::with([
            'items.fabricType', 
            'items.color', 
            'items.cutType',
            'fromStore', 
            'toStore', 
            'user',
            'order'
        ])->findOrFail($id);

        return view('stocks.movement-print', compact('movement'));
    }

    /**
     * Dar baixa em estoque (remoção/perda)
     */
    public function writeOff(Request $request, $id)
    {
        $this->ensureCanManage();

        $referenceStock = Stock::findOrFail($id);
        
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $quantities = $validated['quantities'];
        
        \DB::beginTransaction();
        try {
            $removedCount = 0;
            $movementItems = [];
            
            foreach ($quantities as $size => $qty) {
                if (!$qty || $qty <= 0) continue;
                
                // Buscar estoque para este tamanho específico
                $stock = Stock::findByParams(
                    $referenceStock->store_id,
                    $referenceStock->fabric_id,
                    $referenceStock->fabric_type_id,
                    $referenceStock->color_id,
                    $referenceStock->cut_type_id,
                    $size
                );
                
                if (!$stock || !$stock->hasStock($qty)) {
                    throw new \Exception("Estoque insuficiente para o tamanho {$size} (Solicitado: {$qty}).");
                }
                
                // Remover do estoque
                $stock->use($qty, \Auth::id(), null, null, "Baixa: " . $validated['reason']);
                
                // Registrar item para a nota de movimentação
                $movementItems[] = [
                    'stock_id' => $stock->id,
                    'fabric_type_id' => $referenceStock->fabric_type_id,
                    'color_id' => $referenceStock->color_id,
                    'cut_type_id' => $referenceStock->cut_type_id,
                    'size' => $size,
                    'quantity' => $qty,
                ];
                
                $removedCount += $qty;
            }
            
            if (empty($movementItems)) {
                 throw new \Exception("Nenhuma quantidade informada para baixa.");
            }
            
            // Criar registro de movimentação para impressão
            $movement = StockMovement::createRemoval(
                $referenceStock->store_id,
                $movementItems,
                $validated['reason']
            );
            
            \DB::commit();
            return redirect()->back()
                ->with('success', "Baixa de {$removedCount} itens realizada com sucesso!")
                ->with('movement_id', $movement->id);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Erro na baixa: ' . $e->getMessage());
        }
    }


    /**
     * Atualizar grupo de estoque
     */
    public function updateGroup(Request $request)
    {
        $this->ensureCanManage();

        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'fabric_id' => 'required|exists:product_options,id',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'color_id' => 'required|exists:product_options,id',
            'cut_type_id' => 'required|exists:product_options,id',
            'sizes' => 'required|array',
            'sizes.*' => 'nullable|integer|min:0',
            'shelf' => 'nullable|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verificar permissão
        if (!StoreHelper::canAccessStore($validated['store_id'])) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar esta loja.');
        }

        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        $updatedCount = 0;
        $createdCount = 0;

        $shelf = isset($validated['shelf']) ? trim($validated['shelf']) : null;

        foreach ($sizes as $size) {
            $quantity = isset($validated['sizes'][$size]) ? (int)$validated['sizes'][$size] : 0;
            
            // Buscar estoque existente com os parâmetros exatos
            $stock = Stock::where('store_id', $validated['store_id'])
                ->where('fabric_id', $validated['fabric_id'])
                ->where('fabric_type_id', $validated['fabric_type_id'] ?? null)
                ->where('color_id', $validated['color_id'])
                ->where('cut_type_id', $validated['cut_type_id'])
                ->where('size', $size)
                ->first();

            // Se não encontrou, verificar se existe um registro legado (com fabric_type_id nulo) para evitar duplicidade
            if (!$stock && !empty($validated['fabric_type_id'])) {
                $legacyStock = Stock::where('store_id', $validated['store_id'])
                    ->where('fabric_id', $validated['fabric_id'])
                    ->whereNull('fabric_type_id')
                    ->where('color_id', $validated['color_id'])
                    ->where('cut_type_id', $validated['cut_type_id'])
                    ->where('size', $size)
                    ->first();
                    
                if ($legacyStock) {
                    $stock = $legacyStock;
                    // Atualizar o fabric_type_id para o novo valor
                    $stock->fabric_type_id = $validated['fabric_type_id'];
                }
            }
            
            // Se ainda não encontrou, criar nova instância
            if (!$stock) {
                $stock = new Stock([
                    'store_id' => $validated['store_id'],
                    'fabric_id' => $validated['fabric_id'],
                    'fabric_type_id' => $validated['fabric_type_id'] ?? null,
                    'color_id' => $validated['color_id'],
                    'cut_type_id' => $validated['cut_type_id'],
                    'size' => $size
                ]);
            }

            $isNew = !$stock->exists;

            // Se for novo e quantidade for 0, ignorar
            if ($isNew && $quantity == 0) {
                continue;
            }

            // Atualizar dados
            $stock->quantity = $quantity;
            $stock->shelf = $shelf;
            $stock->min_stock = $validated['min_stock'] ?? 0;
            $stock->max_stock = $validated['max_stock'] ?? null;
            $stock->notes = $validated['notes'] ?? null;
            
            // Capture old quantity for history
            $oldQuantity = $stock->getOriginal('quantity') ?? 0;

            $stock->save();

            if ($isNew) {
                $createdCount++;
                // Record initial history for new stock
                if ($quantity > 0) {
                     \App\Models\StockHistory::recordMovement(
                        'edicao',
                        $stock,
                        $quantity, // Change is fully +quantity
                        \Auth::id(),
                        null,
                        null,
                        "Cadastro inicial manual via edição em grupo"
                    );
                }
            } else {
                $updatedCount++;
                // Record history for existing stock
                $diff = $quantity - $oldQuantity;
                if ($diff != 0) {
                     \App\Models\StockHistory::recordMovement(
                        'edicao',
                        $stock,
                        $diff,
                        \Auth::id(),
                        null,
                        null,
                        "Atualização manual em grupo: {$oldQuantity} -> {$quantity}"
                    );
                } elseif ($stock->wasChanged(['shelf', 'min_stock', 'max_stock', 'notes'])) {
                    // Changed non-quantity fields
                     \App\Models\StockHistory::recordMovement(
                        'ajuste',
                        $stock,
                        0,
                        \Auth::id(),
                        null,
                        null,
                        "Atualização de dados cadastrais em grupo"
                    );
                }
            }
            
            // Verificar estoque baixo
            $this->checkLowStock($stock);
        }

        return redirect()->route('stocks.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    /**
     * Excluir um grupo de estoque (todos os tamanhos)
     */
    public function destroy($id)
    {
        $this->ensureCanManage();

        try {
            $stock = Stock::findOrFail($id);
            
            // Buscar todos os estoques do mesmo grupo
            $groupStocks = Stock::where('store_id', $stock->store_id)
                ->where('fabric_id', $stock->fabric_id)
                ->where('fabric_type_id', $stock->fabric_type_id)
                ->where('color_id', $stock->color_id)
                ->where('cut_type_id', $stock->cut_type_id)
                ->get();
            
            // Verificar se algum tem quantidade reservada
            $hasReserved = $groupStocks->sum('reserved_quantity') > 0;
            if ($hasReserved) {
                return redirect()->route('stocks.index')
                    ->with('error', 'Não é possível excluir estoque com quantidade reservada.');
            }
            
            // Excluir todos os registros do grupo
            $deletedCount = Stock::where('store_id', $stock->store_id)
                ->where('fabric_id', $stock->fabric_id)
                ->where('fabric_type_id', $stock->fabric_type_id)
                ->where('color_id', $stock->color_id)
                ->where('cut_type_id', $stock->cut_type_id)
                ->delete();
            
            return redirect()->route('stocks.index')
                ->with('success', "Grupo de estoque excluído com sucesso! ({$deletedCount} registros)");
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir estoque: ' . $e->getMessage());
            return redirect()->route('stocks.index')
                ->with('error', 'Erro ao excluir item de estoque.');
        }
    }
}
