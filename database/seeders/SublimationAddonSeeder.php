<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SublimationAddon;

class SublimationAddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            // Adicionais da tabela SUBLIMAÇÃO TOTAL
            ['name' => 'REGATA', 'description' => 'Camiseta regata', 'price_adjustment' => -3.00, 'order' => 1],
            ['name' => 'M. LONGA', 'description' => 'Manga longa', 'price_adjustment' => 20.00, 'order' => 2],
            ['name' => 'POLO', 'description' => 'Camisa polo', 'price_adjustment' => 20.00, 'order' => 3],
            ['name' => 'RIB. SUBLI', 'description' => 'Rib sublimado', 'price_adjustment' => 3.50, 'order' => 4],
            ['name' => 'PUNHO SU', 'description' => 'Punho sublimado', 'price_adjustment' => 3.50, 'order' => 5],
            ['name' => 'GOLA V', 'description' => 'Gola V', 'price_adjustment' => 3.15, 'order' => 6],
            ['name' => 'GOLA PAD', 'description' => 'Gola padrão', 'price_adjustment' => 5.25, 'order' => 7],
            
            // Adicionais da seção ADICIONAIS
            ['name' => 'AERODRY', 'description' => 'Tecido aerodry', 'price_adjustment' => 3.50, 'order' => 8],
            ['name' => 'UV CACH', 'description' => 'Proteção UV', 'price_adjustment' => 12.00, 'order' => 9],
            ['name' => 'CREPE POLIE', 'description' => 'Crepe poliéster', 'price_adjustment' => 10.50, 'order' => 10],
            ['name' => 'COR DIFERE', 'description' => 'Cor diferenciada', 'price_adjustment' => 11.67, 'order' => 11],
            ['name' => 'DRYFIT', 'description' => 'Tecido dryfit', 'price_adjustment' => 12.00, 'order' => 12],
            ['name' => 'FRISO OU AD', 'description' => 'Friso ou adesivo', 'price_adjustment' => 4.00, 'order' => 13],
            ['name' => 'M. RAGLAN', 'description' => 'Manga raglan', 'price_adjustment' => 7.00, 'order' => 14],
            ['name' => 'GOLEIRO', 'description' => 'Camiseta goleiro', 'price_adjustment' => 20.00, 'order' => 15],
            ['name' => 'PP ELASTANO-ALURE', 'description' => 'PP elastano allure', 'price_adjustment' => 10.00, 'order' => 16],
            ['name' => 'M.L.DRY', 'description' => 'Manga longa dry', 'price_adjustment' => 38.75, 'order' => 17],
            ['name' => 'CINZA MESCLADO', 'description' => 'Cinza mesclado', 'price_adjustment' => 2.34, 'order' => 18],
            ['name' => 'SUB NOME/NUMERO SUB', 'description' => 'Nome/número sublimado', 'price_adjustment' => 8.00, 'order' => 19],
            ['name' => 'SERIGRAFIA NOME/NUME', 'description' => 'Nome/número serigrafado', 'price_adjustment' => 15.00, 'order' => 20],
            ['name' => 'MATERIAL ESPORTIVO', 'description' => 'Material esportivo', 'price_adjustment' => 72.00, 'order' => 21],
        ];

        foreach ($addons as $addon) {
            SublimationAddon::create($addon);
        }
    }
}