<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeds do sistema...');

        // Loja Principal (deve ser criada primeiro)
        $this->command->info('🏪 Criando loja principal...');
        $this->call(StoreSeeder::class);

        // Usuários
        $this->command->info('📝 Criando usuários...');
        $this->call(UserSeeder::class);

        // Status padrão para o Kanban
        $this->command->info('📊 Criando status...');
        $this->call(StatusSeeder::class);

        // Parâmetros iniciais de preços
        $this->command->info('⚙️  Configurando parâmetros...');
        $settings = [
            ['key' => 'price.serigrafia.a4', 'value' => '59.40', 'type' => 'decimal'],
            ['key' => 'price.dtf.a4', 'value' => '59.40', 'type' => 'decimal'],
            ['key' => 'delivery.fee.default', 'value' => '0', 'type' => 'decimal'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        // Opções de produtos e preços
        $this->command->info('🏷️  Criando opções de produtos e preços...');
        $this->call([
            ProductOptionSeeder::class,
            PersonalizationPriceSeeder::class,
            SublimationSeeder::class,
            SerigraphySeeder::class,
            SizeSurchargeSeeder::class,
            SublimationAddonSeeder::class,
            TermsConditionSeeder::class,
            LisasSeeder::class,
        ]);

        $this->command->info('✅ Seeds concluídos com sucesso!');
    }
}
