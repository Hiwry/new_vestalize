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
        'apply_size_surcharge',
        'order',
        'models',
        'models_surcharge_disabled',
        'collars',
    ];

    protected $casts = [
        'active' => 'boolean',
        'apply_size_surcharge' => 'boolean',
        'models' => 'array',
        'models_surcharge_disabled' => 'array',
        'collars' => 'array',
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

    public static function getScopedBySlug(?int $tenantId, ?string $slug)
    {
        return static::where('slug', $slug)
            ->where(function ($query) use ($tenantId) {
                $query->whereNull('tenant_id');

                if ($tenantId) {
                    $query->orWhere('tenant_id', $tenantId);
                }
            })
            ->orderByRaw(
                'CASE
                    WHEN tenant_id = ? THEN 0
                    WHEN tenant_id IS NULL THEN 1
                    ELSE 2
                END',
                [$tenantId]
            )
            ->get();
    }

    public static function getEffectiveModelsForSlug(?int $tenantId, ?string $slug, array $fallback = ['BASICA', 'BABYLOOK', 'INFANTIL']): array
    {
        $models = static::getScopedBySlug($tenantId, $slug)
            ->pluck('models')
            ->filter(fn ($models) => is_array($models) && !empty($models))
            ->map(function ($models) {
                return collect($models)
                    ->map(function ($model) {
                        $model = trim((string) $model);

                        return function_exists('mb_strtoupper')
                            ? mb_strtoupper($model, 'UTF-8')
                            : strtoupper($model);
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            })
            ->first();

        return !empty($models) ? $models : $fallback;
    }

    public static function getEffectiveCollarsForSlug(?int $tenantId, ?string $slug): array
    {
        $collars = static::getScopedBySlug($tenantId, $slug)
            ->pluck('collars')
            ->filter(fn ($collars) => is_array($collars) && !empty($collars))
            ->map(function ($collars) {
                return collect($collars)
                    ->map(function ($collar) {
                        $collar = trim((string) $collar);

                        return function_exists('mb_strtoupper')
                            ? mb_strtoupper($collar, 'UTF-8')
                            : strtoupper($collar);
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            })
            ->first();

        return !empty($collars) ? $collars : [];
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
