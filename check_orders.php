<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$latest = Order::latest()->first();
if ($latest) {
    echo "LATEST ORDER:\n";
    print_r($latest->only(['id', 'origin', 'tenant_id', 'store_id', 'status_id', 'is_pdv', 'is_draft', 'created_at']));
} else {
    echo "No orders found.\n";
}

$latestPersonalized = Order::where('origin', 'personalized')->latest()->first();
if ($latestPersonalized) {
    echo "\nLATEST PERSONALIZED ORDER:\n";
    print_r($latestPersonalized->only(['id', 'origin', 'tenant_id', 'store_id', 'status_id', 'is_pdv', 'is_draft', 'created_at']));
} else {
    echo "\nNo personalized orders found.\n";
}
