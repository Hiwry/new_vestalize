<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
             'basic' => [
                'name' => 'Básico',
                'slug' => 'basic',
                'price' => 200.00,
                'description' => 'Plano inicial para pequenos negócios.',
                'limits' => [
                    'stores' => 1,
                    'users' => 2,
                ],
                'features' => ['orders', 'crm', 'reports_simple'],
            ],
            'pro' => [
                'name' => 'Médio (Pro)',
                'slug' => 'pro',
                'price' => 300.00,
                'description' => 'Plano ideal para negócios em crescimento.',
                'limits' => [
                    'stores' => 1,
                    'users' => 5,
                ],
                'features' => ['orders', 'crm', 'reports_complete', 'pdf_quotes', 'kanban', 'pdv'],
            ],
            'premium' => [
                'name' => 'Avançado (Premium)',
                'slug' => 'premium',
                'price' => 500.00,
                'description' => 'Acesso total e ilimitado.',
                'limits' => [
                    'stores' => 9999,
                    'users' => 9999,
                ],
                'features' => ['*'],
            ],
        ];

        foreach ($plans as $key => $data) {
            $plan = \App\Models\Plan::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            
            // Update existing tenants
            \App\Models\Tenant::where('plan', $key)->update(['plan_id' => $plan->id]);
        }
    }
}
