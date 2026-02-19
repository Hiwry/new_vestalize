<?php

// Buscar tenant de teste
$testTenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$testTenant) {
    echo "Tenant TNFE01 não encontrado!\n";
    exit;
}

echo "=== CRIANDO DADOS PARA: {$testTenant->name} ===\n\n";

// Usar withoutGlobalScopes para bypassar o filtro de tenant
$ProductOption = \App\Models\ProductOption::class;

// TECIDOS
echo "Criando Tecidos...\n";
$tecidos = [
    ['name' => 'Malha PP', 'price' => 35.00],
    ['name' => 'Malha Fria', 'price' => 38.00],
    ['name' => 'Dry Fit', 'price' => 45.00],
    ['name' => 'Dry Fit Premium', 'price' => 55.00],
    ['name' => 'Poliéster', 'price' => 40.00],
    ['name' => 'Piquet', 'price' => 50.00],
    ['name' => 'Algodão', 'price' => 42.00],
    ['name' => 'Oxford', 'price' => 48.00],
];
foreach ($tecidos as $t) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $t['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'fabric')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $t['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'fabric',
            'price' => $t['price'],
            'active' => true
        ]);
        echo "  + {$t['name']}\n";
    } else {
        echo "  - {$t['name']} (já existe)\n";
    }
}

// MODELOS
echo "\nCriando Modelos...\n";
$modelos = [
    ['name' => 'Camiseta Básica', 'price' => 25.00],
    ['name' => 'Camiseta Gola V', 'price' => 28.00],
    ['name' => 'Regata', 'price' => 20.00],
    ['name' => 'Polo', 'price' => 35.00],
    ['name' => 'Baby Look', 'price' => 22.00],
    ['name' => 'Camisa Social', 'price' => 45.00],
    ['name' => 'Moletom', 'price' => 65.00],
    ['name' => 'Jaqueta', 'price' => 80.00],
];
foreach ($modelos as $m) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $m['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'model')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $m['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'model',
            'price' => $m['price'],
            'active' => true
        ]);
        echo "  + {$m['name']}\n";
    } else {
        echo "  - {$m['name']} (já existe)\n";
    }
}

// PERSONALIZAÇÕES
echo "\nCriando Personalizações...\n";
$personalizacoes = [
    ['name' => 'Sublimação Frente Inteira', 'price' => 20.00],
    ['name' => 'Sublimação Costas Inteira', 'price' => 20.00],
    ['name' => 'Sublimação A4 Frente', 'price' => 12.00],
    ['name' => 'Sublimação A4 Costas', 'price' => 12.00],
    ['name' => 'Bordado Peito (até 10cm)', 'price' => 15.00],
    ['name' => 'Bordado Costas (até 20cm)', 'price' => 25.00],
    ['name' => 'Bordado Nome', 'price' => 8.00],
    ['name' => 'Silk 1 Cor', 'price' => 8.00],
    ['name' => 'Silk 2 Cores', 'price' => 12.00],
    ['name' => 'Silk 3 Cores', 'price' => 15.00],
    ['name' => 'Transfer Digital', 'price' => 10.00],
    ['name' => 'Estampa DTF', 'price' => 18.00],
];
foreach ($personalizacoes as $p) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $p['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'customization')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $p['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'customization',
            'price' => $p['price'],
            'active' => true
        ]);
        echo "  + {$p['name']}\n";
    } else {
        echo "  - {$p['name']} (já existe)\n";
    }
}

echo "\n=== RESUMO ===\n";
$totalFabrics = \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->where('type', 'fabric')->count();
$totalModels = \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->where('type', 'model')->count();
$totalCustom = \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->where('type', 'customization')->count();
$totalClients = \App\Models\Client::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->count();

echo "Tecidos: {$totalFabrics}\n";
echo "Modelos: {$totalModels}\n";
echo "Personalizações: {$totalCustom}\n";
echo "Clientes: {$totalClients}\n";
echo "\nPRONTO!\n";
