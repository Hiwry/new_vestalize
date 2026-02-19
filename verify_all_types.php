<?php

$testTenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$testTenant) {
    echo "Tenant TNFE01 não encontrado!\n";
    exit;
}

echo "=== VERIFICANDO TODOS OS DADOS OBRIGATÓRIOS ===\n\n";

$tipos = ['personalizacao', 'tecido', 'tipo_tecido', 'cor', 'corte', 'gola', 'detalhe'];

foreach ($tipos as $tipo) {
    $count = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('tenant_id', $testTenant->id)
        ->where('type', $tipo)
        ->count();
    
    $status = $count > 0 ? "✓" : "✗";
    echo "{$status} {$tipo}: {$count}\n";
    
    if ($count > 0 && $count <= 5) {
        $items = \App\Models\ProductOption::withoutGlobalScopes()
            ->where('tenant_id', $testTenant->id)
            ->where('type', $tipo)
            ->get();
        foreach ($items as $item) {
            echo "    - {$item->name} (R$ {$item->price})\n";
        }
    }
}

// ATENÇÃO: O tipo_corte no controller é 'tipo_corte', não 'corte'
echo "\n=== VERIFICANDO TIPO_CORTE (nome correto) ===\n";
$tipoCorteCount = \App\Models\ProductOption::withoutGlobalScopes()
    ->where('tenant_id', $testTenant->id)
    ->where('type', 'tipo_corte')
    ->count();
echo "tipo_corte: {$tipoCorteCount}\n";

if ($tipoCorteCount == 0) {
    echo "\nCriando tipo_corte...\n";
    $tiposCorte = [
        ['name' => 'Tradicional', 'price' => 0.00],
        ['name' => 'Slim Fit', 'price' => 8.00],
        ['name' => 'Oversized', 'price' => 10.00],
        ['name' => 'Regular', 'price' => 0.00],
        ['name' => 'Baby Look', 'price' => 5.00],
    ];
    foreach ($tiposCorte as $c) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $c['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'tipo_corte',
            'price' => $c['price'],
            'active' => true
        ]);
        echo "  + {$c['name']}\n";
    }
}

// Verificar se há cortes com type errado e corrigir
$cortesErrados = \App\Models\ProductOption::withoutGlobalScopes()
    ->where('tenant_id', $testTenant->id)
    ->where('type', 'corte')
    ->get();

if ($cortesErrados->count() > 0) {
    echo "\nCorrigindo 'corte' para 'tipo_corte'...\n";
    foreach ($cortesErrados as $corte) {
        $corte->update(['type' => 'tipo_corte']);
        echo "  - {$corte->name} corrigido\n";
    }
}

echo "\n=== CONTAGEM FINAL ===\n";
foreach (['personalizacao', 'tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'gola', 'detalhe'] as $tipo) {
    $count = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('tenant_id', $testTenant->id)
        ->where('type', $tipo)
        ->count();
    echo "{$tipo}: {$count}\n";
}
