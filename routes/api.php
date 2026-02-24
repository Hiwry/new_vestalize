<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Validação de Afiliado (Público)
Route::get('/affiliates/validate/{code}', [\App\Http\Controllers\AffiliateController::class, 'validateCode']);

// Rotas da API sem autenticação

// Adicionais de Sublimação
Route::get('/sublimation-addons', function () {
    $addons = \App\Models\SublimationAddon::getActiveAddons();
    return response()->json([
        'success' => true,
        'data' => $addons,
        'count' => $addons->count()
    ]);
});

// Cores de Serigrafia
Route::get('/serigraphy-colors', function () {
    $colors = \App\Models\SerigraphyColor::where('active', true)
        ->orderBy('order')
        ->get();
    return response()->json($colors);
});

// Termos e Condições
Route::get('/terms-conditions', function (Request $request) {
    $personalizationType = $request->input('personalization_type');
    $fabricTypeId = $request->input('fabric_type_id');
    $orderId = $request->input('order_id');
    
    \Log::info('API Terms Conditions called', [
        'order_id' => $orderId,
        'personalization_type' => $personalizationType,
        'fabric_type_id' => $fabricTypeId
    ]);
    
    $combinedContent = '';
    $termsFound = false;
    
    // 1. Buscar Termos da Loja (CompanySetting) - NOVO
    $storeSettings = null;
    if ($orderId) {
        $order = \App\Models\Order::find($orderId);
        if ($order) {
            $storeSettings = \App\Models\CompanySetting::getSettings($order->store_id);
        }
    } else {
        $storeSettings = \App\Models\CompanySetting::getSettings();
    }
    
    if ($storeSettings && $storeSettings->terms_conditions) {
        $combinedContent .= "<h3 class='font-bold text-lg mb-2'>Termos da Loja</h3>";
        $combinedContent .= '<div class="mb-4">' . nl2br(e($storeSettings->terms_conditions)) . '</div>';
        $termsFound = true;
    }
    
    // 2. Buscar Termos Específicos (TermsCondition)
    if ($orderId) {
        $order = $order ?? \App\Models\Order::with(['items.sublimations', 'items'])->find($orderId);
        
        if ($order) {
            $terms = \App\Models\TermsCondition::getActiveForOrder($order);
            
            \Log::info('Specific terms found for order', [
                'order_id' => $orderId,
                'terms_count' => $terms->count()
            ]);
            
            if ($terms->isNotEmpty()) {
                if ($termsFound) $combinedContent .= '<hr class="my-4">';
                
                $combinedContent .= $terms->map(function($term) {
                    $title = $term->title ? "<h3 class='font-bold text-lg mb-2'>{$term->title}</h3>" : '';
                    $content = nl2br(e($term->content));
                    return $title . '<div class="mb-4">' . $content . '</div>';
                })->implode('<hr class="my-4">');
                
                $termsFound = true;
            }
        }
    } else {
        $terms = \App\Models\TermsCondition::getActive($personalizationType, $fabricTypeId);
        if ($terms) {
            if ($termsFound) $combinedContent .= '<hr class="my-4">';
            
            $title = $terms->title ? "<h3 class='font-bold text-lg mb-2'>{$terms->title}</h3>" : '';
            $content = nl2br(e($terms->content));
            $combinedContent .= $title . '<div class="mb-4">' . $content . '</div>';
            $termsFound = true;
        }
    }
    
    if ($termsFound) {
        return response()->json([
            'success' => true,
            'content' => $combinedContent,
            'combined_content' => $combinedContent,
            'version' => '1.0'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Termos e condições não encontrados'
    ]);
});

// Preços de Personalização
Route::get('/personalization-prices/price', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPrice']);
Route::get('/personalization-prices/sizes', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getSizes']);
Route::get('/personalization-prices/ranges', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPriceRanges']);
Route::post('/personalization-prices/multiple', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getMultiplePrices']);

// Personalizações - ROTAS REMOVIDAS (Movidas para web.php para suporte a sessões)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/personalizations/{id}', [\App\Http\Controllers\Api\PersonalizationController::class, 'show']);
//     Route::delete('/personalizations/{id}', [\App\Http\Controllers\Api\PersonalizationController::class, 'destroy']);
// });


// Product Options
Route::get('/product-options', function (Request $request) {
    $type = $request->input('type');
    $name = $request->input('name');
    
    $query = \App\Models\ProductOption::query();
    
    if ($type) {
        $query->where('type', $type);
    }
    
    if ($name) {
        $query->whereRaw('UPPER(name) = ?', [strtoupper($name)]);
    }
    
    $option = $query->first();
    
    if ($option) {
        return response()->json([
            'id' => $option->id,
            'name' => $option->name,
            'type' => $option->type,
        ]);
    }
    
    return response()->json(['id' => null], 404);
});

// Buscar dados do item para edição - PROTEGIDO
Route::get('/order-items/{id}', [\App\Http\Controllers\Api\ClientController::class, 'getOrderItem'])->middleware('auth:sanctum');

// Upload de imagem de capa do item - PROTEGIDO
Route::post('/order-items/{id}/cover-image', [\App\Http\Controllers\Api\ClientController::class, 'updateItemCoverImage'])->middleware('auth:sanctum');

// Atualizar nome da arte do item - PROTEGIDO
Route::post('/order-items/{id}/art-name', [\App\Http\Controllers\Api\ClientController::class, 'updateArtName'])->middleware('auth:sanctum');

// Stock APIs moved to web.php for proper session authentication (tenant_id support)

// =============================================
// SUB. TOTAL API
// =============================================

// Buscar tipos de produtos SUB. TOTAL
Route::get('/sublimation-total/types', function (Request $request) {
    $user = $request->user();
    $tenantId = $user ? $user->tenant_id : null;
    
    $types = \App\Models\SublimationProductType::getForTenant($tenantId);
    
    return response()->json([
        'success' => true,
        'data' => $types->map(function($type) {
            return [
                'slug' => $type->slug,
                'name' => $type->name,
            ];
        }),
    ]);
})->withoutMiddleware(['web']);

// Buscar adicionais de um tipo SUB. TOTAL
Route::get('/sublimation-total/addons/{type}', function (Request $request, string $type) {
    $user = $request->user();
    $tenantId = $user ? $user->tenant_id : session('current_tenant_id');
    
    $addons = \App\Models\SublimationProductAddon::where('product_type', $type)
        ->where(function($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })
        ->orderBy('order')
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $addons->map(function($addon) {
            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => (float) $addon->price,
            ];
        }),
    ]);
})->withoutMiddleware(['web']);

// Buscar preço para quantidade SUB. TOTAL
Route::get('/sublimation-total/price/{type}/{quantity}', function (Request $request, string $type, int $quantity) {
    $user = $request->user();
    $tenantId = $user ? $user->tenant_id : session('current_tenant_id');
    
    $price = \App\Models\SublimationProductPrice::getPriceFor($type, $quantity, $tenantId);
    
    return response()->json([
        'success' => $price !== null,
        'price' => $price ?? 0,
    ]);
})->withoutMiddleware(['web']);

// =============================================
// API V1 - MOBILE & INTEGRATIONS
// =============================================
Route::prefix('v1')->group(function () {
    // Autenticação Mobile (Token) - COM RATE LIMITING para prevenir brute force
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id
            ]
        ]);
    })->middleware('throttle:5,1'); // Máximo 5 tentativas por minuto

    // Rotas Protegidas
    Route::middleware('auth:sanctum')->group(function () {
        // Pedidos
        Route::get('/orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
        Route::get('/orders/{id}', [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [\App\Http\Controllers\Api\V1\OrderController::class, 'updateStatus']);
        
        // Dashboard Stats (Útil para Mobile)
        Route::get('/dashboard/stats', function () {
            $user = Auth::user();
            return response()->json([
                'total_orders' => \App\Models\Order::where('tenant_id', $user->tenant_id)->count(),
                'pending_orders' => \App\Models\Order::where('tenant_id', $user->tenant_id)->where('status_id', 1)->count(), // Exemplo
                'monthly_revenue' => 0, // Implementar logica real se necessário
            ]);
        });
    });
});