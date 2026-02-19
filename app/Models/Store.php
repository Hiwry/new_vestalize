<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BelongsToTenant;

class Store extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'name',
        'tenant_id',
        'parent_id',
        'is_main',
        'active',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Tenant proprietário desta loja
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Loja pai (apenas um nível)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'parent_id');
    }

    /**
     * Sub-lojas (apenas um nível)
     */
    public function subStores(): HasMany
    {
        return $this->hasMany(Store::class, 'parent_id');
    }

    /**
     * Usuários administradores desta loja
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Pedidos desta loja
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Orçamentos desta loja
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Clientes desta loja
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Transações de caixa desta loja
     */
    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    /**
     * Configurações da empresa desta loja
     */
    public function companySettings(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    /**
     * Verificar se é a loja principal
     */
    public function isMain(): bool
    {
        return $this->is_main === true;
    }

    /**
     * Obter todas as sub-lojas (incluindo esta)
     */
    public function getSubStores()
    {
        return $this->subStores()->get();
    }

    /**
     * Obter IDs de todas as lojas (esta + sub-lojas)
     */
    public function getAllStoreIds(): array
    {
        $ids = [$this->id];
        
        // Carregar sub-lojas se não estiverem carregadas
        if (!$this->relationLoaded('subStores')) {
            $this->load('subStores');
        }
        
        foreach ($this->subStores as $subStore) {
            $ids[] = $subStore->id;
        }
        
        return $ids;
    }

    /**
     * Scope para lojas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para loja principal
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Scope para sub-lojas
     */
    public function scopeSubStores($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
