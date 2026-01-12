<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalizationPrice;

class PersonalizationPriceSeeder extends Seeder
{
    public function run(): void
    {
        // DTF - Preços baseados no guia existente
        $dtfPrices = [
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 8.00],
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 6.00],
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 4.00],
            
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 9.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 20.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
        ];

        foreach ($dtfPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'DTF']));
        }

        // SERIGRAFIA - Preços baseados no guia existente
        $serigraphyPrices = [
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 15.00],
            
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 14.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
        ];

        foreach ($serigraphyPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SERIGRAFIA']));
        }

        // BORDADO - Preços baseados no guia existente
        $embroideryPrices = [
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 12.00],
            
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 16.00],
        ];

        foreach ($embroideryPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'BORDADO']));
        }

        // SUBLIMACAO - Preços baseados no sistema existente
        $sublimationPrices = [
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 10.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 8.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 9.00],
            
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
        ];

        foreach ($sublimationPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SUBLIMACAO']));
        }

        // EMBORRACHADO - Preços baseados no banco de referência (John)
        $emborrachadoPrices = [
            // COR
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 3.60],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 3.24],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 2.92],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 2.62],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 2.36],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 2.13],
            ['size_name' => 'COR', 'size_dimensions' => null, 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 1.91],
            
            // ESCUDO
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 10.92],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 9.83],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 8.85],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 7.96],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 7.17],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 6.45],
            ['size_name' => 'ESCUDO', 'size_dimensions' => null, 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 5.81],
            
            // A4
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 14.28],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 12.86],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 11.58],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 10.41],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 9.38],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 8.44],
            ['size_name' => 'A4', 'size_dimensions' => null, 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 7.60],
            
            // A3
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 17.64],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 15.88],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 14.30],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => 299, 'price' => 12.87],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 300, 'quantity_to' => 499, 'price' => 11.58],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 500, 'quantity_to' => 999, 'price' => 10.42],
            ['size_name' => 'A3', 'size_dimensions' => null, 'quantity_from' => 1000, 'quantity_to' => 9999, 'price' => 9.38],
        ];

        foreach ($emborrachadoPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'EMBORRACHADO']));
        }

        // SUBLIMACAO_TOTAL - Preços para sublimação em peças inteiras
        $sublimacaoTotalPrices = [
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 8.00],
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 6.00],
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 4.00],
            
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 10.00],
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 8.00],
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
            
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 12.00],
        ];

        foreach ($sublimacaoTotalPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SUBLIMACAO_TOTAL']));
        }

        // SUB. TOTAL - Preços para sublimação total (como você configurou)
        $subTotalPrices = [
            ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 40.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 36.00],
            ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 34.95],
            ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 32.90],
            ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => null, 'price' => 30.85],
        ];

        foreach ($subTotalPrices as $price) {
            PersonalizationPrice::updateOrCreate(
                [
                    'personalization_type' => 'SUB. TOTAL',
                    'size_name' => $price['size_name'],
                    'quantity_from' => $price['quantity_from'],
                    'quantity_to' => $price['quantity_to']
                ],
                $price
            );
        }

        // SUB. LOCAL - Preços para sublimação local
        $subLocalPrices = [
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 20.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 16.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 13.00],
            
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 14.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 11.00],
        ];

        foreach ($subLocalPrices as $price) {
            PersonalizationPrice::updateOrCreate(
                [
                    'personalization_type' => 'SUB. LOCAL',
                    'size_name' => $price['size_name'],
                    'quantity_from' => $price['quantity_from'],
                    'quantity_to' => $price['quantity_to']
                ],
                $price
            );
        }
    }
}