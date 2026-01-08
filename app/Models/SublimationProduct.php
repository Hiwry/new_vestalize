<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublimationProduct extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'type',
        'base_price',
        'fabric_always_white',
        'active',
        'order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'fabric_always_white' => 'boolean',
        'active' => 'boolean',
    ];

    // Tipos de produto disponíveis
    const TYPES = [
        'camisa' => 'Camisa',
        'conjunto' => 'Conjunto Esportivo',
        'bandeira' => 'Bandeira',
        'winderbanner' => 'Winderbanner',
        'custom' => 'Personalizado',
    ];

    /**
     * Relacionamento com tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Opções do produto (modelo, tamanho, acabamento)
     */
    public function options(): HasMany
    {
        return $this->hasMany(SublimationProductOption::class, 'product_id')->orderBy('option_group')->orderBy('order');
    }

    /**
     * Faixas de preço por quantidade
     */
    public function prices(): HasMany
    {
        return $this->hasMany(SublimationProductPrice::class, 'product_id')->orderBy('quantity_from');
    }

    /**
     * Adicionais específicos do produto
     */
    public function addons(): HasMany
    {
        return $this->hasMany(SublimationProductAddon::class, 'product_id')->orderBy('order');
    }

    /**
     * Scope para produtos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Buscar preço baseado na quantidade
     */
    public function getPriceForQuantity(int $quantity): ?float
    {
        $priceRange = $this->prices()
            ->where('quantity_from', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
            })
            ->orderBy('quantity_from', 'desc')
            ->first();

        return $priceRange ? (float) $priceRange->price : (float) $this->base_price;
    }

    /**
     * Buscar opções por grupo
     */
    public function getOptionsByGroup(string $group)
    {
        return $this->options()->where('option_group', $group)->where('active', true)->get();
    }

    /**
     * Grupos de opções únicos
     */
    public function getOptionGroups(): array
    {
        return $this->options()
            ->select('option_group')
            ->distinct()
            ->pluck('option_group')
            ->toArray();
    }
}
