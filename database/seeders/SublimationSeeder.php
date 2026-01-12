<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SublimationSeeder extends Seeder
{
    public function run(): void
    {
        // Tamanhos de Sublimação
        $sizes = [
            ['id' => 1, 'name' => 'A4', 'dimensions' => '28X21', 'order' => 1, 'active' => true],
            ['id' => 2, 'name' => 'ESCUDO', 'dimensions' => '10X10', 'order' => 2, 'active' => true],
            ['id' => 3, 'name' => 'A3', 'dimensions' => '38X28', 'order' => 3, 'active' => true],
            ['id' => 4, 'name' => 'MEIA FOLHA', 'dimensions' => '28X10,5', 'order' => 4, 'active' => true],
            ['id' => 5, 'name' => 'NOME', 'dimensions' => '10X3', 'order' => 5, 'active' => true],
        ];

        foreach ($sizes as $size) {
            DB::table('sublimation_sizes')->updateOrInsert(
                ['id' => $size['id']],
                $size
            );
        }

        // Preços por quantidade (exemplo)
        $prices = [
            // A4 (28X21)
            ['size_id' => 1, 'quantity_from' => 1, 'quantity_to' => 10, 'price' => 15.00],
            ['size_id' => 1, 'quantity_from' => 11, 'quantity_to' => 20, 'price' => 12.00],
            ['size_id' => 1, 'quantity_from' => 21, 'quantity_to' => 50, 'price' => 10.00],
            ['size_id' => 1, 'quantity_from' => 51, 'quantity_to' => null, 'price' => 8.00],

            // ESCUDO (10X10)
            ['size_id' => 2, 'quantity_from' => 1, 'quantity_to' => 10, 'price' => 5.00],
            ['size_id' => 2, 'quantity_from' => 11, 'quantity_to' => 20, 'price' => 4.00],
            ['size_id' => 2, 'quantity_from' => 21, 'quantity_to' => 50, 'price' => 3.50],
            ['size_id' => 2, 'quantity_from' => 51, 'quantity_to' => null, 'price' => 3.00],

            // A3 (38X28)
            ['size_id' => 3, 'quantity_from' => 1, 'quantity_to' => 10, 'price' => 25.00],
            ['size_id' => 3, 'quantity_from' => 11, 'quantity_to' => 20, 'price' => 20.00],
            ['size_id' => 3, 'quantity_from' => 21, 'quantity_to' => 50, 'price' => 18.00],
            ['size_id' => 3, 'quantity_from' => 51, 'quantity_to' => null, 'price' => 15.00],

            // MEIA FOLHA (28X10,5)
            ['size_id' => 4, 'quantity_from' => 1, 'quantity_to' => 10, 'price' => 8.00],
            ['size_id' => 4, 'quantity_from' => 11, 'quantity_to' => 20, 'price' => 6.50],
            ['size_id' => 4, 'quantity_from' => 21, 'quantity_to' => 50, 'price' => 5.50],
            ['size_id' => 4, 'quantity_from' => 51, 'quantity_to' => null, 'price' => 4.50],

            // NOME (10X3)
            ['size_id' => 5, 'quantity_from' => 1, 'quantity_to' => 10, 'price' => 3.00],
            ['size_id' => 5, 'quantity_from' => 11, 'quantity_to' => 20, 'price' => 2.50],
            ['size_id' => 5, 'quantity_from' => 21, 'quantity_to' => 50, 'price' => 2.00],
            ['size_id' => 5, 'quantity_from' => 51, 'quantity_to' => null, 'price' => 1.50],
        ];

        foreach ($prices as $price) {
            DB::table('sublimation_prices')->updateOrInsert(
                [
                    'size_id' => $price['size_id'],
                    'quantity_from' => $price['quantity_from']
                ],
                $price
            );
        }

        // Locais de Aplicação
        $locations = [
            ['id' => 1, 'name' => 'Frente', 'order' => 1, 'active' => true],
            ['id' => 2, 'name' => 'Costas', 'order' => 2, 'active' => true],
            ['id' => 3, 'name' => 'Manga Direita', 'order' => 3, 'active' => true],
            ['id' => 4, 'name' => 'Manga Esquerda', 'order' => 4, 'active' => true],
            ['id' => 5, 'name' => 'Bolso', 'order' => 5, 'active' => true],
            ['id' => 6, 'name' => 'Capuz', 'order' => 6, 'active' => true],
        ];

        foreach ($locations as $location) {
            DB::table('sublimation_locations')->updateOrInsert(
                ['id' => $location['id']],
                $location
            );
        }
    }
}
