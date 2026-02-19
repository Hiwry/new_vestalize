<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionSupply extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'type',
        'color',
        'quantity',
        'unit',
        'min_stock',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'min_stock' => 'decimal:3',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
