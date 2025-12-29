<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricPiece extends Model
{
    protected $fillable = [
        'store_id',
        'fabric_id',
        'fabric_type_id',
        'color_id',
        'supplier',
        'invoice_number',
        'invoice_key',
        'weight',
        'weight_current',
        'meters',
        'barcode',
        'shelf',
        'origin',
        'destination',
        'origin_store_id',
        'destination_store_id',
        'between_stores',
        'status',
        'received_at',
        'opened_at',
        'sold_at',
        'purchase_price',
        'sale_price',
        'order_id',
        'sold_by',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'weight_current' => 'decimal:3',
        'meters' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'received_at' => 'datetime',
        'opened_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    protected $appends = ['status_label', 'status_color'];

    /**
     * Status labels em português
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'fechada' => 'Fechada',
            'aberta' => 'Aberta',
            'vendida' => 'Vendida',
            'em_transferencia' => 'Em Transferência',
            default => $this->status,
        };
    }

    /**
     * Cores do status para badges
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'fechada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'aberta' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'vendida' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'em_transferencia' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Relacionamentos

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function fabric(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_id');
    }

    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_type_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'color_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(FabricPieceTransfer::class);
    }

    // Métodos de ação

    /**
     * Marcar peça como aberta
     */
    public function markAsOpened(?float $currentWeight = null): bool
    {
        $this->update([
            'status' => 'aberta',
            'opened_at' => now(),
            'weight_current' => $currentWeight ?? $this->weight,
        ]);

        return true;
    }

    /**
     * Registrar uma venda (total ou parcial) da peça
     */
    public function recordSale(float $soldWeight, ?int $orderId = null, ?int $soldBy = null): bool
    {
        $newWeight = max(0, $this->weight_current - $soldWeight);
        
        $data = [
            'weight_current' => $newWeight,
            'sold_at' => now(),
            'sold_by' => $soldBy ?? auth()->id(),
        ];

        // Se o peso zerou, marca como vendida
        if ($newWeight <= 0.001) {
            $data['status'] = 'vendida';
            $data['order_id'] = $orderId; // Salva o order_id apenas se a peça foi "finalizada"
        } else {
            // Se ainda tem peso, garante que está "aberta"
            $data['status'] = 'aberta';
        }

        return $this->update($data);
    }

    /**
     * Marcar peça como vendida (Legado/Total)
     */
    public function markAsSold(?int $orderId = null, ?int $soldBy = null): bool
    {
        return $this->update([
            'status' => 'vendida',
            'sold_at' => now(),
            'order_id' => $orderId,
            'sold_by' => $soldBy ?? auth()->id(),
            'weight_current' => 0, // Zera o peso ao marcar como vendida
        ]);
    }

    /**
     * Iniciar transferência para outra loja
     */
    public function startTransfer(int $toStoreId, ?string $notes = null): FabricPieceTransfer
    {
        $this->update(['status' => 'em_transferencia']);

        return FabricPieceTransfer::create([
            'fabric_piece_id' => $this->id,
            'from_store_id' => $this->store_id,
            'to_store_id' => $toStoreId,
            'requested_by' => auth()->id(),
            'status' => 'pendente',
            'requested_at' => now(),
            'request_notes' => $notes,
        ]);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['fechada', 'aberta']);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
