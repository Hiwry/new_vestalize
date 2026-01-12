<?php
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'testnfe@teste.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

Auth::login($user);

$statuses = Status::orderBy('position')->get();
echo "Statuses count: " . $statuses->count() . "\n";
foreach ($statuses as $status) {
    echo "- {$status->name} (ID: {$status->id}, Color: {$status->color})\n";
}

$ordersCount = \App\Models\Order::notDrafts()->where('is_cancelled', false)->count();
echo "Active orders count: $ordersCount\n";
