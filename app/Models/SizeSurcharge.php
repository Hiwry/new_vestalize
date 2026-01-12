<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// Force sync
class SizeSurcharge extends Model
{
    protected $fillable = [
        'size',
        'price_from',
        'price_to',
        'surcharge',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
        'surcharge' => 'decimal:2',
    ];
    
    private static $valuesEnsured = false;

    public static function getSurchargeForSize($size, $totalPrice)
    {
        // Garantir que os valores estejam atualizados (apenas uma vez por requisição)
        if (!self::$valuesEnsured) {
            self::ensureUpdatedValues();
            self::$valuesEnsured = true;
        }
        
        return self::where('size', $size)
            ->where('price_from', '<=', $totalPrice)
            ->where(function($query) use ($totalPrice) {
                $query->whereNull('price_to')
                      ->orWhere('price_to', '>=', $totalPrice);
            })
            ->orderBy('price_from', 'desc')
            ->first();
    }
    
    /**
     * Garantir que os valores de acréscimo estejam atualizados
     */
    private static function ensureUpdatedValues()
    {
        // Limpar tabela antes de atualizar para evitar duplicatas ou faixas antigas
        \DB::table('size_surcharges')->delete();

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
            \DB::table('size_surcharges')->updateOrInsert(
                [
                    'size' => $surcharge['size'],
                    'price_from' => $surcharge['price_from']
                ],
                $surcharge
            );
        }
    }
}
