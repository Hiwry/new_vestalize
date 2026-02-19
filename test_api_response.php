<?php
$user = \App\Models\User::whereNull('tenant_id')->first();
if (!$user) {
    echo "Super Admin not found\n";
    exit;
}

// Simular login
auth()->login($user);

echo "User: {$user->name} | Tenant: " . ($user->tenant_id ?? 'NULL') . "\n";

// Testar o que o controller retornaria
$options = \App\Models\ProductOption::where('active', true)
    ->orderBy('order')
    ->get()
    ->groupBy('type');

echo "\nTypes found for this user:\n";
foreach ($options as $type => $group) {
    echo "- $type: " . count($group) . " items\n";
    if ($type == 'personalizacao') {
        foreach ($group as $o) {
            echo "  * {$o->name} (Tenant: " . ($o->tenant_id ?? 'GLOBAL') . ")\n";
        }
    }
}
