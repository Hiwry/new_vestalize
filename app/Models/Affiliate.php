<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'code',
        'commission_rate',
        'status',
        'bank_info',
        'total_earnings',
        'pending_balance',
        'withdrawn_balance',
    ];

    protected $casts = [
        'bank_info' => 'array',
        'commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'withdrawn_balance' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->code)) {
                $affiliate->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Gera um código único de 8 caracteres
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Relacionamento com User (opcional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tenants indicados por este afiliado
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Comissões deste afiliado
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    /**
     * Cliques do link de indicação
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    /**
     * Calcula a comissão para um pagamento
     */
    public function calculateCommission(float $paymentAmount, ?float $customRate = null): float
    {
        $rate = $customRate ?? $this->commission_rate;
        return round($paymentAmount * ($rate / 100), 2);
    }

    /**
     * Registra uma nova comissão
     */
    public function registerCommission(Tenant $tenant, SubscriptionPayment $payment): AffiliateCommission
    {
        $amount = $this->calculateCommission($payment->amount);

        $commission = $this->commissions()->create([
            'tenant_id' => $tenant->id,
            'subscription_payment_id' => $payment->id,
            'payment_amount' => $payment->amount,
            'rate' => $this->commission_rate,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Atualiza saldo pendente
        $this->increment('pending_balance', $amount);
        $this->increment('total_earnings', $amount);

        return $commission;
    }

    /**
     * Scope para afiliados ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para buscar por código
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    /**
     * Verifica se o afiliado está ativo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Quantidade de indicações com pagamento confirmado
     */
    public function paidReferralsCount(): int
    {
        return $this->tenants()
            ->whereHas('subscriptionPayments', function ($query) {
                $query->whereNotNull('paid_at')
                    ->orWhereIn('status', ['succeeded', 'approved', 'paid']);
            })
            ->count();
    }

    /**
     * Quantidade de indicações ativas
     */
    public function getActiveReferralsCountAttribute(): int
    {
        return $this->paidReferralsCount();
    }
}
