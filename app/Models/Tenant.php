<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'store_code',
        'email',
        'phone',
        'plan_id',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'stripe_id',
        'logo_path',
        'primary_color',
        'secondary_color',
        'sublimation_total_enabled',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'sublimation_total_enabled' => 'boolean',
    ];

    /**
     * Constantes para os planos
     */
    const PLAN_START = 'start';
    const PLAN_BASIC = 'basic';
    const PLAN_PRO = 'pro';
    const PLAN_PREMIUM = 'premium';

    /**
     * Limites por plano
     */
    /**
     * Limites por plano
     * Basic: R$ 200 - 2 users - 1 store - Core features
     * Pro (Médio): R$ 300 - 5 users - 1 store - Productivity (PDV, Kanban, PDF)
     * Premium (Avançado): R$ 500 - Unlimited users - Multi-store - Full (Stock, BI)
     */
    /**
     * Relationship with Plan
     */
    public function currentPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Lojas pertencentes a este tenant
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    /**
     * Usuários pertencentes a este tenant
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Gerar código único de loja
     */
    public static function generateStoreCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('store_code', $code)->exists());

        return $code;
    }

    /**
     * Obter limites do plano atual
     */
    public function getPlanLimits(): array
    {
        return $this->currentPlan ? $this->currentPlan->limits : ['users' => 1, 'stores' => 1];
    }

    /**
     * Verificar se pode adicionar mais lojas
     */
    public function canAddStore(): bool
    {
        $limit = $this->getPlanLimits()['stores'];
        return $this->stores()->count() < $limit;
    }

    /**
     * Verificar se pode adicionar mais usuários
     */
    public function canAddUser(): bool
    {
        $limit = $this->getPlanLimits()['users'];
        return $this->users()->count() < $limit;
    }

    /**
     * Verificar se tem acesso a uma funcionalidade
     */
    public function canAccess(string $feature): bool
    {
        if (!$this->currentPlan) {
             // Fallback to legacy check if plan_id is missing but plan string exists?
             // Or just return false. Let's return false for now to enforce migration.
             return false;
        }
        
        $features = $this->currentPlan->features ?? [];
        
        // Plano premium tem acesso total
        if (in_array('*', $features)) {
            return true;
        }

        return in_array($feature, $features);
    }

    /**
     * Verificar se a assinatura está ativa
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Verificar se está em período de trial
        if ($this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        // Verificar se a assinatura está válida
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Obter nome do plano formatado
     */
    public function getPlanNameAttribute(): string
    {
        return $this->currentPlan ? $this->currentPlan->name . ' (R$ ' . number_format($this->currentPlan->price, 2, ',', '.') . '/mês)' : 'Sem plano';
    }

    /**
     * Scope para tenants ativos
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
        return $query->where('store_code', strtoupper($code));
    }
}
