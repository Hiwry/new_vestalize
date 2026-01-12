<?php

echo "=== LISTANDO TENANTS ===\n";
$tenants = \App\Models\Tenant::all();
foreach ($tenants as $t) {
    echo "{$t->id} - {$t->store_code} - {$t->name}\n";
}

echo "\n=== BUSCANDO TENANT MASTER ===\n";
// Tenant master geralmente é o primeiro ou tem mais dados
$masterTenant = \App\Models\Tenant::where('id', 1)->orWhere('store_code', 'LIKE', '%MASTER%')->first();
if (!$masterTenant) {
    $masterTenant = \App\Models\Tenant::whereHas('productOptions')->first();
}

if (!$masterTenant) {
    echo "Nenhum tenant master encontrado com dados.\n";
    exit;
}
echo "Master: {$masterTenant->id} - {$masterTenant->store_code}\n";

// Contar dados do master
$fabrics = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'fabric')->count();
$models = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'model')->count();
$customizations = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'customization')->count();

echo "Tecidos: {$fabrics}\n";
echo "Modelos: {$models}\n";
echo "Personalizações: {$customizations}\n";

// Buscar tenant de teste
$testTenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$testTenant) {
    echo "\nTenant de teste TNFE01 não encontrado!\n";
    exit;
}
echo "\n=== COPIANDO PARA TENANT TESTE ({$testTenant->store_code}) ===\n";

// Copiar tecidos
$masterFabrics = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'fabric')->get();
foreach ($masterFabrics as $f) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $f->name, 'tenant_id' => $testTenant->id, 'type' => 'fabric'],
        ['price' => $f->price, 'active' => true]
    );
}
echo "Tecidos copiados: " . count($masterFabrics) . "\n";

// Copiar modelos
$masterModels = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'model')->get();
foreach ($masterModels as $m) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $m->name, 'tenant_id' => $testTenant->id, 'type' => 'model'],
        ['price' => $m->price, 'active' => true]
    );
}
echo "Modelos copiados: " . count($masterModels) . "\n";

// Copiar personalizações
$masterCustom = \App\Models\ProductOption::where('tenant_id', $masterTenant->id)->where('type', 'customization')->get();
foreach ($masterCustom as $c) {
    \App\Models\ProductOption::updateOrCreate(
        ['name' => $c->name, 'tenant_id' => $testTenant->id, 'type' => 'customization'],
        ['price' => $c->price, 'active' => true]
    );
}
echo "Personalizações copiadas: " . count($masterCustom) . "\n";

echo "\n=== CONCLUIDO ===\n";
