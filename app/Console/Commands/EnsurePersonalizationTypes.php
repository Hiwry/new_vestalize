<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PersonalizationPrice;
use Illuminate\Support\Facades\DB;

class EnsurePersonalizationTypes extends Command
{
    protected $signature = 'personalization:ensure-types';
    protected $description = 'Garante que todos os tipos de personalização padrão existam no banco de dados';

    public function handle()
    {
        $this->info('Verificando tipos de personalização...');

        // Tipos obrigatórios
        $requiredTypes = [
            'SUB. TOTAL',
            'SUB. LOCAL', 
            'EMBORRACHADO',
            'DTF',
            'SERIGRAFIA',
            'BORDADO'
        ];

        foreach ($requiredTypes as $type) {
            $this->info("Verificando tipo: {$type}");
            
            // Verifica se existe pelo menos um preço para este tipo
            $exists = PersonalizationPrice::where('personalization_type', $type)->exists();
            
            if (!$exists) {
                $this->warn("Tipo {$type} não encontrado. Criando preços padrão...");
                $this->createDefaultPrices($type);
            } else {
                $this->info("Tipo {$type} já existe.");
            }
        }

        // Criar tipos nas opções de produto também
        $this->ensureProductOptions();

        $this->info('Todos os tipos de personalização foram verificados!');
    }

    private function createDefaultPrices($type)
    {
        switch ($type) {
            case 'SUB. TOTAL':
                $prices = [
                    ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 10, 'quantity_to' => 29, 'price' => 36.00],
                    ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 30, 'quantity_to' => 49, 'price' => 34.95],
                    ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 50, 'quantity_to' => 99, 'price' => 32.90],
                    ['size_name' => 'CACHARREL', 'size_dimensions' => null, 'quantity_from' => 100, 'quantity_to' => null, 'price' => 30.85],
                ];
                break;

            case 'SUB. LOCAL':
                $prices = [
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 20.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 16.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 13.00],
                ];
                break;

            case 'EMBORRACHADO':
                $prices = [
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 9.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 15.00],
                ];
                break;

            case 'DTF':
                $prices = [
                    ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 8.00],
                    ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 6.00],
                    ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 4.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 9.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
                ];
                break;

            case 'SERIGRAFIA':
                $prices = [
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
                    ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
                    ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 15.00],
                ];
                break;

            case 'BORDADO':
                $prices = [
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
                    ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
                    ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 12.00],
                ];
                break;
        }

        if (isset($prices)) {
            foreach ($prices as $price) {
                PersonalizationPrice::updateOrCreate(
                    [
                        'personalization_type' => $type,
                        'size_name' => $price['size_name'],
                        'quantity_from' => $price['quantity_from'],
                        'quantity_to' => $price['quantity_to']
                    ],
                    $price
                );
            }
        }
    }

    private function ensureProductOptions()
    {
        $this->info('Verificando opções de produto...');
        
        $personalizacoes = [
            ['type' => 'personalizacao', 'name' => 'SUB. TOTAL', 'price' => 0, 'order' => 1],
            ['type' => 'personalizacao', 'name' => 'SUB. LOCAL', 'price' => 0, 'order' => 2],
            ['type' => 'personalizacao', 'name' => 'EMBORRACHADO', 'price' => 0, 'order' => 3],
            ['type' => 'personalizacao', 'name' => 'DTF', 'price' => 0, 'order' => 4],
            ['type' => 'personalizacao', 'name' => 'SERIGRAFIA', 'price' => 0, 'order' => 5],
            ['type' => 'personalizacao', 'name' => 'BORDADO', 'price' => 0, 'order' => 6],
        ];

        foreach ($personalizacoes as $item) {
            DB::table('product_options')->updateOrInsert(
                ['type' => 'personalizacao', 'name' => $item['name']],
                $item
            );
        }
    }
}