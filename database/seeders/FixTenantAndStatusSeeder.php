<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class FixTenantAndStatusSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garantir que existe um Tenant
        $tenant = Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create(['name' => 'Vestalize Matriz']);
            $this->command->info('Tenant "Vestalize Matriz" criado.');
        } else {
            $this->command->info('Tenant existente encontrado: ' . $tenant->name);
        }

        // 2. Associar Admin ao Tenant
        $admin = User::where('email', 'admin@vestalize.com')->first();
        if ($admin) {
            $admin->tenant_id = $tenant->id;
            $admin->save();
            $this->command->info('Admin associado ao tenant.');
        }

        // 3. Rodar StatusSeeder
        $this->call(StatusSeeder::class);
    }
}
