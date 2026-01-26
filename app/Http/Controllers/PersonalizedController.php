<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SublimationProduct;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PixService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonalizedController extends Controller
{
    /**
     * Display the main personalized sales page.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        
        // Fetch SublimationProducts
        // If type is 'all', we might want to group them or show categories
        // For now, let's fetch based on type if provided, otherwise all active
        
        $query = SublimationProduct::active();
        
        if ($type !== 'all') {
            $query->where('type', $type);
        }
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $products = $query->orderBy('name')->get();
        
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
            'camisa' => 'Camisas',
            'almofada' => 'Almofadas',
            'tirante' => 'Tirantes',
            'custom' => 'Outros'
        ];

        return view('personalized.index', compact('products', 'clients', 'cart', 'categories', 'type'));
    }

    /**
     * Add item to specialized cart.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:sublimation_products,id',
            'quantity' => 'required|integer|min:1',
            'customization_note' => 'nullable|string',
            'client_name' => 'nullable|string', // If it's a specific name on the item
            'price' => 'nullable|numeric|min:0', // Allow override or default
        ]);

        $product = SublimationProduct::findOrFail($validated['product_id']);
        
        // Determine price logic
        // If user submitted a specific price override, use it (if allowed)
        // Otherwise calculate based on quantity using existing logic
        $unitPrice = $validated['price'] ?? $product->getPriceForQuantity($validated['quantity']);
        
        $cart = Session::get('personalized_cart', []);
        
        $cartItem = [
            'id' => uniqid(),
            'product_id' => $product->id,
            'name' => $product->name,
            'type' => $product->type,
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $validated['quantity'],
            'customization_note' => $validated['customization_note'] ?? '',
            'client_name' => $validated['client_name'] ?? '', // Name to print on item
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
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'payment_method' => 'required|string', // pix, dinheiro, cartao, etc.
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cart = Session::get('personalized_cart', []);
        
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Carrinho vazio.'], 400);
        }

        DB::beginTransaction();
        try {
            $totalItems = array_sum(array_column($cart, 'total_price'));
            $discount = $validated['discount'] ?? 0;
            $finalTotal = max(0, $totalItems - $discount);
            
            // Create Order
            // Assuming we use the standard Order model
            $order = Order::create([
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(), // Seller
                'status' => 'pending', // Or completed depending on payment? Let's say pending until paid? Or confirmed.
                'payment_status' => 'pending',
                'total_amount' => $finalTotal,
                'discount' => $discount,
                'notes' => $validated['notes'] ?? 'Venda via Módulo Personalizados',
                'origin' => 'personalized', // Need to make sure database supports this or use generic
                'delivery_date' => now()->addDays(2), // Standard default? Or ask?
            ]);

            // Add Items
            foreach ($cart as $item) {
                // We might need to map to OrderItem model
                // Assuming OrderItem structure. 
                // Since this is custom, we might use the 'product_name' field if exact product_id structure differs from standard
                $order->items()->create([
                    'product_id' => null, // If it's a SublimationProduct, it might not link to standard 'products' table if they are separate. 
                    // If SublimationProduct IS NOT Product, we can't link directly if FK exists.
                    // Checking implementation_plan, I see SublimationProduct is a separate model.
                    // I will store the name and details in JSON or description if possible. 
                    'name' => $item['name'] . ($item['customization_note'] ? " ({$item['customization_note']})" : ""),
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'type' => 'custom', // To distinguish
                ]);
            }

            // Create Payment Record (Simplified)
            Payment::create([
                'order_id' => $order->id,
                'method' => $validated['payment_method'],
                'amount' => $finalTotal,
                'status' => 'completed', // Assuming immediate payment for POS logic? Or pending?
                // If POS, usually completed.
            ]);

            Session::forget('personalized_cart');
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venda realizada!', 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro ao processar: ' . $e->getMessage()], 500);
        }
    }

    private function calculateCartTotal($cart)
    {
        return array_sum(array_column($cart, 'total_price'));
    }
}
