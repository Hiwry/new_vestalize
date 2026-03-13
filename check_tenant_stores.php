<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tenantId = 8;
echo "--- Stores for Tenant $tenantId (Raw SQL) ---\n";
$stores = DB::select('SELECT id, name, is_main, tenant_id FROM stores WHERE tenant_id = ?', [$tenantId]);
foreach ($stores as $s) {
    echo "ID: {$s->id}, Name: {$s->name}, Main: {$s->is_main}, Tenant: {$s->tenant_id}\n";
}

echo "\n--- Company Settings for these Stores ---\n";
foreach ($stores as $s) {
    $set = DB::select('SELECT id, store_id, terms_conditions FROM company_settings WHERE store_id = ?', [$s->id]);
    if (!empty($set)) {
        foreach ($set as $r) {
            echo "Store ID: {$s->id}, Settings ID: {$r->id}, Terms Len: " . strlen($r->terms_conditions ?? '') . "\n";
        }
    } else {
        echo "Store ID: {$s->id}: No settings record found.\n";
    }
}
