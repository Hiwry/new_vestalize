<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementItem extends Model
{
    protected $fillable = [
        'stock_movement_id',
        'stock_id',
        'fabric_type_id',
        'color_id',
        'cut_type_id',
        'size',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relacionamentos

    public function movement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_type_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'color_id');
    }

    public function cutType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'cut_type_id');
    }

    // Accessors

    public function getDescriptionAttribute(): string
    {
        $parts = [];
        
        if ($this->fabricType) {
            $parts[] = $this->fabricType->name;
        }
        if ($this->color) {
            $parts[] = $this->color->name;
        }
        if ($this->cutType) {
            $parts[] = $this->cutType->name;
        }
        
        return implode(' / ', $parts) ?: 'Item';
    }
}
