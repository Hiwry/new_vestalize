<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'item_number',
        'fabric',
        'fabric_type',
        'color',
        'quantity',
        'personalization_types',
        'item_total',
        'cover_image',
    ];

    protected $casts = [
        'item_total' => 'decimal:2',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function customizations(): HasMany
    {
        return $this->hasMany(BudgetCustomization::class);
    }

    /**
     * Get personalization types as array
     */
    public function getPersonalizationTypesArray(): array
    {
        return $this->personalization_types ? json_decode($this->personalization_types, true) : [];
    }
}
