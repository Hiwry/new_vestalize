<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\Tenant;

class AddProductionStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $newStatuses = [
            ['name' => 'Quando não assina', 'color' => '#EF4444', 'position' => 10, 'type' => 'production'],
            ['name' => 'Assinado', 'color' => '#22C55E', 'position' => 12, 'type' => 'production'],
            ['name' => 'Inicio', 'color' => '#F59E0B', 'position' => 15, 'type' => 'production'],
            ['name' => 'Fila Corte', 'color' => '#6366F1', 'position' => 20, 'type' => 'production'],
            ['name' => 'Cortado', 'color' => '#3B82F6', 'position' => 25, 'type' => 'production'],
            ['name' => 'Costura', 'color' => '#8B5CF6', 'position' => 30, 'type' => 'production'],
            ['name' => 'Costurar Novamente', 'color' => '#EC4899', 'position' => 35, 'type' => 'production'],
            ['name' => 'Personalização', 'color' => '#10B981', 'position' => 40, 'type' => 'production'],
            ['name' => 'Limpeza', 'color' => '#14B8A6', 'position' => 45, 'type' => 'production'],
            ['name' => 'Concluído', 'color' => '#059669', 'position' => 50, 'type' => 'production'],
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($newStatuses as $status) {
                Status::firstOrCreate(
                    [
                        'name' => $status['name'],
                        'tenant_id' => $tenant->id,
                        'type' => $status['type'],
                    ],
                    [
                        'color' => $status['color'],
                        'position' => $status['position'],
                        'tenant_id' => $tenant->id,
                        'type' => $status['type'],
                    ]
                );
            }
        }

        $this->command->info('Novos status de produção adicionados com sucesso!');
    }
}
