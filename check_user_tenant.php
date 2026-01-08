<?php
$u = \App\Models\User::where('email', 'testnfe@teste.com')->first();
if (!$u) {
    echo "User testnfe@teste.com not found.\n";
    exit;
}
echo "User: {$u->name}\n";
echo "Tenant ID: {$u->tenant_id}\n";
if ($u->tenant_id) {
    $t = \App\Models\Tenant::find($u->tenant_id);
    echo "Tenant: {$t->store_code} - {$t->name}\n";
    
    $optionsCount = \App\Models\ProductOption::where('tenant_id', $u->tenant_id)->count();
    echo "Options Count (query with tenant_id): $optionsCount\n";
    
    $scopedCount = \App\Models\ProductOption::count(); // Should use global scope if not in tinker? 
    // Actually tinker doesn't have an Auth user unless we act as one.
    echo "Options Count (ProductOption::count()): $scopedCount\n";
}
