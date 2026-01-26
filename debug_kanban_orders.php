<?php

use App\Models\Status;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debug Kanban Orders:\n";
echo "------------------------------------------------\n";

// 1. Listar Statuses Atuais
$statuses = Status::orderBy('position')->get();
echo "Status Disponíveis:\n";
foreach ($statuses as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Slug: {$s->slug} | Pos: {$s->position}\n";
}
echo "------------------------------------------------\n";

// 2. Contar Pedidos por Status
echo "Contagem de Pedidos por ID de Status:\n";
$counts = Order::select('status_id', DB::raw('count(*) as total'))
    ->groupBy('status_id')
    ->get();

foreach ($counts as $c) {
    $statusName = $statuses->where('id', $c->status_id)->first()->name ?? 'DESCONHECIDO (ID ' . $c->status_id . ')';
    echo "Status ID {$c->status_id} ({$statusName}): {$c->total} pedidos\n";
}
echo "------------------------------------------------\n";

// 3. Verificar Filtros Comuns (Tenant)
$totalOrders = Order::count();
echo "Total Global de Pedidos: {$totalOrders}\n";

// Verificar se há pedidos sem status
$nullStatus = Order::whereNull('status_id')->count();
if ($nullStatus > 0) {
    echo "ALERTA: {$nullStatus} pedidos com status_id NULL!\n";
}
