<?php
use App\Models\Order;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = Order::withoutGlobalScopes()
    ->with(['client', 'user'])
    ->latest()
    ->limit(5)
    ->get();

foreach ($orders as $order) {
    echo "ID: {$order->id}\n";
    echo "Tenant ID: {$order->tenant_id}\n";
    echo "User: {$order->user->name} ({$order->user->email})\n";
    echo "Client: " . ($order->client->name ?? 'N/A') . "\n";
    echo "Draft: " . ($order->is_draft ? 'Yes' : 'No') . "\n";
    echo "PDV: " . ($order->is_pdv ? 'Yes' : 'No') . "\n";
    echo "Store ID: " . ($order->store_id ?? 'NULL') . "\n";
    echo "Created At: {$order->created_at}\n";
    echo "--------------------------\n";
}
