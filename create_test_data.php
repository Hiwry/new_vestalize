<?php

// Buscar o tenant de teste
$tenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$tenant) { 
    echo "Tenant não encontrado\n"; 
    exit; 
}

echo "Tenant encontrado: {$tenant->name}\n\n";

// Criar tecidos
echo "Criando tecidos...\n";
$tecidos = [
    ['name' => 'Malha PP', 'type' => 'fabric', 'price' => 35.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Dry Fit', 'type' => 'fabric', 'price' => 45.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Poliéster', 'type' => 'fabric', 'price' => 40.00, 'active' => true, 'tenant_id' => $tenant->id],
];
foreach ($tecidos as $t) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $t['name'], 'tenant_id' => $tenant->id],
        $t
    );
}
echo "Tecidos criados!\n";

// Criar modelos
echo "Criando modelos...\n";
$modelos = [
    ['name' => 'Camiseta Básica', 'type' => 'model', 'price' => 25.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Regata', 'type' => 'model', 'price' => 20.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Polo', 'type' => 'model', 'price' => 35.00, 'active' => true, 'tenant_id' => $tenant->id],
];
foreach ($modelos as $m) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $m['name'], 'tenant_id' => $tenant->id],
        $m
    );
}
echo "Modelos criados!\n";

// Criar opções de personalização
echo "Criando personalizações...\n";
$personalizacoes = [
    ['name' => 'Sublimação Frente', 'type' => 'customization', 'price' => 15.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Sublimação Costas', 'type' => 'customization', 'price' => 15.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Bordado Peito', 'type' => 'customization', 'price' => 10.00, 'active' => true, 'tenant_id' => $tenant->id],
    ['name' => 'Silk 1 Cor', 'type' => 'customization', 'price' => 8.00, 'active' => true, 'tenant_id' => $tenant->id],
];
foreach ($personalizacoes as $p) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $p['name'], 'tenant_id' => $tenant->id],
        $p
    );
}
echo "Personalizações criadas!\n";

// Criar cliente de teste
echo "\nCriando cliente de teste...\n";
$client = \App\Models\Client::updateOrCreate(
    ['phone_primary' => '11999999999', 'tenant_id' => $tenant->id],
    [
        'name' => 'Cliente Teste NF-e',
        'email' => 'cliente@teste.com',
        'cpf_cnpj' => '12345678909',
        'address' => 'Rua do Cliente, 456',
        'city' => 'Sao Paulo',
        'state' => 'SP',
        'zip_code' => '01310100',
        'tenant_id' => $tenant->id,
    ]
);
echo "Cliente criado: {$client->name}\n";

echo "\n=== DADOS DE TESTE CRIADOS COM SUCESSO ===\n";
echo "Tecidos: 3\n";
echo "Modelos: 3\n";
echo "Personalizações: 4\n";
echo "Cliente: {$client->name}\n";
echo "\nAgora você pode criar um pedido pelo sistema!\n";
