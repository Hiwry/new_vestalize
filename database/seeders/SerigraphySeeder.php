<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SerigraphySeeder extends Seeder
{
    public function run(): void
    {
        // Cores para Serigrafia (preço por cor adicional)
        $colors = [
            ['id' => 1, 'name' => '1 Cor', 'price' => 0, 'is_neon' => false, 'order' => 1, 'active' => true], // Primeira cor incluída
            ['id' => 2, 'name' => '2 Cores', 'price' => 2.00, 'is_neon' => false, 'order' => 2, 'active' => true], // +R$ 2,00
            ['id' => 3, 'name' => '3 Cores', 'price' => 4.00, 'is_neon' => false, 'order' => 3, 'active' => true], // +R$ 4,00
            ['id' => 4, 'name' => '4 Cores', 'price' => 6.00, 'is_neon' => false, 'order' => 4, 'active' => true], // +R$ 6,00
            ['id' => 5, 'name' => '5 Cores', 'price' => 8.00, 'is_neon' => false, 'order' => 5, 'active' => true], // +R$ 8,00
            ['id' => 6, 'name' => '6 Cores', 'price' => 10.00, 'is_neon' => false, 'order' => 6, 'active' => true], // +R$ 10,00
        ];

        foreach ($colors as $color) {
            DB::table('serigraphy_colors')->updateOrInsert(
                ['id' => $color['id']],
                $color
            );
        }
    }
}
