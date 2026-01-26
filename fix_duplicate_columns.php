<?php

use App\Models\Status;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Iniciando limpeza de colunas duplicadas...\n";

// Buscar todos os status
$statuses = Status::orderBy('id')->get();
$uniqueNames = [];

DB::beginTransaction();
try {
    foreach ($statuses as $status) {
        $name = trim(strtoupper($status->name));
        
        if (isset($uniqueNames[$name])) {
            // Duplicata encontrada!
            $originalId = $uniqueNames[$name];
            echo "Duplicata encontrada: '{$status->name}' (ID: {$status->id}). Movendo para ID: {$originalId}...\n";
            
            // Mover pedidos
            $movedOrders = DB::table('orders')->where('status_id', $status->id)->update(['status_id' => $originalId]);
            echo "  - {$movedOrders} pedidos movidos.\n";
            
            // Deletar status duplicado
            $status->delete();
            echo "  - Coluna deletada.\n";
        } else {
            // Primeiro encontro deste nome, manter
            $uniqueNames[$name] = $status->id;
        }
    }
    
    DB::commit();
    echo "Limpeza concluída com sucesso!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Erro: " . $e->getMessage() . "\n";
}
