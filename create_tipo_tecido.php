<?php

// Buscar tenant de teste
$testTenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$testTenant) {
    echo "Tenant TNFE01 não encontrado!\n";
    exit;
}

echo "=== CRIANDO TIPOS DE TECIDO E TIPOS DE CORTE ===\n\n";

// Primeiro, buscar os IDs dos tecidos existentes
$tecidos = \App\Models\ProductOption::withoutGlobalScopes()
    ->where('tenant_id', $testTenant->id)
    ->where('type', 'tecido')
    ->get();

echo "Tecidos encontrados:\n";
foreach ($tecidos as $tecido) {
    echo "  - [{$tecido->id}] {$tecido->name}\n";
}

// Criar TIPOS DE TECIDO (filhos dos tecidos) - type: tipo_tecido
echo "\nCriando Tipos de Tecido...\n";

$tiposTecidoMap = [
    'Dry Fit' => [
        ['name' => 'Dry Fit Liso', 'price' => 0.00],
        ['name' => 'Dry Fit Texturizado', 'price' => 5.00],
        ['name' => 'Dry Fit Furadinho', 'price' => 8.00],
    ],
    'Malha PP' => [
        ['name' => 'PP 30.1', 'price' => 0.00],
        ['name' => 'PP 26.1', 'price' => 3.00],
        ['name' => 'PP Penteado', 'price' => 5.00],
    ],
    'Poliéster' => [
        ['name' => 'Poliéster Liso', 'price' => 0.00],
        ['name' => 'Poliéster Acetinado', 'price' => 4.00],
    ],
];

foreach ($tecidos as $tecido) {
    if (isset($tiposTecidoMap[$tecido->name])) {
        foreach ($tiposTecidoMap[$tecido->name] as $tipo) {
            $exists = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('name', $tipo['name'])
                ->where('tenant_id', $testTenant->id)
                ->where('type', 'tipo_tecido')
                ->first();
            
            if (!$exists) {
                \App\Models\ProductOption::withoutGlobalScopes()->create([
                    'name' => $tipo['name'],
                    'tenant_id' => $testTenant->id,
                    'type' => 'tipo_tecido',
                    'price' => $tipo['price'],
                    'parent_id' => $tecido->id, // Associa ao tecido pai
                    'active' => true
                ]);
                echo "  + {$tipo['name']} (filho de {$tecido->name})\n";
            } else {
                // Atualizar parent_id se não estiver correto
                if ($exists->parent_id != $tecido->id) {
                    $exists->update(['parent_id' => $tecido->id]);
                }
                echo "  - {$tipo['name']} (ok)\n";
            }
        }
    }
}

// Criar mais TIPOS DE CORTE com preço
echo "\nAtualizando Tipos de Corte com preços...\n";
$tiposCorte = [
    ['name' => 'Tradicional', 'price' => 0.00],
    ['name' => 'Slim Fit', 'price' => 8.00],
    ['name' => 'Oversized', 'price' => 10.00],
    ['name' => 'Regular', 'price' => 0.00],
    ['name' => 'Baby Look Feminino', 'price' => 5.00],
];

foreach ($tiposCorte as $corte) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $corte['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'corte')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $corte['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'corte',
            'price' => $corte['price'],
            'active' => true
        ]);
        echo "  + {$corte['name']}: R$ {$corte['price']}\n";
    } else {
        $exists->update(['price' => $corte['price']]);
        echo "  - {$corte['name']}: R$ {$corte['price']} (atualizado)\n";
    }
}

echo "\n=== RESUMO ===\n";
$tiposTecido = \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->where('type', 'tipo_tecido')->count();
$tiposCorte = \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $testTenant->id)->where('type', 'corte')->count();
echo "Tipos de Tecido: $tiposTecido\n";
echo "Tipos de Corte: $tiposCorte\n";
echo "\nPRONTO! Atualize a página e selecione um tecido para ver os tipos.\n";
