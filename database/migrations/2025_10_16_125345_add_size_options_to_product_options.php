<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductOption;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tamanhos = [
            ['name' => 'PP', 'order' => 1],
            ['name' => 'P', 'order' => 2],
            ['name' => 'M', 'order' => 3],
            ['name' => 'G', 'order' => 4],
            ['name' => 'GG', 'order' => 5],
            ['name' => 'EXG', 'order' => 6],
            ['name' => 'G1', 'order' => 7],
            ['name' => 'G2', 'order' => 8],
            ['name' => 'G3', 'order' => 9],
            ['name' => 'Especial', 'order' => 10],
        ];

        foreach ($tamanhos as $tamanho) {
            ProductOption::create([
                'type' => 'tamanho',
                'name' => $tamanho['name'],
                'price' => '0.00',
                'parent_type' => 'personalizacao',
                'parent_id' => 100,
                'active' => true,
                'order' => $tamanho['order'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ProductOption::where('type', 'tamanho')->delete();
    }
};