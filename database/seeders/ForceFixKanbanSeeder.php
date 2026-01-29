<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;

class ForceFixKanbanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Kanban Force Fix for ALL Possible Tenants...');

        // 1. Collect all potential Tenant IDs
        $tenantIds = collect();

        // From Tenants table
        $tenantIds = $tenantIds->merge(Tenant::pluck('id'));

        // From Users table
        $tenantIds = $tenantIds->merge(User::whereNotNull('tenant_id')->distinct()->pluck('tenant_id'));

        // From Stores table
        try {
            $storeTenantIds = \App\Models\Store::whereNotNull('tenant_id')->distinct()->pluck('tenant_id');
            $tenantIds = $tenantIds->merge($storeTenantIds);
        } catch (\Exception $e) {
            $this->command->warn("Could not fetch IDs from Stores: " . $e->getMessage());
        }
        
        // Default ID 1 just in case
        $tenantIds->push(1);

        $uniqueTenantIds = $tenantIds->unique()->filter()->values();
        
        if ($uniqueTenantIds->isEmpty()) {
            $this->command->warn('No tenant IDs found anywhere! forcing ID 1.');
            $uniqueTenantIds = collect([1]);
        }

        $this->command->info("Found " . $uniqueTenantIds->count() . " unique Tenant IDs: " . $uniqueTenantIds->implode(', '));

        // Define Required Statuses
        $requiredStatuses = [
            'Pendente' => ['color' => '#f59e0b', 'pos' => 1],
            'Quando não assina' => ['color' => '#ef4444', 'pos' => 2],
            'Assinado' => ['color' => '#22c55e', 'pos' => 3],
            'Inicio' => ['color' => '#3b82f6', 'pos' => 4],
            'Fila Corte' => ['color' => '#6366f1', 'pos' => 5],
            'Cortado' => ['color' => '#8b5cf6', 'pos' => 6],
            'Costura' => ['color' => '#ec4899', 'pos' => 7],
            'Costurar Novamente' => ['color' => '#f43f5e', 'pos' => 8],
            'Personalização' => ['color' => '#10b981', 'pos' => 9],
            'Limpeza' => ['color' => '#14b8a6', 'pos' => 10],
            'Concluído' => ['color' => '#059669', 'pos' => 11],
        ];

        foreach ($uniqueTenantIds as $tenantId) {
            $this->command->info("Processing Tenant ID: {$tenantId}");

            foreach ($requiredStatuses as $name => $props) {
                $status = Status::where('tenant_id', $tenantId)
                    ->where('name', $name)
                    ->first();

                if (!$status) {
                    $this->command->warn("  Status MISSING: {$name}. Creating...");
                    Status::create([
                        'tenant_id' => $tenantId,
                        'name' => $name,
                        'color' => $props['color'],
                        'position' => $props['pos'],
                    ]);
                } else {
                    // Update position if it exists just to be safe
                    // $status->update(['position' => $props['pos']]); 
                }
            }
        }
        
        $this->command->info("Done.");
    }
}
