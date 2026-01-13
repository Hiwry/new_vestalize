<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLocalProduct extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'description',
        'image',
        'price',
        'cost',
        'quantity_pricing',
        'has_quantity_pricing',
        'allow_price_edit',
        'is_active',
        'sort_order',
        'requires_customization',
        'available_sizes',
        'requires_size',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'quantity_pricing' => 'array',
        'has_quantity_pricing' => 'boolean',
        'allow_price_edit' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'requires_customization' => 'boolean',
        'available_sizes' => 'array',
        'requires_size' => 'boolean',
    ];

    /**
     * Calcula o preço baseado na quantidade
     */
    public function getPriceForQuantity(int $quantity): float
    {
        if (!$this->has_quantity_pricing || empty($this->quantity_pricing)) {
            return (float) $this->price;
        }

        foreach ($this->quantity_pricing as $tier) {
            $minQty = $tier['min_quantity'] ?? 0;
            $maxQty = $tier['max_quantity'] ?? PHP_INT_MAX;
            
            if ($quantity >= $minQty && $quantity <= $maxQty) {
                return (float) $tier['price'];
            }
        }

        // Retorna o preço padrão se não encontrar faixa
        return (float) $this->price;
    }
}

