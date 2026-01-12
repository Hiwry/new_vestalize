<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SublimationProductType extends Model
{
    protected $fillable = [
        'tenant_id',
        'tecido_id',
        'slug',
        'name',
        'active',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Gerar slug automaticamente
     */
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Buscar tipos disponíveis para um tenant
     * Combina tipos globais (tenant_id = null) com tipos do tenant específico
     */
    public static function getForTenant(?int $tenantId)
    {
        return static::where(function($query) use ($tenantId) {
            $query->whereNull('tenant_id')
                  ->orWhere('tenant_id', $tenantId);
        })
        ->where('active', true)
        ->orderBy('order')
        ->get();
    }

    /**
     * Ícone SVG padrão
     */
    public function getIconAttribute(): string
    {
        return '<svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>';
    }

    /**
     * Relacionamento com tecido padrão
     */
    public function tecido()
    {
        return $this->belongsTo(Tecido::class, 'tecido_id');
    }
}
