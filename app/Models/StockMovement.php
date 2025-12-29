<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    protected $fillable = [
        'type',
        'from_store_id',
        'to_store_id',
        'order_id',
        'user_id',
        'notes',
    ];

    // Tipos de movimentação
    const TYPE_TRANSFER = 'transferencia';
    const TYPE_ORDER = 'pedido';
    const TYPE_REMOVAL = 'remocao';
    const TYPE_ENTRY = 'entrada';
    const TYPE_RETURN = 'devolucao';

    // Labels para exibição
    public static array $typeLabels = [
        'transferencia' => 'Transferência entre Lojas',
        'pedido' => 'Saída para Pedido',
        'remocao' => 'Remoção/Baixa',
        'entrada' => 'Entrada de Estoque',
        'devolucao' => 'Devolução',
    ];

    // Relacionamentos

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    // Accessors

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    // Métodos estáticos

    /**
     * Criar movimento de transferência
     */
    public static function createTransfer(
        int $fromStoreId,
        int $toStoreId,
        array $items,
        ?string $notes = null
    ): self {
        $movement = self::create([
            'type' => self::TYPE_TRANSFER,
            'from_store_id' => $fromStoreId,
            'to_store_id' => $toStoreId,
            'user_id' => auth()->id(),
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $movement->items()->create($item);
        }

        return $movement;
    }

    /**
     * Criar movimento de pedido
     */
    public static function createOrderMovement(
        int $storeId,
        int $orderId,
        array $items,
        ?string $notes = null
    ): self {
        $movement = self::create([
            'type' => self::TYPE_ORDER,
            'from_store_id' => $storeId,
            'order_id' => $orderId,
            'user_id' => auth()->id(),
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $movement->items()->create($item);
        }

        return $movement;
    }

    /**
     * Criar movimento de remoção/baixa
     */
    public static function createRemoval(
        int $storeId,
        array $items,
        ?string $notes = null
    ): self {
        $movement = self::create([
            'type' => self::TYPE_REMOVAL,
            'from_store_id' => $storeId,
            'user_id' => auth()->id(),
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            $movement->items()->create($item);
        }

        return $movement;
    }
}
