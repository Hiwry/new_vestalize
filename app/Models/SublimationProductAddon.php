<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublimationProductAddon extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_id',
        'product_type',
        'name',
        'price',
        'active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope para adicionais ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para adicionais de um tipo específico
     */
    public function scopeForType($query, string $productType)
    {
        return $query->where('product_type', $productType);
    }

    /**
     * Scope para adicionais globais (sem tipo específico)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('product_type');
    }
}
