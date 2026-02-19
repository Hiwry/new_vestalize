<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalizationSpecialOption extends Model
{
    protected $fillable = [
        'personalization_type',
        'name',
        'charge_type',
        'charge_value',
        'cost',
        'description',
        'active',
        'order',
    ];

    protected $casts = [
        'charge_value' => 'decimal:2',
        'cost' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Configuração de personalização pai
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(PersonalizationSetting::class, 'personalization_type', 'personalization_type');
    }

    /**
     * Calcula o adicional baseado no preço base
     */
    public function calculateAdditional(float $basePrice): float
    {
        if ($this->charge_type === 'percentage') {
            return $basePrice * ((float) $this->charge_value / 100);
        }
        
        // Valor fixo
        return (float) $this->charge_value;
    }

    /**
     * Retorna o valor formatado para exibição
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->charge_type === 'percentage') {
            return '+' . number_format($this->charge_value, 0) . '%';
        }
        
        return '+R$ ' . number_format($this->charge_value, 2, ',', '.');
    }

    /**
     * Scope para opções ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope por tipo de personalização
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('personalization_type', $type);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
