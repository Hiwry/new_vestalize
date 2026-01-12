<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se usuário já existe
        if (User::where('email', 'admin@vestalize.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@vestalize.com',
            'password' => Hash::make('admin123'), // Senha padrão
            'role' => 'admin',
            'tenant_id' => null, // Super Admin não tem tenant
            'store_name' => 'Sistema Vestalize',
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@vestalize.com');
        $this->command->info('Password: admin123');
    }
}
