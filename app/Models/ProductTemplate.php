<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'category',
        'tecido_keyword',
        'personalizacao_keyword',
        'modelo_keyword',
        'default_price',
        'icon',
        'compatible_cuts',
        'allow_application',
        'active',
    ];

    protected $casts = [
        'compatible_cuts' => 'array',
        'allow_application' => 'boolean', // Ensure boolean cast
        'active' => 'boolean',
        'default_price' => 'decimal:2',
    ];

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)
              ->orWhereNull('tenant_id');
        })->where('active', true);
    }
}
