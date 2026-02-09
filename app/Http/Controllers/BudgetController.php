<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetCustomization;
use App\Models\Client;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;
use App\Helpers\StoreHelper;
use App\Helpers\BudgetObservationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\ImageProcessor;

class BudgetController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    public function __construct(private readonly ImageProcessor $imageProcessor)
    {
    }

    /**
     * Lista todos os orçamentos
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // AUTO-FIX: Check if quick budget columns are missing and add them
        // This is a self-healing block to fix the DB without needing artisan
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('budgets', 'is_quick')) {
                \Illuminate\Support\Facades\Schema::table('budgets', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('is_quick')->default(false)->after('status');
                    $table->string('contact_name')->nullable()->after('is_quick');
                    $table->string('contact_phone')->nullable()->after('contact_name');
                    $table->integer('deadline_days')->default(15)->after('contact_phone');
                    $table->string('product_internal')->nullable()->after('deadline_days');
                    $table->string('technique')->nullable()->after('product_internal');
                    $table->integer('quantity')->nullable()->after('technique');
                    $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
                    $table->foreignId('client_id')->nullable()->change();
                });
                \Illuminate\Support\Facades\Log::info('Budgets table auto-fixed: added is_quick and other fields.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to auto-fix budgets table: ' . $e->getMessage());
        }

        $query = Budget::with(['client', 'user', 'items']);
        
        // Aplicar filtros baseados no role do usuário
        if ($user->isVendedor()) {
            // Vendedor: vê apenas seus próprios orçamentos
            $query->where('user_id', $user->id);
        } elseif ($user->isAdminLoja()) {
            // Admin de loja: vê apenas orçamentos das suas lojas
            $storeIds = $user->getStoreIds();
            if (!empty($storeIds)) {
                $query->whereIn('store_id', $storeIds);
            } else {
                // Se não tem lojas atribuídas, não vê nada
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isAdminGeral()) {
            // Admin geral: vê todos os orçamentos (sem filtro)
            // Não aplica nenhum filtro
        }

        // Filtro por status
        $status = $request->query('status');
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Busca por número do orçamento/pedido ou nome/contato do cliente
        $search = trim((string)$request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('budget_number', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%")
                         ->orWhere('cpf_cnpj', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%")
                         ->orWhere('phone_secondary', 'like', "%{$search}%");
                  });
            });
        }
        
        $budgets = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->only(['status', 'search']));

        // --- DASHBOARD METRICS ---
        // Clone query for metrics to respect current user scope (Vendedor/AdminLoja)
        // We use a fresh query builder dependent on user role logic again to ensure correct scope for totals
        
        $metricsQuery = Budget::query();
        if ($user->isVendedor()) {
            $metricsQuery->where('user_id', $user->id);
        } elseif ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            if (!empty($storeIds)) $metricsQuery->whereIn('store_id', $storeIds);
            else $metricsQuery->whereRaw('1 = 0');
        }
        
        // 1. Pending Count & Open Value
        $pendingQuery = clone $metricsQuery;
        $pendingStats = $pendingQuery->where('status', 'pending')
            ->selectRaw('count(*) as count, sum(total) as total_value')
            ->first();
            
        $pendingCount = $pendingStats->count ?? 0;
        $openValue = $pendingStats->total_value ?? 0;
        
        // 2. Status Stats (Proportion)
        $statusQuery = clone $metricsQuery;
        $statusStats = $statusQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            
        // 3. Expiring Soon (Next 3 days)
        $expiringQuery = clone $metricsQuery;
        $expiringSoonCount = $expiringQuery->where('status', 'pending')
            ->whereBetween('valid_until', [now(), now()->addDays(3)])
            ->count();
            
        // 4. Follow-up Needed (Created > 7 days ago & Pending)
        $followUpQuery = clone $metricsQuery;
        $followUpCount = $followUpQuery->where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(7))
            ->count();
        
        return view('budgets.index', [
            'budgets' => $budgets,
            'pendingCount' => $pendingCount,
            'openValue' => $openValue,
            'statusStats' => $statusStats,
            'expiringSoonCount' => $expiringSoonCount,
            'followUpCount' => $followUpCount,
        ]);
    }

    /**
     * Solicitar edição do orçamento (notifica administradores)
     */
    public function requestEdit($id, Request $request)
    {
        $budget = Budget::with(['client', 'user'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para solicitar edição deste orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para solicitar edição deste orçamento.');
        }

        // Apenas usuários autenticados, e tipicamente não-admins, podem solicitar
        if ($user->isAdmin()) {
            return redirect()->route('budget.show', $id)
                ->with('info', 'Administradores não precisam solicitar edição.');
        }

        $reason = trim((string) $request->input('reason', 'Solicitação de edição do orçamento'));

        // Notificar todos os admins gerais
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::createBudgetEditRequest(
                $admin->id,
                $budget->id,
                $budget->budget_number,
                Auth::user()->name
            );
        }

        return redirect()->route('budget.show', $id)
            ->with('success', 'Solicitação de edição enviada aos administradores.');
    }

    /**
     * Inicia novo orçamento
     */
    public function start()
    {
        // Limpar todas as sessões relacionadas ao orçamento
        Session::forget('budget_data');
        Session::forget('budget_items');
        Session::forget('budget_customizations');
        
        return view('budgets.wizard.client');
    }

    /**
     * Salva dados do cliente
     */
    public function storeClient(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:12',
            'category' => 'nullable|string|max:50',
        ]);

        // Obter store_id do usuário para novos clientes
        $user = Auth::user();
        $storeId = null;
        
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            $storeId = !empty($storeIds) ? $storeIds[0] : null;
        } else {
            $mainStore = Store::where('is_main', true)->first();
            $storeId = $mainStore ? $mainStore->id : null;
        }

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
            $clientId = $client->id;
        } else {
            // Tentar encontrar por telefone antes de criar duplicado
            $existingClient = Client::where('phone_primary', $validated['phone_primary'])->first();
            if ($existingClient) {
                $existingClient->update($validated);
                $clientId = $existingClient->id;
            } else {
                $client = Client::create(array_merge($validated, [
                    'store_id' => $storeId,
                    'tenant_id' => $user->tenant_id
                ]));
                $clientId = $client->id;
            }
        }

        // Salvar IDs na sessão para o orçamento
        $budgetData = Session::get('budget_data', []);
        $budgetData['client_id'] = $clientId;
        Session::put('budget_data', $budgetData);

        // Salvar dados no formato que a view espera (session('budget.client.*'))
        Session::put('budget.client', [
            'id' => $clientId,
            'name' => $validated['name'],
            'phone_primary' => $validated['phone_primary'],
            'phone_secondary' => $validated['phone_secondary'] ?? '',
            'email' => $validated['email'] ?? '',
            'cpf_cnpj' => $validated['cpf_cnpj'] ?? '',
            'address' => $validated['address'] ?? '',
            'city' => $validated['city'] ?? '',
            'state' => $validated['state'] ?? '',
            'zip_code' => $validated['zip_code'] ?? '',
            'category' => $validated['category'] ?? '',
        ]);

        return redirect()->route('budget.items');
    }

    /**
     * Adicionar/editar itens
     */
    public function items(Request $request)
    {
        $action = $request->input('action');

        // Se for continuar para próxima etapa
        if ($action === 'continue') {
            $items = session('budget_items', []);
            
            if (empty($items)) {
                return redirect()->back()->with('error', 'Adicione pelo menos um item antes de continuar.');
            }
            
            // Ir para personalização
            return redirect()->route('budget.customization');
        }
        
        // Se for adicionar/editar/remover item
        if ($request->isMethod('post')) {
            $items = session('budget_items', []);
            
            if ($action === 'add_item') {
                $validated = $request->validate([
                    'personalizacao' => 'required|array|min:1',
                    'personalizacao.*' => 'required|string',
                    'tecido' => 'required|string',
                    'tipo_tecido' => 'nullable|string',
                    'cor' => 'required|string',
                    'tipo_corte' => 'required|string',
                    'detalhe' => 'nullable|string',
                    'gola' => 'nullable|string',
                    'quantity' => 'required|integer|min:1',
                    'unit_price' => 'required|numeric|min:0',
                    'notes' => 'nullable|string|max:1000',
                    'preco_inclui_personalizacao' => 'nullable|string',
                ]);
                
                // Capturar se o preço já inclui personalização
                $validated['preco_inclui_personalizacao'] = $request->input('preco_inclui_personalizacao', '0');
                
                // Buscar os nomes das opções selecionadas
                $personalizacaoNames = \App\Models\ProductOption::whereIn('id', $validated['personalizacao'])->pluck('name')->toArray();
                $tecidoName = \App\Models\ProductOption::find($validated['tecido'])->name ?? '';
                $tipoTecidoName = $validated['tipo_tecido'] ? (\App\Models\ProductOption::find($validated['tipo_tecido'])->name ?? '') : '';
                $corName = \App\Models\ProductOption::find($validated['cor'])->name ?? '';
                $tipoCorteName = \App\Models\ProductOption::find($validated['tipo_corte'])->name ?? '';
                $detalheName = $validated['detalhe'] ? (\App\Models\ProductOption::find($validated['detalhe'])->name ?? '') : '';
                $golaName = \App\Models\ProductOption::find($validated['gola'])->name ?? '';
                
                // Adicionar nomes para exibição
                $validated['print_type'] = implode(', ', $personalizacaoNames);
                $validated['fabric'] = $tecidoName . ($tipoTecidoName ? ' - ' . $tipoTecidoName : '');
                $validated['color'] = $corName;
                $validated['model'] = $tipoCorteName;
                $validated['detail'] = $detalheName;
                $validated['collar'] = $golaName;
                $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
                
                $items[] = $validated;
                session(['budget_items' => $items]);
                
                return redirect()->back()->with('success', 'Item adicionado com sucesso!');
            }
            
            if ($action === 'update_item') {
                $index = $request->input('editing_item_id');
                
                $validated = $request->validate([
                    'personalizacao' => 'required|array|min:1',
                    'personalizacao.*' => 'required|string',
                    'tecido' => 'required|string',
                    'tipo_tecido' => 'nullable|string',
                    'cor' => 'required|string',
                    'tipo_corte' => 'required|string',
                    'detalhe' => 'nullable|string',
                    'gola' => 'nullable|string',
                    'quantity' => 'required|integer|min:1',
                    'unit_price' => 'required|numeric|min:0',
                    'notes' => 'nullable|string|max:1000',
                    'preco_inclui_personalizacao' => 'nullable|string',
                ]);
                
                // Capturar se o preço já inclui personalização
                $validated['preco_inclui_personalizacao'] = $request->input('preco_inclui_personalizacao', '0');
                
                // Buscar os nomes das opções selecionadas
                $personalizacaoNames = \App\Models\ProductOption::whereIn('id', $validated['personalizacao'])->pluck('name')->toArray();
                $tecidoName = \App\Models\ProductOption::find($validated['tecido'])->name ?? '';
                $tipoTecidoName = $validated['tipo_tecido'] ? (\App\Models\ProductOption::find($validated['tipo_tecido'])->name ?? '') : '';
                $corName = \App\Models\ProductOption::find($validated['cor'])->name ?? '';
                $tipoCorteName = \App\Models\ProductOption::find($validated['tipo_corte'])->name ?? '';
                $detalheName = $validated['detalhe'] ? (\App\Models\ProductOption::find($validated['detalhe'])->name ?? '') : '';
                $golaName = \App\Models\ProductOption::find($validated['gola'])->name ?? '';
                
                // Adicionar nomes para exibição
                $validated['print_type'] = implode(', ', $personalizacaoNames);
                $validated['fabric'] = $tecidoName . ($tipoTecidoName ? ' - ' . $tipoTecidoName : '');
                $validated['color'] = $corName;
                $validated['model'] = $tipoCorteName;
                $validated['detail'] = $detalheName;
                $validated['collar'] = $golaName;
                $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
                
                $items[$index] = $validated;
                session(['budget_items' => $items]);
                
                return redirect()->back()->with('success', 'Item atualizado com sucesso!');
            }
            
            if ($action === 'remove_item') {
                $index = $request->input('item_index');
                unset($items[$index]);
                $items = array_values($items); // Reindexar
                session(['budget_items' => $items]);
                
                return redirect()->back()->with('success', 'Item removido com sucesso!');
            }
        }
        
        // Exibir formulário
        $items = session('budget_items', []);
        return view('budgets.wizard.items', compact('items'));
    }

    public function personalizationType(Request $request)
    {
        if ($request->isMethod('post')) {
            $types = $request->input('types', []);
            Session::put('budget_personalization_types', $types);
            return redirect()->route('budget.items');
        }

        $selectedTypes = Session::get('budget_personalization_types', []);
        return view('budgets.wizard.personalization-type', compact('selectedTypes'));
    }

    /**
     * Adicionar personalizações
     */
    public function customization(Request $request)
    {
        // Processar POST - adicionar personalização
        if ($request->isMethod('post')) {
            // Receber dados do formulário
            $data = $request->all();
            
            // Salvar personalização na sessão
            $customizations = session('budget_customizations', []);
            $itemIndex = $data['item_id'] ?? 0;
            
            // Lidar com upload de imagem da aplicação
            $imagePath = null;
            if ($request->hasFile('application_image')) {
                $imagePath = $request->file('application_image')->store('budgets/applications', 'public');
            }
            
            // Lidar com upload de arquivos da arte
            $artFiles = [];
            if ($request->hasFile('art_files')) {
                foreach ($request->file('art_files') as $file) {
                    $artFiles[] = $file->store('budgets/arts', 'public');
                }
            }

            $addons = $data['addons'] ?? [];
            if (!is_array($addons)) {
                $addons = [$addons];
            }
            $addons = array_values(array_filter($addons, function ($addonId) {
                return $addonId !== null && $addonId !== '';
            }));
            $addons = array_map(function ($addonId) {
                return is_numeric($addonId) ? (int) $addonId : $addonId;
            }, $addons);
            $regataDiscount = !empty($data['regata_discount']);
            
            $customizations[] = [
                'item_index' => $itemIndex,
                'personalization_id' => $data['personalization_id'] ?? null,
                'personalization_name' => $data['personalization_type'] ?? '',
                'location' => $data['location'] ?? '',
                'size' => $data['size'] ?? 'PADRÃO',
                'quantity' => $data['quantity'] ?? 0,
                'color_count' => $data['color_count'] ?? 1,
                'unit_price' => $data['unit_price'] ?? 0,
                'final_price' => $data['final_price'] ?? 0,
                'image' => $imagePath,
                'art_files' => $artFiles,
                'color_details' => $data['color_details'] ?? '',
                'notes' => $data['seller_notes'] ?? '',
                'addons' => $addons,
                'regata_discount' => $regataDiscount,
            ];
            
            // Aplicar descontos automáticos
            $customizations = \App\Helpers\PersonalizationDiscountHelper::applySessionDiscounts($customizations, $itemIndex);
            
            session(['budget_customizations' => $customizations]);

            // Retornar JSON para AJAX
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Personalização adicionada com sucesso!']);
            }
            
            // Redirecionar se não for AJAX
            return redirect()->back()->with('success', 'Personalização adicionada com sucesso!');
        }

        // GET - Exibir formulário
        $items = session('budget_items', []);
        
        if (empty($items)) {
            return redirect()->route('budget.items')->with('error', 'Adicione itens antes de configurar personalizações.');
        }

        // Preparar dados dos itens com suas personalizações
        $itemPersonalizations = [];
        foreach ($items as $index => $item) {
            // Converter string de personalização em IDs
            $persIds = isset($item['personalizacao']) && is_array($item['personalizacao']) 
                ? $item['personalizacao'] 
                : [];
            
            // Buscar nomes das personalizações
            $persNames = [];
            if (!empty($persIds)) {
                $options = \App\Models\ProductOption::whereIn('id', $persIds)->get();
                foreach ($options as $opt) {
                    $persNames[$opt->id] = $opt->name;
                }
            }
            
            $itemPersonalizations[] = [
                'item' => (object)[
                    'id' => $index,
                    'item_number' => $index + 1,
                    'quantity' => $item['quantity'] ?? 0,
                    'fabric' => $item['fabric'] ?? '',
                    'color' => $item['color'] ?? '',
                    'print_type' => $item['print_type'] ?? '',
                ],
                'personalization_ids' => $persIds,
                'personalization_names' => $persNames,
            ];
        }

        // Buscar tamanhos e localizações para cada tipo de personalização
        $personalizationData = [];
        $allTypes = array_keys(\App\Models\PersonalizationPrice::getPersonalizationTypes());
        
        foreach ($allTypes as $type) {
            $query = \App\Models\PersonalizationPrice::where('personalization_type', $type)
                ->where('active', true);
            
            // Para SERIGRAFIA e EMBORRACHADO, excluir "COR" dos tamanhos (COR é apenas para número de cores)
            if ($type === 'SERIGRAFIA' || $type === 'EMBORRACHADO') {
                $query->where('size_name', '!=', 'COR');
            }
            
            $sizes = $query->select('size_name', 'size_dimensions', DB::raw('MIN(`order`) as min_order'))
                ->groupBy('size_name', 'size_dimensions')
                ->orderBy('min_order')
                ->get()
                ->map(function($item) {
                    return (object)[
                        'size_name' => $item->size_name,
                        'size_dimensions' => $item->size_dimensions
                    ];
                });
            
            $personalizationData[$type] = [
                'sizes' => $sizes
            ];
        }
        
        // Localizações disponíveis
        $locations = \App\Models\SublimationLocation::where('active', true)
            ->orderBy('order')
            ->get();

        return view('budgets.wizard.customization-multiple', compact('itemPersonalizations', 'personalizationData', 'locations'));
    }

    /**
     * Confirmar orçamento
     */
    public function confirm()
    {
        $budgetData = Session::get('budget_data', []);
        $items = Session::get('budget_items', []);
        
        if (empty($budgetData) || empty($items)) {
            return redirect()->route('budget.start')->with('error', 'Nenhum dado de orçamento encontrado.');
        }

        return view('budgets.wizard.confirm');
    }

    /**
     * Finalizar e salvar orçamento
     */
    public function finalize(Request $request)
    {
        \Log::info(' FINALIZE BUDGET - Iniciando');
        
        $budgetData = Session::get('budget_data', []);
        $items = Session::get('budget_items', []);
        
        \Log::info(' Dados da sessão:', [
            'budget_data' => $budgetData,
            'items_count' => count($items),
            'customizations_count' => count(Session::get('budget_customizations', []))
        ]);
        
        if (empty($budgetData) || empty($items)) {
            \Log::warning(' Dados de orçamento ou itens vazios');
            return redirect()->route('budget.start')->with('error', 'Nenhum dado de orçamento encontrado.');
        }

        $validated = $request->validate([
            'observations' => 'nullable|string|max:1000',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        try {
            // Calcular subtotal (itens + personalizações)
            $itemsTotal = array_sum(array_map(function($item) {
                return ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }, $items));
            
            $customizations = Session::get('budget_customizations', []);
            $customizationsTotal = array_sum(array_column($customizations, 'final_price'));
            
            $subtotal = $itemsTotal + $customizationsTotal;
            
            // Obter store_id do usuário
            $user = Auth::user();
            $storeId = null;
            
            if ($user->isAdminLoja()) {
                $storeIds = $user->getStoreIds();
                $storeId = !empty($storeIds) ? $storeIds[0] : null;
            } else {
                // Admin geral ou vendedor: usar loja principal
                $mainStore = Store::where('is_main', true)->first();
                $storeId = $mainStore ? $mainStore->id : null;
            }
            
            // Criar orçamento
            $budget = Budget::createWithUniqueNumber([
                'client_id' => $budgetData['client_id'],
                'user_id' => Auth::id(),
                'store_id' => $storeId,
                'valid_until' => now()->addDays(15), // 15 dias de validade
                'subtotal' => $subtotal,
                'discount' => 0,
                'discount_type' => null,
                'total' => $subtotal,
                'observations' => $validated['observations'] ?? null,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'status' => 'pending',
            ]);

            // Criar itens
            foreach ($items as $index => $itemData) {
                $itemTotal = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                
                $budgetItem = BudgetItem::create([
                    'budget_id' => $budget->id,
                    'item_number' => $index + 1,
                    'fabric' => $itemData['fabric'] ?? '',
                    'fabric_type' => $itemData['tipo_tecido'] ?? null,
                    'color' => $itemData['color'] ?? '',
                    'quantity' => $itemData['quantity'] ?? 0,
                    'personalization_types' => json_encode([
                        'print_type' => $itemData['print_type'] ?? '',
                        'model' => $itemData['model'] ?? '',
                        'collar' => $itemData['collar'] ?? '',
                        'detail' => $itemData['detail'] ?? '',
                        'unit_price' => $itemData['unit_price'] ?? 0,
                        'notes' => $itemData['notes'] ?? '',
                        'sizes' => $itemData['tamanhos'] ?? [], // Salvar tamanhos se disponíveis
                    ]),
                    'cover_image' => $itemData['cover_image'] ?? null, // Salvar imagem de capa se disponível
                    'item_total' => $itemTotal,
                ]);
            }

            // Criar personalizações/artes
            $customizations = Session::get('budget_customizations', []);
            $createdItems = [];
            
            // Criar um array associativo de itens criados pelo index original
            foreach ($items as $index => $itemData) {
                $createdItems[$index] = BudgetItem::where('budget_id', $budget->id)
                    ->where('item_number', $index + 1)
                    ->first();
            }
            
            foreach ($customizations as $customData) {
                $itemIndex = $customData['item_index'] ?? 0;
                $relatedItem = $createdItems[$itemIndex] ?? null;
                
                BudgetCustomization::create([
                    'budget_id' => $budget->id,
                    'budget_item_id' => $relatedItem?->id,
                    'personalization_type' => $customData['personalization_name'] ?? $customData['personalization_type'] ?? 'Personalização',
                    'art_name' => $customData['art_name'] ?? '',
                    'location' => $customData['location'] ?? '',
                    'size' => $customData['size'] ?? '',
                    'quantity' => $customData['quantity'] ?? 0,
                    'color_count' => $customData['color_count'] ?? 1,
                    'unit_price' => $customData['unit_price'] ?? 0,
                    'total_price' => $customData['final_price'] ?? 0,
                    'image' => $customData['image'] ?? null,
                    'art_files' => isset($customData['art_files']) ? json_encode($customData['art_files']) : null,
                    'notes' => $customData['notes'] ?? '',
                    'addons' => !empty($customData['addons']) ? $customData['addons'] : null,
                    'regata_discount' => !empty($customData['regata_discount']),
                ]);
            }

            Session::forget('budget_data');
            Session::forget('budget_items');
            Session::forget('budget_customizations');

            \Log::info(' Orçamento criado com sucesso!', ['budget_id' => $budget->id]);

            return redirect()->route('budget.index')
                ->with('success', 'Orçamento #' . $budget->budget_number . ' criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error(' Erro ao criar orçamento:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retornar JSON se for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar orçamento: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Erro ao criar orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar orçamento
     */
    public function show($id)
    {
        $budget = Budget::with(['client', 'user', 'items.customizations'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para visualizar este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para visualizar este orçamento.');
        }
        
        return view('budgets.show', compact('budget'));
    }

    /**
     * Download PDF do orçamento
     */
    public function downloadPdf($id, Request $request)
    {
        $budget = Budget::with(['client', 'user', 'items.customizations', 'store'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para baixar este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para baixar este orçamento.');
        }
        
        // Buscar configurações da loja do orçamento
        $settings = \App\Models\CompanySetting::getSettings($budget->store_id);
        
        // Verificar modo do PDF (detalhado ou unificado)
        $modo = $request->query('modo', 'detalhado');
        
        $pdf = \PDF::loadView('budgets.pdf', compact('budget', 'settings', 'modo'));
        
        $filename = 'orcamento-' . $budget->budget_number;
        if ($modo === 'unificado') {
            $filename .= '-valor-unico';
        }

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Deletar personalização da sessão
     */
    public function deleteCustomization($index)
    {
        $customizations = Session::get('budget_customizations', []);
        
        if (isset($customizations[$index])) {
            unset($customizations[$index]);
            // Reindexar o array
            $customizations = array_values($customizations);
            Session::put('budget_customizations', $customizations);
            
            return response()->json(['success' => true, 'message' => 'Personalização removida com sucesso!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Personalização não encontrada.'], 404);
    }

    /**
     * Refresh customizations (AJAX)
     */
    public function refreshCustomizations()
    {
        $budgetData = Session::get('budget_data', []);
        // Retornar HTML atualizado das personalizações
        return response()->json(['success' => true]);
    }

    /**
     * Aprovar orçamento
     */
    public function approve($id)
    {
        $budget = Budget::with(['items.customizations'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para aprovar este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para aprovar este orçamento.');
        }
        if (!$user->isAdmin() && !$user->isVendedor()) {
            abort(403, 'Você não tem permissão para aprovar orçamentos.');
        }
        
        // Gerar número de pedido padrão (começando do 0)
        // Verificar se já existe um order_number
        if (!$budget->order_number) {
            // Buscar o último número de pedido gerado
            $lastOrderNumber = Budget::whereNotNull('order_number')
                ->orderBy('order_number', 'desc')
                ->value('order_number');
            
            // Se não existe nenhum, começar do 0, senão incrementar
            $newOrderNumber = $lastOrderNumber !== null ? (int)$lastOrderNumber + 1 : 0;
            
            $budget->update([
                'status' => 'approved',
                'order_number' => (string)$newOrderNumber
            ]);
        } else {
            $budget->update(['status' => 'approved']);
        }
        
        return redirect()->route('budget.show', $id)
            ->with('success', 'Orçamento aprovado com sucesso! Número do pedido: ' . $budget->order_number);
    }

    /**
     * Rejeitar orçamento
     */
    public function reject($id)
    {
        $budget = Budget::findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para rejeitar este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para rejeitar este orçamento.');
        }
        if (!$user->isAdmin() && !$user->isVendedor()) {
            abort(403, 'Você não tem permissão para rejeitar orçamentos.');
        }
        
        $budget->update(['status' => 'rejected']);
        
        return redirect()->route('budget.show', $id)
            ->with('success', 'Orçamento rejeitado.');
    }

    /**
     * Exibir formulário simplificado para converter orçamento em pedido
     */
    public function showConvertForm($id)
    {
        $budget = Budget::with(['client', 'items.customizations'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para converter este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para converter este orçamento.');
        }
        if (!$user->isAdmin() && !$user->isVendedor()) {
            abort(403, 'Você não tem permissão para converter orçamentos em pedidos.');
        }
        
        // Verificar se o orçamento foi aprovado (para orçamentos normais)
        if (!$budget->is_quick && $budget->status !== 'approved') {
            return redirect()->route('budget.index')
                ->with('error', 'Apenas orçamentos aprovados podem ser convertidos em pedidos.');
        }    
        
        // Buscar tamanhos disponíveis para seleção
        $availableSizes = ['PP', 'P', 'M', 'G', 'GG', 'XG', 'EXG', '2G', '3G', '4G', '5G', '6G'];
        
        // Buscar métodos de pagamento
        $paymentMethods = [
            'dinheiro' => 'Dinheiro',
            'pix' => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'boleto' => 'Boleto',
            'transferencia' => 'Transferência',
        ];

        $clients = collect();
        if ($budget->is_quick) {
            $clients = \App\Models\Client::orderBy('name')->get();
        }
        
        return view('budgets.convert-to-order', compact('budget', 'availableSizes', 'paymentMethods', 'clients'));
    }
    
    /**
     * Converter orçamento em pedido (processar POST)
     */
    public function convertToOrder($id, Request $request)
    {
        $budget = Budget::with(['client', 'items.customizations'])->findOrFail($id);
        
        // Verificar permissão de acesso
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para converter este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para converter este orçamento.');
        }
        if (!$user->isAdmin() && !$user->isVendedor()) {
            abort(403, 'Você não tem permissão para converter orçamentos em pedidos.');
        }

        \Log::info(' Iniciando conversão de orçamento em pedido', [
            'budget_id' => $id,
            'request_data' => $request->except(['_token', 'item_files', 'customization_images']),
            'has_files' => $request->hasFile('item_files'),
            'all_files' => $request->allFiles(),
        ]);
        
        // Verificar se o orçamento foi aprovado (para orçamentos normais)
        if (!$budget->is_quick && $budget->status !== 'approved') {
            \Log::warning(' Tentativa de converter orçamento não aprovado', ['budget_id' => $id, 'status' => $budget->status]);
            return redirect()->route('budget.index')
                ->with('error', 'Apenas orçamentos aprovados podem ser convertidos em pedidos.');
        }    
        
        return $this->processConversion($budget, $request);
    }
    
    /**
     * Processar conversão com dados do formulário
     */
    private function processConversion($budget, $request)
    {
        \Log::info(' Validando dados do formulário');
        
        try {
            // Validação básica primeiro
            $validated = $request->validate([
                'sizes' => 'nullable|array',
                'sizes.*' => 'nullable|array',
                'sizes.*.*' => 'nullable|integer|min:0',
                'payments' => 'required|array|min:1',
                'payments.*.method' => 'required|string',
                'payments.*.amount' => 'required|numeric|min:0',
                'payment_notes' => 'nullable|string',
                'delivery_date' => 'nullable|date',
                'is_event' => 'nullable|boolean',
                'discount_amount' => 'nullable|numeric|min:0',
                'client_id' => $budget->client_id ? 'nullable|exists:clients,id' : 'required|exists:clients,id',
                'cover_image' => 'nullable|image|max:5120', // Max 5MB
            ], [
                'payments.required' => 'Informe pelo menos um pagamento',
                'payments.*.method.required' => 'Selecione a forma de pagamento',
                'payments.*.amount.required' => 'Informe o valor do pagamento',
                'payments.*.amount.min' => 'O valor do pagamento deve ser maior que zero',
                'delivery_date.required' => 'Informe a data de entrega prevista',
                'delivery_date.date' => 'Data de entrega inválida',
            ]);
            
            \Log::info(' Dados de tamanhos recebidos', [
                'sizes' => $validated['sizes'] ?? [],
            ]);
            
            // Validar arquivos separadamente com tratamento especial
            if ($request->hasFile('item_files')) {
                foreach ($request->file('item_files') as $itemIndex => $files) {
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file && $file->isValid()) {
                                // Validar tamanho (10MB)
                                if ($file->getSize() > 10240 * 1024) {
                                    throw new \Exception("Arquivo {$file->getClientOriginalName()} excede o tamanho máximo de 10MB");
                                }
                                
                                // Validar extensão
                                $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'cdr', 'ai', 'pdf', 'svg'];
                                $extension = strtolower($file->getClientOriginalExtension());
                                if (!in_array($extension, $allowedExtensions)) {
                                    throw new \Exception("Arquivo {$file->getClientOriginalName()} tem extensão inválida. Permitido: " . implode(', ', $allowedExtensions));
                                }
                            }
                        }
                    }
                }
            }
            
            // Validar imagens de personalização
            if ($request->hasFile('customization_images')) {
                foreach ($request->file('customization_images') as $itemIndex => $images) {
                    if (is_array($images)) {
                        foreach ($images as $image) {
                            if ($image && $image->isValid()) {
                                // Validar tamanho (5MB)
                                if ($image->getSize() > 5120 * 1024) {
                                    throw new \Exception("Imagem {$image->getClientOriginalName()} excede o tamanho máximo de 5MB");
                                }
                                
                                // Validar se é imagem
                                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                                if (!in_array($image->getMimeType(), $allowedMimes)) {
                                    throw new \Exception("Arquivo {$image->getClientOriginalName()} não é uma imagem válida");
                                }
                            }
                        }
                    }
                }
            }
            
            \Log::info(' Validação passou com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error(' Erro de validação', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }
        
        try {
            // Obter store_id do orçamento ou do usuário
            $storeId = $budget->store_id;
            if (!$storeId) {
                $user = Auth::user();
                if ($user->isAdminLoja()) {
                    $storeIds = $user->getStoreIds();
                    $storeId = !empty($storeIds) ? $storeIds[0] : null;
                } elseif ($user->isVendedor()) {
                    $userStores = $user->stores()->get();
                    if ($userStores->isNotEmpty()) {
                        $storeId = $userStores->first()->id;
                    }
                }
                
                // Se ainda não encontrou, usar loja principal
                if (!$storeId) {
                    $mainStore = \App\Models\Store::where('is_main', true)->first();
                    $storeId = $mainStore ? $mainStore->id : null;
                }
            }

            // Criar pedido
            // Garantir que o status pertence ao tenant da loja
            $store = \App\Models\Store::find($storeId);
            $tenantId = $store ? $store->tenant_id : Auth::user()->tenant_id;
            
            $status = \App\Models\Status::where('tenant_id', $tenantId)->orderBy('position')->first();
            
            // Calcular data de entrega: usar a data do formulário ou calcular 15 dias úteis
            $deliveryDate = !empty($validated['delivery_date']) 
                ? $validated['delivery_date'] 
                : \App\Helpers\DateHelper::calculateDeliveryDate(\Carbon\Carbon::now(), 15)->format('Y-m-d');
            
            \Log::info('Criando pedido a partir do orçamento', [
                'budget_id' => $budget->id,
                'budget_store_id' => $budget->store_id,
                'order_store_id' => $storeId,
                'delivery_date' => $deliveryDate,
                'user_id' => Auth::id()
            ]);
            
            $clientId = $budget->client_id;
            
            if (!$clientId) {
                if ($request->input('create_new_client') == '1') {
                    $newClient = \App\Models\Client::create([
                        'name' => $request->input('new_client_name'),
                        'phone_primary' => $request->input('new_client_phone'),
                        'store_id' => $storeId,
                        'user_id' => Auth::id(),
                        'tenant_id' => Auth::user()->tenant_id,
                    ]);
                    $clientId = $newClient->id;
                    
                    \Log::info(' Novo cliente criado durante conversão', ['client_id' => $clientId, 'name' => $newClient->name]);
                } else {
                    $clientId = $request->input('client_id');
                }
            }
            
            // Se for orçamento rápido e vinculou cliente agora, atualizar o orçamento
            if ($budget->is_quick && !$budget->client_id && $clientId) {
                $budget->update(['client_id' => $clientId]);
            }

            // Upload of order cover image if exists
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                try {
                    $file = $request->file('cover_image');
                    // Ensure directory exists
                    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('orders/covers')) {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('orders/covers');
                    }
                    
                    $filename = 'order_cover_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $coverImagePath = $file->storeAs('orders/covers', $filename, 'public');
                    \Log::info(' Capa do pedido salva: ' . $coverImagePath);
                } catch (\Exception $e) {
                    \Log::error('Erro ao salvar capa do pedido: ' . $e->getMessage());
                }
            }

            $order = \App\Models\Order::create([
                'client_id' => $clientId,
                'user_id' => Auth::id(),
                'store_id' => $storeId,
                'status_id' => $status?->id ?? 1,
                'order_date' => now()->toDateString(),
                'delivery_date' => $deliveryDate,
                'is_draft' => false, // Não é rascunho, já está confirmado
                'is_event' => $request->boolean('is_event'),
                'total' => $budget->total - ($request->input('discount_amount', 0) ?? 0),
                'subtotal' => $budget->subtotal,
                'discount' => ($budget->discount ?? 0) + ($request->input('discount_amount', 0) ?? 0),
                'notes' => $budget->observations,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'cover_image' => $coverImagePath,
            ]);
            
            // Copiar itens do orçamento para o pedido
            foreach ($budget->items as $index => $budgetItem) {
                $personalizationTypes = $budgetItem->getPersonalizationTypesArray();
                
                // Pegar tamanhos informados pelo usuário para este item
                $itemSizesRaw = $validated['sizes'][$index] ?? [];
                
                // Processar tamanhos: remover zeros e garantir formato correto
                $itemSizes = [];
                foreach ($itemSizesRaw as $size => $quantity) {
                    $quantity = (int)$quantity;
                    if ($quantity > 0) {
                        $itemSizes[$size] = $quantity;
                    }
                }
                
                \Log::info(' Processando tamanhos do item', [
                    'item_index' => $index,
                    'raw_sizes' => $itemSizesRaw,
                    'processed_sizes' => $itemSizes,
                ]);
                
                // Criar item do pedido
                $orderItem = \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'item_number' => $budgetItem->item_number,
                    'fabric' => $personalizationTypes['fabric'] ?? $budgetItem->fabric ?? '',
                    'color' => $personalizationTypes['color'] ?? $budgetItem->color ?? '',
                    'model' => $personalizationTypes['model'] ?? '',
                    'detail' => $personalizationTypes['detail'] ?? '',
                    'collar' => $personalizationTypes['collar'] ?? '',
                    'print_type' => $personalizationTypes['print_type'] ?? '',
                    'art_name' => $personalizationTypes['art_name'] ?? '',
                    'quantity' => $budgetItem->quantity,
                    'unit_price' => $personalizationTypes['unit_price'] ?? 0,
                    'total_price' => $budgetItem->item_total,
                    'art_notes' => $personalizationTypes['notes'] ?? '',
                    'sizes' => !empty($itemSizes) ? json_encode($itemSizes) : null, // Salvar apenas se houver tamanhos
                    'cover_image' => $budgetItem->cover_image ? $this->copyImageFile($budgetItem->cover_image, 'orders/items/covers') : null,
                ]);
                
                // Upload de arquivos para este item (se houver)
                if ($request->hasFile("item_files.{$index}")) {
                    $files = $request->file("item_files.{$index}");
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file && $file->isValid()) {
                                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                                $filePath = $file->storeAs('orders/items/files', $fileName, 'public');
                                
                                \App\Models\OrderFile::create([
                                    'order_item_id' => $orderItem->id,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_path' => $filePath,
                                    'file_type' => $file->getMimeType(),
                                    'file_size' => $file->getSize(),
                                ]);
                                
                                \Log::info('Arquivo do item copiado ao converter orçamento', [
                                    'order_item_id' => $orderItem->id,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_path' => $filePath
                                ]);
                            }
                        }
                    }
                }
                
                // Copiar arquivos das personalizações que podem ter sido enviados como arquivos dos itens
                // Verificar se há arquivos nas personalizações que devem ser copiados para o item
                foreach ($budgetItem->customizations as $budgetCustomization) {
                    if ($budgetCustomization->art_files) {
                        // Os arquivos das personalizações já são copiados abaixo, mas vamos garantir que estejam acessíveis
                        \Log::info('Personalização tem arquivos para copiar', [
                            'budget_customization_id' => $budgetCustomization->id,
                            'art_files' => $budgetCustomization->art_files
                        ]);
                    }
                }
                
                // Copiar personalizações do orçamento para o pedido
                foreach ($budgetItem->customizations as $customIndex => $budgetCustomization) {
                    // Verificar se há uma nova imagem para esta personalização
                    $applicationImage = null;
                    if ($request->hasFile("customization_images.{$index}.{$customIndex}")) {
                        $imageFile = $request->file("customization_images.{$index}.{$customIndex}");
                        if ($imageFile && $imageFile->isValid()) {
                            $imageName = time() . '_' . uniqid() . '_' . $imageFile->getClientOriginalName();
                            $imagePath = $imageFile->storeAs('orders/sublimations/images', $imageName, 'public');
                            $applicationImage = $imagePath;
                        }
                    } elseif ($budgetCustomization->image) {
                        // Se não houver nova imagem, copiar a existente do orçamento
                        $applicationImage = $this->copyImageFile($budgetCustomization->image, 'orders/sublimations/images');
                    }
                    
                    $addons = $budgetCustomization->addons ?? [];
                    if (!is_array($addons)) {
                        $addons = json_decode($addons, true) ?? [];
                    }
                    $regataDiscount = (bool) ($budgetCustomization->regata_discount ?? false);
                    if ($regataDiscount) {
                        $addons[] = 'REGATA_DISCOUNT';
                    }

                    $sublimation = \App\Models\OrderSublimation::create([
                        'order_item_id' => $orderItem->id,
                        'application_type' => $budgetCustomization->personalization_type,
                        'art_name' => $budgetCustomization->art_name ?? '',
                        'size_name' => $budgetCustomization->size ?? '',
                        'location_name' => $budgetCustomization->location ?? '',
                        'quantity' => $budgetCustomization->quantity,
                        'color_count' => $budgetCustomization->color_count ?? 1,
                        'unit_price' => $budgetCustomization->unit_price,
                        'final_price' => $budgetCustomization->total_price,
                        'seller_notes' => $budgetCustomization->notes ?? '',
                        'application_image' => $applicationImage,
                        'addons' => !empty($addons) ? $addons : null,
                        'regata_discount' => $regataDiscount,
                    ]);
                    
                    // Copiar arquivos de arte (art_files) - incluindo arquivos Corel (.cdr)
                    if ($budgetCustomization->art_files) {
                        \Log::info('Copiando arquivos de arte da personalização', [
                            'budget_customization_id' => $budgetCustomization->id,
                            'sublimation_id' => $sublimation->id,
                            'art_files' => $budgetCustomization->art_files
                        ]);
                        $this->copyArtFiles($budgetCustomization->art_files, $sublimation->id);
                    } else {
                        \Log::warning('Personalização não tem arquivos de arte', [
                            'budget_customization_id' => $budgetCustomization->id,
                            'sublimation_id' => $sublimation->id
                        ]);
                    }
                }
            }
            
            // Recarregar pedido com relacionamentos para garantir dados atualizados
            $order->refresh();
            $order->load('items.sublimations');
            
            // Atualizar totais do pedido (incluindo personalizações)
            $subtotal = 0;
            foreach ($order->items as $item) {
                // Aplicar descontos automáticos nas personalizações deste item
                \App\Helpers\PersonalizationDiscountHelper::applyDiscounts($item->id);
                
                // Somar preço base do item (costura)
                $itemBasePrice = $item->unit_price * $item->quantity;
                
                // Somar total de personalizações do item (já com descontos aplicados)
                $personalizationTotal = $item->sublimations->sum('final_price');
                
                // Total do item = base + personalizações
                $itemTotal = $itemBasePrice + $personalizationTotal;
                
                // Atualizar total_price do item para incluir personalizações
                $item->update(['total_price' => $itemTotal]);
                
                $subtotal += $itemTotal;
            }
            
            // Aplicar desconto se houver
            $discount = $budget->discount ?? 0;
            $total = $subtotal - $discount;
            
            $order->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ]);
            
            \Log::info(' Totais do pedido atualizados', [
                'order_id' => $order->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ]);
            
            // Processar múltiplos pagamentos
            $paymentsList = [];
            $totalPaid = 0;
            
            foreach ($validated['payments'] as $payment) {
                $amount = (float) $payment['amount'];
                if ($amount > 0) {
                    $paymentsList[] = [
                        'id' => uniqid(),
                        'method' => $payment['method'],
                        'amount' => $amount,
                    ];
                    $totalPaid += $amount;
                }
            }
            
            // Criar pagamento usando o formato correto (payment_methods como array)
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'method' => count($paymentsList) > 1 ? 'multiple' : ($paymentsList[0]['method'] ?? 'money'),
                'payment_methods' => $paymentsList,
                'amount' => $totalPaid,
                'entry_amount' => $totalPaid,
                'payment_date' => now()->toDateString(),
                'entry_date' => now()->toDateString(),
                'status' => 'paid',
                'notes' => $validated['payment_notes'] ?? null,
            ]);
            
            // Atualizar budget com o order_id
            $budget->update([
                'order_id' => $order->id,
            ]);
            
            // Criar log de criação do pedido
            \App\Models\OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'PEDIDO_CRIADO_DE_ORCAMENTO',
                'description' => 'Pedido criado a partir do orçamento #' . $budget->budget_number,
            ]);
            
            // Verificar estoque e criar solicitações de separação
            try {
                $stockResult = \App\Services\StockService::checkAndReserveForOrder($order);
                $order->update(['stock_status' => $stockResult['status']]);
                
                \App\Models\OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'Sistema',
                    'action' => 'ESTOQUE_VERIFICADO',
                    'description' => 'Estoque verificado: ' . strtoupper($stockResult['status']) . 
                                   '. Solicitações criadas: ' . $stockResult['requests_created'],
                ]);
            } catch (\Exception $e) {
                \Log::warning('Erro ao verificar estoque', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id,
                ]);
                $order->update(['stock_status' => 'pending']);
            }
            
            // NÃO redirecionar para web wizard, mas sim para a visualização do pedido
            // session(['current_order_id' => $order->id]);
            
            \Log::info(' Pedido criado e redirecionando para detalhes', ['order_id' => $order->id]);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Orçamento #' . $budget->budget_number . ' convertido em pedido com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao converter orçamento em pedido:', [
                'budget_id' => $budget->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Se for erro de validação, retornar com os erros
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }
            
            return redirect()->route('budget.show', $budget->id)
                ->with('error', 'Erro ao converter orçamento: ' . $e->getMessage());
        }
    }
    
    /**
     * Extrair tamanhos do item do orçamento
     */
    private function extractSizesFromBudgetItem($budgetItem): array
    {
        $personalizationTypes = $budgetItem->getPersonalizationTypesArray();
        return $personalizationTypes['sizes'] ?? [];
    }
    
    /**
     * Copiar arquivo de imagem do orçamento para o pedido
     */
    private function copyImageFile($sourcePath, $destinationFolder): ?string
    {
        try {
            if (empty($sourcePath)) {
                return null;
            }
            
            $fullSourcePath = storage_path('app/public/' . $sourcePath);
            
            // Verificar se o arquivo existe
            if (!file_exists($fullSourcePath)) {
                // Tentar buscar em budgets também
                $altPaths = [
                    storage_path('app/public/budgets/items/covers/' . basename($sourcePath)),
                    storage_path('app/public/budgets/customizations/images/' . basename($sourcePath)),
                    storage_path('app/public/budgets/' . basename($sourcePath)),
                ];
                
                foreach ($altPaths as $altPath) {
                    if (file_exists($altPath)) {
                        $fullSourcePath = $altPath;
                        break;
                    }
                }
                
                if (!file_exists($fullSourcePath)) {
                    return null;
                }
            }
            
            $processedPath = $this->imageProcessor->processAndStore(
                $fullSourcePath,
                $destinationFolder,
                [
                    'max_width' => 1200,
                    'max_height' => 1200,
                    'quality' => 85,
                ]
            );

            if (!$processedPath) {
                throw new \RuntimeException('Falha ao processar imagem do orçamento.');
            }

            return $processedPath;
        } catch (\Exception $e) {
            \Log::error('Erro ao copiar imagem do orçamento:', [
                'source' => $sourcePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Copiar arquivos de arte do orçamento para o pedido
     */
    private function copyArtFiles($artFilesData, $sublimationId): void
    {
        try {
            $artFiles = json_decode($artFilesData, true);
            
            if (!is_array($artFiles)) {
                // Se não é array, pode ser uma string com caminho único
                if (is_string($artFilesData)) {
                    $this->copySingleArtFile($artFilesData, $sublimationId);
                }
                return;
            }
            
            foreach ($artFiles as $fileData) {
                // Verificar se é um objeto com path ou uma string
                $filePath = is_array($fileData) && isset($fileData['path']) 
                    ? $fileData['path'] 
                    : (is_string($fileData) ? $fileData : null);
                
                if (!$filePath) {
                    continue;
                }
                
                $this->copySingleArtFile($filePath, $sublimationId, $fileData);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao copiar arquivos de arte do orçamento:', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Copiar um único arquivo de arte
     */
    private function copySingleArtFile($filePath, $sublimationId, $fileData = null): void
    {
        try {
            $fullSourcePath = storage_path('app/public/' . $filePath);
            
            // Verificar se o arquivo existe
            if (!file_exists($fullSourcePath)) {
                \Log::warning('Arquivo não encontrado no caminho original, tentando locais alternativos', [
                    'original_path' => $fullSourcePath,
                    'file_path' => $filePath
                ]);
                
                // Tentar buscar em diferentes locais possíveis
                $altPaths = [
                    storage_path('app/public/budgets/art_files/' . basename($filePath)),
                    storage_path('app/public/budgets/customizations/files/' . basename($filePath)),
                    storage_path('app/public/budgets/items/files/' . basename($filePath)),
                    storage_path('app/public/budgets/files/' . basename($filePath)),
                    storage_path('app/public/budgets/' . basename($filePath)),
                    // Tentar também com o caminho relativo completo
                    storage_path('app/public/' . $filePath),
                ];
                
                foreach ($altPaths as $altPath) {
                    if (file_exists($altPath)) {
                        \Log::info('Arquivo encontrado em local alternativo', [
                            'found_path' => $altPath
                        ]);
                        $fullSourcePath = $altPath;
                        break;
                    }
                }
                
                if (!file_exists($fullSourcePath)) {
                    \Log::error('Arquivo não encontrado em nenhum local', [
                        'file_path' => $filePath,
                        'original_path' => $fullSourcePath,
                        'tried_paths' => $altPaths
                    ]);
                    return;
                }
            }
            
            // Gerar novo nome de arquivo
            $originalName = is_array($fileData) && isset($fileData['name']) 
                ? $fileData['name'] 
                : basename($filePath);
            $newFileName = time() . '_' . uniqid() . '_' . $originalName;
            $newFilePath = 'orders/art_files/' . $newFileName;
            $fullDestinationPath = storage_path('app/public/' . $newFilePath);
            
            // Criar diretório se não existir
            $directory = dirname($fullDestinationPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Copiar arquivo
            if (copy($fullSourcePath, $fullDestinationPath)) {
                $fileType = is_array($fileData) && isset($fileData['type']) 
                    ? $fileData['type'] 
                    : (function_exists('mime_content_type') ? mime_content_type($fullDestinationPath) : 'application/octet-stream');
                
                $sublimationFile = \App\Models\OrderSublimationFile::create([
                    'order_sublimation_id' => $sublimationId,
                    'file_name' => $originalName,
                    'file_path' => $newFilePath,
                    'file_type' => $fileType,
                    'file_size' => filesize($fullDestinationPath),
                ]);
                
                \Log::info('Arquivo de arte copiado com sucesso', [
                    'sublimation_id' => $sublimationId,
                    'file_name' => $originalName,
                    'file_path' => $newFilePath,
                    'file_id' => $sublimationFile->id
                ]);
            } else {
                \Log::error('Erro ao copiar arquivo de arte', [
                    'source' => $fullSourcePath,
                    'destination' => $fullDestinationPath
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao copiar arquivo de arte:', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display quick budget creation form
     */
    public function quickCreate()
    {
        $observationOptions = BudgetObservationHelper::getOptions();
        return view('budgets.quick-create', compact('observationOptions'));
    }

    /**
     * Store a new quick budget
     */
    public function storeQuick(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
            'technique' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
            'deadline_days' => 'nullable|integer|min:1|max:365',
            'product_internal' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:2000',
        ]);

        try {
            $user = Auth::user();
            $storeId = null;
            $tenantId = $user->tenant_id;
            
            if ($user->isAdminLoja()) {
                $storeIds = $user->getStoreIds();
                $storeId = !empty($storeIds) ? $storeIds[0] : null;
            } else {
                $mainStore = Store::where('is_main', true)->first();
                $storeId = $mainStore ? $mainStore->id : null;
                // For SuperAdmin, get tenant_id from the store
                if ($tenantId === null && $mainStore) {
                    $tenantId = $mainStore->tenant_id;
                }
            }

            $quantity = $validated['quantity'];
            $unitPrice = $validated['unit_price'];
            $total = $quantity * $unitPrice;
            $deadlineDays = $validated['deadline_days'] ?? 15;

            $budget = Budget::createWithUniqueNumber([
                'client_id' => null, // Quick budgets don't require a client
                'user_id' => Auth::id(),
                'tenant_id' => $tenantId,
                'store_id' => $storeId,
                'valid_until' => now()->addDays($deadlineDays),
                'subtotal' => $total,
                'discount' => 0,
                'discount_type' => null,
                'total' => $total,
                'observations' => $validated['observations'] ?? null,
                'status' => 'pending',
                // Quick budget specific fields
                'is_quick' => true,
                'contact_name' => $validated['contact_name'],
                'contact_phone' => $validated['contact_phone'],
                'deadline_days' => $deadlineDays,
                'product_internal' => $validated['product_internal'] ?? null,
                'technique' => $validated['technique'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);

            // For AJAX requests (save with actions)
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Orçamento rápido criado com sucesso!',
                    'budget_id' => $budget->id,
                    'budget_number' => $budget->budget_number,
                    'redirect_url' => route('budget.show', $budget->id),
                    'pdf_url' => route('budget.pdf', $budget->id),
                    'whatsapp_url' => route('budget.whatsapp', $budget->id),
                ]);
            }

            return redirect()->route('budget.show', $budget->id)
                ->with('success', 'Orçamento Rápido #' . $budget->budget_number . ' criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar orçamento rápido:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar orçamento: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Erro ao criar orçamento: ' . $e->getMessage());
        }
    }

    /**
     * Share budget via WhatsApp
     */
    public function shareWhatsApp($id)
    {
        $budget = Budget::findOrFail($id);
        
        // Verify access permission
        $user = Auth::user();
        if ($user->isVendedor() && $budget->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para compartilhar este orçamento.');
        }
        if ($user->isAdminLoja() && !$user->canAccessStore($budget->store_id)) {
            abort(403, 'Você não tem permissão para compartilhar este orçamento.');
        }

        // Get contact info
        $contactInfo = $budget->getContactInfo();
        $contactName = $contactInfo['name'] ?? 'Cliente';
        $contactPhone = $contactInfo['phone'] ?? '';

        // Build message
        $validDate = \Carbon\Carbon::parse($budget->valid_until)->startOfDay();
        $today = now()->startOfDay();
        $daysValid = $validDate->diffInDays($today); // Returns absolute difference

        $message = "*Orçamento #{$budget->budget_number} (Validade {$daysValid} dias)*\n\n";
        
        if ($budget->isQuick()) {
            $message .= "Modelo: " . ($budget->product_internal ?? 'Personalizado') . "\n";
            $message .= "Cor: -\n";
            $message .= "Tecido/ Malha: -\n";
            $message .= "Serviço : {$budget->technique}\n";
            $message .= "Tabela de quantidade: {$budget->quantity} unidades\n";
            
            $pixPrice = $budget->unit_price * 0.95;
            $message .= "Valor unitário (CARTÃO/BOLETO*) R$ " . number_format($budget->unit_price, 2, ',', '.') . "\n";
            $message .= "Valor Unitário(PIX/DINHEIRO) R$ " . number_format($pixPrice, 2, ',', '.') . "\n\n";
        } else {
            $locationNames = [];
            $locationIds = $budget->items
                ->flatMap->customizations
                ->pluck('location')
                ->filter(fn ($loc) => is_numeric($loc))
                ->map(fn ($loc) => (int) $loc)
                ->unique()
                ->values();

            if ($locationIds->isNotEmpty()) {
                $locationNames = \App\Models\SublimationLocation::whereIn('id', $locationIds)
                    ->pluck('name', 'id')
                    ->toArray();
            }

            foreach ($budget->items as $index => $item) {
                // Determine item details
                $personalizationTypes = json_decode($item->personalization_types, true) ?? [];
                $model = $personalizationTypes['model'] ?? 'Personalizado';
                $printType = $personalizationTypes['print_type'] ?? '-';
                
                // Calculate item totals
                $itemBaseTotal = $item->item_total;
                $customizationsTotal = $item->customizations->sum('total_price');
                $finalItemTotal = $itemBaseTotal + $customizationsTotal;
                $finalUnitPrice = $item->quantity > 0 ? ($finalItemTotal / $item->quantity) : 0;
                
                if ($index > 0) $message .= "--------------------------------\n\n";
                
                $message .= "Modelo: {$model}\n";
                $message .= "Cor: " . ($item->color ?? '-') . "\n";
                $message .= "Tecido/ Malha: " . ($item->fabric ?? '-') . "\n";
                
                // Collect services
                $services = [];
                if ($item->customizations->count() > 0) {
                     foreach ($item->customizations as $cust) {
                         $locRaw = $cust->location ?? '';
                         $locName = $locRaw;
                         if (is_numeric($locRaw)) {
                             $locName = $locationNames[(int) $locRaw] ?? $locRaw;
                         }

                         $sizeLabel = trim((string) ($cust->size ?? ''));
                         $detailParts = [];
                         if (!empty($locName)) {
                             $detailParts[] = $locName;
                         }
                         if (!empty($sizeLabel) && strtoupper($sizeLabel) !== 'PADRÃO') {
                             $detailParts[] = $sizeLabel;
                         }
                         $detail = !empty($detailParts) ? ' (' . implode(' - ', $detailParts) . ')' : '';

                         $services[] = "{$cust->personalization_type}{$detail}";
                     }
                } else {
                     $services[] = $printType;
                }
                $servicesStr = implode(', ', array_unique($services));
                
                $message .= "Serviço : {$servicesStr}\n";
                $message .= "Tabela de quantidade: {$item->quantity} unidades\n";
                
                $pixPrice = $finalUnitPrice * 0.95;
                $message .= "Valor unitário (CARTÃO/BOLETO*) R$ " . number_format($finalUnitPrice, 2, ',', '.') . "\n";
                $message .= "Valor Unitário(PIX/DINHEIRO) R$ " . number_format($pixPrice, 2, ',', '.') . "\n\n";
            }
        }
        
        $message .= " Valor de redesenhar ou criar a arte: A combinar se for preciso.\n\n";
        
        $message .= " Valores referente aos tamanhos PP, P, M, G das básicas/regatas; e todos os tamanhos infantis/babylook.\n\n";
        
        $message .= "Acréscimo de tamanho: GG aumenta R$2,00 e EXG aumenta R$4,00. Especial: A combinar.\n\n";
        
        $message .= "Prazo de entrega padrão é de até 15 a 20 dias úteis. Converse conosco sobre seu prazo que vamos fazer o possível para ajudar você. \n\n";
        
        $message .= "Forma de pagamento:\n";
        $message .= " 50% de entrada e 50% na entrega, se for em dinheiro ou pix.\n";
        $message .= " Pague por cartão de crédito de forma presencial ou virtual através de link. O valor pode ser parcelado e precisa ser pago integralmente na entrada do pedido.\n";
        $message .= "* Para CNPJ aprovado emitimos boletos\n";
        $message .= "**Valor promocional para compras realizadas fisicamente com pagamento a dinheiro espécie.\n\n";
        
        $message .= " Esse é apenas o orçamento, no fechamento do pedido pode não ter o tecido e a cor correspondente, verificar a disponibilidade antes de fechar!\n\n";
        
        $message .= "Estamos à disposição para maiores informações! ";

        // Clean phone number (remove non-digits)
        $phone = preg_replace('/\D/', '', $contactPhone);
        
        // Add country code if not present
        if (strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        // Build WhatsApp URL with rawurlencode to ensure line breaks work
        $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

        // If AJAX, return URL and Message
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'whatsapp_url' => $whatsappUrl,
                'message' => $message,
            ]);
        }

        // Redirect to WhatsApp
        return redirect()->away($whatsappUrl);
    }

}

