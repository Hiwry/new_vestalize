<?php
try {
    $user = \App\Models\User::where('name', 'like', '%Thiago%')->first();
    echo 'User: ' . ($user ? $user->name : 'None') . PHP_EOL;
    echo 'User Tenant ID: ' . ($user ? $user->tenant_id : 'None') . PHP_EOL;
    
    $tenantCount = \App\Models\Tenant::count();
    echo 'Total Tenants: ' . $tenantCount . PHP_EOL;
    
    $tenants = \App\Models\Tenant::all();
    if ($tenants->isEmpty()) {
        echo "WARNING: No tenants found in database!\n";
    } else {
        foreach($tenants as $t) {
            echo "Tenant found: ID {$t->id}, Name {$t->name}\n";
        }
    }

    $targetTenantId = $user ? $user->tenant_id : 1;
    echo "Checking statuses for Tenant ID: $targetTenantId\n";
    
    $statuses = \App\Models\Status::where('tenant_id', $targetTenantId)->orderBy('position')->get();
    
    if ($statuses->isEmpty()) {
        echo "No statuses found for this tenant.\n";
    } else {
        foreach($statuses as $s) {
            echo "Status: {$s->name} (ID: {$s->id}, Pos: {$s->position})\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
