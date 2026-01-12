<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SublimationAddon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_adjustment',
        'active',
        'order',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Scope para buscar apenas adicionais ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para ordenar por ordem
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Buscar todos os adicionais ativos ordenados
     */
    public static function getActiveAddons()
    {
        return static::active()->ordered()->get();
    }
}