<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSizeSurcharges extends Command
{
    protected $signature = 'size-surcharges:update';
    protected $description = 'Atualiza os valores de acréscimo de tamanhos especiais';

    public function handle()
    {
        $this->info('Atualizando acréscimos de tamanhos especiais...');

        // Limpar tabela antes de atualizar
        DB::table('size_surcharges')->truncate();

        $surcharges = [
            // GG
            ['size' => 'GG', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 1.00],
            ['size' => 'GG', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 2.00],
            ['size' => 'GG', 'price_from' => 50.00, 'price_to' => null, 'surcharge' => 5.00],
            
            // EXG
            ['size' => 'EXG', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 2.00],
            ['size' => 'EXG', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 4.00],
            ['size' => 'EXG', 'price_from' => 50.00, 'price_to' => null, 'surcharge' => 10.00],
            
            // G1
            ['size' => 'G1', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 5.00],
            ['size' => 'G1', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 10.00],
            ['size' => 'G1', 'price_from' => 50.00, 'price_to' => null, 'surcharge' => 20.00],
            
            // G2
            ['size' => 'G2', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 10.00],
            ['size' => 'G2', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 20.00],
            ['size' => 'G2', 'price_from' => 50.00, 'price_to' => null, 'surcharge' => 40.00],
            
            // G3
            ['size' => 'G3', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 20.00],
            ['size' => 'G3', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 30.00],
            ['size' => 'G3', 'price_from' => 50.00, 'price_to' => null, 'surcharge' => 60.00],
        ];

        foreach ($surcharges as $surcharge) {
            DB::table('size_surcharges')->updateOrInsert(
                [
                    'size' => $surcharge['size'],
                    'price_from' => $surcharge['price_from']
                ],
                $surcharge
            );
            
            $this->line(" {$surcharge['size']} - R$ {$surcharge['price_from']} a " . 
                       ($surcharge['price_to'] ?? '∞') . ": R$ {$surcharge['surcharge']}");
        }

        $this->info('Acréscimos atualizados com sucesso!');
        return 0;
    }
}

