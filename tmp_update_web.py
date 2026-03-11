import sys

file_path = r'c:\xampp\htdocs\vestalize\routes\web.php'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# 1. Add Request import at the top
if 'use Illuminate\\Http\\Request;' not in ''.join(lines[:20]):
    lines.insert(1, "use Illuminate\\Http\\Request;\n")

# 2. Add sublimation-total routes
# Look for the end of the stock APIs or a specific marker
target_line = "        Route::get('/stocks/check', [\\App\\Http\\Controllers\\StockController::class, 'check']);\n"
new_routes = """
        // =============================================
        // SUB. TOTAL API (Moved from api.php for session/tenant support)
        // =============================================
        Route::prefix('sublimation-total')->group(function () {
            // Buscar tipos de produtos SUB. TOTAL
            Route::get('/types', function (Request $request) {
                $tenantId = Auth::user() ? Auth::user()->tenant_id : session('current_tenant_id');
                $types = \\App\\Models\\SublimationProductType::getForTenant($tenantId);
                
                return response()->json([
                    'success' => true,
                    'data' => $types->map(function($type) {
                        return [
                            'slug' => $type->slug,
                            'name' => $type->name,
                        ];
                    }),
                ]);
            });

            // Buscar adicionais de um tipo SUB. TOTAL
            Route::get('/addons/{type}', function (Request $request, string $type) {
                $tenantId = Auth::user() ? Auth::user()->tenant_id : session('current_tenant_id');

                $productType = \\App\\Models\\SublimationProductType::with('tecido')
                    ->where('slug', $type)
                    ->where(function($q) use ($tenantId) {
                        $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
                    })
                    ->first();

                $startingPriceRow = \\App\\Models\\SublimationProductPrice::where('product_type', $type)
                    ->where(function($q) use ($tenantId) {
                        $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
                    })
                    ->orderBy('quantity_from')
                    ->first();
                
                $addons = \\App\\Models\\SublimationProductAddon::where('product_type', $type)
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
                    'type_name' => $productType?->name,
                    'default_fabric_name' => $productType?->tecido?->name,
                    'starting_price' => $startingPriceRow ? (float) $startingPriceRow->price : 0,
                    'starting_quantity_from' => $startingPriceRow?->quantity_from,
                ]);
            });

            // Buscar preço para quantidade SUB. TOTAL
            Route::get('/price/{type}/{quantity}', function (Request $request, string $type, int $quantity) {
                $tenantId = Auth::user() ? Auth::user()->tenant_id : session('current_tenant_id');
                $price = \\App\\Models\\SublimationProductPrice::getPriceFor($type, $quantity, $tenantId);
                
                return response()->json([
                    'success' => $price !== null,
                    'price' => $price ?? 0,
                ]);
            });
        });
"""

found = False
for i, line in enumerate(lines):
    if target_line in line:
        lines.insert(i + 1, new_routes)
        found = True
        break

if not found:
    print("Could not find target line in web.php")
    sys.exit(1)

with open(file_path, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("Successfully updated web.php with sublimation-total routes")
