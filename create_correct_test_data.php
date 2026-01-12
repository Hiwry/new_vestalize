<?php

// Buscar tenant de teste
$testTenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$testTenant) {
    echo "Tenant TNFE01 não encontrado!\n";
    exit;
}

echo "=== CRIANDO DADOS COM TYPES CORRETOS PARA: {$testTenant->name} ===\n\n";

// TECIDOS (type: tecido)
echo "Criando Tecidos (type: tecido)...\n";
$tecidos = [
    ['name' => 'Malha PP', 'price' => 35.00],
    ['name' => 'Malha Fria', 'price' => 38.00],
    ['name' => 'Dry Fit', 'price' => 45.00],
    ['name' => 'Dry Fit Premium', 'price' => 55.00],
    ['name' => 'Poliéster', 'price' => 40.00],
    ['name' => 'Piquet', 'price' => 50.00],
    ['name' => 'Algodão', 'price' => 42.00],
];
foreach ($tecidos as $t) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $t['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'tecido')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $t['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'tecido',
            'price' => $t['price'],
            'active' => true
        ]);
        echo "  + {$t['name']}\n";
    } else {
        echo "  - {$t['name']} (ok)\n";
    }
}

// CORES (type: cor)
echo "\nCriando Cores (type: cor)...\n";
$cores = [
    ['name' => 'Branco', 'price' => 0.00],
    ['name' => 'Preto', 'price' => 0.00],
    ['name' => 'Azul Marinho', 'price' => 0.00],
    ['name' => 'Azul Royal', 'price' => 0.00],
    ['name' => 'Vermelho', 'price' => 0.00],
    ['name' => 'Verde', 'price' => 0.00],
    ['name' => 'Amarelo', 'price' => 0.00],
    ['name' => 'Cinza', 'price' => 0.00],
    ['name' => 'Rosa', 'price' => 0.00],
    ['name' => 'Laranja', 'price' => 0.00],
];
foreach ($cores as $c) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $c['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'cor')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $c['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'cor',
            'price' => $c['price'],
            'active' => true
        ]);
        echo "  + {$c['name']}\n";
    } else {
        echo "  - {$c['name']} (ok)\n";
    }
}

// CORTES (type: corte)
echo "\nCriando Cortes (type: corte)...\n";
$cortes = [
    ['name' => 'Reto', 'price' => 0.00],
    ['name' => 'Slim', 'price' => 5.00],
    ['name' => 'Oversized', 'price' => 8.00],
    ['name' => 'Babylook', 'price' => 0.00],
];
foreach ($cortes as $c) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $c['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'corte')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $c['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'corte',
            'price' => $c['price'],
            'active' => true
        ]);
        echo "  + {$c['name']}\n";
    } else {
        echo "  - {$c['name']} (ok)\n";
    }
}

// GOLAS (type: gola)
echo "\nCriando Golas (type: gola)...\n";
$golas = [
    ['name' => 'Careca', 'price' => 0.00],
    ['name' => 'V', 'price' => 0.00],
    ['name' => 'Polo', 'price' => 8.00],
    ['name' => 'Canoa', 'price' => 0.00],
];
foreach ($golas as $g) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $g['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'gola')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $g['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'gola',
            'price' => $g['price'],
            'active' => true
        ]);
        echo "  + {$g['name']}\n";
    } else {
        echo "  - {$g['name']} (ok)\n";
    }
}

// DETALHES (type: detalhe)
echo "\nCriando Detalhes (type: detalhe)...\n";
$detalhes = [
    ['name' => 'Manga Curta', 'price' => 0.00],
    ['name' => 'Manga Longa', 'price' => 12.00],
    ['name' => 'Regata', 'price' => 0.00],
    ['name' => 'Sem Manga', 'price' => 0.00],
];
foreach ($detalhes as $d) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $d['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'detalhe')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $d['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'detalhe',
            'price' => $d['price'],
            'active' => true
        ]);
        echo "  + {$d['name']}\n";
    } else {
        echo "  - {$d['name']} (ok)\n";
    }
}

// PERSONALIZAÇÕES (type: personalizacao)
echo "\nCriando Personalizações (type: personalizacao)...\n";
$personalizacoes = [
    ['name' => 'Sublimação Frente', 'price' => 20.00],
    ['name' => 'Sublimação Costas', 'price' => 20.00],
    ['name' => 'Bordado', 'price' => 15.00],
    ['name' => 'Silk', 'price' => 10.00],
    ['name' => 'Transfer', 'price' => 12.00],
    ['name' => 'DTF', 'price' => 18.00],
];
foreach ($personalizacoes as $p) {
    $exists = \App\Models\ProductOption::withoutGlobalScopes()
        ->where('name', $p['name'])
        ->where('tenant_id', $testTenant->id)
        ->where('type', 'personalizacao')
        ->first();
    
    if (!$exists) {
        \App\Models\ProductOption::withoutGlobalScopes()->create([
            'name' => $p['name'],
            'tenant_id' => $testTenant->id,
            'type' => 'personalizacao',
            'price' => $p['price'],
            'active' => true
        ]);
        echo "  + {$p['name']}\n";
    } else {
        echo "  - {$p['name']} (ok)\n";
    }
}

echo "\n=== RESUMO ===\n";
$tenant_id = $testTenant->id;
echo "Tecidos: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'tecido')->count() . "\n";
echo "Cores: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'cor')->count() . "\n";
echo "Cortes: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'corte')->count() . "\n";
echo "Golas: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'gola')->count() . "\n";
echo "Detalhes: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'detalhe')->count() . "\n";
echo "Personalizações: " . \App\Models\ProductOption::withoutGlobalScopes()->where('tenant_id', $tenant_id)->where('type', 'personalizacao')->count() . "\n";
echo "\nPRONTO! Atualize a página do pedido.\n";
