<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublimationProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'option_group',
        'name',
        'price_modifier',
        'active',
        'order',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'active' => 'boolean',
    ];

    // Grupos de opções padrão
    const GROUPS = [
        'modelo' => 'Modelo',
        'tamanho' => 'Tamanho',
        'acabamento' => 'Acabamento',
        'dimensao' => 'Dimensão',
    ];

    /**
     * Produto pai
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(SublimationProduct::class, 'product_id');
    }

    /**
     * Scope para opções ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope por grupo
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('option_group', $group);
    }
}
