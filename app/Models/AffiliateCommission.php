<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    protected $fillable = [
        'affiliate_id',
        'tenant_id',
        'subscription_payment_id',
        'payment_amount',
        'rate',
        'amount',
        'status',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Afiliado dono desta comissão
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Tenant que gerou esta comissão
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Pagamento que originou esta comissão
     */
    public function subscriptionPayment(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPayment::class);
    }

    /**
     * Aprova a comissão
     */
    public function approve(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Marca como paga
     */
    public function markAsPaid(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Atualiza saldos do afiliado
        $this->affiliate->decrement('pending_balance', $this->amount);
        $this->affiliate->increment('withdrawn_balance', $this->amount);

        return true;
    }

    /**
     * Cancela a comissão
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['paid', 'cancelled'])) {
            return false;
        }

        $previousStatus = $this->status;

        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);

        // Remove do saldo pendente se estava pendente ou aprovada
        if (in_array($previousStatus, ['pending', 'approved'])) {
            $this->affiliate->decrement('pending_balance', $this->amount);
            $this->affiliate->decrement('total_earnings', $this->amount);
        }

        return true;
    }

    /**
     * Scope para comissões pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para comissões aprovadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope para comissões pagas
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
