<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existe loja principal
        $mainStore = Store::where('is_main', true)->first();
        
        if (!$mainStore) {
            Store::create([
                'name' => 'Loja Principal',
                'is_main' => true,
                'active' => true,
            ]);
            
            $this->command->info('✅ Loja principal criada com sucesso!');
        } else {
            $this->command->info('ℹ️  Loja principal já existe.');
        }
    }
}

