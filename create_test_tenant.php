<?php

$plan = \App\Models\Plan::where('slug', 'basic')->first();
if (!$plan) { 
    $plan = \App\Models\Plan::first(); 
}

$tenant = \App\Models\Tenant::create([
    'name' => 'Loja Teste NF-e',
    'store_code' => 'TNFE01',
    'email' => 'testnfe@teste.com',
    'status' => 'active',
    'plan_id' => $plan->id ?? 1,
    'subscription_ends_at' => now()->addYear(),
]);

$user = \App\Models\User::create([
    'name' => 'Teste NF-e',
    'email' => 'testnfe@teste.com',
    'password' => bcrypt('123456'),
    'tenant_id' => $tenant->id,
    'role' => 'admin',
]);

echo "Tenant criado: " . $tenant->store_code . "\n";
echo "Email: " . $user->email . "\n";
echo "Senha: 123456\n";
