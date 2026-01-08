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
    public function __construct(private readonly ImageProcessor $imageProcessor)
    {
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

    public function storeClient(Request $request): RedirectResponse
    {
        \Log::info('=== STORE CLIENT DEBUG START ===');
        \Log::info('Request method:', ['method' => $request->method()]);
        \Log::info('Request data:', $request->all());
        \Log::info('User authenticated:', ['auth' => auth()->check(), 'user_id' => auth()->id()]);
        
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

        // Obter store_id do usuário (respeitando isolamento de tenant)
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
        
        // Se ainda não encontrou (super admin ou usuário sem tenant), usar loja principal do tenant ou geral
    if (!$storeId) {
        $mainStore = Store::where('is_main', true)
            ->where('tenant_id', $user->tenant_id)
            ->first();
        
        if (!$mainStore && !$user->tenant_id) {
            $mainStore = Store::where('is_main', true)->first();
        }
        
        $storeId = $mainStore ? $mainStore->id : null;
    }
        
        \Log::info('Store ID resolvido para pedido', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'tenant_id' => $user->tenant_id,
            'store_id' => $storeId,
        ]);
        
        // Se client_id foi enviado, atualiza o cliente existente
        if (!empty($validated['client_id'])) {
            $client = Client::findOrFail($validated['client_id']);
            $client->update($validated);
        } else {
            // Senão, cria um novo cliente com store_id
            $validated['store_id'] = $storeId;
            $client = Client::create($validated);
        }

        $status = Status::orderBy('position')->first();

        // Calcular data de entrega (15 dias úteis)
        $deliveryDate = DateHelper::calculateDeliveryDate(Carbon::now(), 15);

        $order = Order::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'store_id' => $storeId,
            'status_id' => $status?->id ?? Status::withoutGlobalScopes()->orderBy('id')->first()?->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => $deliveryDate->toDateString(),
            'is_draft' => true, // Criar como rascunho
        ]);

        session(['current_order_id' => $order->id]);
        // Salvar dados do cliente na sessão para exibição no wizard
        session(['wizard.client' => [
            'id' => $client->id,
            'name' => $client->name,
            'phone_primary' => $client->phone_primary,
            'email' => $client->email,
        ]]);
        // Marcar para limpar personalizações residuais quando chegar na etapa de personalização
        session(['fresh_customization_cleanup' => true]);

        \Log::info('=== STORE CLIENT DEBUG END - SUCCESS ===');
        return redirect()->route('orders.wizard.personalization-type');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('=== STORE CLIENT VALIDATION FAILED ===', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('=== STORE CLIENT GENERAL ERROR ===', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Etapa 2: Escolher tipo de personalização
     */
    public function personalizationType(Request $request)
    {
        // Verificar se tem ordem ou cliente na sessão
        $orderId = session('current_order_id');
        
        if (!$orderId && !session('wizard.client')) {
            return redirect()->route('orders.wizard.start')->with('error', 'Selecione um cliente primeiro.');
        }

        // Se não tem wizard.client mas tem order_id, buscar do pedido
        if (!session('wizard.client') && $orderId) {
            $order = Order::with('client')->find($orderId);
            if ($order && $order->client) {
                session(['wizard.client' => [
                    'id' => $order->client->id,
                    'name' => $order->client->name,
                    'phone_primary' => $order->client->phone_primary,
                    'email' => $order->client->email,
                ]]);
            }
        }

        return view('orders.wizard.personalization-type');
    }

    /**
     * Etapa 3: Itens baseados no tipo de personalização
     */
    public function items(Request $request)
    {
        $type = $request->get('type', session('wizard.personalization_type'));
        
        if (!$type) {
            return redirect()->route('orders.wizard.personalization-type')->with('error', 'Selecione um tipo de personalização.');
        }

        // Salvar tipo na sessão
        session(['wizard.personalization_type' => $type]);

        // Retornar a view específica baseada no tipo
        switch ($type) {
            case 'sub_local':
                // Sublimação Local - estilo totem McDonald's
                $products = \App\Models\SubLocalProduct::where('is_active', true)->orderBy('sort_order')->get();
                return view('orders.wizard.items-sub-local', compact('products'));
            
            case 'sub_total':
                // Sublimação Total - usa o sistema existente
                return redirect()->route('orders.wizard.sewing', ['type' => 'sub_total']);
            
            case 'serigrafia':
            case 'dtf':
            case 'bordado':
            case 'emborrachado':
            case 'lisas':
            default:
                // Outros tipos - usa o fluxo de costura padrão
                return redirect()->route('orders.wizard.sewing', ['type' => $type]);
        }
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
            
            return view('orders.wizard.sewing', compact('order', 'fabrics', 'colors', 'currentStoreId', 'sublimationTypes', 'sublimationEnabled'));
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

        // Opcional: Limpar itens anteriores se for substituir
        // $order->items()->delete(); 

        foreach ($validated['items'] as $itemData) {
            $itemNumber = $order->items()->count() + 1;
            
            // Adaptar para OrderItem
            // Usamos campos existentes para armazenar dados do produto pronto
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
                'unit_cost' => 0,
                'total_cost' => 0,
                'print_desc' => json_encode(['is_sub_local' => true, 'product_id' => $itemData['id']]),
            ]);
            
            $order->items()->save($item);
        }

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

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
            'apply_surcharge' => 'nullable|boolean',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));

        // Processar upload da imagem de capa do item
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

        // Otimização: Buscar todas as opções em uma única query
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
            'total_price' => (function() use ($validated) {
                $basePrice = $validated['unit_price'];
                $totalSurcharge = 0;
                foreach ($validated['tamanhos'] as $size => $quantity) {
                    if ($quantity > 0) {
                        $surchargeModel = \App\Models\SizeSurcharge::getSurchargeForSize($size, $basePrice);
                        if ($surchargeModel) {
                            $totalSurcharge += $surchargeModel->surcharge * $quantity;
                        }
                    }
                }
                return ($basePrice * $validated['quantity']) + $totalSurcharge;
            })(),
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'print_desc' => json_encode(['apply_surcharge' => $request->boolean('apply_surcharge')]),
        ]);
        $order->items()->save($item);
        $item->refresh(); // Garantir que temos o ID correto gerado pelo banco

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        // Salvar IDs de personalização vinculadas a este item
        session()->push('item_personalizations.' . $item->id, $validated['personalizacao']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item ' . $itemNumber . ' adicionado com sucesso!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item ' . $itemNumber . ' adicionado com sucesso!');
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

        // Processar upload da imagem de capa
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

        // Processar upload do arquivo Corel
        $corelFilePath = null;
        if ($request->hasFile('corel_file')) {
            $corelFile = $request->file('corel_file');
            $fileName = time() . '_' . uniqid() . '_' . $corelFile->getClientOriginalName();
            $corelFilePath = $corelFile->storeAs('orders/corel_files', $fileName, 'public');
        }

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

        // Criar item SUB. TOTAL
        $item = new OrderItem([
            'item_number' => $itemNumber,
            'fabric' => $fabricName,
            'color' => 'Branco',
            'collar' => $addonsLabel ?: '-',
            'model' => $typeLabel,
            'detail' => null,
            'print_type' => 'SUB. TOTAL',
            'art_name' => $validated['art_name'],
            'sizes' => $validated['tamanhos'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $validated['unit_price'] * $validated['quantity'],
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'corel_file_path' => $corelFilePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'is_sublimation_total' => true,
            'sublimation_type' => $validated['sublimation_type'],
            'sublimation_addons' => $validated['sublimation_addons'] ?? [],
        ]);
        $order->items()->save($item);
        $item->refresh();

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item SUB. TOTAL #' . $itemNumber . ' adicionado!',
                'html' => view('orders.wizard.partials.items_sidebar', compact('order'))->render(),
                'items_data' => $order->items->toArray()
            ]);
        }

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item SUB. TOTAL #' . $itemNumber . ' adicionado!');
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
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
            'apply_surcharge' => 'nullable|boolean',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        $item = $order->items()->findOrFail($validated['editing_item_id']);

        // Processar upload da imagem de capa do item
        $coverImagePath = $item->cover_image; // Manter imagem atual se não houver nova
        if ($request->hasFile('item_cover_image')) {
            $newCoverImagePath = $this->imageProcessor->processAndStore(
                $request->file('item_cover_image'),
                'orders/items/covers',
                [
                    'max_width' => 1200,
                    'max_height' => 1200,
                    'quality' => 85,
                ]
            );

            if ($newCoverImagePath) {
                $this->imageProcessor->delete($coverImagePath);
                $coverImagePath = $newCoverImagePath;
            }
        }

        // Buscar nomes das opções
        $personalizacoes = \App\Models\ProductOption::whereIn('id', $validated['personalizacao'])->get();
        $personalizacaoNames = $personalizacoes->pluck('name')->join(', ');

        $tecido = \App\Models\ProductOption::find($validated['tecido']);
        $tipoTecido = $validated['tipo_tecido'] ? \App\Models\ProductOption::find($validated['tipo_tecido']) : null;
        $cor = \App\Models\ProductOption::find($validated['cor']);
        $tipoCorte = \App\Models\ProductOption::find($validated['tipo_corte']);
        $detalhe = $validated['detalhe'] ? \App\Models\ProductOption::find($validated['detalhe']) : null;
        $gola = \App\Models\ProductOption::find($validated['gola']);

        // Calcular preço base
        $basePrice = $tipoCorte->price ?? 0;
        if ($detalhe) {
            $basePrice += $detalhe->price ?? 0;
        }
        if ($gola) {
            $basePrice += $gola->price ?? 0;
        }

        // Processar tamanhos
        $sizes = [];
        $totalQuantity = 0;
        foreach ($validated['tamanhos'] as $size => $quantity) {
            if ($quantity > 0) {
                $sizes[$size] = $quantity;
                $totalQuantity += $quantity;
            }
        }

        // Atualizar item
        $item->update([
            'print_type' => $personalizacaoNames,
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'sizes' => json_encode($sizes),
            'quantity' => $totalQuantity,
            'unit_price' => $basePrice,
            'total_price' => (function() use ($sizes, $basePrice) {
                $totalSurcharge = 0;
                $totalQuantity = array_sum($sizes);
                foreach ($sizes as $size => $quantity) {
                    if ($quantity > 0) {
                        $surchargeModel = \App\Models\SizeSurcharge::getSurchargeForSize($size, $basePrice);
                        if ($surchargeModel) {
                            $totalSurcharge += $surchargeModel->surcharge * $quantity;
                        }
                    }
                }
                return ($basePrice * $totalQuantity) + $totalSurcharge;
            })(),
            'unit_cost' => $validated['unit_cost'] ?? 0,
            'total_cost' => ($validated['unit_cost'] ?? 0) * $totalQuantity,
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
            'print_desc' => json_encode(['apply_surcharge' => $request->boolean('apply_surcharge')]),
        ]);

        // Forçar refresh do modelo para garantir que os dados estão atualizados
        $item->refresh();

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        // Forçar refresh do pedido para garantir dados atualizados
        $order->refresh();

        // Atualizar personalizações na sessão
        session(['item_personalizations.' . $item->id => [$validated['personalizacao']]]);

        \Log::info('Item atualizado com sucesso', [
            'item_id' => $item->id,
            'order_id' => $order->id,
            'new_quantity' => $totalQuantity,
            'new_unit_price' => $basePrice,
            'new_data' => $item->toArray()
        ]);

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item atualizado com sucesso!');
    }

    private function deleteItem(Request $request)
    {
        $itemId = $request->input('item_id');
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        $item = $order->items()->findOrFail($itemId);
        $item->delete();

        // Renumerar itens
        $items = $order->items()->orderBy('id')->get();
        foreach ($items as $index => $it) {
            $it->update(['item_number' => $index + 1]);
        }

        // Garantir que a relaçã̃o esteja atualizada para a view/JSON
        $order->setRelation('items', $items);

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

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
            return view('orders.wizard.customization-multiple', compact('order', 'itemPersonalizations', 'personalizationData', 'locations', 'specialOptions', 'personalizationSettings'));
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
            $item->update([
                'art_name' => filled($validated['order_art_name'] ?? null)
                    ? trim($validated['order_art_name'])
                    : null,
            ]);

            if ($request->hasFile('order_art_files')) {
                foreach ($request->file('order_art_files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . $originalName;
                    $filePath = $file->storeAs('orders/art_files', $fileName, 'public');

                    OrderFile::create([
                        'order_item_id' => $item->id,
                        'file_name' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Arte do item atualizada com sucesso!');
        }

        // Debug: Log dos dados recebidos antes da validação
        \Log::info('=== PERSONALIZATION FORM DEBUG ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Files received: ' . ($request->file('art_files') ? 'YES' : 'NO'));
        
        try {
            // Validação dos campos do formulário
            $validated = $request->validate([
                'item_id' => 'required',
                'personalization_type' => 'required|string',
                'personalization_id' => 'required|integer',
                'art_name' => 'nullable|string|max:255',
                'location' => 'nullable', // Tornado opcional para SUB. TOTAL
                'size' => 'nullable|string', // Tornado opcional para SUB. TOTAL
                'quantity' => 'nullable|integer|min:1', // Tornado opcional para SUB. TOTAL
                'color_count' => 'nullable|integer|min:1',
                'unit_price' => 'nullable|numeric|min:0',
                'final_price' => 'nullable|numeric|min:0',
                'application_image' => 'nullable|image|max:10240',
                'art_files' => 'nullable|array',
                'art_files.*' => 'nullable|file|max:51200', // Máximo 50MB por arquivo
                'color_details' => 'nullable|string|max:500',
                'seller_notes' => 'nullable|string|max:1000',
                'addons' => 'nullable|array', // Para adicionais de SUB. TOTAL
                'regata_discount' => 'nullable|boolean', // Para desconto REGATA
            ]);
            
            \Log::info('Validation passed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', Arr::flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during validation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage()
            ], 500);
        }

        try {
            $order = Order::with('items')->findOrFail(session('current_order_id'));
            
            if ($validated['item_id'] == 0) {
                 // Fallback for new items in budget mode (implied context)
                 $item = $order->items()->first();
            } else {
                 $item = $order->items()->findOrFail($validated['item_id']);
            }

            // Processar imagem da aplicação
        $applicationImagePath = null;
        if ($request->hasFile('application_image')) {
            $appImage = $request->file('application_image');
            $appImageName = time() . '_' . uniqid() . '_' . $appImage->getClientOriginalName();
            $applicationImagePath = $appImage->storeAs('orders/applications', $appImageName, 'public');
        }

        // Debug: Log dos dados recebidos
        \Log::info('=== PERSONALIZATION LOCATION DEBUG ===');
        \Log::info('Received data:', [
            'personalization_type' => $validated['personalization_type'],
            'location' => $validated['location'] ?? 'NOT_SET',
            'size' => $validated['size'] ?? 'NOT_SET',
            'art_name' => $validated['art_name'] ?? 'NOT_SET'
        ]);

        // Buscar localização (apenas se não for SUB. TOTAL)
        $locationId = null;
        $locationName = null;
        if ($validated['personalization_type'] !== 'SUB. TOTAL' && isset($validated['location']) && $validated['location']) {
            $location = \App\Models\SublimationLocation::find($validated['location']);
            $locationId = $location ? $location->id : null;
            $locationName = $location ? $location->name : ($validated['location'] ?? null);
            
            \Log::info('Location found:', [
                'location_id' => $locationId,
                'location_name' => $locationName
            ]);
        } else {
            \Log::info('Location not set or SUB. TOTAL type');
        }

        // Para SUB. TOTAL, buscar quantidade do "Total de Peças" do item
        $quantity = $validated['quantity'] ?? 1;
        if ($validated['personalization_type'] === 'SUB. TOTAL') {
            // Para SUB. TOTAL, usar a quantidade total do item
            $quantity = $item->quantity;
        }

        // Debug: Log dos preços recebidos
        \Log::info('=== PERSONALIZATION PRICE DEBUG ===');
        \Log::info('Received prices:', [
            'unit_price' => $validated['unit_price'] ?? 'null',
            'final_price' => $validated['final_price'] ?? 'null',
            'personalization_type' => $validated['personalization_type'],
            'quantity' => $quantity
        ]);

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

        // Debug: Log da personalização criada
        \Log::info('Personalization created:', [
            'id' => $personalization->id,
            'unit_price' => $personalization->unit_price,
            'final_price' => $personalization->final_price,
            'quantity' => $personalization->quantity
        ]);

        // TODO: Processar adicionais para SUB. TOTAL (após criar migration)
        // if ($validated['personalization_type'] === 'SUB. TOTAL' && $request->has('addons')) {
        //     $addons = $request->input('addons', []);
        //     $regataDiscount = $request->boolean('regata_discount', false);
        //     
        //     // Adicionar desconto REGATA se marcado
        //     if ($regataDiscount) {
        //         $addons[] = 'REGATA_DISCOUNT';
        //     }
        //     
        //     // Salvar adicionais como JSON na personalização
        //     $personalization->update([
        //         'addons' => json_encode($addons),
        //         'regata_discount' => $regataDiscount,
        //     ]);
        // }

        // Processar arquivos da arte (CDR, PDF, etc.)
        if ($request->hasFile('art_files')) {
            foreach ($request->file('art_files') as $file) {
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
        }

        // Aplicar descontos automáticos nas personalizações
        \App\Helpers\PersonalizationDiscountHelper::applyDiscounts($item->id);

        // Atualizar o preço total do item somando todas as personalizações
        $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)
            ->sum('final_price');
        
        // Calcular novo total do item (preço base + personalizações)
        $basePrice = $item->unit_price * $item->quantity;
        $newTotalPrice = $basePrice + $totalPersonalizations;
        
        $item->update([
            'total_price' => $newTotalPrice
        ]);

        // Atualizar subtotal do pedido
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
            ]);

            // Retornar resposta JSON
            return response()->json([
                'success' => true,
                'message' => 'Personalização adicionada com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding personalization:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar personalização: ' . $e->getMessage()
            ], 500);
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
            $newOrderCoverImagePath = $this->imageProcessor->processAndStore(
                $request->file('order_cover_image'),
                'orders/covers',
                [
                    'max_width' => 1400,
                    'max_height' => 1400,
                    'quality' => 85,
                ]
            );

            if ($newOrderCoverImagePath) {
                $this->imageProcessor->delete($orderCoverImagePath);
                $orderCoverImagePath = $newOrderCoverImagePath;
            }
        }
        
        $subtotal = $order->items()->sum('total_price');
        $delivery = (float)($validated['delivery_fee'] ?? 0);
        
        // Processar acréscimos por tamanho
        // Recalcular acréscimos no backend para garantir integridade e aplicar regras de restrição
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
            
            // Se for restrito (Infantil/Baby look) e NÃO tiver o checkbox marcado, ignora os tamanhos deste item
            if ($isRestricted && !$applySurcharge) {
                continue;
            }
            
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

        // Desconto
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float)($validated['discount_value'] ?? 0);
        $subtotalWithFees = $subtotal + $totalSurcharges + $delivery;
        $discountAmount = 0.0;
        if ($discountType === 'percentage') {
            $discountValue = max(0, min(100, $discountValue));
            $discountAmount = ($subtotalWithFees * $discountValue) / 100.0;
        } elseif ($discountType === 'fixed') {
            $discountAmount = min($discountValue, $subtotalWithFees);
        }
        
        // Processar múltiplas formas de pagamento
        $paymentMethods = json_decode($validated['payment_methods'], true);
        $totalPaid = array_sum(array_column($paymentMethods, 'amount'));
        
        $total = max(0, $subtotalWithFees - $discountAmount);

        $order->update([
            'subtotal' => $subtotal,
            'delivery_fee' => $delivery,
            'discount' => $discountAmount,
            'total' => $total,
            'cover_image' => $orderCoverImagePath,
        ]);

        // Deletar pagamentos antigos para evitar acúmulo
        Payment::where('order_id', $order->id)->delete();
        
        // Deletar transações de caixa antigas deste pedido
        CashTransaction::where('order_id', $order->id)->delete();

        // Criar registro de pagamento
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

        // Registrar entrada(s) no caixa como "pendente" até o pedido ser entregue
        $user = Auth::user();
        foreach ($paymentMethods as $method) {
            CashTransaction::create([
                'store_id' => $order->store_id,
                'type' => 'entrada',
                'category' => 'Venda',
                'description' => "Pagamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . $order->client->name,
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
            
            // Buscar termos e condições baseados no tipo de personalização do pedido
            $activeTerms = \App\Models\TermsCondition::getActiveForOrder($order);
            $termsVersion = '1.0';
            
            // Se encontrou termos específicos, usar a versão do primeiro termo encontrado
            if ($activeTerms->isNotEmpty()) {
                $termsVersion = $activeTerms->first()->version ?? '1.0';
            } else {
                // Fallback: buscar termos gerais
                $generalTerms = \App\Models\TermsCondition::getActive();
                if ($generalTerms) {
                    $termsVersion = $generalTerms->version ?? '1.0';
                }
            }
            
            // Buscar status "Pendente"
            $pendenteStatus = Status::where('name', 'Pendente')->first();
            
            // Garantir que a data de entrega esteja definida (preservar existente ou calcular 15 dias úteis)
            $deliveryDate = $order->delivery_date;
            if (!$deliveryDate) {
                // Calcular 15 dias úteis a partir da data de criação do pedido
                $deliveryDate = DateHelper::calculateDeliveryDate($order->created_at, 15)->format('Y-m-d');
            } else {
                // Garantir formato correto (apenas data, sem horário)
                $deliveryDate = \Carbon\Carbon::parse($deliveryDate)->format('Y-m-d');
            }
            
            // Confirmar o pedido (tirar do modo rascunho e colocar em Pendente)
            $updateData = [
                'is_draft' => false,
                'status_id' => $pendenteStatus ? $pendenteStatus->id : $order->status_id,
                'delivery_date' => $deliveryDate,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'terms_version' => $termsVersion
            ];
            
            // Processar checkbox de evento
            if ($request->has('is_event') && $request->input('is_event') == '1') {
                $updateData['contract_type'] = 'EVENTO';
                $updateData['is_event'] = true;
            } else {
                $updateData['is_event'] = false;
            }

            // Atualizar itens com dados da tela de confirmação (Nome da Arte e Capa)
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $data) {
                    $item = \App\Models\OrderItem::find($itemId);
                    if ($item && $item->order_id == $order->id) {
                        // Atualizar Nome da Arte
                        if (isset($data['art_name'])) {
                            $item->update(['art_name' => $data['art_name']]);
                        }
                        
                        // Atualizar Imagem de Capa
                        if ($request->hasFile("items.{$itemId}.cover_image")) {
                            // Remover imagem antiga se existir
                            if ($item->cover_image && \Storage::disk('public')->exists($item->cover_image)) {
                                \Storage::disk('public')->delete($item->cover_image);
                            }
                            
                            $file = $request->file("items.{$itemId}.cover_image");
                            $path = $file->store('orders/covers', 'public');
                            $item->update(['cover_image' => $path]);
                        }
                    }
                }
            }
            
            $order->update($updateData);
            
            // Registrar histórico de venda
            try {
                \App\Models\SalesHistory::recordSale($order);
            } catch (\Exception $e) {
                \Log::warning('Erro ao registrar histórico de venda', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id,
                ]);
            }

            // Registrar tracking de status inicial
            try {
                \App\Models\OrderStatusTracking::recordEntry($order->id, $order->status_id, Auth::id());
            } catch (\Exception $e) {
                \Log::warning('Erro ao registrar tracking de status', [
                    'error' => $e->getMessage(),
                    'order_id' => $order->id,
                ]);
            }
            
            // Criar log de confirmação
            \App\Models\OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Sistema',
                'action' => 'PEDIDO_CONFIRMADO',
                'description' => 'Pedido confirmado e enviado para produção.',
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
            
            // Limpar sessão
            session()->forget(['current_order_id', 'item_personalizations', 'size_surcharges']);
            
            return redirect()->route('kanban.index')->with('success', 'Pedido #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' confirmado com sucesso!');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar pedido: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Erro ao confirmar pedido: ' . $e->getMessage());
        }
    }

    public function deletePersonalization($id)
    {
        try {
            $personalization = \App\Models\OrderSublimation::findOrFail($id);
            $itemId = $personalization->order_item_id;
            $personalization->delete();
            
            // Recalcular total do item
            $item = \App\Models\OrderItem::findOrFail($itemId);
            $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $itemId)
                ->sum('final_price');
            
            $basePrice = $item->unit_price * $item->quantity;
            $newTotalPrice = $basePrice + $totalPersonalizations;
            
            $item->update([
                'total_price' => $newTotalPrice
            ]);
            
            // Recalcular subtotal do pedido
            $order = $item->order;
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Personalização removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover personalização'
            ], 500);
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
