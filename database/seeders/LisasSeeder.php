<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalizationPrice;

class LisasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Adicionar "Lisas" como opção de personalização em product_options
        $lisasId = DB::table('product_options')->insertGetId([
            'type' => 'personalizacao',
            'name' => 'Lisas',
            'price' => 0.00,
            'parent_type' => null,
            'parent_id' => null,
            'active' => 1,
            'order' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Buscar os IDs dos tecidos existentes
        $algodaoId = DB::table('product_options')->where('type', 'tecido')->where('name', 'Algodão')->value('id');
        $poliesterId = DB::table('product_options')->where('type', 'tecido')->where('name', 'Poliéster')->value('id');
        
        // Adicionar PV se não existir
        $pvId = DB::table('product_options')->where('type', 'tecido')->where('name', 'PV')->value('id');
        if (!$pvId) {
            $pvId = DB::table('product_options')->insertGetId([
                'type' => 'tecido',
                'name' => 'PV',
                'price' => 0.00,
                'parent_type' => 'personalizacao',
                'parent_id' => 101, // SERIGRAFIA
                'active' => 1,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Criar relações entre "Lisas" e os tecidos em product_option_relations
        $relations = [
            ['parent_id' => $lisasId, 'option_id' => $algodaoId],
            ['parent_id' => $lisasId, 'option_id' => $poliesterId],
            ['parent_id' => $lisasId, 'option_id' => $pvId],
        ];

        foreach ($relations as $relation) {
            DB::table('product_option_relations')->updateOrInsert(
                ['parent_id' => $relation['parent_id'], 'option_id' => $relation['option_id']],
                array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 4. Adicionar preços para camisetas LISAS na tabela personalization_prices
        // Baseado nos valores de mercado para camisetas lisas por tipo de tecido
        
        // LISAS - ALGODÃO (PP - 100% Algodão)
        $lisasAlgodaoPP = [
            // Quantidade 1-9
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 15.00],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 13.50],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 12.00],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 10.50],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 9.50],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 8.50],
            ['size_name' => 'PP', 'size_dimensions' => '100% Algodão', 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 7.50],
        ];

        // LISAS - PV (Misto - Poliéster/Viscose ou Algodão/Poliéster)
        $lisasPV = [
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 16.00],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 13.50],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 10.50],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 9.00],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 8.00],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 7.00],
            ['size_name' => 'PV', 'size_dimensions' => 'Misto PV', 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 6.50],
        ];

        // LISAS - POLIÉSTER/DRY FIT
        $lisasPoliester = [
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 20.00],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 17.00],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 13.00],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 11.50],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 10.50],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 9.50],
            ['size_name' => 'POLIÉSTER', 'size_dimensions' => '100% Poliéster', 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 8.50],
        ];

        // LISAS - CACHARREL (tecido mais premium)
        $lisasCacharrel = [
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 22.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 19.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 17.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 15.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 13.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 11.50],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 10.50],
            ['size_name' => 'CACHARREL', 'size_dimensions' => 'Cacharrel', 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 9.50],
        ];

        // LISAS - DRY FIT (tecido técnico esportivo)
        $lisasDryFit = [
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 22.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 18.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 16.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 14.50],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 13.00],
            ['size_name' => 'DRY FIT', 'size_dimensions' => 'Dry Fit', 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 12.00],
        ];

        // Inserir todos os preços na tabela personalization_prices
        $allPrices = array_merge(
            $lisasAlgodaoPP,
            $lisasPV,
            $lisasPoliester,
            $lisasCacharrel,
            $lisasDryFit
        );

        foreach ($allPrices as $price) {
            PersonalizationPrice::updateOrCreate(
                [
                    'personalization_type' => 'LISAS',
                    'size_name' => $price['size_name'],
                    'quantity_from' => $price['quantity_from'],
                    'quantity_to' => $price['quantity_to']
                ],
                array_merge($price, [
                    'active' => 1,
                    'order' => 0,
                ])
            );
        }

        echo " Preços e personalização 'Lisas' adicionados com sucesso!\n";
    }
}

