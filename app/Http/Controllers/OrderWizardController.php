<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Status;
use App\Models\CashTransaction;
use App\Models\Store;
use App\Models\ProductOption;
use App\Models\OrderFile;
use App\Models\SizeSurcharge;
use App\Helpers\DateHelper;
use App\Helpers\StoreHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Services\ImageProcessor;

class OrderWizardController extends Controller
{
    public function __construct(
        private readonly ImageProcessor $imageProcessor,
        private readonly \App\Services\OrderWizardService $orderWizardService
    ) {
    }

    public function start(Request $request): View
    {
        \Log::info('OrderWizard: start method called', [
            'session_id' => session()->getId(),
            'current_order_id' => session('current_order_id'),
            'user_id' => auth()->id()
        ]);
        return view('orders.wizard.client');
    }

    public function storeClient(Request $request)
    {
        try {
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

            $order = $this->orderWizardService->processStartOrder($validated, Auth::user());

            session(['current_order_id' => $order->id]);
            session(['wizard.client' => [
                'id' => $order->client->id,
                'name' => $order->client->name,
                'phone_primary' => $order->client->phone_primary,
                'email' => $order->client->email,
            ]]);
            session(['fresh_customization_cleanup' => true]);

            return redirect()->route('orders.wizard.sewing');
        } catch (\Exception $e) {
            \Log::error('Error starting order: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erro ao iniciar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Etapa 2: Escolher tipo de personalização (Deprecated/Skipped)
     */
    public function personalizationType(Request $request)
    {
        return redirect()->route('orders.wizard.sewing');
    }

    /**
     * Etapa 3: Itens baseados no tipo de personalização (Deprecated/Skipped)
     */
    public function items(Request $request)
    {
        return redirect()->route('orders.wizard.sewing');
    }

    public function sewing(Request $request)
    {
        \Log::info('OrderWizard: sewing method called', [
            'method' => $request->method(),
            'session_id' => session()->getId(),
            'current_order_id' => session('current_order_id'),
            'all_session' => session()->all()
        ]);

        if ($request->isMethod('get')) {
            // Debug: Log session data
            \Log::info('=== SEWING DEBUG ===');
            \Log::info('Session current_order_id:', ['id' => session('current_order_id')]);
            \Log::info('All session data:', session()->all());
            
            // Verificar se há um pedido em andamento na sessão
            if (!session('current_order_id')) {
                \Log::info('No current_order_id in session, redirecting to start');
                return redirect()->route('orders.wizard.start')
                    ->with('info', 'Nenhum pedido em andamento. Por favor, inicie um novo pedido.');
            }
            
            // Buscar pedido com fresh() para garantir dados atualizados do banco
            $order = Order::with(['items' => function($query) {
                $query->orderBy('is_pinned', 'desc')->orderBy('id', 'asc');
            }])->find(session('current_order_id'));
            
            // Verificar se o pedido foi encontrado (pode ter sido deletado)
            if (!$order) {
                session()->forget('current_order_id');
                return redirect()->route('orders.wizard.start')
                    ->with('warning', 'O pedido anterior não foi encontrado. Por favor, inicie um novo pedido.');
            }
            
            // Forçar reload dos items para garantir dados frescos
            $order->load('items');
            
            // Buscar tecidos e cores para controle de estoque
            $fabrics = \App\Models\ProductOption::where('type', 'tecido')
                ->where('active', true)
                ->orderBy('name')
                ->get();
            $colors = \App\Models\ProductOption::where('type', 'cor')
                ->where('active', true)
                ->orderBy('name')
                ->get();
            
            // Buscar opções de personalização para seleção no wizard
            $personalizationOptions = \App\Models\ProductOption::where('type', 'personalizacao')
                ->where('active', true)
                ->orderBy('name')
                ->get();

            // Obter loja atual do usuário (respeitando isolamento de tenant)
            $user = Auth::user();
            $currentStoreId = null;
            
            // Primeiro, tentar obter loja do tenant do usuário
            if ($user->tenant_id) {
                $tenantStore = Store::where('tenant_id', $user->tenant_id)
                    ->where('is_main', true)
                    ->first();
                
                if (!$tenantStore) {
                    $tenantStore = Store::where('tenant_id', $user->tenant_id)->first();
                }
                
                if ($tenantStore) {
                    $currentStoreId = $tenantStore->id;
                }
            }
            
            // Fallbacks
            if (!$currentStoreId) {
                if ($user->isAdminLoja()) {
                    $storeIds = $user->getStoreIds();
                    $currentStoreId = !empty($storeIds) ? $storeIds[0] : null;
                } elseif ($user->isVendedor()) {
                    $userStores = $user->stores()->get();
                    if ($userStores->isNotEmpty()) {
                        $currentStoreId = $userStores->first()->id;
                    }
                }
            }
            
            if (!$currentStoreId) {
                $mainStore = Store::where('is_main', true)->first();
                $currentStoreId = $mainStore ? $mainStore->id : null;
            }
            
            \Log::info('=== SEWING VIEW RENDERING ===', [
                'order_id' => $order->id,
                'items_count' => $order->items->count(),
                'currentStoreId' => $currentStoreId
            ]);
            
            // Buscar tipos SUB. TOTAL para o tenant
            $sublimationTypes = \App\Models\SublimationProductType::getForTenant($user->tenant_id);
            // Habilitar se: tenant habilitou OU se existem tipos cadastrados (para super admin)
            $sublimationEnabled = ($user->tenant && $user->tenant->sublimation_total_enabled) || $sublimationTypes->isNotEmpty();
            
            $preselectedTypes = [];
            $preselectedIds = [];
            
            return view('orders.wizard.sewing', compact('order', 'fabrics', 'colors', 'personalizationOptions', 'currentStoreId', 'sublimationTypes', 'sublimationEnabled', 'preselectedTypes', 'preselectedIds'));
        }

        $action = $request->input('action', 'add');

        if ($action === 'add_item') {
            return $this->addItem($request);
        } elseif ($action === 'add_sublimation_item') {
            return $this->addSublimationItem($request);
        } elseif ($action === 'update_item') {
            return $this->updateItem($request);
        } elseif ($action === 'finish') {
            return $this->finishSewing($request);
        } elseif ($action === 'delete_item') {
            return $this->deleteItem($request);
        } elseif ($action === 'save_sub_local_items') {
            return $this->saveSubLocalItems($request);
        }

        return $this->addItem($request);
    }

    private function saveSubLocalItems(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.icon' => 'nullable|string',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));

        $this->orderWizardService->processSaveSubLocalItems($order, $validated['items']);

        return response()->json(['success' => true]);
    }

    private function addItem(Request $request)
    {
        $validated = $request->validate([
            'personalizacao' => 'required|array|min:1',
            'personalizacao.*' => 'exists:product_options,id',
            'tecido' => 'required|exists:product_options,id',
            'tipo_tecido' => 'nullable|exists:product_options,id',
            'cor' => 'required|exists:product_options,id',
            'tipo_corte' => 'required|exists:product_options,id',
            'detalhe' => 'nullable|exists:product_options,id',
            'gola' => 'required|exists:product_options,id',
            'tamanhos' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
            'collar_color' => 'nullable|string|max:100',
            'detail_color' => 'nullable|string|max:100',
            'apply_surcharge' => 'nullable|boolean',
            'is_client_modeling' => 'nullable|boolean',
            'existing_cover_image' => 'nullable|string'
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));

        $coverImagePath = $validated['existing_cover_image'] ?? null;
        if ($request->hasFile('item_cover_image')) {
            $coverImagePath = $this->imageProcessor->processAndStore(
                $request->file('item_cover_image'),
                'orders/items/covers'
            );
        }

        $item = $this->orderWizardService->processAddItem($order, $validated, $coverImagePath);

        // Salvar IDs de personalização na sessão
        session()->push('item_personalizations.' . $item->id, $validated['personalizacao']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item adicionado com sucesso!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item adicionado com sucesso!');
    }

    /**
     * Adicionar item SUB. TOTAL (sublimação total)
     */
    private function addSublimationItem(Request $request)
    {
        $validated = $request->validate([
            'sublimation_type' => 'required|string|max:50',
            'sublimation_addons' => 'nullable|array',
            'sublimation_addons.*' => 'integer',
            'art_name' => 'required|string|max:255',
            'tamanhos' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'corel_file' => 'nullable|file|max:51200',
            'art_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));

        $coverImagePath = null;
        if ($request->hasFile('item_cover_image')) {
            $coverImagePath = $this->imageProcessor->processAndStore(
                $request->file('item_cover_image'),
                'orders/items/covers',
                [
                    'max_width' => 1200,
                    'max_height' => 1200,
                    'quality' => 85,
                ]
            );
        }

        $corelFilePath = null;
        if ($request->hasFile('corel_file')) {
            $corelFile = $request->file('corel_file');
            $fileName = time() . '_' . uniqid() . '_' . $corelFile->getClientOriginalName();
            $corelFilePath = $corelFile->storeAs('orders/corel_files', $fileName, 'public');
        }

        $item = $this->orderWizardService->processAddSublimationItem($order, $validated, $coverImagePath, $corelFilePath);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item SUB. TOTAL adicionado com sucesso!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item adicionado com sucesso!');
    }

    private function updateItem(Request $request)
    {
        $validated = $request->validate([
            'editing_item_id' => 'required|integer',
            'personalizacao' => 'required|array|min:1',
            'personalizacao.*' => 'exists:product_options,id',
            'tecido' => 'required|exists:product_options,id',
            'tipo_tecido' => 'nullable|exists:product_options,id',
            'cor' => 'required|exists:product_options,id',
            'tipo_corte' => 'required|exists:product_options,id',
            'detalhe' => 'nullable|exists:product_options,id',
            'gola' => 'required|exists:product_options,id',
            'tamanhos' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
            'collar_color' => 'nullable|string|max:100',
            'detail_color' => 'nullable|string|max:100',
            'apply_surcharge' => 'nullable|boolean',
            'is_client_modeling' => 'nullable|boolean',
            'existing_cover_image' => 'nullable|string'
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        $item = $order->items()->findOrFail($validated['editing_item_id']);

        $coverImagePath = $item->cover_image;
        if ($request->hasFile('item_cover_image')) {
            $newCoverImagePath = $this->imageProcessor->processAndStore(
                $request->file('item_cover_image'),
                'orders/items/covers'
            );

            if ($newCoverImagePath) {
                if ($coverImagePath) $this->imageProcessor->delete($coverImagePath);
                $coverImagePath = $newCoverImagePath;
            }
        }

        $this->orderWizardService->processUpdateItem($item, $validated, $coverImagePath);

        // Atualizar personalizações na sessão
        session(['item_personalizations.' . $item->id => [$validated['personalizacao']]]);


        $order->refresh();
        $order->load('items');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item atualizado com sucesso!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item atualizado com sucesso!');
    }

    private function deleteItem(Request $request)
    {
        $itemId = $request->input('item_id');
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        $item = $order->items()->findOrFail($itemId);

        $this->orderWizardService->processDeleteItem($item);
        
        // Refresh order to get updated items list
        $order->refresh();
        $order->load('items');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removido com sucesso!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item removido com sucesso!');
    }

    private function finishSewing(Request $request)
    {
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        if ($order->items()->count() === 0) {
            return redirect()->route('orders.wizard.sewing')->with('error', 'Adicione pelo menos um item antes de continuar.');
        }

        // Verificar se todos os itens sÃ£o SUB. TOTAL
        $allSublimationTotal = $order->items->every(function ($item) {
            return (bool) $item->is_sublimation_total;
        });

        // Coletar todas as personalizaÃ§Ãµes Ãºnicas de todos os itens
        $allPersonalizations = [];
        foreach ($order->items as $item) {
            $itemPersonalizations = session('item_personalizations.' . $item->id, [[]]);
            $allPersonalizations = array_merge($allPersonalizations, $itemPersonalizations[0] ?? []);
        }
        $allPersonalizations = array_unique($allPersonalizations);

        // Salvar total de camisas na sessÃ£o para usar na personalizaÃ§Ã£o
        session(['total_shirts' => $order->items()->sum('quantity')]);
        
        // Salvar personalizaÃ§Ãµes selecionadas na sessÃ£o
        session(['selected_personalizations' => $allPersonalizations]);

        // Se todos os itens forem SUB. TOTAL, pular a etapa de personalizaÃ§Ã£o (Stage 3)
        // pois eles jÃ¡ sÃ£o configurados inteiramente na Stage 2.
        if ($allSublimationTotal) {
            session(['selected_personalizations' => []]);
            return redirect()->route('orders.wizard.payment');
        }

        // Se todas as personalizaÃ§Ãµes forem "lisas", pular a etapa de personalizaÃ§Ã£o
        if (!empty($allPersonalizations)) {
            $personalizationNames = ProductOption::whereIn('id', $allPersonalizations)->pluck('name');
            $onlyLisas = $personalizationNames->isNotEmpty() &&
                $personalizationNames->every(function ($name) {
                    return stripos($name ?? '', 'LISA') !== false;
                });

            if ($onlyLisas) {
                // Limpar personalizaÃ§Ãµes para a prÃ³xima etapa e seguir direto
                session(['selected_personalizations' => []]);
                return redirect()->route('orders.wizard.payment');
            }
        } elseif (empty($allPersonalizations)) {
            // Se nÃ£o hÃ¡ personalizaÃ§Ãµes, pular Stage 3
            return redirect()->route('orders.wizard.payment');
        }

        return redirect()->route('orders.wizard.customization');
    }

    public function customization(Request $request)
    {
        if ($request->isMethod('get')) {
            $orderId = session('current_order_id') ?? session('edit_order_id');
            $order = Order::with([
                    'items.files',
                    'items.sublimations.files',
                ])
                ->findOrFail($orderId);

            // Limpar personalizaÇõÇæes/arquivos residuais na primeira visita ÇÜ personalizaÇõÇœo do pedido (modo wizard)
            $shouldCleanup = session()->pull('fresh_customization_cleanup', false)
                || (!$request->routeIs('orders.edit.*') && !session()->get('customization_cleaned_'.$orderId, false));

            if ($shouldCleanup) {
                foreach ($order->items as $item) {
                    // Remover arquivos de aplicaÇõÇœes e aplicaÇõÇæes existentes
                    foreach ($item->sublimations as $sub) {
                        $sub->files()->delete();
                    }
                    $item->sublimations()->delete();

                    // Remover arquivos gerais do item e resetar nome da arte
                    $item->files()->delete();
                    $item->update(['art_name' => null]);
                }

                session()->put('customization_cleaned_'.$orderId, true);

                // Recarregar o pedido jÇÜ limpo
                $order->refresh()->load([
                    'items.files',
                    'items.sublimations.files',
                ]);
            }
            
            // Debug: Log item IDs
            \Log::info('DEBUG ITEM IDS BEFORE VIEW:', ['ids' => $order->items->pluck('id')->toArray()]);
            // dd($order->items->pluck('id')); // Uncomment to force dump in browser if needed, using Log for non-blocking.
            
            // Mapear personalizações disponíveis por nome (fallback para pedidos limpos)
            $personalizationLookup = \App\Models\ProductOption::where('type', 'personalizacao')
                ->get()
                ->keyBy(function ($opt) {
                    return strtoupper(trim($opt->name));
                });

            // Coletar personalizações por item
            $itemPersonalizations = [];
            $globalSelected = session('selected_personalizations', []);
            foreach ($order->items as $item) {
                $itemPers = session('item_personalizations.' . $item->id, [[]]);
                \Log::info('Checking session for item:', ['item_id' => $item->id, 'session_key' => 'item_personalizations.'.$item->id, 'result' => $itemPers]);
                $persIds = $itemPers[0] ?? [];
                if (empty($persIds) && !empty($globalSelected)) {
                    $persIds = $globalSelected;
                }
                if (empty($persIds) && $item->print_type) {
                    $parts = array_filter(array_map('trim', explode(',', strtoupper($item->print_type))));
                    foreach ($parts as $name) {
                        if (isset($personalizationLookup[$name])) {
                            $persIds[] = $personalizationLookup[$name]->id;
                        }
                    }
                }
                
                if (!empty($persIds)) {
                    $persNames = \App\Models\ProductOption::whereIn('id', $persIds)
                        ->pluck('name', 'id')
                        ->toArray();
                    
                    $itemPersonalizations[$item->id] = [
                        'item' => $item,
                        'personalization_ids' => $persIds,
                        'personalization_names' => $persNames,
                    ];
                }
            }
            
            // Buscar tamanhos e localizações para cada tipo de personalização
            $personalizationData = [];
            $allTypes = array_keys(\App\Models\PersonalizationPrice::getPersonalizationTypes());
            
            foreach ($allTypes as $type) {
                $sizes = \App\Models\PersonalizationPrice::where('personalization_type', $type)
                    ->where('active', true)
                    ->select('size_name', 'size_dimensions', 'order')
                    ->orderBy('order')
                    ->get()
                    ->unique(function ($item) {
                        return $item->size_name . $item->size_dimensions;
                    });
                
                $personalizationData[$type] = [
                    'sizes' => $sizes
                ];
            }
            
            // Localizações disponíveis
            $locations = \App\Models\SublimationLocation::where('active', true)
                ->orderBy('order')
                ->get();
            
            // Buscar opções especiais (adicionais) configuradas para SUB. TOTAL
            $specialOptions = \App\Models\PersonalizationSpecialOption::where('active', true)
                ->orderBy('order')
                ->get();
            
            // Buscar configurações de personalização (charge_by_color, etc.)
            $personalizationSettings = \App\Models\PersonalizationSetting::all()->keyBy('personalization_type');
            
            // Usar view unificada que mostra todas as personalizações
            return view('orders.wizard.customization-multiple', compact('order', 'itemPersonalizations', 'personalizationData', 'locations', 'specialOptions', 'personalizationSettings', 'personalizationLookup'));
        }

        if ($request->input('action') === 'save_order_art') {
            $orderId = session('current_order_id') ?? session('edit_order_id');
            $validated = $request->validate([
                'item_id' => 'required|exists:order_items,id',
                'order_art_name' => 'nullable|string|max:255',
                'order_art_files' => 'nullable|array',
                'order_art_files.*' => 'nullable|file|max:51200',
            ]);

            $item = OrderItem::where('order_id', $orderId)->findOrFail($validated['item_id']);
            $this->orderWizardService->processSaveOrderArt($item, $validated, $request->file('order_art_files', []));

            return redirect()->back()->with('success', 'Arte do item atualizada com sucesso!');
        }

        try {
            $validated = $request->validate([
                'item_id' => 'required',
                'personalization_type' => 'required|string',
                'personalization_id' => 'nullable',
                'art_name' => 'nullable|string|max:255',
                'location' => 'nullable',
                'size' => 'nullable|string',
                'quantity' => 'nullable|integer|min:1',
                'color_count' => 'nullable|integer|min:1',
                'unit_price' => 'nullable|numeric|min:0',
                'final_price' => 'nullable|numeric|min:0',
                'application_image' => 'nullable|image|max:10240',
                'art_files' => 'nullable|array',
                'art_files.*' => 'nullable|file|max:51200',
                'color_details' => 'nullable|string|max:500',
                'seller_notes' => 'nullable|string|max:1000',
                'addons' => 'nullable|array',
                'regata_discount' => 'nullable|boolean',
                'editing_personalization_id' => 'nullable|integer',
                'linked_item_ids' => 'nullable|array',
                'linked_item_ids.*' => 'nullable|integer|exists:order_items,id',
            ]);
            
            $orderId = session('current_order_id') ?? session('edit_order_id');
            $order = Order::with('items')->findOrFail($orderId);
            
            $applicationImagePath = null;
            if ($request->hasFile('application_image')) {
                $applicationImagePath = $request->file('application_image')->store('orders/applications', 'public');
            }

            // Get list of items to apply personalization to
            $linkedItemIds = $validated['linked_item_ids'] ?? [$validated['item_id']];
            $linkedItemIds = array_unique(array_filter($linkedItemIds));
            
            // If no linked items, use the original item_id
            if (empty($linkedItemIds)) {
                $linkedItemIds = [$validated['item_id']];
            }
            
            $successCount = 0;
            foreach ($linkedItemIds as $itemId) {
                $item = $order->items()->find($itemId);
                if (!$item) continue;
                
                // For linked items, use the item's own quantity instead of the form quantity
                $itemValidated = $validated;
                $itemValidated['quantity'] = $item->quantity;
                
                // Recalculate prices based on item's quantity
                if (isset($itemValidated['unit_price']) && $itemValidated['unit_price'] > 0) {
                    $itemValidated['final_price'] = $itemValidated['unit_price'] * $item->quantity;
                }
                
                // For linked items, use the same personalization data but with correct quantity
                $this->orderWizardService->processSavePersonalization(
                    $item, 
                    $itemValidated, 
                    $applicationImagePath, 
                    $request->file('art_files', [])
                );
                $successCount++;
            }

            $message = $successCount > 1 
                ? "Personalização aplicada em {$successCount} itens com sucesso!" 
                : 'Personalização adicionada com sucesso!';
            
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            \Log::error('Error adding personalization: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao adicionar personalização: ' . $e->getMessage()], 500);
        }
    }

    public function refreshCustomizations(Request $request)
    {
        try {
            $order = Order::with('items')->findOrFail(session('current_order_id'));
            
            // Recarregar o pedido para pegar valores atualizados
            $order->refresh();
            $order->load('items');
            
            // Mapear personalizações disponíveis por nome (fallback)
            $personalizationLookup = \App\Models\ProductOption::where('type', 'personalizacao')
                ->get()
                ->keyBy(function ($opt) {
                    return strtoupper(trim($opt->name));
                });

            // Buscar personalizações existentes por item
            $itemPersonalizations = [];
            $globalSelected = session('selected_personalizations', []);
            foreach ($order->items as $item) {
                // Buscar IDs das personalizações do item
                $itemPers = session('item_personalizations.' . $item->id, [[]]);
                $persIds = $itemPers[0] ?? [];
                if (empty($persIds) && !empty($globalSelected)) {
                    $persIds = $globalSelected;
                }
                if (empty($persIds) && $item->print_type) {
                    $parts = array_filter(array_map('trim', explode(',', strtoupper($item->print_type))));
                    foreach ($parts as $name) {
                        if (isset($personalizationLookup[$name])) {
                            $persIds[] = $personalizationLookup[$name]->id;
                        }
                    }
                }
                
                if (!empty($persIds)) {
                    $persNames = \App\Models\ProductOption::whereIn('id', $persIds)
                        ->pluck('name', 'id')
                        ->toArray();
                    
                    $itemPersonalizations[$item->id] = [
                        'item' => $item,
                        'personalization_ids' => $persIds,
                        'personalization_names' => $persNames,
                    ];
                }
            }
            
            // Dados de personalização por tipo
            $personalizationData = [];
            $types = array_keys(\App\Models\PersonalizationPrice::getPersonalizationTypes());
            
            foreach ($types as $type) {
                $sizes = \App\Models\PersonalizationPrice::where('personalization_type', $type)
                    ->where('active', true)
                    ->select('size_name', 'size_dimensions', 'order')
                    ->orderBy('order')
                    ->get()
                    ->unique(function ($item) {
                        return $item->size_name . $item->size_dimensions;
                    });
                    
                $personalizationData[$type] = [
                    'sizes' => $sizes
                ];
            }
            
            // Localizações disponíveis
            $locations = \App\Models\SublimationLocation::where('active', true)
                ->orderBy('order')
                ->get();
            
            // Retornar apenas a seção de conteúdo
            return view('orders.wizard.customization-multiple', compact('order', 'itemPersonalizations', 'personalizationData', 'locations'))
                ->render();
                
        } catch (\Exception $e) {
            \Log::error('Error refreshing customizations:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar personalizações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payment(Request $request)
    {
        if ($request->isMethod('get')) {
            $order = Order::with('client', 'items')->findOrFail(session('current_order_id'));
            
            // Determine Back Route logic
            $backRoute = route('orders.wizard.customization');
            
            $allSubLocal = true;
            $allSkipCustomization = true;

            if ($order->items->isEmpty()) {
                $allSubLocal = false;
            } else {
                foreach ($order->items as $item) {
                    // Check if item is Sublimação Local
                    if ($item->print_type !== 'Sublimação Local') {
                        $allSubLocal = false;
                        break;
                    }
                    
                    // Check if underlying product requires customization
                    $printDesc = is_string($item->print_desc) ? json_decode($item->print_desc, true) : $item->print_desc;
                    
                    if (is_array($printDesc) && isset($printDesc['product_id'])) {
                         $product = \App\Models\SubLocalProduct::find($printDesc['product_id']);
                         // If product not found or REQUIRES customization, we cannot skip
                         if (!$product || $product->requires_customization) {
                             $allSkipCustomization = false;
                             break;
                         }
                    } else {
                        // Missing metadata, safe fallback
                        $allSkipCustomization = false; 
                        break;
                    }
                }
            }

            // If everything is Sub Local AND allowed to skip, point back to items
            if ($order->items->isNotEmpty() && $allSubLocal && $allSkipCustomization) {
                 $backRoute = route('orders.wizard.items', ['type' => 'sub_local']);
            }

            return view('orders.wizard.payment', compact('order', 'backRoute'));
        }

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_methods' => 'required|json',
            'size_surcharges' => 'nullable|json',
            'order_cover_image' => 'nullable|image|max:10240',
            'discount_type' => 'nullable|string|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        // Processar upload da imagem de capa do pedido
        $orderCoverImagePath = $order->cover_image;
        if ($request->hasFile('order_cover_image')) {
            $orderCoverImagePath = $this->imageProcessor->processAndStore(
                $request->file('order_cover_image'),
                'orders/covers'
            );
        }
        
        $sizeSurcharges = $this->orderWizardService->processSavePayment($order, $validated, $orderCoverImagePath);

        // Salvar acréscimos na sessão para exibir no resumo
        session(['size_surcharges' => $sizeSurcharges]);

        return redirect()->route('orders.wizard.confirm');
    }

    public function confirm()
    {
        $orderId = session('current_order_id');
        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão do pedido expirada.');
        }

        $order = Order::with(['client', 'items.sublimations.size', 'items.sublimations.location', 'items.files', 'status'])
            ->find($orderId);

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Pedido não encontrado.');
        }
        
        $payment = Payment::where('order_id', $order->id)->get();
        $sizeSurcharges = session('size_surcharges', []);
        
        // Buscar configurações da empresa da loja do pedido
        $companySettings = \App\Models\CompanySetting::getSettings($order->store_id);
        
        return view('orders.wizard.confirm', compact('order', 'payment', 'sizeSurcharges', 'companySettings'));
    }

    public function finalize(Request $request): RedirectResponse
    {
        try {
            $orderId = session('current_order_id');
            if (!$orderId) {
                return redirect()->route('orders.wizard.start')->with('error', 'Sessão expirada. Por favor, inicie um novo pedido.');
            }
            
            $order = Order::with(['items.sublimations'])->findOrFail($orderId);
            
            // Processar uploads de itens se houver
            $itemsData = [];
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $data) {
                    $itemsData[$itemId] = ['art_name' => $data['art_name'] ?? null];
                    
                    if ($request->hasFile("items.{$itemId}.cover_image")) {
                        $itemsData[$itemId]['cover_image_path'] = $request->file("items.{$itemId}.cover_image")->store('orders/covers', 'public');
                    }
                }
            }

            $finalizeData = [
                'is_event' => $request->boolean('is_event'),
                'items' => $itemsData,
            ];

            $this->orderWizardService->processFinalizeOrder($order, $finalizeData, Auth::id());
            
            // Limpar sessão
            session()->forget(['current_order_id', 'item_personalizations', 'size_surcharges', 'personalization_type']);
            
            return redirect()->route('kanban.index')->with('success', 'Pedido #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' confirmado com sucesso!');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar pedido: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao confirmar pedido: ' . $e->getMessage());
        }
    }

    public function deletePersonalization($id)
    {
        try {
            $this->orderWizardService->processDeletePersonalization($id);
            return redirect()->back()->with('success', 'Personalização removida com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao remover personalização: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao remover personalização.');
        }
    }

    public function togglePin($id)
    {
        try {
            $item = \App\Models\OrderItem::findOrFail($id);
            $item->is_pinned = !$item->is_pinned;
            $item->save();

            return response()->json([
                'success' => true,
                'is_pinned' => $item->is_pinned,
                'message' => $item->is_pinned ? 'Item fixado no topo!' : 'Item desafixado.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status do item'
            ], 500);
        }
    }
}
