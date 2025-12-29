<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesHistory extends Model
{
    protected $table = 'sales_history';

    protected $fillable = [
        'order_id',
        'user_id',
        'store_id',
        'client_id',
        'total',
        'total_paid',
        'items_count',
        'is_pdv',
        'is_cancelled',
        'status_id',
        'notes',
        'metadata',
        'sale_date',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'is_pdv' => 'boolean',
        'is_cancelled' => 'boolean',
        'metadata' => 'array',
        'sale_date' => 'datetime',
    ];

    /**
     * Relacionamento com pedido
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com usuário (vendedor)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com loja
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relacionamento com cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relacionamento com status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Registrar histórico de venda
     */
    public static function recordSale(Order $order): self
    {
        return self::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'store_id' => $order->store_id,
            'client_id' => $order->client_id,
            'total' => $order->total,
            'total_paid' => $order->payments()->sum('amount'),
            'items_count' => $order->items()->count(),
            'is_pdv' => $order->is_pdv ?? false,
            'is_cancelled' => $order->is_cancelled ?? false,
            'status_id' => $order->status_id,
            'sale_date' => $order->created_at,
            'metadata' => [
                'order_number' => $order->order_number ?? null,
                'delivery_date' => $order->delivery_date ?? null,
            ],
        ]);
    }
}
