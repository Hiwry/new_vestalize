<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter a loja principal
        $mainStore = Store::where('is_main', true)->first();
        
        // Criar usuário administrador padrão
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin_geral',
            ]
        );

        // Criar usuário vendedor de exemplo
        $vendedor = User::updateOrCreate(
            ['email' => 'vendedor@example.com'],
            [
                'name' => 'Vendedor',
                'email' => 'vendedor@example.com',
                'password' => Hash::make('password'),
                'role' => 'vendedor',
            ]
        );
        
        // Atribuir loja principal ao vendedor (se existir)
        if ($mainStore && $vendedor) {
            // Verificar se já não está atribuído
            if (!$vendedor->stores()->where('stores.id', $mainStore->id)->exists()) {
                $vendedor->stores()->attach($mainStore->id, ['role' => 'vendedor']);
            }
        }

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Admin Geral: admin@example.com / password');
        $this->command->info('Vendedor: vendedor@example.com / password');
    }
}

