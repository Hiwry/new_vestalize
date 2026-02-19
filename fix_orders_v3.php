<?php

use App\Models\Order;
use App\Models\Status;
use App\Models\Tenant;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "fixing cross-tenant status references...\n";

// Get all orders without scope
$orders = Order::withoutGlobalScope('tenant')->get();

foreach ($orders as $order) {
    if (!$order->status_id) continue;
    
    $currentStatus = Status::withoutGlobalScope('tenant')->find($order->status_id);
    
    if ($currentStatus && $currentStatus->tenant_id != $order->tenant_id) {
        echo "Order #{$order->id} (Tenant {$order->tenant_id}) points to Status {$currentStatus->id} (Tenant {$currentStatus->tenant_id}) - MISMATCH!\n";
        
        // Find correct status for this tenant with the same name
        $correctStatus = Status::withoutGlobalScope('tenant')
            ->where('tenant_id', $order->tenant_id)
            ->where('name', $currentStatus->name)
            ->first();
            
        if ($correctStatus) {
            $order->update(['status_id' => $correctStatus->id]);
            echo "  -> FIXED: Updated to Status ID {$correctStatus->id} ({$correctStatus->name})\n";
        } else {
            // Fallback to 'Pendente'
             $pendenteStatus = Status::withoutGlobalScope('tenant')
                ->where('tenant_id', $order->tenant_id)
                ->where('name', 'Pendente')
                ->first();
             
             if ($pendenteStatus) {
                $order->update(['status_id' => $pendenteStatus->id]);
                echo "  -> FIXED: Updated to Status ID {$pendenteStatus->id} (Pendente fallback)\n";
             } else {
                 echo "  -> ERROR: Could not find suitable status for tenant {$order->tenant_id}\n";
             }
        }
    }
}
echo "Done.\n";
