<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

$user = Auth::loginUsingId(1); // Simular login do usuário 1 (geralmente o admin)

$orders = Order::where('origin', 'personalized')->latest()->take(5)->get();
$results = [];
foreach ($orders as $o) {
    $results[] = [
        'id' => $o->id,
        'origin' => $o->origin,
        'is_pdv' => $o->is_pdv,
        'is_draft' => $o->is_draft,
        'tenant_id' => $o->tenant_id,
        'store_id' => $o->store_id,
        'status_id' => $o->status_id,
        'created_at' => $o->created_at->toDateTimeString(),
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'user_id' => $user->id,
    'user_tenant_id' => $user->tenant_id,
    'orders' => $results
], JSON_PRETTY_PRINT);
