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
            ['name' => 'Pendente', 'color' => '#F59E0B', 'position' => 1],
            ['name' => 'Fila de Impressão', 'color' => '#6366F1', 'position' => 2],
            ['name' => 'Em Produção', 'color' => '#3B82F6', 'position' => 3],
            ['name' => 'Aguardando Aprovação', 'color' => '#8B5CF6', 'position' => 4],
            ['name' => 'Pronto', 'color' => '#10B981', 'position' => 5],
            ['name' => 'Entregue', 'color' => '#059669', 'position' => 6],
            ['name' => 'Cancelado', 'color' => '#EF4444', 'position' => 7],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }

        $this->command->info('Status criados com sucesso!');
    }
}

