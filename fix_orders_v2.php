<?php

use App\Models\Order;
use App\Models\Status;
use App\Models\Tenant;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Inspecting orders...\n";

// Get all orders without scope to see what's going on
$orders = Order::withoutGlobalScope('tenant')->get();

foreach ($orders as $order) {
    echo "Order #{$order->id} (Tenant {$order->tenant_id}): Status ID = " . var_export($order->status_id, true) . "\n";
    
    // Check if status exists
    if ($order->status_id) {
        $status = Status::withoutGlobalScope('tenant')->find($order->status_id);
        if (!$status) {
            echo "  -> Status ID {$order->status_id} NOT FOUND in DB.\n";
            
            // Fix it
             $pendenteStatus = Status::withoutGlobalScope('tenant')
                ->where('tenant_id', $order->tenant_id)
                ->where('name', 'Pendente')
                ->first();
                
             if ($pendenteStatus) {
                 $order->update(['status_id' => $pendenteStatus->id]);
                 echo "  -> FIXED: Set to Pendente (ID {$pendenteStatus->id})\n";
             }
        } else {
             echo "  -> Status found: {$status->name} (Tenant {$status->tenant_id})\n";
        }
    } else {
         echo "  -> Status ID is NULL/Empty.\n";
         // Fix it
         $pendenteStatus = Status::withoutGlobalScope('tenant')
            ->where('tenant_id', $order->tenant_id)
            ->where('name', 'Pendente')
            ->first();
            
         if ($pendenteStatus) {
             $order->update(['status_id' => $pendenteStatus->id]);
             echo "  -> FIXED: Set to Pendente (ID {$pendenteStatus->id})\n";
         }
    }
}
echo "Done.\n";
