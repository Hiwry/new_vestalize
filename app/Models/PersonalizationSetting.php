<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalizationSetting extends Model
{
    protected $fillable = [
        'personalization_type',
        'display_name',
        'description',
        'charge_by_color',
        'color_price_per_unit',
        'color_cost_per_unit',
        'min_colors',
        'max_colors',
        'discount_2nd_application',
        'discount_3rd_application',
        'discount_4th_plus_application',
        'has_sizes',
        'has_locations',
        'has_special_options',
        'active',
        'order',
    ];

    protected $casts = [
        'charge_by_color' => 'boolean',
        'color_price_per_unit' => 'decimal:2',
        'color_cost_per_unit' => 'decimal:2',
        'discount_2nd_application' => 'decimal:2',
        'discount_3rd_application' => 'decimal:2',
        'discount_4th_plus_application' => 'decimal:2',
        'has_sizes' => 'boolean',
        'has_locations' => 'boolean',
        'has_special_options' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Opções especiais (adicionais) deste tipo de personalização
     */
    public function specialOptions(): HasMany
    {
        return $this->hasMany(PersonalizationSpecialOption::class, 'personalization_type', 'personalization_type');
    }

    /**
     * Preços deste tipo de personalização
     */
    public function prices()
    {
        return PersonalizationPrice::where('personalization_type', $this->personalization_type);
    }

    /**
     * Obtém os tamanhos disponíveis para este tipo
     */
    public function getSizes()
    {
        return PersonalizationPrice::getSizesForType($this->personalization_type);
    }

    /**
     * Obtém o desconto para uma aplicação específica
     */
    public function getDiscountForApplication(int $applicationNumber): float
    {
        return match($applicationNumber) {
            2 => (float) $this->discount_2nd_application,
            3 => (float) $this->discount_3rd_application,
            default => $applicationNumber >= 4 ? (float) $this->discount_4th_plus_application : 0
        };
    }

    /**
     * Calcula o preço adicional por cores
     */
    public function calculateColorSurcharge(int $colorCount): float
    {
        if (!$this->charge_by_color || $colorCount <= $this->min_colors) {
            return 0;
        }

        $extraColors = $colorCount - $this->min_colors;
        return $extraColors * (float) $this->color_price_per_unit;
    }

    /**
     * Scope para configurações ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('display_name');
    }

    /**
     * Buscar configuração por tipo
     */
    public static function findByType(string $type): ?self
    {
        return static::where('personalization_type', $type)->first();
    }
}
