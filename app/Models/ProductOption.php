<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProductOption extends Model
{
    protected $fillable = [
        'tenant_id',
        'type',
        'name',
        'price',
        'cost',
        'parent_type',
        'parent_id',
        'active',
        'is_pinned',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'active' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    /**
     * Garantir isolamento por tenant
     */
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (!Schema::hasColumn('product_options', 'tenant_id')) {
                return;
            }

            $user = Auth::user();

            // Super admin (tenant_id null) deve ver apenas opÇõÇæes globais (tenant_id null)
            if (!$user || $user->tenant_id === null) {
                $builder->whereNull('tenant_id');
                return;
            }

            $builder->where('tenant_id', $user->tenant_id);
        });
    }

    public function children()
    {
        return $this->hasMany(ProductOption::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductOption::class, 'parent_id');
    }

    // Relacionamento muitos-para-muitos (múltiplos pais)
    public function parents()
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_option_relations',
            'option_id',
            'parent_id'
        )->withTimestamps();
    }

    // Relacionamento muitos-para-muitos (múltiplos filhos)
    public function relatedChildren()
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_option_relations',
            'parent_id',
            'option_id'
        )->withTimestamps();
    }
}
