<?php

$t = \App\Models\Tenant::where('store_code', 'TNFE01')->first();

echo "=== VERIFICANDO DADOS EXISTENTES ===\n\n";

echo "TECIDOS:\n";
$tecidos = \App\Models\ProductOption::withoutGlobalScopes()
    ->where('tenant_id', $t->id)
    ->where('type', 'tecido')
    ->get();
foreach ($tecidos as $x) {
    echo "  - {$x->name}: R$ " . number_format($x->price, 2, ',', '.') . "\n";
}

echo "\nCORTES:\n";
$cortes = \App\Models\ProductOption::withoutGlobalScopes()
    ->where('tenant_id', $t->id)
    ->where('type', 'corte')
    ->get();
foreach ($cortes as $x) {
    echo "  - {$x->name}: R$ " . number_format($x->price, 2, ',', '.') . "\n";
}

// Atualizar preços se estiverem zerados
echo "\n=== ATUALIZANDO PREÇOS ===\n";

$precosTecido = [
    'Malha PP' => 35.00,
    'Malha Fria' => 38.00,
    'Dry Fit' => 45.00,
    'Dry Fit Premium' => 55.00,
    'Poliéster' => 40.00,
    'Piquet' => 50.00,
    'Algodão' => 42.00,
];

foreach ($precosTecido as $nome => $preco) {
    \App\Models\ProductOption::withoutGlobalScopes()
        ->where('tenant_id', $t->id)
        ->where('type', 'tecido')
        ->where('name', $nome)
        ->update(['price' => $preco]);
    echo "Tecido '$nome' => R$ $preco\n";
}

$precosCorte = [
    'Reto' => 0.00,
    'Slim' => 5.00,
    'Oversized' => 8.00,
    'Babylook' => 0.00,
];

foreach ($precosCorte as $nome => $preco) {
    \App\Models\ProductOption::withoutGlobalScopes()
        ->where('tenant_id', $t->id)
        ->where('type', 'corte')
        ->where('name', $nome)
        ->update(['price' => $preco]);
    echo "Corte '$nome' => R$ $preco\n";
}

echo "\n=== DADOS ATUALIZADOS ===\n";
