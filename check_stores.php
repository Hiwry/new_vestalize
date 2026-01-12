<?php
use App\Models\Store;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stores = Store::all();
foreach ($stores as $store) {
    echo "ID: {$store->id}, Name: {$store->name}, Tenant ID: " . ($store->tenant_id ?? 'NULL') . "\n";
}
