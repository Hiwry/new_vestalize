<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetCustomization extends Model
{
    protected $fillable = [
        'budget_id',
        'budget_item_id',
        'personalization_type',
        'art_name',
        'location',
        'size',
        'quantity',
        'color_count',
        'unit_price',
        'total_price',
        'image',
        'art_files',
        'notes',
        'addons',
        'regata_discount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'addons' => 'array',
        'regata_discount' => 'boolean',
    ];

    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(BudgetItem::class);
    }
}
