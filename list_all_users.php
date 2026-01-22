<?php
$users = \App\Models\User::all();
echo "Total Users: " . $users->count() . "\n";
foreach($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | TenantID: {$u->tenant_id}\n";
}

$distinctTenants = \App\Models\User::distinct()->pluck('tenant_id');
echo "Distinct Tenant IDs in Users table: " . implode(', ', $distinctTenants->toArray()) . "\n";
