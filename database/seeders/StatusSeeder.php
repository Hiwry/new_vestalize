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
            ['name' => 'Pendente', 'color' => '#64748b', 'position' => 1],
            ['name' => 'Assinado', 'color' => '#0ea5e9', 'position' => 2],
            ['name' => 'Fila de Impressão', 'color' => '#6366f1', 'position' => 3],
            ['name' => 'Corte', 'color' => '#eab308', 'position' => 4],
            ['name' => 'Costura', 'color' => '#db2777', 'position' => 5],
            ['name' => 'Personalização', 'color' => '#8b5cf6', 'position' => 6],
            ['name' => 'Revisão', 'color' => '#f97316', 'position' => 7],
            ['name' => 'Limpeza', 'color' => '#14b8a6', 'position' => 8],
            ['name' => 'Concluído', 'color' => '#10b981', 'position' => 9],
            ['name' => 'Entregue', 'color' => '#059669', 'position' => 10],
            ['name' => 'Cancelado', 'color' => '#ef4444', 'position' => 11],
        ];

        // Seear status para todos os tenants
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($statuses as $status) {
                Status::updateOrCreate(
                    [
                        'name' => $status['name'], 
                        'tenant_id' => $tenant->id
                    ],
                    $status
                );
            }
        }

        $this->command->info('Status criados com sucesso para todos os tenants!');
    }
}

