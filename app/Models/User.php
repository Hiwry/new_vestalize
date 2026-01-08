<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToTenant;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'store',
        'store_name',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Tenant proprietário deste usuário
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Lojas que o usuário administra
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Verificar se é admin geral
     */
    public function isAdminGeral(): bool
    {
        return $this->role === 'admin_geral' || $this->role === 'admin';
    }

    /**
     * Verificar se é admin de loja
     */
    public function isAdminLoja(): bool
    {
        if ($this->role === 'admin_loja') {
            return true;
        }

        // CORRECTION 1: Never run query in boolean method
        // Only check if relation is eagerly loaded
        return $this->relationLoaded('stores')
            ? $this->stores->contains(fn ($s) => $s->pivot && $s->pivot->role === 'admin_loja')
            : false;
    }

    /**
     * Verificar se é admin (geral ou loja)
     */
    public function isAdmin(): bool
    {
        return $this->isAdminGeral() || $this->isAdminLoja() || $this->role === 'admin';
    }

    /**
     * Verificar se é vendedor
     */
    public function isVendedor(): bool
    {
        return $this->role === 'vendedor';
    }

    /**
     * Verificar se é usuário de produção
     */
    public function isProducao(): bool
    {
        return $this->role === 'producao';
    }

    /**
     * Verificar se é usuário de caixa
     */
    public function isCaixa(): bool
    {
        return $this->role === 'caixa';
    }

    /**
     * Verificar se é usuário de estoque
     */
    public function isEstoque(): bool
    {
        return $this->role === 'estoque';
    }

    /**
     * Obter IDs das lojas que o usuário pode acessar (incluindo sub-lojas)
     */
    public function getStoreIds(): array
    {
        // Lojas do tenant do usuário (ou todas caso seja super admin sem tenant)
        $tenantStoreIds = $this->tenant_id
            ? Store::active()->where('tenant_id', $this->tenant_id)->pluck('id')->toArray()
            : Store::active()->pluck('id')->toArray();

        if ($this->isAdminGeral() || $this->isEstoque()) {
            return $tenantStoreIds;
        }

        if ($this->isAdminLoja()) {
            // Admin loja vê suas lojas + sub-lojas, respeitando o tenant
            $storeIds = [];
            
            // CORRECTION 2: Avoid lazy load explosion
            $stores = $this->relationLoaded('stores') ? $this->stores : collect();

            foreach ($stores as $store) {
                if ($this->tenant_id === null || $store->tenant_id === $this->tenant_id) {
                    $storeIds = array_merge($storeIds, $store->getAllStoreIds());
                }
            }
            
            $storeIds = array_intersect(array_unique($storeIds), $tenantStoreIds);

            return array_values($storeIds);
        }

        // Vendedor não tem acesso por loja, apenas por user_id
        return [];
    }

    /**
     * Verificar se o usuário pode acessar uma loja específica
     */
    public function canAccessStore($storeId): bool
    {
        $store = Store::find($storeId);

        if (!$store || ($this->tenant_id !== null && $store->tenant_id !== $this->tenant_id)) {
            return false;
        }

        if ($this->isAdminGeral() || $this->isEstoque()) {
            return true;
        }

        if ($this->isAdminLoja()) {
            $storeIds = $this->getStoreIds();
            return in_array($storeId, $storeIds);
        }

        return false;
    }
}
