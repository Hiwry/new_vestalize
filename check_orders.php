<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$latest = Order::latest()->first();
$latestPersonalized = Order::where('origin', 'personalized')->latest()->first();
$data = [
    'latest_order' => $latest ? $latest->toArray() : null,
    'latest_personalized_order' => $latestPersonalized ? $latestPersonalized->toArray() : null,
];
echo json_encode($data, JSON_PRETTY_PRINT);
