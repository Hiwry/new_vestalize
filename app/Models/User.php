<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\BelongsToTenant;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     * SEGURANÇA: 'role' e 'tenant_id' foram removidos para prevenir
     * escalação de privilégio via mass assignment.
     * Atribuir explicitamente: $user->role = 'vendedor'; $user->tenant_id = $tenantId;
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'store',
        'store_name',
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

    public function affiliate(): HasOne
    {
        return $this->hasOne(Affiliate::class);
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
        return $this->role === 'admin_loja' || 
               $this->stores()->wherePivot('role', 'admin_loja')->exists();
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
     * Verificar se é designer do marketplace
     */
    public function isDesigner(): bool
    {
        return $this->role === 'designer';
    }

    /**
     * Perfil de designer do marketplace
     */
    public function designerProfile(): HasOne
    {
        return $this->hasOne(\App\Models\Marketplace\DesignerProfile::class);
    }

    /**
     * Rótulo legível da função do usuário
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin', 'admin_geral' => 'Admin Geral',
            'admin_loja'           => 'Admin Loja',
            'vendedor'             => 'Vendedor',
            'caixa'                => 'Caixa',
            'producao'             => 'Produção',
            'estoque'              => 'Estoque',
            'designer'             => 'Designer',
            default                => ucfirst($this->role ?? 'Usuário'),
        };
    }

    /**
     * Classes Tailwind de cor para o badge da função
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'admin', 'admin_geral' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            'admin_loja'           => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
            'vendedor'             => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
            'caixa'                => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300',
            'producao'             => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
            'estoque'              => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
            'designer'             => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
            default                => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Obter IDs das lojas que o usuário pode acessar (incluindo sub-lojas)
     */
    public function getStoreIds(): array
    {
        // Lojas do tenant do usuário (ou do tenant selecionado caso seja super admin)
        $activeTenantId = $this->tenant_id;
        if ($activeTenantId === null) {
            $activeTenantId = session('selected_tenant_id');
        }

        $tenantStoreIds = $activeTenantId
            ? Store::active()->where('tenant_id', $activeTenantId)->pluck('id')->toArray()
            : ($this->tenant_id === null ? Store::active()->pluck('id')->toArray() : []);

        if ($this->isAdminGeral() || $this->isEstoque()) {
            return $tenantStoreIds;
        }

        if ($this->isAdminLoja()) {
            // Admin loja vê suas lojas + sub-lojas, respeitando o tenant
            $storeIds = [];
            
            // Buscar as lojas se não estiverem carregadas
            $stores = $this->stores;

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
