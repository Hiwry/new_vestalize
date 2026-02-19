<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class StockHistory extends Model
{
    use BelongsToTenant;

    protected $table = 'stock_history';

    protected $fillable = [
        'tenant_id',
        'stock_id',
        'store_id',
        'user_id',
        'order_id',
        'stock_request_id',
        'action_type',
        'fabric_id',
        'color_id',
        'cut_type_id',
        'size',
        'quantity_before',
        'quantity_after',
        'quantity_change',
        'reserved_quantity_before',
        'reserved_quantity_after',
        'notes',
        'metadata',
        'action_date',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_change' => 'integer',
        'reserved_quantity_before' => 'integer',
        'reserved_quantity_after' => 'integer',
        'metadata' => 'array',
        'action_date' => 'datetime',
    ];

    /**
     * Relacionamento com estoque
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Relacionamento com loja
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com pedido
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com solicitação de estoque
     */
    public function stockRequest(): BelongsTo
    {
        return $this->belongsTo(StockRequest::class);
    }

    /**
     * Relacionamento com tipo de tecido
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_id');
    }

    /**
     * Relacionamento com cor
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'color_id');
    }

    /**
     * Relacionamento com tipo de corte
     */
    public function cutType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'cut_type_id');
    }

    /**
     * Registrar movimentação de estoque
     */
    public static function recordMovement(
        string $actionType,
        ?Stock $stock,
        int $quantityChange,
        ?int $userId = null,
        ?int $orderId = null,
        ?int $stockRequestId = null,
        ?string $notes = null,
        ?array $metadata = null
    ): self {
        // Recarregar stock para ter valores atualizados
        if ($stock) {
            $stock->refresh();
        }
        
        $quantityBefore = $stock ? $stock->quantity : 0;
        $quantityAfter = $quantityBefore;
        $reservedBefore = $stock ? $stock->reserved_quantity : 0;
        $reservedAfter = $reservedBefore;

        // Ajustar valores baseado no tipo de ação
        if (in_array($actionType, ['entrada', 'devolucao'])) {
            // Entrada/devolução aumenta quantity
            $quantityAfter = $quantityBefore + abs($quantityChange);
        } elseif (in_array($actionType, ['saida', 'perda'])) {
            // Saída/perda diminui quantity
            $quantityAfter = max(0, $quantityBefore - abs($quantityChange));
        } elseif ($actionType === 'reserva') {
            // Reserva aumenta reserved_quantity, não muda quantity
            $reservedAfter = $reservedBefore + abs($quantityChange);
            $quantityAfter = $quantityBefore; // Não muda
        } elseif ($actionType === 'liberacao') {
            // Liberação diminui reserved_quantity, não muda quantity
            $reservedAfter = max(0, $reservedBefore - abs($quantityChange));
            $quantityAfter = $quantityBefore; // Não muda
        } elseif ($actionType === 'transferencia') {
            // Transferência pode aumentar ou diminuir dependendo da direção
            $quantityAfter = $quantityBefore + $quantityChange;
        } elseif ($actionType === 'edicao') {
            // Edição: A movimentação é chamada APÓS o update no banco.
            // Logo, $quantityBefore (lido do banco) é na verdade a quantidade FINAL.
            // Precisamos inverter a lógica para registrar corretamente.
            $quantityAfter = $quantityBefore;
            $quantityBefore = $quantityAfter - $quantityChange;
        }

        return self::create([
            'stock_id' => $stock?->id,
            'store_id' => $stock?->store_id,
            'user_id' => $userId ?? auth()->id(),
            'order_id' => $orderId,
            'stock_request_id' => $stockRequestId,
            'action_type' => $actionType,
            'fabric_id' => $stock?->fabric_id,
            'color_id' => $stock?->color_id,
            'cut_type_id' => $stock?->cut_type_id,
            'size' => $stock?->size ?? '',
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'quantity_change' => $quantityChange,
            'reserved_quantity_before' => $reservedBefore,
            'reserved_quantity_after' => $reservedAfter,
            'notes' => $notes,
            'metadata' => $metadata,
            'action_date' => now(),
        ]);
    }
}
