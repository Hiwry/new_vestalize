<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSurchargeSeeder extends Seeder
{
    public function run(): void
    {
        $surcharges = [
            // GG
            ['size' => 'GG', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 1.00],
            ['size' => 'GG', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 2.00],
            ['size' => 'GG', 'price_from' => 50.00, 'price_to' => 549.99, 'surcharge' => 5.00],
            ['size' => 'GG', 'price_from' => 550.00, 'price_to' => null, 'surcharge' => 5.00],
            
            // EXG
            ['size' => 'EXG', 'price_from' => 0, 'price_to' => 19.99, 'surcharge' => 2.00],
            ['size' => 'EXG', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 4.00],
            ['size' => 'EXG', 'price_from' => 50.00, 'price_to' => 549.99, 'surcharge' => 10.00],
            ['size' => 'EXG', 'price_from' => 550.00, 'price_to' => null, 'surcharge' => 10.00],
            
            // G1
            ['size' => 'G1', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 10.00],
            ['size' => 'G1', 'price_from' => 50.00, 'price_to' => 549.99, 'surcharge' => 15.00],
            ['size' => 'G1', 'price_from' => 550.00, 'price_to' => null, 'surcharge' => 20.00],
            
            // G2
            ['size' => 'G2', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 20.00],
            ['size' => 'G2', 'price_from' => 50.00, 'price_to' => 549.99, 'surcharge' => 25.00],
            ['size' => 'G2', 'price_from' => 550.00, 'price_to' => null, 'surcharge' => 40.00],
            
            // G3
            ['size' => 'G3', 'price_from' => 20.00, 'price_to' => 49.99, 'surcharge' => 30.00],
            ['size' => 'G3', 'price_from' => 50.00, 'price_to' => 549.99, 'surcharge' => 35.00],
            ['size' => 'G3', 'price_from' => 550.00, 'price_to' => null, 'surcharge' => 60.00],
        ];

        foreach ($surcharges as $surcharge) {
            DB::table('size_surcharges')->updateOrInsert(
                [
                    'size' => $surcharge['size'],
                    'price_from' => $surcharge['price_from']
                ],
                $surcharge
            );
        }
    }
}
