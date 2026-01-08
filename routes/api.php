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
    
    // Se order_id for fornecido, buscar todos os termos relevantes para o pedido
    if ($orderId) {
        $order = \App\Models\Order::with(['items.sublimations', 'items'])->find($orderId);
        
        if ($order) {
            $terms = \App\Models\TermsCondition::getActiveForOrder($order);
            
            \Log::info('Terms found for order', [
                'order_id' => $orderId,
                'terms_count' => $terms->count()
            ]);
            
            if ($terms->isNotEmpty()) {
                $combinedContent = $terms->map(function($term) {
                    $title = $term->title ? "<h3 class='font-bold text-lg mb-2'>{$term->title}</h3>" : '';
                    $content = nl2br(e($term->content));
                    return $title . '<div class="mb-4">' . $content . '</div>';
                })->implode('<hr class="my-4">');
                
                return response()->json([
                    'success' => true,
                    'content' => $combinedContent,
                    'combined_content' => $combinedContent,
                    'version' => $terms->first()->version ?? '1.0',
                    'terms' => $terms->map(function($term) {
                        return [
                            'id' => $term->id,
                            'title' => $term->title,
                            'content' => $term->content,
                            'version' => $term->version,
                            'personalization_type' => $term->personalization_type,
                            'fabric_type' => $term->fabricType ? $term->fabricType->name : null,
                        ];
                    }),
                ]);
            }
        }
    }
    
    // Buscar termos específicos ou gerais
    $terms = \App\Models\TermsCondition::getActive($personalizationType, $fabricTypeId);
    
    if ($terms) {
        return response()->json([
            'success' => true,
            'content' => $terms->content,
            'title' => $terms->title,
            'version' => $terms->version,
            'personalization_type' => $terms->personalization_type,
            'fabric_type' => $terms->fabricType ? $terms->fabricType->name : null,
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

// Personalizações (sem middleware de autenticação temporariamente)
Route::get('/personalizations/{id}', [\App\Http\Controllers\Api\PersonalizationController::class, 'show']);
Route::delete('/personalizations/{id}', [\App\Http\Controllers\Api\PersonalizationController::class, 'destroy']);

// Product Options
// Route::get('/product-options', function (Request $request) {
//     $type = $request->input('type');
//     $name = $request->input('name');
//     
//     $query = \App\Models\ProductOption::query();
//     
//     if ($type) {
//         $query->where('type', $type);
//     }
//     
//     if ($name) {
//         $query->where('name', $name);
//     }
//     
//     $option = $query->first();
//     
//     if ($option) {
//         return response()->json([
//             'id' => $option->id,
//             'name' => $option->name,
//             'type' => $option->type,
//         ]);
//     }
//     
//     return response()->json(['id' => null], 404);
// });

// Buscar dados do item para edição
Route::get('/order-items/{id}', [\App\Http\Controllers\Api\ClientController::class, 'getOrderItem'])->withoutMiddleware(['web']);

// Upload de imagem de capa do item (sem CSRF para API)
Route::post('/order-items/{id}/cover-image', [\App\Http\Controllers\Api\ClientController::class, 'updateItemCoverImage'])->withoutMiddleware(['web']);

// Atualizar nome da arte do item
Route::post('/order-items/{id}/art-name', [\App\Http\Controllers\Api\ClientController::class, 'updateArtName'])->withoutMiddleware(['web']);

// Verificação de Estoque em Tempo Real
Route::get('/stocks/check', [\App\Http\Controllers\StockController::class, 'check'])->withoutMiddleware(['web']);

// Buscar estoque por tipo de corte
Route::get('/stocks/by-cut-type', [\App\Http\Controllers\StockController::class, 'getByCutType'])->withoutMiddleware(['web']);

// Buscar tecido relacionado ao tipo de corte
Route::get('/stocks/fabric-by-cut-type', [\App\Http\Controllers\StockController::class, 'getFabricByCutType'])->withoutMiddleware(['web']);

// Buscar tipos de tecido baseados no tecido pai
Route::get('/stocks/fabric-types', [\App\Http\Controllers\StockController::class, 'getFabricTypes'])->withoutMiddleware(['web']);

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