<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class RestoreUserSeeder extends Seeder
{
    /**
     * Recriar o usuário perdido após migrate:fresh
     */
    public function run(): void
    {
        // Criar o Tenant (empresa cliente)
        $tenant = Tenant::updateOrCreate(
            ['store_code' => 'ORCQWR'],
            [
                'name' => 'Nóbrega Confecções',
                'store_code' => 'ORCQWR',
                'plan' => 'premium',
                'status' => 'active',
                'subscription_ends_at' => now()->addYear(),
            ]
        );

        // Criar a Store principal do tenant
        $store = Store::updateOrCreate(
            ['tenant_id' => $tenant->id, 'is_main' => true],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Nóbrega Confecções',
                'is_main' => true,
                'active' => true,
            ]
        );

        // Criar o usuário com as credenciais do email
        $user = User::updateOrCreate(
            ['email' => 'hiwry-keveny2013@hotmail.com'],
            [
                'name' => 'Nóbrega Confecções',
                'email' => 'hiwry-keveny2013@hotmail.com',
                'password' => Hash::make('qyVv'),
                'role' => 'admin_geral',
                'tenant_id' => $tenant->id,
            ]
        );

        // Associar usuário à loja
        if (!$user->stores()->where('stores.id', $store->id)->exists()) {
            $user->stores()->attach($store->id, ['role' => 'admin_geral']);
        }

        $this->command->info(' Usuário restaurado com sucesso!');
        $this->command->info(' Email: hiwry-keveny2013@hotmail.com');
        $this->command->info(' Senha: qyVv');
        $this->command->info(' Código da Loja: ORCQWR');
    }
}
