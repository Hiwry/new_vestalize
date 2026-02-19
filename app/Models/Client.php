<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'store_id',
        'name',
        'phone_primary',
        'phone_secondary',
        'email',
        'cpf_cnpj',
        'address',
        'city',
        'state',
        'zip_code',
        'category',
    ];

    /**
     * Garantir isolamento por tenant via store_id
     */
    protected static function booted()
    {
        // Trait handle tenant isolation.
    }


    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope para buscar clientes por nome, telefone, email ou CPF/CNPJ
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone_primary', 'like', "%{$search}%")
                ->orWhere('phone_secondary', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('cpf_cnpj', 'like', "%{$search}%");
        });
    }

    public function scopeByStore($query, $storeIds)
    {
        if (empty($storeIds)) {
            return $query;
        }
        
        if (is_array($storeIds)) {
            return $query->whereIn('store_id', $storeIds);
        }
        
        return $query->where('store_id', $storeIds);
    }
}
