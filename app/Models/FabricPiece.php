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
        'meters_current',
        'control_unit',
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
        'min_quantity_alert',
        'available_in_pdv',
        'available_in_catalog',
        'available_in_orders',
        'order_id',
        'sold_by',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'weight_current' => 'decimal:3',
        'meters' => 'decimal:2',
        'meters_current' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_quantity_alert' => 'decimal:3',
        'available_in_pdv' => 'boolean',
        'available_in_catalog' => 'boolean',
        'available_in_orders' => 'boolean',
        'received_at' => 'datetime',
        'opened_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'available_quantity',
        'initial_quantity',
        'control_unit_label',
        'display_name',
        'is_below_alert',
    ];

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

    public function getAvailableQuantityAttribute(): float
    {
        if ($this->control_unit === 'metros') {
            return (float) ($this->meters_current ?? $this->meters ?? 0);
        }

        return (float) ($this->weight_current ?? $this->weight ?? 0);
    }

    public function getInitialQuantityAttribute(): float
    {
        if ($this->control_unit === 'metros') {
            return (float) ($this->meters ?? 0);
        }

        return (float) ($this->weight ?? 0);
    }

    public function getControlUnitLabelAttribute(): string
    {
        return $this->control_unit === 'metros' ? 'Metros' : 'Kg';
    }

    public function getDisplayNameAttribute(): string
    {
        $type = $this->fabricType?->name;
        $fabric = $this->fabric?->name;
        
        $name = ($type && $fabric && $type !== $fabric) 
            ? "{$type} ({$fabric})" 
            : ($type ?: ($fabric ?: 'Tecido'));

        $color = $this->color?->name ? ' - ' . $this->color->name : '';
        $reference = $this->invoice_number ?: ('Peça #' . $this->id);

        return trim($reference . ' - ' . $name . $color);
    }

    public function getIsBelowAlertAttribute(): bool
    {
        if ((float) $this->min_quantity_alert <= 0) {
            return false;
        }

        if ($this->status === 'vendida') {
            return false;
        }

        return $this->available_quantity <= (float) $this->min_quantity_alert;
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
        if ($this->control_unit === 'metros') {
            $this->meters_current = $currentWeight ?? $this->meters;
        } else {
            $this->weight_current = $currentWeight ?? $this->weight;
        }

        $this->status = 'aberta';
        $this->opened_at = now();
        $this->save();

        return true;
    }

    /**
     * Registrar uma venda (total ou parcial) da peça
     */
    public function recordSale(float $soldWeight, ?int $orderId = null, ?int $soldBy = null): bool
    {
        $currentQuantity = $this->available_quantity;
        $newQuantity = max(0, $currentQuantity - $soldWeight);

        $data = [
            'sold_at' => now(),
            'sold_by' => $soldBy ?? auth()->id(),
        ];

        if ($this->control_unit === 'metros') {
            $data['meters_current'] = $newQuantity;
        } else {
            $data['weight_current'] = $newQuantity;
        }

        if ($newQuantity <= 0.001) {
            $data['status'] = 'vendida';
            $data['order_id'] = $orderId;
        } else {
            $data['status'] = 'aberta';
        }

        return $this->update($data);
    }

    /**
     * Marcar peça como vendida (Legado/Total)
     */
    public function markAsSold(?int $orderId = null, ?int $soldBy = null): bool
    {
        $data = [
            'status' => 'vendida',
            'sold_at' => now(),
            'order_id' => $orderId,
            'sold_by' => $soldBy ?? auth()->id(),
        ];

        if ($this->control_unit === 'metros') {
            $data['meters_current'] = 0;
        } else {
            $data['weight_current'] = 0;
        }

        return $this->update($data);
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

    public function scopeHasAvailableQuantity($query)
    {
        return $query->where(function ($builder) {
            $builder->where(function ($subQuery) {
                $subQuery->where('control_unit', 'kg')
                    ->whereRaw('COALESCE(weight_current, weight, 0) > 0');
            })->orWhere(function ($subQuery) {
                $subQuery->where('control_unit', 'metros')
                    ->whereRaw('COALESCE(meters_current, meters, 0) > 0');
            });
        });
    }

    public function scopeAvailableForChannel($query, string $channel)
    {
        $column = match ($channel) {
            'catalog' => 'available_in_catalog',
            'orders' => 'available_in_orders',
            default => 'available_in_pdv',
        };

        return $query->where($column, true);
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
