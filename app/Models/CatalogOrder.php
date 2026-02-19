<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CatalogOrder extends Model
{
    protected $fillable = [
        'tenant_id',
        'store_id',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_cpf',
        'items',
        'total_items',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'pricing_mode',
        'payment_method',
        'payment_status',
        'payment_gateway_id',
        'payment_data',
        'status',
        'notes',
        'admin_notes',
        'order_code',
        'delivery_address',
    ];

    protected $casts = [
        'items' => 'array',
        'payment_data' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Generate unique order code on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_code)) {
                $model->order_code = 'CAT-' . strtoupper(Str::random(8));
            }
        });
    }

    // ─── Relationships ───

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Linked internal order (after admin approval)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Helpers ───

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isWholesale(): bool
    {
        return $this->pricing_mode === 'atacado';
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }

    /**
     * Get status label in Portuguese
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'converted' => 'Convertido',
            'cancelled' => 'Cancelado',
            default => $this->status,
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Aguardando',
            'paid' => 'Pago',
            'failed' => 'Falhou',
            'refunded' => 'Reembolsado',
            default => $this->payment_status,
        };
    }

    // ─── Scopes ───

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }
}
