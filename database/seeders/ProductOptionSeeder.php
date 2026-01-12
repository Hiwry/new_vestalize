<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductOptionSeeder extends Seeder
{
    public function run(): void
    {
        // Personalização
        $personalizacoes = [
            ['id' => 100, 'type' => 'personalizacao', 'name' => 'DTF', 'price' => 0, 'order' => 1],
            ['id' => 101, 'type' => 'personalizacao', 'name' => 'SERIGRAFIA', 'price' => 0, 'order' => 2],
            ['id' => 102, 'type' => 'personalizacao', 'name' => 'BORDADO', 'price' => 0, 'order' => 3],
            ['id' => 103, 'type' => 'personalizacao', 'name' => 'EMBORRACHADO', 'price' => 0, 'order' => 4],
            ['id' => 104, 'type' => 'personalizacao', 'name' => 'SUB. LOCAL', 'price' => 0, 'order' => 5],
            ['id' => 105, 'type' => 'personalizacao', 'name' => 'SUB. TOTAL', 'price' => 0, 'order' => 6],
        ];

        foreach ($personalizacoes as $item) {
            DB::table('product_options')->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
        }

        // Tecidos (filhos de personalização)
        // Associando tecidos à DTF (id 100) como parent_id principal
        $tecidos = [
            ['id' => 1, 'type' => 'tecido', 'name' => 'Algodão', 'price' => 0, 'parent_type' => 'personalizacao', 'parent_id' => 100, 'order' => 1],
            ['id' => 2, 'type' => 'tecido', 'name' => 'Poliéster', 'price' => 0, 'parent_type' => 'personalizacao', 'parent_id' => 100, 'order' => 2],
        ];

        foreach ($tecidos as $item) {
            DB::table('product_options')->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
        }

        // Relacionamentos múltiplos para tecidos (associar a todas as personalizações)
        $tecidoRelations = [
            // Algodão disponível para DTF, SERIGRAFIA, BORDADO e EMBORRACHADO
            ['option_id' => 1, 'parent_id' => 100], // Algodão -> DTF
            ['option_id' => 1, 'parent_id' => 101], // Algodão -> SERIGRAFIA
            ['option_id' => 1, 'parent_id' => 102], // Algodão -> BORDADO
            ['option_id' => 1, 'parent_id' => 103], // Algodão -> EMBORRACHADO
            // Poliéster disponível para DTF, SERIGRAFIA, SUB. LOCAL e SUB. TOTAL
            ['option_id' => 2, 'parent_id' => 100], // Poliéster -> DTF
            ['option_id' => 2, 'parent_id' => 101], // Poliéster -> SERIGRAFIA
            ['option_id' => 2, 'parent_id' => 104], // Poliéster -> SUB. LOCAL
            ['option_id' => 2, 'parent_id' => 105], // Poliéster -> SUB. TOTAL
        ];

        foreach ($tecidoRelations as $relation) {
            DB::table('product_option_relations')->updateOrInsert(
                ['option_id' => $relation['option_id'], 'parent_id' => $relation['parent_id']],
                array_merge($relation, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Tipos de Tecido (associados aos tecidos)
        $tiposTecido = [
            ['type' => 'tipo_tecido', 'name' => 'PP', 'price' => 0, 'parent_type' => 'tecido', 'parent_id' => 1, 'order' => 1],
            ['type' => 'tipo_tecido', 'name' => 'CACHARREL', 'price' => 0, 'parent_type' => 'tecido', 'parent_id' => 1, 'order' => 2],
            ['type' => 'tipo_tecido', 'name' => 'DRY FIT', 'price' => 0, 'parent_type' => 'tecido', 'parent_id' => 2, 'order' => 1],
        ];

        foreach ($tiposTecido as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => $item['type'], 'name' => $item['name'], 'parent_id' => $item['parent_id']],
                $item
            );
        }

        // Cores
        $cores = [
            ['type' => 'cor', 'name' => 'Preto', 'price' => 0, 'order' => 1],
            ['type' => 'cor', 'name' => 'Branco', 'price' => 0, 'order' => 2],
            ['type' => 'cor', 'name' => 'Azul', 'price' => 0, 'order' => 3],
            ['type' => 'cor', 'name' => 'Vermelho', 'price' => 0, 'order' => 4],
            ['type' => 'cor', 'name' => 'Verde', 'price' => 0, 'order' => 5],
        ];

        foreach ($cores as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => $item['type'], 'name' => $item['name']],
                $item
            );
        }

        // Tipos de Corte (com preço)
        $tiposCorte = [
            ['type' => 'tipo_corte', 'name' => 'Tradicional', 'price' => 0, 'order' => 1],
            ['type' => 'tipo_corte', 'name' => 'Babylook', 'price' => 5.00, 'order' => 2],
            ['type' => 'tipo_corte', 'name' => 'Oversized', 'price' => 3.00, 'order' => 3],
        ];

        foreach ($tiposCorte as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => $item['type'], 'name' => $item['name']],
                $item
            );
        }

        // Detalhes (com preço)
        $detalhes = [
            ['type' => 'detalhe', 'name' => 'Sem detalhe', 'price' => 0, 'order' => 1],
            ['type' => 'detalhe', 'name' => 'Bolso', 'price' => 2.50, 'order' => 2],
            ['type' => 'detalhe', 'name' => 'Ribana', 'price' => 3.00, 'order' => 3],
            ['type' => 'detalhe', 'name' => 'Vivo', 'price' => 2.00, 'order' => 4],
        ];

        foreach ($detalhes as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => $item['type'], 'name' => $item['name']],
                $item
            );
        }

        // Golas (com preço)
        $golas = [
            ['type' => 'gola', 'name' => 'Careca', 'price' => 0, 'order' => 1],
            ['type' => 'gola', 'name' => 'Gola V', 'price' => 2.00, 'order' => 2],
            ['type' => 'gola', 'name' => 'Gola Polo', 'price' => 5.00, 'order' => 3],
            ['type' => 'gola', 'name' => 'Gola Redonda', 'price' => 1.50, 'order' => 4],
        ];

        foreach ($golas as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => $item['type'], 'name' => $item['name']],
                $item
            );
        }
    }
}