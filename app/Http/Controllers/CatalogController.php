<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CatalogOrder;
use App\Models\FabricPiece;
use App\Models\Product;
use App\Models\Store;
use App\Models\Tenant;
use App\Services\CatalogGatewaySettingsService;
use App\Services\PixService;
use App\Support\MercadoPagoWebhookVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class CatalogController extends Controller
{
    /**
     * Public catalog storefront
     */
    public function show(string $storeCode)
    {
        $tenant = Tenant::byCode($storeCode)->first();

        if (!$tenant) {
            abort(404, 'Loja não encontrada.');
        }

        $store = Store::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('is_main', true)
            ->first()
            ?? Store::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->first();

        // Auto-criar loja principal se não existir
        if (!$store) {
            $store = Store::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name ?? 'Minha Loja',
                'is_main' => true,
                'active' => true,
            ]);
        }

        $categories = Category::where('tenant_id', $tenant->id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        $categorySlug = request('category');
        $activeCategory = null;
        $showOnlyFabricPieces = $categorySlug === 'tecidos';

        $query = Product::where('tenant_id', $tenant->id)
            ->catalogVisible()
            ->with(['images', 'category']);

        if ($categorySlug && !$showOnlyFabricPieces) {
            $activeCategory = $categories->firstWhere('slug', $categorySlug);
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $products = $showOnlyFabricPieces
            ? $query->whereRaw('1 = 0')->paginate(24)
            : $query->orderBy('order')->paginate(24);

        $fabricPieces = collect();
        if (!$activeCategory) {
            $fabricPieces = FabricPiece::with(['fabric', 'fabricType', 'color', 'store'])
                ->availableForChannel('catalog')
                ->whereIn('status', ['aberta', 'fechada'])
                ->hasAvailableQuantity()
                ->whereHas('store', function ($query) use ($tenant) {
                    $query->withoutGlobalScopes()->where('tenant_id', $tenant->id);
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        // Get cart from session
        $cart = $this->getSessionCart($storeCode);

        return view('catalog.show', compact(
            'tenant', 'store', 'categories', 'products', 'fabricPieces',
            'activeCategory', 'cart', 'storeCode', 'showOnlyFabricPieces'
        ));
    }

    /**
     * Fabric piece detail page
     */
    public function fabricPieceDetail(string $storeCode, int $pieceId)
    {
        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = Store::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('is_main', true)
            ->first()
            ?? Store::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->first();

        $fabricPiece = FabricPiece::with(['fabric', 'fabricType', 'color', 'store'])
            ->availableForChannel('catalog')
            ->whereIn('status', ['aberta', 'fechada'])
            ->hasAvailableQuantity()
            ->whereHas('store', function ($query) use ($tenant) {
                $query->withoutGlobalScopes()->where('tenant_id', $tenant->id);
            })
            ->findOrFail($pieceId);

        $relatedPieces = FabricPiece::with(['fabric', 'fabricType', 'color'])
            ->availableForChannel('catalog')
            ->whereIn('status', ['aberta', 'fechada'])
            ->hasAvailableQuantity()
            ->whereHas('store', function ($query) use ($tenant) {
                $query->withoutGlobalScopes()->where('tenant_id', $tenant->id);
            })
            ->where('id', '!=', $fabricPiece->id)
            ->when($fabricPiece->fabric_type_id, fn ($query) => $query->where('fabric_type_id', $fabricPiece->fabric_type_id))
            ->limit(4)
            ->get();

        $cart = $this->getSessionCart($storeCode);

        return view('catalog.fabric-piece', compact(
            'tenant',
            'store',
            'fabricPiece',
            'relatedPieces',
            'cart',
            'storeCode'
        ));
    }

    /**
     * Product detail page
     */
    public function productDetail(string $storeCode, int $productId)
    {
        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = Store::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('is_main', true)
            ->first()
            ?? Store::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->first();

        $product = Product::where('tenant_id', $tenant->id)
            ->catalogVisible()
            ->with(['images', 'category', 'subcategory', 'tecido', 'personalizacao', 'modelo', 'cutType'])
            ->findOrFail($productId);
        // Buscar tamanhos e cores do estoque real (consolidado em todas as lojas do tenant)
        $stockSizes = [];
        $stockColors = [];
        $stockGrid = [];
        $totalStock = 0;

        $storeIds = Store::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->pluck('id');

        if ($storeIds->isNotEmpty()) {
            $stockQuery = \App\Models\Stock::withoutGlobalScopes()
                ->whereIn('store_id', $storeIds)
                ->whereRaw('(quantity - reserved_quantity) > 0');

            // Filtrar SOMENTE se o produto tem um tipo de corte associado
            // Se nao tiver, nao deve mostrar estoque de outros itens aleatorios
            if ($product->cut_type_id) {
                $stockQuery->where('cut_type_id', $product->cut_type_id);
            } else {
                // Forcar query vazia se nao houver cut_type_id
                // (ja que neste sistema o estoque e gerido por cut_type)
                $stockQuery->whereRaw('1 = 0');
            }

            $stockItems = $stockQuery->with(['color' => function ($query) {
                $query->withoutGlobalScopes();
            }])->get();

            $availableQty = static fn ($item): int => max(0, (int) $item->quantity - (int) $item->reserved_quantity);

            // Extrair tamanhos unicos com quantidades disponiveis
            $stockSizes = $stockItems
                ->filter(fn ($item) => !empty($item->size))
                ->groupBy(fn ($item) => trim((string) $item->size))
                ->map(fn ($items) => $items->sum($availableQty))
                ->filter(fn ($qty) => $qty > 0)
                ->sortKeys()
                ->toArray();

            // Consolidar cores por nome para evitar duplicidade de IDs com o mesmo rotulo
            $stockColors = $stockItems
                ->map(function ($item) use ($availableQty) {
                    $qty = $availableQty($item);
                    $name = trim((string) optional($item->color)->name);

                    if ($qty <= 0 || $name === '') {
                        return null;
                    }

                    return [
                        'name' => $name,
                        'hex' => optional($item->color)->color_hex ?: '#666666',
                        'qty' => $qty,
                    ];
                })
                ->filter()
                ->groupBy(fn ($item) => mb_strtolower((string) $item['name']))
                ->map(function ($items) {
                    $first = $items->first();

                    return [
                        'name' => $first['name'],
                        'hex' => $first['hex'],
                        'total_qty' => $items->sum('qty'),
                    ];
                })
                ->values()
                ->sortBy('name')
                ->values()
                ->toArray();

            $totalStock = $stockItems->sum($availableQty);

            // Criar matriz de estoque: Tamanho x Cor (Nome), somando entre lojas
            foreach ($stockItems as $si) {
                $size = trim((string) $si->size);
                $colorName = trim((string) optional($si->color)->name);
                $qty = $availableQty($si);

                if ($size === '' || $colorName === '' || $qty <= 0) {
                    continue;
                }

                $stockGrid[$size][$colorName] = ($stockGrid[$size][$colorName] ?? 0) + $qty;
            }
        }

        $relatedProducts = Product::where('tenant_id', $tenant->id)
            ->catalogVisible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->limit(8)
            ->get();

        $cart = $this->getSessionCart($storeCode);

        return view('catalog.product', compact(
            'tenant', 'store', 'product', 'relatedProducts', 'cart', 'storeCode',
            'stockSizes', 'stockColors', 'totalStock', 'stockGrid'
        ));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function addToCart(Request $request, string $storeCode)
    {
        try {
            $request->validate([
                'item_type' => 'nullable|in:product,fabric_piece',
                'product_id' => 'nullable|integer',
                'fabric_piece_id' => 'nullable|integer',
                'items' => 'nullable|array',
                'quantity' => 'nullable|numeric|min:0.001',
                'size' => 'nullable|string',
                'color' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Catalog addToCart Validation Error:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            throw $e;
        }

        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $itemType = $request->input('item_type', 'product');
        $cart = $this->getSessionCart($storeCode);

        if ($itemType === 'fabric_piece') {
            $fabricPiece = FabricPiece::with(['fabric', 'fabricType', 'color', 'store'])
                ->availableForChannel('catalog')
                ->whereIn('status', ['aberta', 'fechada'])
                ->hasAvailableQuantity()
                ->whereHas('store', function ($query) use ($tenant) {
                    $query->withoutGlobalScopes()->where('tenant_id', $tenant->id);
                })
                ->findOrFail($request->fabric_piece_id);

            $requestedQuantity = (float) ($request->quantity ?? 0);
            if ($requestedQuantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informe uma quantidade válida para o tecido.',
                ], 422);
            }

            $cartKey = 'fabric-piece-' . $fabricPiece->id;
            $currentQuantity = (float) ($cart[$cartKey]['quantity'] ?? 0);
            $newQuantity = $currentQuantity + $requestedQuantity;

            if ($newQuantity > (float) $fabricPiece->available_quantity + 0.0001) {
                $decimals = $fabricPiece->control_unit === 'metros' ? 2 : 3;
                return response()->json([
                    'success' => false,
                    'message' => 'Quantidade indisponível. Saldo atual: '
                        . number_format((float) $fabricPiece->available_quantity, $decimals, ',', '.')
                        . ' '
                        . ($fabricPiece->control_unit === 'metros' ? 'm' : 'kg')
                        . '.',
                ], 422);
            }

            $cart[$cartKey] = [
                'item_type' => 'fabric_piece',
                'fabric_piece_id' => $fabricPiece->id,
                'product_id' => null,
                'title' => $fabricPiece->display_name,
                'image' => null,
                'size' => null,
                'color' => $fabricPiece->color?->name,
                'quantity' => $newQuantity,
                'retail_price' => (float) $fabricPiece->sale_price,
                'wholesale_price' => null,
                'wholesale_min_qty' => null,
                'sku' => $fabricPiece->invoice_number ?: ('TEC-' . $fabricPiece->id),
                'control_unit' => $fabricPiece->control_unit,
                'fabric_type_name' => $fabricPiece->fabricType?->name ?? $fabricPiece->fabric?->name,
                'supplier_name' => $fabricPiece->supplier,
                'available_quantity' => (float) $fabricPiece->available_quantity,
            ];

            $this->saveSessionCart($storeCode, $cart);

            return response()->json([
                'success' => true,
                'cart' => $this->buildCartSummary($cart),
                'message' => 'Tecido adicionado ao carrinho!',
            ]);
        }

        $product = Product::where('tenant_id', $tenant->id)
            ->catalogVisible()
            ->findOrFail($request->product_id);

        // Enforce size/color only for single-item additions
        $isBulk = $request->has('items') && is_array($request->items) && count($request->items) > 0;
        
        if ($product->cut_type_id && !$isBulk && (!$request->size || !$request->color)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, selecione o tamanho e a cor antes de adicionar ao carrinho.'
            ], 422);
        }

        $primaryImage = $product->primary_image_url;

        // Process bulk items if present
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                if (($item['quantity'] ?? 0) <= 0) continue;
                
                $size = $item['size'] ?? 'none';
                $color = $item['color'] ?? 'none';
                $cartKey = $product->id . '-' . $size . '-' . $color;

                if (isset($cart[$cartKey])) {
                    $cart[$cartKey]['quantity'] += (int) $item['quantity'];
                } else {
                    $cart[$cartKey] = [
                        'product_id' => $product->id,
                        'title' => $product->title,
                        'image' => $primaryImage,
                        'size' => ($size !== 'none') ? $size : null,
                        'color' => ($color !== 'none') ? $color : null,
                        'quantity' => (int) $item['quantity'],
                        'retail_price' => (float) $product->price,
                        'wholesale_price' => $product->wholesale_price ? (float) $product->wholesale_price : null,
                        'wholesale_min_qty' => $product->wholesale_min_qty,
                        'sku' => $product->sku,
                    ];
                }
            }
        } else {
            // Legacy single item logic
            if ($product->cut_type_id && (!$request->size || !$request->color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, selecione o tamanho e a cor antes de adicionar ao carrinho.'
                ], 422);
            }

            $cartKey = $product->id . '-' . ($request->size ?? 'none') . '-' . ($request->color ?? 'none');

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $request->quantity;
            } else {
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'image' => $primaryImage,
                    'size' => $request->size,
                    'color' => $request->color,
                    'quantity' => $request->quantity,
                    'retail_price' => (float) $product->price,
                    'wholesale_price' => $product->wholesale_price ? (float) $product->wholesale_price : null,
                    'wholesale_min_qty' => $product->wholesale_min_qty,
                    'sku' => $product->sku,
                ];
            }
        }

        $this->saveSessionCart($storeCode, $cart);

        return response()->json([
            'success' => true,
            'cart' => $this->buildCartSummary($cart),
            'message' => 'Produtos adicionados ao carrinho!',
        ]);
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function updateCart(Request $request, string $storeCode)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|numeric|min:0',
        ]);

        $cart = $this->getSessionCart($storeCode);

        if ($request->quantity <= 0) {
            unset($cart[$request->cart_key]);
        } elseif (isset($cart[$request->cart_key])) {
            if (($cart[$request->cart_key]['item_type'] ?? 'product') === 'fabric_piece') {
                $pieceId = $cart[$request->cart_key]['fabric_piece_id'] ?? null;
                $piece = $pieceId ? FabricPiece::find($pieceId) : null;

                if (!$piece) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Peça de tecido não encontrada.',
                    ], 404);
                }

                if ((float) $request->quantity > (float) $piece->available_quantity + 0.0001) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A quantidade informada excede o saldo disponível.',
                    ], 422);
                }

                $cart[$request->cart_key]['available_quantity'] = (float) $piece->available_quantity;
            }

            $cart[$request->cart_key]['quantity'] = $request->quantity;
        }

        $this->saveSessionCart($storeCode, $cart);

        return response()->json([
            'success' => true,
            'cart' => $this->buildCartSummary($cart),
        ]);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function removeFromCart(Request $request, string $storeCode)
    {
        $request->validate([
            'cart_key' => 'required|string',
        ]);

        $cart = $this->getSessionCart($storeCode);
        unset($cart[$request->cart_key]);
        $this->saveSessionCart($storeCode, $cart);

        return response()->json([
            'success' => true,
            'cart' => $this->buildCartSummary($cart),
        ]);
    }

    /**
     * Get cart data (AJAX)
     */
    public function getCart(string $storeCode)
    {
        $cart = $this->getSessionCart($storeCode);

        return response()->json([
            'success' => true,
            'cart' => $this->buildCartSummary($cart),
        ]);
    }

    /**
     * Checkout page
     */
    public function checkout(string $storeCode)
    {
        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = $tenant->stores()->where('is_main', true)->first()
              ?? $tenant->stores()->first();

        $cart = $this->getSessionCart($storeCode);

        if (empty($cart)) {
            return redirect()->route('catalog.show', $storeCode)
                ->with('error', 'Seu carrinho está vazio.');
        }

        $cartSummary = $this->buildCartSummary($cart);

        return view('catalog.checkout', compact(
            'tenant', 'store', 'cart', 'cartSummary', 'storeCode'
        ));
    }

    /**
     * Process checkout - create CatalogOrder
     */
    public function processCheckout(Request $request, string $storeCode)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_cpf' => 'nullable|string|max:14',
            'notes' => 'nullable|string|max:1000',
            'delivery_address' => 'nullable|string|max:500',
            'payment_method' => 'required|in:pix,delivery',
        ]);

        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = $tenant->stores()->where('is_main', true)->first()
              ?? $tenant->stores()->first();

        $cart = $this->getSessionCart($storeCode);

        if (empty($cart)) {
            return redirect()->route('catalog.show', $storeCode)
                ->with('error', 'Seu carrinho está vazio.');
        }

        $cartSummary = $this->buildCartSummary($cart);

        // Build items array for storage
        $items = [];
        foreach ($cartSummary['items'] as $key => $item) {
            $items[] = [
                'item_type' => $item['item_type'] ?? 'product',
                'product_id' => $item['product_id'] ?? null,
                'fabric_piece_id' => $item['fabric_piece_id'] ?? null,
                'title' => $item['title'],
                'sku' => $item['sku'] ?? null,
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
                'quantity' => $item['quantity'],
                'quantity_label' => $item['quantity_label'] ?? null,
                'control_unit' => $item['control_unit'] ?? null,
                'unit_price' => $item['effective_price'],
                'total' => $item['line_total'],
                'image' => $item['image'] ?? null,
                'fabric_type_name' => $item['fabric_type_name'] ?? null,
                'supplier_name' => $item['supplier_name'] ?? null,
            ];
        }

        $catalogOrder = CatalogOrder::create([
            'tenant_id' => $tenant->id,
            'store_id' => $store->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_cpf' => $request->customer_cpf,
            'items' => $items,
            'total_items' => $cartSummary['total_items'],
            'subtotal' => $cartSummary['subtotal'],
            'total' => $cartSummary['total'],
            'pricing_mode' => $cartSummary['is_wholesale'] ? 'atacado' : 'varejo',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => $request->notes,
            'delivery_address' => $request->delivery_address,
        ]);

        $gatewaySettings = CatalogGatewaySettingsService::forTenant((int) $tenant->id);

        // If PIX, generate payment
        if ($request->payment_method === 'pix') {
            $this->generatePixPayment($catalogOrder, $gatewaySettings);
        }

        // Clear cart
        $this->saveSessionCart($storeCode, []);

        return redirect()->route('catalog.confirmation', [
            'storeCode' => $storeCode,
            'orderCode' => $catalogOrder->order_code,
        ]);
    }

    /**
     * Generate PIX payment via Mercado Pago
     */
    protected function generatePixPayment(CatalogOrder $order, array $gatewaySettings = [])
    {
        $provider = $gatewaySettings['provider'] ?? CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO;

        if ($provider !== CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO) {
            $this->generateInternalPixPayment($order, 'PIX gerado em modo manual.', [
                'gateway' => $provider,
            ]);
            return;
        }

        try {
            $credentials = CatalogGatewaySettingsService::resolveMercadoPagoCredentialsForTenant((int) $order->tenant_id);
            $accessToken = (string) ($credentials['access_token'] ?? '');
            if ($accessToken === '') {
                $this->generateInternalPixPayment($order, 'Chave API do Mercado Pago nao configurada para esta loja.');
                return;
            }

            // Setup MP SDK
            \MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);
            $client = new \MercadoPago\Client\Payment\PaymentClient();

            // Payer data
            $firstName = explode(' ', $order->customer_name)[0] ?? 'Cliente';
            $lastName = explode(' ', $order->customer_name)[1] ?? 'Vestalize';

            $payload = [
                'transaction_amount' => (float) $order->total,
                'description' => "Pedido {$order->order_code} - {$order->tenant->name}",
                'payment_method_id' => 'pix',
                'installments' => 1,
                'external_reference' => $order->order_code,
                'payer' => [
                    'email' => $order->customer_email ?: 'cliente@vestalize.com',
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ],
                'metadata' => [
                    'catalog_order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'tenant_id' => $order->tenant_id,
                ],
            ];

            if ($this->shouldAttachCatalogPaymentWebhookUrl()) {
                $payload['notification_url'] = route('catalog.payment.webhook');
            }

            $payment = $client->create($payload);

            if ($payment && isset($payment->point_of_interaction->transaction_data)) {
                $order->update([
                    'payment_gateway_id' => $payment->id,
                    'payment_data' => [
                        'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
                        'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                        'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
                        'source' => 'mercadopago',
                        'credentials_source' => $credentials['source'] ?? 'unknown',
                    ]
                ]);
            } else {
                $this->generateInternalPixPayment($order, 'Gateway sem QR Code. PIX alternativo gerado.');
            }
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $content = null;
            try {
                $apiResponse = $e->getApiResponse();
                $content = $apiResponse && method_exists($apiResponse, 'getContent')
                    ? $apiResponse->getContent()
                    : null;
            } catch (\Throwable $inner) {
                $content = null;
            }

            Log::error('Catalog PIX API Error', [
                'order_code' => $order->order_code,
                'message' => $e->getMessage(),
                'status_code' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null,
                'content' => $content,
            ]);

            $this->generateInternalPixPayment($order, 'Falha no Mercado Pago. PIX alternativo gerado.', [
                'gateway_error_details' => $content,
            ]);
        } catch (\Exception $e) {
            Log::error('Catalog PIX Error: ' . $e->getMessage());

            $this->generateInternalPixPayment($order, 'Falha no gateway. PIX alternativo gerado.');
        }
    }

    /**
     * Webhook Mercado Pago para atualização de pagamento do catálogo
     */
    public function mercadoPagoWebhook(Request $request)
    {
        try {
            $webhookSecret = config('services.mercadopago.webhook_secret');
            $enforceSignature = (bool) config('services.mercadopago.enforce_webhook_signature', false);
            if (!MercadoPagoWebhookVerifier::isValid($request, is_string($webhookSecret) ? $webhookSecret : null, $enforceSignature)) {
                Log::warning('Catalog webhook Mercado Pago inválido (assinatura).', [
                    'request_id' => $request->header('x-request-id'),
                ]);

                return response()->json(['status' => 'invalid_signature'], 401);
            }

            $topic = strtolower((string) ($request->input('type') ?? $request->input('action') ?? 'payment'));
            if ($topic !== '' && !str_contains($topic, 'payment')) {
                return response()->json(['status' => 'ignored'], 200);
            }

            $paymentId = MercadoPagoWebhookVerifier::extractDataId($request);

            if (!$paymentId) {
                return response()->json(['status' => 'ignored'], 200);
            }

            $catalogOrder = CatalogOrder::where('payment_gateway_id', (string) $paymentId)->first();

            if (!$catalogOrder) {
                Log::warning('Catalog webhook recebeu pagamento sem pedido associado.', [
                    'payment_id' => $paymentId,
                    'payload' => $request->all(),
                ]);

                return response()->json(['status' => 'not_found'], 200);
            }

            $credentials = CatalogGatewaySettingsService::resolveMercadoPagoCredentialsForTenant((int) $catalogOrder->tenant_id);
            $accessToken = (string) ($credentials['access_token'] ?? '');
            if ($accessToken === '') {
                Log::error('Catalog webhook sem credencial Mercado Pago para tenant.', [
                    'payment_id' => $paymentId,
                    'tenant_id' => $catalogOrder->tenant_id,
                ]);

                return response()->json(['status' => 'missing_credentials'], 200);
            }

            \MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            $gatewaySettings = CatalogGatewaySettingsService::forTenant((int) $catalogOrder->tenant_id);
            $newPaymentStatus = $this->mapMercadoPagoStatus(
                (string) ($payment->status ?? ''),
                (bool) ($gatewaySettings['mark_failed_payments'] ?? true)
            );

            $paymentData = is_array($catalogOrder->payment_data) ? $catalogOrder->payment_data : [];
            $paymentData['gateway'] = CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO;
            $paymentData['gateway_status'] = $payment->status ?? null;
            $paymentData['gateway_status_detail'] = $payment->status_detail ?? null;
            $paymentData['last_webhook_at'] = now()->toDateTimeString();
            $paymentData['credentials_source'] = $credentials['source'] ?? 'unknown';

            if ($newPaymentStatus === 'paid') {
                $paymentData['paid_at'] = $paymentData['paid_at'] ?? now()->toDateTimeString();
            }

            if ($newPaymentStatus === 'failed') {
                $paymentData['failed_at'] = now()->toDateTimeString();
            }

            $catalogOrder->update([
                'payment_status' => $newPaymentStatus,
                'payment_gateway_id' => (string) $paymentId,
                'payment_data' => $paymentData,
            ]);

            return response()->json(['status' => 'updated'], 200);
        } catch (\Throwable $e) {
            Log::error('Catalog MercadoPago webhook error: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Order confirmation page
     */
    public function confirmation(string $storeCode, string $orderCode)
    {
        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = $tenant->stores()->where('is_main', true)->first()
              ?? $tenant->stores()->first();

        $catalogOrder = CatalogOrder::where('order_code', $orderCode)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $hasPixQrCode = !empty($catalogOrder->payment_data['qr_code_base64']);
        if ($catalogOrder->payment_method === 'pix' && !$hasPixQrCode) {
            $gatewaySettings = CatalogGatewaySettingsService::forTenant((int) $tenant->id);
            $this->generatePixPayment($catalogOrder, $gatewaySettings);
            $catalogOrder->refresh();
        }

        return view('catalog.confirmation', compact(
            'tenant', 'store', 'catalogOrder', 'storeCode'
        ));
    }

    /**
     * Download order PDF
     */
    public function downloadOrderPdf(string $storeCode, string $orderCode)
    {
        $tenant = Tenant::byCode($storeCode)->firstOrFail();
        $store = $tenant->stores()->where('is_main', true)->first()
              ?? $tenant->stores()->first();

        // Eager load items relationships if needed, though items is a JSON column
        $catalogOrder = CatalogOrder::where('order_code', $orderCode)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $pdf = Pdf::loadView('catalog.pdf.order', compact('tenant', 'store', 'catalogOrder', 'storeCode'));
        
        return $pdf->download("pedido-{$orderCode}.pdf");
    }

    // ─── Private Helpers ───

    private function mapMercadoPagoStatus(string $status, bool $markFailedPayments): string
    {
        $normalized = strtolower(trim($status));

        if ($normalized === 'approved') {
            return 'paid';
        }

        if ($normalized === 'refunded' || $normalized === 'charged_back') {
            return 'refunded';
        }

        if (in_array($normalized, ['rejected', 'cancelled'], true)) {
            return $markFailedPayments ? 'failed' : 'pending';
        }

        return 'pending';
    }

    private function generateInternalPixPayment(CatalogOrder $order, string $message, array $extraData = []): void
    {
        try {
            /** @var PixService $pixService */
            $pixService = app(PixService::class);
            $txId = 'CAT' . $order->id . time();
            $pix = $pixService->generate((float) $order->total, $txId);

            $paymentData = is_array($order->payment_data) ? $order->payment_data : [];
            $paymentData = array_merge($paymentData, [
                'gateway' => CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO,
                'source' => 'pixservice',
                'gateway_message' => $message,
                'gateway_error_at' => now()->toDateTimeString(),
                'qr_code' => $pix['payload'],
                'qr_code_base64' => str_replace('data:image/png;base64,', '', $pix['qrcode']),
                'ticket_url' => '#',
                'pix_key' => $pix['pix_key'] ?? null,
                'merchant_name' => $pix['merchant_name'] ?? null,
            ], $extraData);

            $order->update([
                'payment_gateway_id' => $order->payment_gateway_id ?: $txId,
                'payment_status' => 'pending',
                'payment_data' => $paymentData,
            ]);
        } catch (\Throwable $e) {
            Log::error('Catalog PIX fallback error: ' . $e->getMessage(), [
                'order_code' => $order->order_code,
            ]);

            $paymentData = is_array($order->payment_data) ? $order->payment_data : [];
            $paymentData = array_merge($paymentData, [
                'gateway' => CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO,
                'gateway_message' => 'Nao foi possivel gerar o QR Code PIX automaticamente.',
                'gateway_error' => $e->getMessage(),
                'gateway_error_at' => now()->toDateTimeString(),
            ]);

            $order->update([
                'payment_status' => 'pending',
                'payment_data' => $paymentData,
            ]);
        }
    }

    private function shouldAttachCatalogPaymentWebhookUrl(): bool
    {
        $appUrl = (string) config('app.url');
        if ($appUrl === '') {
            return false;
        }

        $host = strtolower((string) parse_url($appUrl, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return false;
        }

        if (str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
            return false;
        }

        return true;
    }

    private function getSessionCart(string $storeCode): array
    {
        return Session::get("catalog_cart.{$storeCode}", []);
    }

    private function saveSessionCart(string $storeCode, array $cart): void
    {
        Session::put("catalog_cart.{$storeCode}", $cart);
    }

    /**
     * Build a summary of the current cart with totals
     */
    private function buildCartSummary(array $cart): array
    {
        $totalItems = 0;
        $subtotalRetail = 0;
        $subtotalWholesale = 0;
        $isWholesale = false;
        $items = [];

        foreach ($cart as $key => $item) {
            $itemType = $item['item_type'] ?? 'product';
            $qty = (float) ($item['quantity'] ?? 0);

            if ($itemType === 'fabric_piece') {
                $piece = !empty($item['fabric_piece_id'])
                    ? FabricPiece::with(['color', 'fabric', 'fabricType'])->find($item['fabric_piece_id'])
                    : null;

                if ($piece) {
                    $item['title'] = $piece->display_name;
                    $item['color'] = $piece->color?->name;
                    $item['fabric_type_name'] = $piece->fabricType?->name ?? $piece->fabric?->name;
                    $item['available_quantity'] = (float) $piece->available_quantity;
                    $item['retail_price'] = (float) $piece->sale_price;
                }

                $effectivePrice = (float) ($item['retail_price'] ?? 0);
                $lineTotal = round($effectivePrice * $qty, 2);
                $subtotalRetail += $lineTotal;
                $subtotalWholesale += $lineTotal;
                $totalItems += 1;

                $decimals = ($item['control_unit'] ?? 'kg') === 'metros' ? 2 : 3;
                $unitSuffix = ($item['control_unit'] ?? 'kg') === 'metros' ? 'm' : 'kg';

                $items[$key] = array_merge($item, [
                    'effective_price' => $effectivePrice,
                    'line_total' => $lineTotal,
                    'is_wholesale' => false,
                    'requires_variants' => false,
                    'is_incomplete' => false,
                    'quantity_label' => number_format($qty, $decimals, ',', '.') . ' ' . $unitSuffix,
                    'quantity_decimals' => $decimals,
                    'step_quantity' => ($item['control_unit'] ?? 'kg') === 'metros' ? 0.01 : 0.001,
                    'display_type' => 'Peça de tecido',
                ]);

                continue;
            }

            $qty = (int) $qty;
            $totalItems += $qty;

            $retailTotal = $item['retail_price'] * $qty;
            $subtotalRetail += $retailTotal;

            $wholesaleTotal = $item['wholesale_price']
                ? $item['wholesale_price'] * $qty
                : $retailTotal;
            $subtotalWholesale += $wholesaleTotal;

            // Check if this item qualifies for wholesale
            $itemIsWholesale = $item['wholesale_price']
                && $qty >= ($item['wholesale_min_qty'] ?? 6);

            $effectivePrice = $itemIsWholesale
                ? $item['wholesale_price']
                : $item['retail_price'];

            $product = Product::withoutGlobalScopes()->find($item['product_id']);
            $requiresVariants = $product && $product->cut_type_id;
            $isIncomplete = $requiresVariants && (!$item['size'] || !$item['color']);

            $items[$key] = array_merge($item, [
                'effective_price' => $effectivePrice,
                'line_total' => $effectivePrice * $qty,
                'is_wholesale' => $itemIsWholesale,
                'requires_variants' => $requiresVariants,
                'is_incomplete' => $isIncomplete,
                'quantity_label' => $qty . ' un',
                'quantity_decimals' => 0,
                'step_quantity' => 1,
                'display_type' => 'Produto',
            ]);

            if ($itemIsWholesale) {
                $isWholesale = true;
            }
        }

        // Calculate effective subtotal
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['line_total'];
        }

        return [
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => round($subtotal, 2),
            'total' => round($subtotal, 2),
            'is_wholesale' => $isWholesale,
            'subtotal_retail' => round($subtotalRetail, 2),
            'subtotal_wholesale' => round($subtotalWholesale, 2),
        ];
    }
}
