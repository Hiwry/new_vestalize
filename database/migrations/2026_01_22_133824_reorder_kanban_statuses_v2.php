<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Status;
use App\Models\Tenant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newOrder = [
            'Quando não assina',
            'Assinado',
            'Fila de Impressão',
            'Pendente',
            'Inicio',
            'Fila Corte',
            'Cortado',
            'Costura',
            'Costurar Novamente',
            'Personalização',
            'Limpeza',
            'Concluido',
            'Concluído', // Incluindo variação com acento para garantir
            'Pronto',
            'Entregue',
            'Cancelado'
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($newOrder as $index => $name) {
                Status::where('tenant_id', $tenant->id)
                    ->where('name', $name)
                    ->update(['position' => $index + 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert positions as it depends on previous state
    }
};
