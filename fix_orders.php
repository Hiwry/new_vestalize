<?php

use App\Models\Order;
use App\Models\Status;
use App\Models\Tenant;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Correcting order statuses...\n";

$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "Processing Tenant: " . $tenant->name . " (ID: " . $tenant->id . ")\n";
    
    // Find 'Pendente' status for this tenant
    $pendenteStatus = Status::withoutGlobalScope('tenant')
        ->where('tenant_id', $tenant->id)
        ->where('name', 'Pendente')
        ->first();
        
    if (!$pendenteStatus) {
        // Create if missing (fallback)
        $pendenteStatus = Status::create([
            'tenant_id' => $tenant->id,
            'name' => 'Pendente',
            'color' => '#F59E0B',
            'position' => 1
        ]);
        echo "Created Status 'Pendente' for tenant.\n";
    }
    
    // Fix orders with null status
    $updatedCount = Order::withoutGlobalScope('tenant')
        ->where('tenant_id', $tenant->id)
        ->whereNull('status_id')
        ->update(['status_id' => $pendenteStatus->id]);
        
    echo "Updated " . $updatedCount . " orders to 'Pendente'.\n";
    
    // Also fix orders that might have status Indefinido (if that was a placeholder name, usually it's just null or 0)
    // Assuming status_id is nullable foreign key.
}

echo "Done.\n";
