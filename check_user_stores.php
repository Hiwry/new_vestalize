<?php
use App\Models\User;
use App\Models\Store;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'testnfe@teste.com')->first();
echo "User: {$user->name}, Tenant ID: {$user->tenant_id}\n";

$stores = Store::where('tenant_id', $user->tenant_id)->get();
echo "Stores for this tenant: " . $stores->count() . "\n";
foreach ($stores as $store) {
    echo "- ID: {$store->id}, Name: {$store->name}\n";
}

$allStores = Store::all();
echo "All available stores IDs: " . implode(', ', $allStores->pluck('id')->toArray()) . "\n";
