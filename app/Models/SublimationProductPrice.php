<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublimationProductPrice extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_type',
        'quantity_from',
        'quantity_to',
        'price',
        'cost',
    ];

    protected $casts = [
        'quantity_from' => 'integer',
        'quantity_to' => 'integer',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * Relação com Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Label da faixa de quantidade
     */
    public function getRangeLabelAttribute(): string
    {
        if ($this->quantity_to) {
            return "{$this->quantity_from} - {$this->quantity_to}";
        }
        return "{$this->quantity_from}+";
    }

    /**
     * Buscar preço para um tipo e quantidade específicos
     * Prioriza preços do tenant, depois busca preços globais (tenant_id = null)
     */
    public static function getPriceFor(string $productType, int $quantity, ?int $tenantId = null): ?float
    {
        // Primeiro, tentar buscar preço específico do tenant
        if ($tenantId) {
            $price = static::where('product_type', $productType)
                ->where('tenant_id', $tenantId)
                ->where('quantity_from', '<=', $quantity)
                ->where(function($q) use ($quantity) {
                    $q->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
                })
                ->orderBy('quantity_from', 'desc')
                ->first();
            
            if ($price) {
                return (float) $price->price;
            }
        }
        
        // Se não encontrou preço do tenant, buscar preço global
        $globalPrice = static::where('product_type', $productType)
            ->whereNull('tenant_id')
            ->where('quantity_from', '<=', $quantity)
            ->where(function($q) use ($quantity) {
                $q->whereNull('quantity_to')
                  ->orWhere('quantity_to', '>=', $quantity);
            })
            ->orderBy('quantity_from', 'desc')
            ->first();
        
        return $globalPrice ? (float) $globalPrice->price : null;
    }
}
