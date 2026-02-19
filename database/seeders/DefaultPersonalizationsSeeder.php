<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductOption;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;

class DefaultPersonalizationsSeeder extends Seeder
{
    public function run()
    {
        $options = [
            'Lisas',
            'Emborrachado',
            'Serigrafia',
            'Sub. Total',
            'Sub.local',
            'Bordado',
            'DTF'
        ];

        // Se houver tenants, insere para cada um
        if (class_exists(Tenant::class)) {
            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                $this->createOptionsForTenant($options, $tenant->id);
            }
        } 
        
        // Se não houver Tenant model ou for single tenant, tenta inserir null (global) ou para o user atual se estivesse rodando em contexto
        // Assumindo multi-tenant pelo contexto
    }

    private function createOptionsForTenant($options, $tenantId)
    {
        foreach ($options as $optionName) {
            ProductOption::firstOrCreate(
                [
                    'name' => $optionName,
                    'type' => 'personalizacao',
                    'tenant_id' => $tenantId
                ],
                [
                    'price' => 0,
                    'active' => true,
                    'order' => 0
                ]
            );
        }
    }
}
