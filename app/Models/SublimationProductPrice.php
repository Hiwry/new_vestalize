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
     */
    public static function getPriceFor(string $productType, int $quantity, ?int $tenantId = null): ?float
    {
        $query = static::where('product_type', $productType)
            ->where('quantity_from', '<=', $quantity)
            ->where(function($q) use ($quantity) {
                $q->whereNull('quantity_to')
                  ->orWhere('quantity_to', '>=', $quantity);
            });
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        $price = $query->orderBy('quantity_from', 'desc')->first();
        
        return $price ? (float) $price->price : null;
    }
}
