<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SublimationProduct;
use App\Models\Client;
use App\Models\Order;
use App\Models\Status;
use App\Models\Store;
use App\Models\Payment;
use App\Services\PixService;
use App\Services\OrderWizardService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonalizedController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user) {
                $user->loadMissing('tenant.currentPlan');
            }

            $tenant = $user?->tenant;
            if ($user && $user->tenant_id !== null && $tenant && !$tenant->canAccess('personalized')) {
                \Log::warning('Personalized access denied', [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'plan_id' => $tenant->plan_id,
                    'plan_slug' => $tenant->currentPlan?->slug,
                    'features' => $tenant->currentPlan?->features,
                    'db' => config('database.connections.mysql.database'),
                    'path' => $request->path(),
                ]);
                abort(403, 'Seu plano não inclui o módulo de Personalizados.');
            }
            return $next($request);
        });
    }

    /**
     * Display the main personalized sales page.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        
        $query = \App\Models\SubLocalProduct::where('is_active', true);
        
        if ($type !== 'all') {
            // Map legacy types to new categories
            $categoryMap = [
                'caneca' => 'canecas',
                'camisa' => 'vestuario',
                'almofada' => 'diversos', // Fallback
                'tirante' => 'acessorios',
                'custom' => 'diversos'
            ];
            
            $category = $categoryMap[$type] ?? $type;
            $query->where('category', $category);
        }
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $products = $query->with('addons')->orderBy('name')->get();
        
        // If it's an AJAX request (filtering), return just the grid
        if ($request->ajax()) {
            return view('personalized.partials.grid', compact('products'));
        }
        
        $clients = Client::orderBy('name')->limit(50)->get(); // Optimization
        $cart = Session::get('personalized_cart', []);
        
        // Define categories for the sidebar/tabs
        $categories = [
            'all' => 'Todos',
            'caneca' => 'Canecas',
            'camisa' => 'Camisas/Vestuário',
            'tirante' => 'Acessórios',
            'custom' => 'Diversos'
        ];

        return view('personalized.index', compact('products', 'clients', 'cart', 'categories', 'type'));
    }

    /**
     * Add item to specialized cart.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:sub_local_products,id',
            'quantity' => 'required|integer|min:1',
            'customization_note' => 'nullable|string',
            'client_name' => 'nullable|string', // If it's a specific name on the item
            'price' => 'nullable|numeric|min:0', // Allow override or default
            'addons' => 'nullable|array',
            'addons.*.id' => 'exists:sub_local_product_addons,id'
        ]);

        $product = \App\Models\SubLocalProduct::with('addons')->findOrFail($validated['product_id']);
        
        // Determine price logic
        // If user submitted a specific price override, use it (if allowed)
        // Otherwise calculate based on quantity using existing logic
        $basePrice = $validated['price'] ?? $product->getPriceForQuantity($validated['quantity']);
        
        // Calculate Addons Price
        $addonsTotal = 0;
        $selectedAddons = [];
        
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addonInput) {
                $addon = $product->addons->where('id', $addonInput['id'])->first();
                if ($addon) {
                    $addonsTotal += $addon->price;
                    $selectedAddons[] = [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price
                    ];
                }
            }
        }
        
        $unitPrice = $basePrice + $addonsTotal;
        
        $cart = Session::get('personalized_cart', []);
        
        $cartItem = [
            'id' => uniqid(),
            'product_id' => $product->id,
            'name' => $product->name,
            'type' => $product->category,
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $validated['quantity'],
            'customization_note' => $validated['customization_note'] ?? '',
            'client_name' => $validated['client_name'] ?? '', // Name to print on item
            'addons' => $selectedAddons,
            'base_price_unit' => $basePrice
        ];
        
        $cart[] = $cartItem;
        Session::put('personalized_cart', $cart);
        
        return response()->json([
            'success' => true, 
            'message' => 'Item adicionado!', 
            'cart' => $cart,
            'count' => count($cart),
            'total' => $this->calculateCartTotal($cart)
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(Request $request)
    {
        $id = $request->input('id');
        $cart = Session::get('personalized_cart', []);
        
        $cart = array_filter($cart, function($item) use ($id) {
            return $item['id'] !== $id;
        });
        
        Session::put('personalized_cart', $cart);
        
        return response()->json([
            'success' => true, 
            'cart' => $cart,
            'count' => count($cart),
            'total' => $this->calculateCartTotal($cart)
        ]);
    }

    /**
     * Clear the cart.
     */
    public function clearCart()
    {
        Session::forget('personalized_cart');
        return response()->json(['success' => true]);
    }

    /**
     * Finalize the order.
     */
    public function checkout(Request $request)
    {
        Log::info('Personalized checkout starting...', ['request' => $request->all()]);
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'payment_method' => 'required|string|in:pix,dinheiro,cartao,cartao_credito,cartao_debito,transferencia,boleto',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        Log::info('Personalized checkout validation passed', ['validated' => $validated]);

        $cart = Session::get('personalized_cart', []);
        Log::info('Personalized checkout cart retrieved', ['cart_count' => count($cart)]);
        
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Carrinho vazio.'], 400);
        }

        DB::beginTransaction();
        try {
            Log::info('Personalized checkout transaction started');
            $normalizedPaymentMethod = $this->normalizePaymentMethod($validated['payment_method']);
            $subtotal = $this->calculateCartTotal($cart);
            $discount = $validated['discount'] ?? 0;
            $finalTotal = max(0, $subtotal - $discount);
            $totalQuantity = array_sum(array_column($cart, 'quantity'));
            Log::info('Personalized checkout values', [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'final_total' => $finalTotal,
                'total_quantity' => $totalQuantity
            ]);
            Log::info('Personalized checkout values', ['subtotal' => $subtotal, 'discount' => $discount, 'finalTotal' => $finalTotal]);

            $user = Auth::user();
            $storeId = app(OrderWizardService::class)->resolveStoreId($user);
            $tenantId = $user?->tenant_id ?? session('selected_tenant_id');
            Log::info('Personalized checkout context', ['user_id' => $user->id, 'store_id' => $storeId, 'tenant_id' => $tenantId]);

            if ($tenantId === null && $storeId) {
                $tenantId = Store::find($storeId)?->tenant_id;
            }

            $statusQuery = Status::withoutGlobalScopes();
            if ($tenantId !== null) {
                $statusQuery->where('tenant_id', $tenantId);
            }
            $status = (clone $statusQuery)->where('type', 'personalized')->orderBy('position')->first();
            if (!$status) {
                $status = (clone $statusQuery)->orderBy('position')->first();
            }
            Log::info('Personalized checkout status identified', ['status_id' => $status?->id]);
            
            // Create Order
            Log::info('Personalized checkout creating order');
            $order = Order::create([
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(), // Seller
                'store_id' => $storeId,
                'status_id' => $status?->id ?? 1, // Default status (Pending/Open)
                'order_date' => now()->toDateString(),
                'delivery_date' => now()->addDays(2)->toDateString(),
                'is_draft' => false,
                'is_pdv' => false,
                'total_items' => $totalQuantity,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $finalTotal,
                'notes' => $validated['notes'] ?? 'Venda via Módulo Personalizados',
                'origin' => 'personalized',
            ]);
            Log::info('Personalized checkout order created', ['order_id' => $order->id]);

            // Add Items
            $itemNumber = 1;
            foreach ($cart as $item) {
                $customizationNote = $item['customization_note'] ?? null;
                $artName = $customizationNote ?: ($item['name'] ?? null);
                // We might need to map to OrderItem model
                // Assuming OrderItem structure. 
                // Since this is custom, we might use the 'product_name' field if exact product_id structure differs from standard
                $order->items()->create([
                    'item_number' => $itemNumber++,
                    'print_type' => $item['name'] ?? '',
                    'print_desc' => $customizationNote,
                    'art_name' => $artName,
                    'art_notes' => $customizationNote,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            // Create Payment Record (Simplified)
            $paymentMethodPayload = [
                'method' => $normalizedPaymentMethod,
                'amount' => $finalTotal,
                'date' => now()->toDateString(),
            ];

            if ($validated['payment_method'] !== $normalizedPaymentMethod) {
                $paymentMethodPayload['original_method'] = $validated['payment_method'];
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $normalizedPaymentMethod,
                'payment_method' => $normalizedPaymentMethod,
                'payment_methods' => [$paymentMethodPayload],
                'amount' => $finalTotal,
                'entry_amount' => $finalTotal,
                'remaining_amount' => 0,
                'payment_date' => now(),
                'status' => 'completed', // Assuming immediate payment for POS logic? Or pending?
            ]);

            Session::forget('personalized_cart');
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venda realizada!', 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Personalized checkout exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erro ao processar: ' . $e->getMessage()], 500);
        }
    }

    private function calculateCartTotal($cart)
    {
        return array_sum(array_column($cart, 'total_price'));
    }

    private function normalizePaymentMethod(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cartao_credito', 'cartao_debito' => 'cartao',
            default => $paymentMethod,
        };
    }
}
