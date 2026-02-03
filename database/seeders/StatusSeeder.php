<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pendente', 'color' => '#64748b', 'position' => 1, 'type' => 'production'],
            ['name' => 'Assinado', 'color' => '#0ea5e9', 'position' => 2, 'type' => 'production'],
            ['name' => 'Fila de Impressão', 'color' => '#6366f1', 'position' => 3, 'type' => 'production'],
            ['name' => 'Corte', 'color' => '#eab308', 'position' => 4, 'type' => 'production'],
            ['name' => 'Costura', 'color' => '#db2777', 'position' => 5, 'type' => 'production'],
            ['name' => 'Personalização', 'color' => '#8b5cf6', 'position' => 6, 'type' => 'production'],
            ['name' => 'Revisão', 'color' => '#f97316', 'position' => 7, 'type' => 'production'],
            ['name' => 'Limpeza', 'color' => '#14b8a6', 'position' => 8, 'type' => 'production'],
            ['name' => 'Concluído', 'color' => '#10b981', 'position' => 9, 'type' => 'production'],
            ['name' => 'Entregue', 'color' => '#059669', 'position' => 10, 'type' => 'production'],
            ['name' => 'Cancelado', 'color' => '#ef4444', 'position' => 11, 'type' => 'production'],
        ];

        // Seear status para todos os tenants
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($statuses as $status) {
                Status::updateOrCreate(
                    [
                        'name' => $status['name'],
                        'tenant_id' => $tenant->id,
                        'type' => $status['type'],
                    ],
                    array_merge($status, ['tenant_id' => $tenant->id])
                );
            }
        }

        $this->command->info('Status criados com sucesso para todos os tenants!');
    }
}
