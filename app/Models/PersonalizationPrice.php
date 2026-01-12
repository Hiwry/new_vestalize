<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PersonalizationPrice extends Model
{
    protected $fillable = [
        'personalization_type',
        'size_name',
        'size_dimensions',
        'quantity_from',
        'quantity_to',
        'price',
        'cost',
        'active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Buscar preço para um tipo de personalização, tamanho e quantidade específicos
     */
    public static function getPriceForPersonalization($type, $sizeName, $quantity)
    {
        return static::where('personalization_type', $type)
            ->where('size_name', $sizeName)
            ->where('quantity_from', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
            })
            ->orderBy('quantity_from', 'desc')
            ->first();
    }

    /**
     * Buscar a faixa de preço com menor quantidade inicial (para fallback)
     */
    public static function getLowestPriceRange($type, $sizeName)
    {
        return static::where('personalization_type', $type)
            ->where('size_name', $sizeName)
            ->orderBy('quantity_from', 'asc')
            ->first();
    }

    /**
     * Buscar todos os tamanhos disponíveis para um tipo de personalização
     */
    public static function getSizesForType($type)
    {
        return static::where('personalization_type', $type)
            ->where('active', true)
            ->select('size_name', 'size_dimensions', DB::raw('MIN(`order`) as min_order'))
            ->groupBy('size_name', 'size_dimensions')
            ->orderBy('min_order')
            ->get()
            ->map(function($item) {
                return (object)[
                    'size_name' => $item->size_name,
                    'size_dimensions' => $item->size_dimensions
                ];
            });
    }

    /**
     * Buscar todas as faixas de preço para um tipo e tamanho específicos
     */
    public static function getPriceRangesForTypeAndSize($type, $sizeName)
    {
        return static::where('personalization_type', $type)
            ->where('size_name', $sizeName)
            ->orderBy('quantity_from')
            ->get();
    }

    /**
     * Tipos de personalização disponíveis (busca dinâmica do ProductOption)
     */
    public static function getPersonalizationTypes()
    {
        // Buscar personalizações cadastradas na tabela product_options
        $productOptions = \App\Models\ProductOption::where('type', 'personalizacao')
            ->where('active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        
        if ($productOptions->isEmpty()) {
            // Fallback para tipos fixos se não houver nenhum cadastrado
            return [
                'DTF' => 'DTF',
                'SERIGRAFIA' => 'Serigrafia',
                'BORDADO' => 'Bordado',
                'EMBORRACHADO' => 'Emborrachado',
                'SUB. LOCAL' => 'Sublimação Local',
                'SUB. TOTAL' => 'Sublimação Total',
            ];
        }
        
        $types = [];
        foreach ($productOptions as $option) {
            // Usar o nome em maiúsculas como chave para manter compatibilidade
            $key = strtoupper($option->name);
            $types[$key] = $option->name;
        }
        
        return $types;
    }
}