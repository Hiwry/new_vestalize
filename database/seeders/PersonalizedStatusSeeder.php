<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\Tenant;

class PersonalizedStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        $statuses = [
            ['name' => 'Arte', 'color' => '#3B82F6', 'position' => 1],      // Blue
            ['name' => 'Impresso', 'color' => '#8B5CF6', 'position' => 2],  // Purple
            ['name' => 'Aplicado', 'color' => '#EC4899', 'position' => 3],  // Pink
            ['name' => 'Embalado', 'color' => '#F59E0B', 'position' => 4],  // Amber
            ['name' => 'Concluído', 'color' => '#10B981', 'position' => 5], // Emerald
            ['name' => 'Entregue', 'color' => '#6B7280', 'position' => 6],  // Gray
        ];

        foreach ($tenants as $tenant) {
            foreach ($statuses as $status) {
                Status::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name' => $status['name'],
                        'type' => 'personalized'
                    ],
                    [
                        'color' => $status['color'],
                        'position' => $status['position']
                    ]
                );
            }
        }
    }
}
