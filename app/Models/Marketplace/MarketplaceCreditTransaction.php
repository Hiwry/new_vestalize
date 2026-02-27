<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceCreditTransaction extends Model
{
    protected $table = 'marketplace_credit_transactions';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'payment_method',
        'payment_id',
        'payment_amount',
        'subscriber_discount_applied',
    ];

    protected $casts = [
        'payment_amount'               => 'decimal:2',
        'subscriber_discount_applied'  => 'boolean',
    ];

    public static array $typeLabels = [
        'purchase' => 'Compra de Créditos',
        'spend'    => 'Uso de Créditos',
        'refund'   => 'Reembolso',
        'earn'     => 'Créditos Ganhos',
        'bonus'    => 'Bônus',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }
}
