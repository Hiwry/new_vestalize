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
            // Valores fixos por tamanho (baseado no orçamento do usuário)
            // GG = Acréscimo de R$ 2,00 por peça
            ['size' => 'GG', 'price_from' => 0, 'price_to' => null, 'surcharge' => 2.00],
            
            // EXG (Tamanho Especial X) = Acréscimo de R$ 35,00 por peça
            ['size' => 'EXG', 'price_from' => 0, 'price_to' => null, 'surcharge' => 35.00],
            
            // G1 = Acréscimo de R$ 10,00 por peça
            ['size' => 'G1', 'price_from' => 0, 'price_to' => null, 'surcharge' => 10.00],
            
            // G2 = Acréscimo de R$ 20,00 por peça
            ['size' => 'G2', 'price_from' => 0, 'price_to' => null, 'surcharge' => 20.00],
            
            // G3 = Acréscimo de R$ 40,00 por peça
            ['size' => 'G3', 'price_from' => 0, 'price_to' => null, 'surcharge' => 40.00],
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
