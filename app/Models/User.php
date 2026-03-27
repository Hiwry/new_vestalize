<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     * SEGURANCA: 'role' e 'tenant_id' foram removidos para prevenir
     * escalacao de privilegio via mass assignment.
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
     * Tenant proprietario deste usuario
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
     * Lojas que o usuario administra
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Verificar se e admin geral
     */
    public function isAdminGeral(): bool
    {
        return $this->role === 'admin_geral' || $this->role === 'admin';
    }

    /**
     * Verificar se e admin de loja
     */
    public function isAdminLoja(): bool
    {
        return $this->role === 'admin_loja'
            || $this->stores()->wherePivot('role', 'admin_loja')->exists();
    }

    /**
     * Verificar se e admin (geral ou loja)
     */
    public function isAdmin(): bool
    {
        return $this->isAdminGeral() || $this->isAdminLoja() || $this->role === 'admin';
    }

    /**
     * Verificar se e vendedor
     */
    public function isVendedor(): bool
    {
        return $this->role === 'vendedor';
    }

    /**
     * Verificar se e usuario de producao
     */
    public function isProducao(): bool
    {
        return $this->role === 'producao';
    }

    /**
     * Verificar se e usuario de caixa
     */
    public function isCaixa(): bool
    {
        return $this->role === 'caixa';
    }

    /**
     * Verificar se e usuario de estoque
     */
    public function isEstoque(): bool
    {
        return $this->role === 'estoque';
    }

    /**
     * Verificar se e designer do marketplace
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
     * Rotulo legivel da funcao do usuario
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin', 'admin_geral' => 'Admin Geral',
            'admin_loja' => 'Admin Loja',
            'vendedor' => 'Vendedor',
            'caixa' => 'Caixa',
            'producao' => 'Producao',
            'estoque' => 'Estoque',
            'designer' => 'Designer',
            default => ucfirst($this->role ?? 'Usuario'),
        };
    }

    /**
     * Classes Tailwind de cor para o badge da funcao
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'admin', 'admin_geral' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            'admin_loja' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
            'vendedor' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
            'caixa' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300',
            'producao' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
            'estoque' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
            'designer' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Obter o tenant ativo no contexto atual.
     */
    public function getActiveTenantId(): ?int
    {
        return $this->tenant_id ?? session('selected_tenant_id');
    }

    /**
     * Verificar se o usuario possui lojas vinculadas explicitamente.
     */
    public function hasExplicitStoreAssignments(): bool
    {
        if ($this->relationLoaded('stores')) {
            return $this->stores->isNotEmpty();
        }

        return $this->stores()->exists();
    }

    /**
     * Verificar se o usuario deve acessar todas as lojas do tenant.
     */
    public function hasGeneralStoreAccess(): bool
    {
        if ($this->isAdminGeral()) {
            return true;
        }

        if ($this->isAdminLoja() || $this->isCaixa() || $this->isEstoque()) {
            return $this->getActiveTenantId() !== null && !$this->hasExplicitStoreAssignments();
        }

        return false;
    }

    /**
     * Obter IDs das lojas que o usuario pode acessar.
     */
    public function getStoreIds(): array
    {
        $activeTenantId = $this->getActiveTenantId();

        if ($activeTenantId === null && !$this->isAdminGeral()) {
            return [];
        }

        $tenantStoreIds = $activeTenantId !== null
            ? Store::active()->where('tenant_id', $activeTenantId)->pluck('id')->toArray()
            : ($this->tenant_id === null ? Store::active()->pluck('id')->toArray() : []);

        if (empty($tenantStoreIds)) {
            return [];
        }

        if ($this->hasGeneralStoreAccess()) {
            return array_map('intval', array_values(array_unique($tenantStoreIds)));
        }

        if (!$this->isAdminLoja() && !$this->isCaixa() && !$this->isEstoque()) {
            return [];
        }

        $storeIds = [];
        $stores = $this->relationLoaded('stores')
            ? $this->stores
            : $this->stores()->with('subStores')->get();

        foreach ($stores as $store) {
            if ($activeTenantId !== null && (int) $store->tenant_id !== (int) $activeTenantId) {
                continue;
            }

            if ($this->isAdminLoja()) {
                $storeIds = array_merge($storeIds, $store->getAllStoreIds());
                continue;
            }

            $storeIds[] = $store->id;
        }

        $storeIds = array_intersect(array_unique($storeIds), $tenantStoreIds);

        return array_map('intval', array_values($storeIds));
    }

    /**
     * Verificar se o usuario pode acessar uma loja especifica.
     */
    public function canAccessStore($storeId): bool
    {
        $store = Store::withoutGlobalScopes()->find($storeId);

        if (!$store) {
            return false;
        }

        $activeTenantId = $this->getActiveTenantId();
        if ($activeTenantId !== null && (int) $store->tenant_id !== (int) $activeTenantId) {
            return false;
        }

        if ($this->hasGeneralStoreAccess()) {
            return true;
        }

        if ($this->isAdminLoja() || $this->isCaixa() || $this->isEstoque()) {
            return in_array((int) $storeId, $this->getStoreIds(), true);
        }

        return false;
    }
}
