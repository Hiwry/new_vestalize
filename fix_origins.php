<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Status;

$count = Order::whereHas('status', function($q) {
    $q->where('type', 'personalized');
})->where(function($q) {
    $q->whereNull('origin')
      ->orWhere('origin', '!=', 'personalized');
})->update(['origin' => 'personalized']);

echo "Updated $count orders to origin=personalized.\n";
