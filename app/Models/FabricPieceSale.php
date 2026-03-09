<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricPieceSale extends Model
{
    protected $fillable = [
        'fabric_piece_id',
        'store_id',
        'order_id',
        'catalog_order_id',
        'order_item_id',
        'sold_by',
        'channel',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'notes',
        'reverted_at',
        'reverted_by',
        'revert_reason',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'reverted_at' => 'datetime',
    ];

    // Relacionamentos

    public function fabricPiece(): BelongsTo
    {
        return $this->belongsTo(FabricPiece::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function catalogOrder(): BelongsTo
    {
        return $this->belongsTo(CatalogOrder::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('reverted_at');
    }
}
