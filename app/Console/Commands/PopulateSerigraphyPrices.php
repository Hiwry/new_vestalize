<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PersonalizationPrice;
use App\Models\SerigraphyColor;

class PopulateSerigraphyPrices extends Command
{
    protected $signature = 'serigraphy:populate-prices';
    protected $description = 'Popula a tabela de preços de serigrafia com valores de exemplo';

    public function handle()
    {
        $this->info('Populando preços de serigrafia...');

        // Deletar preços existentes de serigrafia
        PersonalizationPrice::where('personalization_type', 'SERIGRAFIA')->delete();
        SerigraphyColor::truncate();

        // Dados da tabela de exemplo
        $priceData = [
            ['qty_from' => 10, 'qty_to' => 29, 'escudo' => 29, 'a4' => 5.46, 'a3' => 7.14, 'cor_plus' => 8.82],
            ['qty_from' => 30, 'qty_to' => 49, 'escudo' => 49, 'a4' => 4.92, 'a3' => 6.43, 'cor_plus' => 7.94],
            ['qty_from' => 50, 'qty_to' => 99, 'escudo' => 99, 'a4' => 4.43, 'a3' => 5.79, 'cor_plus' => 7.14],
            ['qty_from' => 100, 'qty_to' => 299, 'escudo' => 299, 'a4' => 3.94, 'a3' => 5.14, 'cor_plus' => 6.43],
            ['qty_from' => 300, 'qty_to' => 499, 'escudo' => 499, 'a4' => 3.59, 'a3' => 4.69, 'cor_plus' => 5.79],
            ['qty_from' => 500, 'qty_to' => 999, 'escudo' => 999, 'a4' => 3.23, 'a3' => 4.23, 'cor_plus' => 5.21],
            ['qty_from' => 1000, 'qty_to' => 9999, 'escudo' => 9999, 'a4' => 2.91, 'a3' => 3.80, 'cor_plus' => 4.69],
        ];

        // Criar preços base (ESCUDO, A4, A3)
        foreach ($priceData as $row) {
            // ESCUDO
            PersonalizationPrice::create([
                'personalization_type' => 'SERIGRAFIA',
                'size_name' => 'ESCUDO',
                'size_dimensions' => null,
                'quantity_from' => $row['qty_from'],
                'quantity_to' => $row['qty_to'],
                'price' => $row['escudo'],
            ]);

            // A4
            PersonalizationPrice::create([
                'personalization_type' => 'SERIGRAFIA',
                'size_name' => 'A4',
                'size_dimensions' => '21x29.7cm',
                'quantity_from' => $row['qty_from'],
                'quantity_to' => $row['qty_to'],
                'price' => $row['a4'],
            ]);

            // A3
            PersonalizationPrice::create([
                'personalization_type' => 'SERIGRAFIA',
                'size_name' => 'A3',
                'size_dimensions' => '29.7x42cm',
                'quantity_from' => $row['qty_from'],
                'quantity_to' => $row['qty_to'],
                'price' => $row['a3'],
            ]);
        }

        // Criar cores (1 cor base + cores adicionais por faixa de quantidade)
        SerigraphyColor::create([
            'name' => '1 Cor',
            'price' => 0,
            'is_neon' => false,
            'order' => 1,
            'active' => true,
        ]);

        $colorOrder = 2;
        foreach ($priceData as $row) {
            SerigraphyColor::create([
                'name' => "COR + ({$row['qty_from']}-{$row['qty_to']})",
                'price' => $row['cor_plus'],
                'is_neon' => false,
                'order' => $colorOrder++,
                'active' => true,
            ]);
        }

        $this->info(' Preços de serigrafia populados com sucesso!');
        $this->info('Total de preços base criados: ' . (count($priceData) * 3));
        $this->info('Total de preços de cores criados: ' . (count($priceData) + 1));
    }
}
