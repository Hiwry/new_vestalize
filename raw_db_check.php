<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Table Structure: company_settings ---\n";
try {
    $columns = DB::select('SHOW COLUMNS FROM company_settings');
    foreach ($columns as $col) {
        echo "Field: {$col->Field}, Type: {$col->Type}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- Records in company_settings ---\n";
try {
    $records = DB::table('company_settings')->get();
    foreach ($records as $r) {
        echo "ID: " . ($r->id ?? 'N/A') . 
             ", Store: " . ($r->store_id ?? 'NULL') . 
             ", Name: " . ($r->company_name ?? 'N/A') . 
             ", Terms Len: " . strlen($r->terms_conditions ?? '') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- Stores related to Tenant 8 ---\n";
try {
    $stores = DB::table('stores')->where('tenant_id', 8)->get();
    foreach ($stores as $s) {
        echo "ID: {$s->id}, Name: {$s->name}, Main: " . ($s->is_main ? 'Yes' : 'No') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
