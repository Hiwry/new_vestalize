<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\OrderEditHistory;
use App\Models\OrderEditRequest;
use App\Models\OrderLog;
use App\Models\ProductOption;
use App\Models\PersonalizationPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ImageProcessor;

class EditOrderController extends Controller
{
    public function __construct(private readonly ImageProcessor $imageProcessor)
    {
    }

    public function start($id)
    {
        try {
            $order = Order::with(['client', 'items', 'editRequests'])->find($id);
            
            if (!$order) {
                return redirect()->back()->with('error', 'Pedido não encontrado.');
            }

            // Verificar permissões de edição
            if (!Auth::user()->isAdmin()) {
                // Vendedor precisa de aprovação do admin
                $approvedEditRequest = $order->editRequests->where('status', 'approved')->first();
                
                if (!$approvedEditRequest) {
                    return redirect()->route('orders.index')
                        ->with('error', 'Você precisa solicitar e obter aprovação do admin para editar este pedido.');
                }
            }

            // Inicializar sessão com dados básicos
            session([
                'edit_order_id' => $order->id,
                'edit_order_data' => [
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
                ]
            ]);

            return redirect()->route('orders.edit.client');
            
        } catch (\Exception $e) {
            Log::error('Error in start method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao iniciar edição: ' . $e->getMessage());
        }
    }

    public function client(Request $request)
    {
        try {
            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            $order = Order::with('client')->findOrFail($orderId);

            if ($request->isMethod('post')) {
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

                $editData['client'] = $validated;
                session(['edit_order_data' => $editData]);

                return redirect()->route('orders.edit.sewing');
            }

            return view('orders.wizard.client', compact('order', 'editData'));
            
        } catch (\Exception $e) {
            Log::error('Error in client method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function sewing(Request $request)
    {
        try {
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            // Buscar pedido com dados frescos do banco, ordenados
            $order = Order::with(['items' => function($query) {
                $query->orderBy('is_pinned', 'desc')->orderBy('id', 'asc');
            }])->findOrFail($orderId);
            
            // Forçar reload dos items para garantir dados atualizados
            $order->load('items');
            
            $editData = session('edit_order_data', []);

            // Garantir que editData tenha a estrutura esperada
            if (!isset($editData['items'])) {
                $editData['items'] = [];
            }

            // Converter IDs existentes para nomes se necessário (usando tipos em português!)
            if (!empty($editData['items'])) {
                foreach ($editData['items'] as &$item) {
                    // Se o item tem IDs numéricos, converter para nomes
                    if (is_numeric($item['fabric'])) {
                        $item['fabric'] = $this->getProductOptionName($item['fabric'], 'tecido');
                    }
                    if (is_numeric($item['color'])) {
                        $item['color'] = $this->getProductOptionName($item['color'], 'cor');
                    }
                    if (is_numeric($item['collar'])) {
                        $item['collar'] = $this->getProductOptionName($item['collar'], 'gola');
                    }
                    if (is_numeric($item['model'])) {
                        $item['model'] = $this->getProductOptionName($item['model'], 'tipo_corte');
                    }
                    if (is_numeric($item['detail'])) {
                        $item['detail'] = $this->getProductOptionName($item['detail'], 'detalhe');
                    }
                    if (is_numeric($item['print_type'])) {
                        $item['print_type'] = $this->getPersonalizationName($item['print_type']);
                    }
                }
                // Salvar os dados convertidos de volta na sessão
                session(['edit_order_data' => $editData]);
            }

            // Forçar conversão de todos os itens existentes
            if (!empty($editData['items'])) {
                foreach ($editData['items'] as &$item) {
                    // Converter todos os campos que podem ser IDs (usando tipos em português!)
                    $item['fabric'] = $this->convertToName($item['fabric'], 'tecido');
                    $item['color'] = $this->convertToName($item['color'], 'cor');
                    $item['collar'] = $this->convertToName($item['collar'], 'gola');
                    $item['model'] = $this->convertToName($item['model'], 'tipo_corte');
                    $item['detail'] = $this->convertToName($item['detail'], 'detalhe');
                    $item['print_type'] = $this->convertToName($item['print_type'], 'personalization');
                }
                // Salvar os dados convertidos de volta na sessão
                session(['edit_order_data' => $editData]);
            }

            // Se não há itens na sessão, carregar do banco e converter
            if (empty($editData['items']) && $order->items->count() > 0) {
                $editData['items'] = [];
                foreach ($order->items as $item) {
                    // Garantir que sizes é sempre array
                    $sizes = $item->sizes;
                    if (is_string($sizes)) {
                        $sizes = json_decode($sizes, true) ?? [];
                    }
                    
                    Log::info('Carregando item do banco', [
                        'item_id' => $item->id,
                        'sizes_original' => $item->sizes,
                        'sizes_type' => gettype($item->sizes),
                        'sizes_processed' => $sizes,
                        'sizes_processed_type' => gettype($sizes)
                    ]);
                    
                    $editData['items'][] = [
                        'id' => $item->id,
                        'item_number' => $item->item_number,
                        'fabric' => $this->getProductOptionName($item->fabric, 'tecido'),
                        'color' => $this->getProductOptionName($item->color, 'cor'),
                        'collar' => $this->getProductOptionName($item->collar, 'gola'),
                        'model' => $this->getProductOptionName($item->model, 'tipo_corte'),
                        'detail' => $this->getProductOptionName($item->detail, 'detalhe'),
                        'print_type' => $this->getPersonalizationName($item->print_type),
                        'print_desc' => $this->getPersonalizationName($item->print_type),
                        'art_name' => $item->art_name,
                        'sizes' => $sizes, // Garantir que é array
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'art_notes' => $item->art_notes,
                        'cover_image' => $item->cover_image,
                    ];
                }
                session(['edit_order_data' => $editData]);
            }

            // Debug: verificar se há dados na tabela ProductOption
            $productOptions = ProductOption::take(5)->get();
            Log::info("ProductOptions sample: " . json_encode($productOptions->toArray()));

            if ($request->isMethod('post')) {
                $action = $request->input('action', 'add_item');
                
                Log::info('🎯 POST recebido no EditOrderController@sewing', [
                    'action' => $action,
                    'editing_item_id' => $request->input('editing_item_id'),
                    'method' => $request->method()
                ]);

                if ($action === 'add_item') {
                    return $this->addItem($request);
                } elseif ($action === 'add_sublimation_item') {
                    return $this->addSublimationItem($request);
                } elseif ($action === 'update_item') {
                    return $this->updateItem($request);
                } elseif ($action === 'delete_item') {
                    return $this->deleteItem($request);
                } elseif ($action === 'finish') {
                    // Verificar se todas as personalizações são LISAS para pular a etapa de personalização
                    $allPersonalizations = [];
                    foreach ($order->items as $item) {
                        $itemPersonalizations = session('item_personalizations.' . $item->id, [[]]);
                        $allPersonalizations = array_merge($allPersonalizations, $itemPersonalizations[0] ?? []);
                        
                        // Se não tiver na sessão, tentar extrair do print_type
                        if (empty($itemPersonalizations[0]) && $item->print_type) {
                            $personalizationNames = explode(', ', $item->print_type);
                            foreach ($personalizationNames as $name) {
                                $option = ProductOption::where('name', trim($name))
                                    ->where('type', 'personalizacao')
                                    ->first();
                                if ($option) {
                                    $allPersonalizations[] = $option->id;
                                }
                            }
                        }
                    }
                    $allPersonalizations = array_unique($allPersonalizations);

                    // Se todas as personalizações forem "lisas", pular a etapa de personalização
                    if (!empty($allPersonalizations)) {
                        $personalizationNames = ProductOption::whereIn('id', $allPersonalizations)->pluck('name');
                        $onlyLisas = $personalizationNames->isNotEmpty() &&
                            $personalizationNames->every(function ($name) {
                                return stripos($name ?? '', 'LISA') !== false;
                            });

                        if ($onlyLisas) {
                            Log::info('🚀 Pulando etapa de personalização - apenas LISAS detectadas');
                            return redirect()->route('orders.edit.payment');
                        }
                    }
                    
                    return redirect()->route('orders.edit.customization');
                }
            }

            return view('orders.wizard.sewing', compact('order', 'editData'));
        } catch (\Exception $e) {
            Log::error('Error in sewing method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function customization(Request $request)
    {
        try {
            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            $order = Order::with(['items.sublimations', 'items.files'])->findOrFail($orderId);

            // Processar POST - salvar personalização
            if ($request->isMethod('post')) {
                try {
                    // Validar dados recebidos
                    $validated = $request->validate([
                        'item_id' => 'required|exists:order_items,id',
                        'personalization_type' => 'required|string',
                        'personalization_id' => 'nullable|string',
                        'art_name' => 'nullable|string|max:255',
                        'location' => 'nullable|string',
                        'size' => 'nullable|string',
                        'quantity' => 'nullable|integer|min:1',
                        'color_count' => 'nullable|integer|min:0',
                        'color_details' => 'nullable|string',
                        'seller_notes' => 'nullable|string',
                        'unit_price' => 'required|numeric|min:0',
                        'final_price' => 'required|numeric|min:0',
                        'art_files.*' => 'nullable|file|max:10240',
                        'editing_personalization_id' => 'nullable|integer|exists:order_sublimations,id',
                    ]);

                    $item = OrderItem::findOrFail($validated['item_id']);

                    // Buscar localização
                    $locationId = null;
                    $locationName = null;
                    if ($validated['personalization_type'] !== 'SUB. TOTAL' && isset($validated['location']) && $validated['location']) {
                        $location = \App\Models\SublimationLocation::find($validated['location']);
                        $locationId = $location ? $location->id : null;
                        $locationName = $location ? $location->name : $validated['location'];
                    }

                    // Para SUB. TOTAL, usar quantidade total do item
                    $quantity = $validated['quantity'] ?? 1;
                    if ($validated['personalization_type'] === 'SUB. TOTAL') {
                        $quantity = $item->quantity;
                    }

                    // Definir art_name padrão se não fornecido
                    $artName = $validated['art_name'] ?? $item->art_name ?? null;

                    // Verificar se está editando uma personalização existente
                    if (!empty($validated['editing_personalization_id'])) {
                        // EDITAR personalização existente
                        $personalization = \App\Models\OrderSublimation::findOrFail($validated['editing_personalization_id']);
                        $personalization->update([
                            'art_name' => $artName,
                            'size_name' => $validated['personalization_type'] === 'SUB. TOTAL' ? 'CACHARREL' : ($validated['size'] ?? null),
                            'location_id' => $locationId,
                            'location_name' => $locationName,
                            'quantity' => $quantity,
                            'color_count' => $validated['color_count'] ?? 0,
                            'unit_price' => $validated['unit_price'],
                            'final_price' => $validated['final_price'],
                            'color_details' => $validated['color_details'] ?? null,
                            'seller_notes' => $validated['seller_notes'] ?? null,
                        ]);

                        Log::info('Personalização editada com sucesso', ['id' => $personalization->id]);
                    } else {
                        // CRIAR nova personalização
                        $personalization = \App\Models\OrderSublimation::create([
                            'order_item_id' => $item->id,
                            'application_type' => strtolower($validated['personalization_type']),
                            'art_name' => $artName,
                            'size_id' => null,
                            'size_name' => $validated['personalization_type'] === 'SUB. TOTAL' ? 'CACHARREL' : ($validated['size'] ?? null),
                            'location_id' => $locationId,
                            'location_name' => $locationName,
                            'quantity' => $quantity,
                            'color_count' => $validated['color_count'] ?? 0,
                            'has_neon' => false,
                            'neon_surcharge' => 0,
                            'unit_price' => $validated['unit_price'],
                            'discount_percent' => 0,
                            'final_price' => $validated['final_price'],
                            'application_image' => null,
                            'color_details' => $validated['color_details'] ?? null,
                            'seller_notes' => $validated['seller_notes'] ?? null,
                        ]);

                        Log::info('Personalização criada com sucesso', ['id' => $personalization->id]);
                    }

                    // Processar arquivos
                    if ($request->hasFile('art_files')) {
                        foreach ($request->file('art_files') as $file) {
                            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('orders/sublimations', $fileName, 'public');

                            \App\Models\OrderSublimationFile::create([
                                'order_sublimation_id' => $personalization->id,
                                'file_path' => $filePath,
                                'file_name' => $file->getClientOriginalName(),
                                'file_type' => $file->getClientMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                        }
                    }

                    // Atualizar preço total do item (base + personalizações)
                    $basePrice = $item->unit_price * $item->quantity;
                    $totalPersonalizationPrice = $item->sublimations()->sum('final_price');
                    $item->update(['total_price' => $basePrice + $totalPersonalizationPrice]);

                    // Se for requisição AJAX, retornar JSON
                    if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => true,
                            'message' => 'Personalização salva com sucesso!'
                        ]);
                    }

                    return redirect()->back()->with('success', 'Personalização salva com sucesso!');

                } catch (\Exception $e) {
                    Log::error('Error saving personalization in edit mode', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    
                    // Se for requisição AJAX, retornar JSON com erro
                    if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Erro ao adicionar personalização: ' . $e->getMessage()
                        ], 500);
                    }
                    
                    return redirect()->back()->with('error', 'Erro ao adicionar personalização: ' . $e->getMessage());
                }
            }

            // Coletar personalizações por item
            $itemPersonalizations = [];
            foreach ($order->items as $item) {
                // Tentar buscar da sessão primeiro
                $itemPers = session('item_personalizations.' . $item->id, [[]]);
                $persIds = $itemPers[0] ?? [];
                
                // Se não tiver na sessão, buscar do print_type do item
                if (empty($persIds) && $item->print_type) {
                    // print_type pode ser um nome (ex: "SERIGRAFIA") ou ID
                    if (is_numeric($item->print_type)) {
                        $persIds = [$item->print_type];
                    } else {
                        // Buscar ID pelo nome
                        $option = \App\Models\ProductOption::where('name', $item->print_type)
                            ->where('type', 'personalizacao')
                            ->first();
                        if ($option) {
                            $persIds = [$option->id];
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
            
            // Buscar dados de personalização por tipo
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
            
            return view('orders.wizard.customization-multiple', compact('order', 'itemPersonalizations', 'personalizationData', 'locations'));
            
        } catch (\Exception $e) {
            Log::error('Error in customization method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function payment(Request $request)
    {
        try {
            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            $order = Order::with(['client', 'items', 'items.sublimations', 'payments'])->findOrFail($orderId);

            if ($request->isMethod('post')) {
                try {
                    $validated = $request->validate([
                        'entry_date' => 'required|date',
                        'delivery_date' => 'nullable|date',
                        'delivery_fee' => 'nullable|numeric|min:0',
                        'payment_methods' => 'required|json',
                        'size_surcharges' => 'nullable|json',
                        'order_cover_image' => 'nullable|image|max:10240',
                        'discount_type' => 'nullable|string|in:none,percentage,fixed',
                        'discount_value' => 'nullable|numeric|min:0',
                    ]);

                    Log::info('Payment form validated', $validated);

                    // Processar upload da imagem de capa do pedido
                    $orderCoverImagePath = $order->order_cover_image;
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
                            $this->imageProcessor->delete($order->order_cover_image);
                            $orderCoverImagePath = $newOrderCoverImagePath;
                        }
                    }

                    $delivery = (float)($validated['delivery_fee'] ?? 0);
                    
                    // Processar acréscimos por tamanho
                    $sizeSurcharges = json_decode($validated['size_surcharges'] ?? '{}', true);
                    $totalSurcharges = array_sum($sizeSurcharges);

                    // Desconto
                    $discountType = $validated['discount_type'] ?? 'none';
                    $discountValue = (float)($validated['discount_value'] ?? 0);
                    $subtotalWithFees = $order->subtotal + $totalSurcharges + $delivery;
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
                    
                    $total = max(0, $order->subtotal + $totalSurcharges + $delivery - $discountAmount);

                    // Atualizar pedido
                    // Preservar delivery_date existente se não for fornecida uma nova ou se estiver vazia
                    // Se estiver vazia e não houver data existente, calcular 15 dias úteis
                    if (!empty($validated['delivery_date'])) {
                        $deliveryDate = \Carbon\Carbon::parse($validated['delivery_date'])->format('Y-m-d');
                    } elseif ($order->delivery_date) {
                        // Preservar data existente
                        $deliveryDate = \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d');
                    } else {
                        // Calcular 15 dias úteis se não houver data
                        $deliveryDate = \App\Helpers\DateHelper::calculateDeliveryDate(\Carbon\Carbon::now(), 15)->format('Y-m-d');
                    }
                    
                    $order->update([
                        'delivery_fee' => $delivery,
                        'discount' => $discountAmount,
                        'total' => $total,
                        'delivery_date' => $deliveryDate,
                        'entry_date' => $validated['entry_date'],
                        'order_cover_image' => $orderCoverImagePath,
                    ]);

                    // Limpar formas de pagamento antigas e salvar novas
                    \App\Models\Payment::where('order_id', $order->id)->delete();
                    
                    // Criar registro de pagamento único com todos os métodos
                    $primaryMethod = count($paymentMethods) === 1 ? $paymentMethods[0]['method'] : 'pix';
                    
                    \App\Models\Payment::create([
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

                    // Salvar acréscimos na sessão para exibir no resumo
                    session(['size_surcharges' => $sizeSurcharges]);

                    // Incluir delivery_date nos dados salvos na sessão
                    $validated['delivery_date'] = $deliveryDate;
                    $editData['payment'] = $validated;
                    session(['edit_order_data' => $editData]);

                    Log::info('Payment saved successfully, redirecting to confirm');

                    return redirect()->route('orders.edit.confirm');
                    
                } catch (\Exception $e) {
                    Log::error('Error processing payment', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
                }
            }

            // Recalcular subtotal com base nos itens e personalizações
            $subtotal = 0;
            foreach ($order->items as $item) {
                // Somar preço base do item (costura - Etapa 2)
                $itemBasePrice = $item->unit_price * $item->quantity;
                
                // Somar total de personalizações do item (Etapa 3)
                $personalizationTotal = $item->sublimations()->sum('final_price');
                
                // Total do item = base + personalizações
                $subtotal += ($itemBasePrice + $personalizationTotal);
            }
            
            // Atualizar subtotal do pedido
            $order->subtotal = $subtotal;
            $order->save();

            // Se existem dados de pagamento na sessão, usar eles (usuário voltou de uma etapa posterior)
            $sessionPaymentData = $editData['payment'] ?? null;

            return view('orders.wizard.payment', compact('order', 'editData', 'sessionPaymentData'));
            
        } catch (\Exception $e) {
            Log::error('Error in payment method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function confirm(Request $request)
    {
        try {
            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            $order = Order::with(['client', 'items', 'items.sublimations'])->findOrFail($orderId);
            
            // Buscar pagamentos do pedido
            $payment = \App\Models\Payment::where('order_id', $order->id)->get();
            
            // Buscar size surcharges da sessão ou vazio
            $sizeSurcharges = session('size_surcharges', []);

            if ($request->isMethod('post')) {
                return $this->finalizeEdit($request);
            }

            return view('orders.wizard.confirm', compact('order', 'editData', 'payment', 'sizeSurcharges'));
            
        } catch (\Exception $e) {
            Log::error('Error in confirm method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function finalizeEdit(Request $request)
    {
        try {
            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
            }

            DB::beginTransaction();

            $order = Order::with(['client', 'items.sublimations', 'payments'])->findOrFail($orderId);
            
            // Capturar snapshot ANTES da edição para calcular diferenças
            $snapshotBefore = [
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

            // Atualizar dados do cliente
            $client = $order->client;
            $client->update($editData['client']);

            // Determinar store_id baseado no usuário que está editando
            $user = Auth::user();
            $storeId = $order->store_id; // Manter o store_id atual por padrão
            
            if ($user->isAdminLoja()) {
                $storeIds = $user->getStoreIds();
                $storeId = !empty($storeIds) ? $storeIds[0] : $storeId;
            } elseif ($user->isVendedor()) {
                // Vendedor: buscar loja associada através da tabela store_user ou campo store
                $userStores = $user->stores()->get();
                if ($userStores->isNotEmpty()) {
                    // Se tem loja associada na tabela store_user, usar a primeira
                    $storeId = $userStores->first()->id;
                } elseif ($user->store) {
                    // Se tem campo store (string), tentar buscar pelo nome
                    $store = \App\Models\Store::where('name', 'like', '%' . $user->store . '%')->first();
                    if ($store) {
                        $storeId = $store->id;
                    }
                }
                
                // Se ainda não encontrou, manter o store_id atual
            }
            
            \Log::info('Atualizando store_id do pedido durante edição', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'old_store_id' => $order->store_id,
                'new_store_id' => $storeId,
            ]);

            // Atualizar dados do pedido
            // Preservar delivery_date existente se não for fornecida uma nova ou se estiver vazia
            // Se estiver vazia e não houver data existente, calcular 15 dias úteis
            if (!empty($editData['payment']['delivery_date'])) {
                $deliveryDate = \Carbon\Carbon::parse($editData['payment']['delivery_date'])->format('Y-m-d');
            } elseif ($order->delivery_date) {
                // Preservar data existente
                $deliveryDate = \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d');
            } else {
                // Calcular 15 dias úteis se não houver data
                $deliveryDate = \App\Helpers\DateHelper::calculateDeliveryDate(\Carbon\Carbon::now(), 15)->format('Y-m-d');
            }
            
            $order->update([
                'contract_type' => $editData['contract_type'],
                'seller' => $editData['seller'],
                'store_id' => $storeId, // Atualizar store_id baseado no vendedor
                'delivery_date' => $deliveryDate,
                'subtotal' => $editData['payment']['subtotal'] ?? $order->subtotal,
                'discount' => $editData['payment']['discount'] ?? 0,
                'delivery_fee' => $editData['payment']['delivery_fee'] ?? 0,
                'total' => $editData['payment']['total'] ?? $order->total,
                'payment_method' => $editData['payment']['payment_method'] ?? null,
                'entry_date' => $editData['payment']['entry_date'] ?? null,
                'notes' => $editData['payment']['notes'] ?? null,
                'is_modified' => true, // Marcar pedido como modificado
            ]);

            // Atualizar itens do pedido
            foreach ($editData['items'] as $itemData) {
                $item = OrderItem::findOrFail($itemData['id']);
                $item->update([
                    'fabric' => $itemData['fabric'],
                    'color' => $itemData['color'],
                    'collar' => $itemData['collar'],
                    'model' => $itemData['model'],
                    'detail' => $itemData['detail'],
                    'print_type' => $itemData['print_type'],
                    'print_desc' => $itemData['print_desc'],
                    'art_name' => $itemData['art_name'],
                    'sizes' => $itemData['sizes'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['total_price'],
                    'art_notes' => $itemData['art_notes'],
                ]);
            }

            // Capturar snapshot DEPOIS da edição e finalizar EditRequest
            $editRequest = $order->editRequests()->where('status', 'approved')->first();
            if ($editRequest) {
                // Recarregar order com relacionamentos para snapshot
                $order->load([
                    'client', 
                    'items.sublimations.size', 
                    'items.sublimations.location',
                    'items.sublimations.files',
                    'items.files',
                    'payments'
                ]);
                
                // Capturar snapshot DEPOIS
                $snapshotAfter = [
                    'order' => [
                        'id' => $order->id,
                        'seller' => $order->seller,
                        'contract_type' => $order->contract_type,
                        'nt' => $order->nt,
                        'order_date' => $order->order_date,
                        'delivery_date' => $order->delivery_date,
                        'entry_date' => $order->entry_date,
                        'subtotal' => $order->subtotal,
                        'discount' => $order->discount,
                        'delivery_fee' => $order->delivery_fee,
                        'total' => $order->total,
                        'notes' => $order->notes,
                        'cover_image' => $order->cover_image,
                        'is_event' => $order->is_event,
                    ],
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
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'item_number' => $item->item_number,
                            'print_type' => $item->print_type,
                            'art_name' => $item->art_name,
                            'art_notes' => $item->art_notes,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'fabric' => $item->fabric,
                            'color' => $item->color,
                            'collar' => $item->collar,
                            'model' => $item->model,
                            'detail' => $item->detail,
                            'sizes' => $item->sizes,
                            'cover_image' => $item->cover_image,
                            'sublimations' => $item->sublimations->map(function ($sub) {
                                return [
                                    'id' => $sub->id,
                                    'application_type' => $sub->application_type,
                                    'art_name' => $sub->art_name,
                                    'size_name' => $sub->size ? $sub->size->name : $sub->size_name,
                                    'size_dimensions' => $sub->size ? $sub->size->dimensions : null,
                                    'location_name' => $sub->location ? $sub->location->name : $sub->location_name,
                                    'quantity' => $sub->quantity,
                                    'color_count' => $sub->color_count,
                                    'color_details' => $sub->color_details,
                                    'has_neon' => $sub->has_neon,
                                    'unit_price' => $sub->unit_price,
                                    'total_price' => $sub->total_price,
                                    'seller_notes' => $sub->seller_notes,
                                    'application_image' => $sub->application_image,
                                    'files' => $sub->files->map(function ($file) {
                                        return [
                                            'id' => $file->id,
                                            'file_name' => $file->file_name,
                                            'file_path' => $file->file_path,
                                            'file_type' => $file->file_type,
                                        ];
                                    })->toArray(),
                                ];
                            })->toArray(),
                            'files' => $item->files->map(function ($file) {
                                return [
                                    'id' => $file->id,
                                    'file_name' => $file->file_name,
                                    'file_path' => $file->file_path,
                                    'file_type' => $file->file_type,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                    'payments' => $order->payments->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'method' => $payment->method,
                            'payment_method' => $payment->payment_method,
                            'payment_methods' => $payment->payment_methods,
                            'amount' => $payment->amount,
                            'entry_amount' => $payment->entry_amount,
                            'remaining_amount' => $payment->remaining_amount,
                            'entry_date' => $payment->entry_date,
                            'payment_date' => $payment->payment_date,
                            'status' => $payment->status,
                        ];
                    })->toArray(),
                    'totals' => [
                        'total_items' => $order->items->sum('quantity'),
                        'total_sublimations' => $order->items->sum(function ($item) {
                            return $item->sublimations->count();
                        }),
                        'total_paid' => $order->payments->sum('entry_amount'),
                        'total_remaining' => $order->total - $order->payments->sum('entry_amount'),
                    ],
                    'timestamp' => now()->toDateTimeString(),
                    'captured_by' => Auth::user()->name,
                ];
                
                // Calcular diferenças entre before e after
                $differences = $this->calculateEditDifferences(
                    $editRequest->order_snapshot_before,
                    $snapshotAfter
                );
                
                // Atualizar EditRequest com snapshot after e status completed
                $editRequest->update([
                    'order_snapshot_after' => $snapshotAfter,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                
                // Log de finalização com diferenças
                \App\Models\OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'action' => 'order_edited',
                    'description' => 'Pedido editado com sucesso',
                    'changes' => $differences,
                ]);
            } else {
                // Se não há EditRequest, ainda assim criar o log de edição
                // Recarregar order com relacionamentos para snapshot após as alterações
                $order->load([
                    'client', 
                    'items.sublimations',
                    'payments'
                ]);
                
                // Capturar snapshot DEPOIS
                $snapshotAfter = [
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
                
                // Calcular diferenças entre before e after
                $differences = $this->calculateEditDifferences(
                    $snapshotBefore,
                    $snapshotAfter
                );
                
                // Criar log de edição se houver diferenças
                if (!empty($differences)) {
                    \App\Models\OrderLog::create([
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                        'user_name' => Auth::user()->name,
                        'action' => 'order_edited',
                        'description' => 'Pedido editado com sucesso',
                        'changes' => $differences,
                    ]);
                }
            }

            DB::commit();

            // Limpar sessão
            session()->forget('edit_order_data');
            session()->forget('edit_order_id');

            return redirect()->route('orders.show', $order->id)->with('success', 'Pedido editado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error finalizing edit', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()->with('error', 'Erro ao finalizar edição: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares para processamento do formulário de costura
    private function addItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'tecido' => 'nullable|string|max:255',
                'cor' => 'nullable|string|max:255',
                'gola' => 'nullable|string|max:255',
                'tipo_corte' => 'nullable|string|max:255',
                'detalhe' => 'nullable|string|max:255',
                'personalizacao' => 'nullable|array',
                'tamanhos' => 'required|array',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'unit_cost' => 'nullable|numeric|min:0',
                'art_notes' => 'nullable|string|max:1000',
            ]);

            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');
            
            if (!$orderId) {
                return redirect()->back()->with('error', 'Sessão de edição expirada.');
            }
            
            // Calcular total
            $totalPrice = $validated['quantity'] * $validated['unit_price'];
            
            // Processar tamanhos
            $sizes = [];
            $totalQuantity = 0;
            foreach ($validated['tamanhos'] as $size => $qty) {
                if ($qty > 0) {
                    $sizes[$size] = $qty;
                    $totalQuantity += $qty;
                }
            }

            // Processar personalizações
            $printTypes = [];
            $printTypeNames = [];
            if (isset($validated['personalizacao']) && is_array($validated['personalizacao'])) {
                foreach ($validated['personalizacao'] as $personalizacaoId) {
                    $printTypes[] = $personalizacaoId;
                    $printTypeNames[] = $this->getPersonalizationName($personalizacaoId);
                }
            }

            // CRIAR ITEM NO BANCO DE DADOS
            $order = Order::findOrFail($orderId);
            
            $newDbItem = OrderItem::create([
                'order_id' => $orderId,
                'fabric' => $this->getProductOptionName($validated['tecido'], 'tecido'),
                'color' => $this->getProductOptionName($validated['cor'], 'cor'),
                'collar' => $this->getProductOptionName($validated['gola'], 'gola'),
                'model' => $this->getProductOptionName($validated['tipo_corte'], 'tipo_corte'),
                'detail' => $validated['detalhe'] ? $this->getProductOptionName($validated['detalhe'], 'detalhe') : '',
                'print_type' => implode(', ', $printTypeNames),
                'print_desc' => implode(', ', $printTypeNames),
                'art_name' => 'Arte ' . time(),
                'sizes' => $sizes,
                'quantity' => $totalQuantity,
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'unit_cost' => $validated['unit_cost'] ?? 0,
                'total_cost' => ($validated['unit_cost'] ?? 0) * $totalQuantity,
                'art_notes' => $validated['art_notes'],
            ]);

            // Atualizar totais do pedido
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
                'total_items' => $order->items()->sum('quantity'),
            ]);
            
            // Adicionar item na sessão também (com ID real do banco)
            $newItem = [
                'id' => $newDbItem->id, // ID real do banco de dados
                'item_number' => $newDbItem->item_number,
                'fabric' => $newDbItem->fabric,
                'color' => $newDbItem->color,
                'collar' => $newDbItem->collar,
                'model' => $newDbItem->model,
                'detail' => $newDbItem->detail,
                'print_type' => $newDbItem->print_type,
                'print_desc' => $newDbItem->print_desc,
                'art_name' => $newDbItem->art_name,
                'sizes' => $sizes, // Manter como array, não JSON
                'quantity' => $newDbItem->quantity,
                'unit_price' => $newDbItem->unit_price,
                'total_price' => $newDbItem->total_price,
                'unit_cost' => $newDbItem->unit_cost,
                'total_cost' => $newDbItem->total_cost,
                'art_notes' => $newDbItem->art_notes,
                'cover_image' => $newDbItem->cover_image,
            ];

            $editData['items'][] = $newItem;
            session(['edit_order_data' => $editData]);

            Log::info('Novo item adicionado com sucesso', [
                'item_id' => $newDbItem->id,
                'order_id' => $orderId
            ]);

            return redirect()->back()->with('success', 'Item adicionado com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Error adding item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao adicionar item: ' . $e->getMessage());
        }
    }

    private function addSublimationItem(Request $request)
    {
        try {
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

            $orderId = session('edit_order_id');
            $order = Order::with('items')->findOrFail($orderId);
            $editData = session('edit_order_data', []);

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

            // Criar item SUB. TOTAL no banco
            $item = new OrderItem([
                'order_id' => $orderId,
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

            // Atualizar totais do pedido
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
                'total_items' => $order->items()->sum('quantity'),
            ]);

            // Adicionar na sessão de edição
            $newItem = [
                'id' => $item->id,
                'item_number' => $item->item_number,
                'fabric' => $item->fabric,
                'color' => $item->color,
                'collar' => $item->collar,
                'model' => $item->model,
                'detail' => $item->detail,
                'print_type' => $item->print_type,
                'print_desc' => $item->print_type,
                'art_name' => $item->art_name,
                'sizes' => $validated['tamanhos'],
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $item->total_cost,
                'art_notes' => $item->art_notes,
                'cover_image' => $item->cover_image,
            ];

            $editData['items'][] = $newItem;
            session(['edit_order_data' => $editData]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item SUB. TOTAL #' . $itemNumber . ' adicionado!',
                    'items_count' => count($editData['items'])
                ]);
            }

            return redirect()->back()->with('success', 'Item SUB. TOTAL #' . $itemNumber . ' adicionado!');
        } catch (\Exception $e) {
            Log::error('Error adding sublimation item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao adicionar item de sublimação: ' . $e->getMessage());
        }
    }

    private function updateItem(Request $request)
    {
        Log::info('🔧 UPDATE ITEM CHAMADO!', [
            'editing_item_id' => $request->input('editing_item_id'),
            'action' => $request->input('action'),
            'all_inputs' => $request->except(['_token'])
        ]);
        
        try {
            $validated = $request->validate([
                'editing_item_id' => 'required|string',
                'tecido' => 'nullable|string|max:255',
                'cor' => 'nullable|string|max:255',
                'gola' => 'nullable|string|max:255',
                'tipo_corte' => 'nullable|string|max:255',
                'detalhe' => 'nullable|string|max:255',
                'personalizacao' => 'nullable|array',
                'tamanhos' => 'required|array',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'unit_cost' => 'nullable|numeric|min:0',
                'art_notes' => 'nullable|string|max:1000',
            ]);

            $editData = session('edit_order_data', []);
            
            // Calcular total
            $totalPrice = $validated['quantity'] * $validated['unit_price'];
            
            // Processar tamanhos
            $sizes = [];
            $totalQuantity = 0;
            foreach ($validated['tamanhos'] as $size => $qty) {
                if ($qty > 0) {
                    $sizes[$size] = $qty;
                    $totalQuantity += $qty;
                }
            }

            // Processar personalizações
            $printTypes = [];
            $printTypeNames = [];
            if (isset($validated['personalizacao']) && is_array($validated['personalizacao'])) {
                foreach ($validated['personalizacao'] as $personalizacaoId) {
                    $printTypes[] = $personalizacaoId;
                    $printTypeNames[] = $this->getPersonalizationName($personalizacaoId);
                }
            }

            // Buscar o item real do banco de dados
            $orderId = session('edit_order_id');
            $order = Order::with('items')->findOrFail($orderId);
            $dbItem = OrderItem::findOrFail($validated['editing_item_id']);
            
            // Preparar dados atualizados (usando tipos corretos em português!)
            $updatedData = [
                'fabric' => $this->getProductOptionName($validated['tecido'], 'tecido'),
                'color' => $this->getProductOptionName($validated['cor'], 'cor'),
                'collar' => $this->getProductOptionName($validated['gola'], 'gola'),
                'model' => $this->getProductOptionName($validated['tipo_corte'], 'tipo_corte'),
                'detail' => $validated['detalhe'] ? $this->getProductOptionName($validated['detalhe'], 'detalhe') : '',
                'print_type' => implode(', ', $printTypeNames),
                'print_desc' => implode(', ', $printTypeNames),
                'art_name' => 'Arte ' . time(),
                'sizes' => $sizes,
                'quantity' => $totalQuantity,
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'unit_cost' => $validated['unit_cost'] ?? 0,
                'total_cost' => ($validated['unit_cost'] ?? 0) * $totalQuantity,
                'art_notes' => $validated['art_notes'],
            ];
            
            // ATUALIZAR NO BANCO DE DADOS
            $dbItem->update($updatedData);
            $dbItem->refresh();
            
            // Atualizar totais do pedido
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
                'total_items' => $order->items()->sum('quantity'),
            ]);
            $order->refresh();
            
            // Atualizar item na sessão
            foreach ($editData['items'] as &$item) {
                if ($item['id'] == $validated['editing_item_id']) {
                    $item = array_merge($item, [
                        'id' => $dbItem->id,
                        'item_number' => $dbItem->item_number,
                        'fabric' => $updatedData['fabric'],
                        'color' => $updatedData['color'],
                        'collar' => $updatedData['collar'],
                        'model' => $updatedData['model'],
                        'detail' => $updatedData['detail'],
                        'print_type' => $updatedData['print_type'],
                        'print_desc' => $updatedData['print_desc'],
                        'art_name' => $updatedData['art_name'],
                        'sizes' => $sizes, // Manter como array, não JSON
                        'quantity' => $updatedData['quantity'],
                        'unit_price' => $updatedData['unit_price'],
                        'total_price' => $updatedData['total_price'],
                        'unit_cost' => $updatedData['unit_cost'],
                        'total_cost' => $updatedData['total_cost'],
                        'art_notes' => $updatedData['art_notes'],
                        'cover_image' => $dbItem->cover_image,
                    ]);
                    break;
                }
            }

            session(['edit_order_data' => $editData]);
            
            Log::info('Item atualizado com sucesso no EditOrderController', [
                'item_id' => $dbItem->id,
                'order_id' => $order->id,
                'new_data' => $dbItem->toArray()
            ]);

            return redirect()->back()->with('success', 'Item atualizado com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error updating item', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
                'inputs' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Erro ao atualizar item: ' . $e->getMessage());
        }
    }

    private function deleteItem(Request $request)
    {
        try {
            $itemId = $request->input('item_id');
            
            if (!$itemId) {
                return redirect()->back()->with('error', 'ID do item não fornecido.');
            }

            $editData = session('edit_order_data', []);
            $orderId = session('edit_order_id');

            if (!$orderId) {
                return redirect()->back()->with('error', 'Sessão de edição expirada.');
            }

            // Buscar e deletar do banco de dados
            $item = OrderItem::where('id', $itemId)->where('order_id', $orderId)->first();
            
            if ($item) {
                $item->delete();
                
                // Atualizar totais do pedido
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'subtotal' => $order->items()->sum('total_price'),
                        'total_items' => $order->items()->sum('quantity'),
                    ]);
                }
                
                Log::info('Item removido do banco de dados', ['item_id' => $itemId, 'order_id' => $orderId]);
            } else {
                Log::warning('Tentativa de remover item inexistente ou de outro pedido', ['item_id' => $itemId, 'order_id' => $orderId]);
            }
            
            // Remover item da sessão
            $editData['items'] = array_filter($editData['items'], function($item) use ($itemId) {
                return $item['id'] != $itemId;
            });

            session(['edit_order_data' => $editData]);

            return redirect()->back()->with('success', 'Item removido com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao remover item: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares para buscar nomes reais
    private function getProductOptionName($id, $type = null)
    {
        if (!$id) return '';
        
        try {
            $query = ProductOption::where('id', $id);
            if ($type) {
                $query->where('type', $type);
            }
            
            $option = $query->first();
            $result = $option ? $option->name : $id;
            
            Log::info("getProductOptionName: ID={$id}, Type={$type}, Result={$result}");
            return $result;
        } catch (\Exception $e) {
            Log::error("Error in getProductOptionName: " . $e->getMessage());
            return $id;
        }
    }

    private function getPersonalizationName($id)
    {
        if (!$id) return '';
        
        try {
            // Primeiro tentar buscar em ProductOption (usada no formulário de costura)
            $productOption = ProductOption::where('id', $id)
                ->where('type', 'personalizacao')
                ->first();
            
            if ($productOption) {
                Log::info("getPersonalizationName: ID={$id}, Found in ProductOption, Name={$productOption->name}");
                return $productOption->name;
            }
            
            // Se não encontrar, tentar em PersonalizationPrice
            $personalization = PersonalizationPrice::where('id', $id)->first();
            if ($personalization) {
                $result = $personalization->personalization_type;
                Log::info("getPersonalizationName: ID={$id}, Found in PersonalizationPrice, Type={$result}");
                return $result;
            }
            
            // Se não encontrar em nenhum, retornar o próprio valor (pode ser nome já)
            Log::warning("getPersonalizationName: ID={$id} not found, returning as-is");
            return $id;
        } catch (\Exception $e) {
            Log::error("Error in getPersonalizationName: " . $e->getMessage());
            return $id;
        }
    }

    // Método para limpar a sessão e recarregar dados
    public function clearSession()
    {
        session()->forget('edit_order_data');
        return redirect()->back()->with('success', 'Sessão limpa! Os dados serão recarregados.');
    }

    // Método de debug para testar conversão de nomes
    public function debugNames()
    {
        $debug = [];
        
        // Testar ProductOption
        $debug['product_options'] = ProductOption::take(5)->get()->toArray();
        
        // Testar conversões específicas
        $debug['fabric_2'] = $this->convertToName('2', 'fabric');
        $debug['color_106'] = $this->convertToName('106', 'color');
        $debug['collar_120'] = $this->convertToName('120', 'collar');
        $debug['model_112'] = $this->convertToName('112', 'model');
        $debug['detail_115'] = $this->convertToName('115', 'detail');
        $debug['personalization_133'] = $this->convertToName('133', 'personalization');
        
        return response()->json($debug);
    }

    // Método mais robusto para converter IDs para nomes
    private function convertToName($value, $type)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }

        try {
            if ($type === 'personalization') {
                $personalization = PersonalizationPrice::where('id', $value)->first();
                return $personalization ? $personalization->personalization_type : $value;
            } else {
                $option = ProductOption::where('id', $value)->where('type', $type)->first();
                return $option ? $option->name : $value;
            }
        } catch (\Exception $e) {
            Log::error("Error in convertToName: " . $e->getMessage());
            return $value;
        }
    }

    /**
     * Calcular diferenças entre dois snapshots de pedido
     */
    private function calculateEditDifferences($before, $after)
    {
        $differences = [];

        if (!$before || !$after) {
            return $differences;
        }

        // Comparar campos do pedido (order)
        if (isset($before['order']) && isset($after['order'])) {
            foreach ($before['order'] as $key => $oldValue) {
                $newValue = $after['order'][$key] ?? null;
                if ($oldValue != $newValue) {
                    $differences['order'][$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        // Comparar cliente
        if (isset($before['client']) && isset($after['client'])) {
            foreach ($before['client'] as $key => $oldValue) {
                $newValue = $after['client'][$key] ?? null;
                if ($oldValue != $newValue) {
                    $differences['client'][$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        // Comparar itens
        if (isset($before['items']) && isset($after['items'])) {
            $beforeItems = collect($before['items']);
            $afterItems = collect($after['items']);

            // Itens modificados
            foreach ($afterItems as $afterItem) {
                $beforeItem = $beforeItems->firstWhere('id', $afterItem['id']);
                if ($beforeItem) {
                    $itemChanges = [];
                    foreach ($afterItem as $key => $newValue) {
                        $oldValue = $beforeItem[$key] ?? null;
                        // Normalizar comparação de arrays
                        if (is_array($oldValue) && is_array($newValue)) {
                            if (json_encode($oldValue) != json_encode($newValue)) {
                                $itemChanges[$key] = [
                                    'old' => $oldValue,
                                    'new' => $newValue
                                ];
                            }
                        } elseif ($oldValue != $newValue) {
                            $itemChanges[$key] = [
                                'old' => $oldValue,
                                'new' => $newValue
                            ];
                        }
                    }
                    if (!empty($itemChanges)) {
                        $differences['items'][$afterItem['id']] = [
                            'type' => 'modified',
                            'changes' => $itemChanges
                        ];
                    }
                }
            }

            // Itens removidos
            foreach ($beforeItems as $beforeItem) {
                if (!$afterItems->contains('id', $beforeItem['id'])) {
                    $differences['items'][$beforeItem['id']] = [
                        'type' => 'removed',
                        'data' => $beforeItem
                    ];
                }
            }

            // Itens adicionados
            foreach ($afterItems as $afterItem) {
                if (!$beforeItems->contains('id', $afterItem['id'])) {
                    $differences['items'][$afterItem['id']] = [
                        'type' => 'added',
                        'data' => $afterItem
                    ];
                }
            }
        }

        // Comparar pagamentos
        if (isset($before['payments']) && isset($after['payments'])) {
            $beforePayments = collect($before['payments']);
            $afterPayments = collect($after['payments']);

            foreach ($afterPayments as $index => $afterPayment) {
                $beforePayment = $beforePayments->get($index);
                if ($beforePayment) {
                    $paymentChanges = [];
                    foreach ($afterPayment as $key => $newValue) {
                        $oldValue = $beforePayment[$key] ?? null;
                        if (is_array($oldValue) && is_array($newValue)) {
                            if (json_encode($oldValue) != json_encode($newValue)) {
                                $paymentChanges[$key] = [
                                    'old' => $oldValue,
                                    'new' => $newValue
                                ];
                            }
                        } elseif ($oldValue != $newValue) {
                            $paymentChanges[$key] = [
                                'old' => $oldValue,
                                'new' => $newValue
                            ];
                        }
                    }
                    if (!empty($paymentChanges)) {
                        $differences['payments'][$index] = $paymentChanges;
                    }
                }
            }
        }

        return $differences;
    }
}
