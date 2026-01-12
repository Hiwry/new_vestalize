<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Client;
use Illuminate\Support\Facades\DB;

echo "<h1>Remoção de Clientes Duplicados</h1>";

// Buscar duplicatas por telefone
$duplicates = Client::select('phone_primary', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as count'))
    ->whereNotNull('phone_primary')
    ->where('phone_primary', '!=', '')
    ->groupBy('phone_primary')
    ->having('count', '>', 1)
    ->get();

echo "<h2>Duplicatas encontradas: " . count($duplicates) . "</h2>";

if (count($duplicates) == 0) {
    echo "<p style='color: green;'>Nenhum cliente duplicado encontrado!</p>";
    exit;
}

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Telefone</th><th>ID Mantido</th><th>Duplicatas Removidas</th></tr>";

$totalRemoved = 0;

foreach ($duplicates as $dup) {
    // Contar quantos serão removidos
    $toRemove = Client::where('phone_primary', $dup->phone_primary)
        ->where('id', '!=', $dup->keep_id)
        ->count();
    
    // Remover duplicatas (mantendo o mais antigo - menor ID)
    $deleted = Client::where('phone_primary', $dup->phone_primary)
        ->where('id', '!=', $dup->keep_id)
        ->delete();
    
    $totalRemoved += $deleted;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($dup->phone_primary) . "</td>";
    echo "<td>" . $dup->keep_id . "</td>";
    echo "<td>" . $deleted . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2 style='color: green;'>Total de clientes duplicados removidos: " . $totalRemoved . "</h2>";
echo "<p><a href='/clientes'>Voltar para lista de clientes</a></p>";
