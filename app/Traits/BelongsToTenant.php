<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // Global scope to filter by tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Prevent scope from running in console (artisan commands, migrations)
            if (app()->runningInConsole()) {
                return;
            }

            // Skip scope for API routes without authentication to avoid tenant_id null errors
            // This allows public API endpoints to work without requiring authentication
            try {
                if (app()->bound('auth') && Auth::guard()->hasUser()) {
                    $user = Auth::user();
                    
                    // Only apply scope if user exists AND has a tenant_id
                    if ($user && $user->tenant_id !== null) {
                        $builder->where($builder->getQuery()->from . '.tenant_id', $user->tenant_id);
                    }
                }
            } catch (\Exception $e) {
                // If any error occurs during auth check, just skip the scope
                // This prevents breaking API routes that don't require authentication
                \Log::debug('BelongsToTenant scope skipped due to: ' . $e->getMessage());
            }
        });

        // Automatically assign tenant_id when creating
        static::creating(function ($model) {
            if (empty($model->tenant_id) && Auth::check() && Auth::user()->tenant_id !== null) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    /**
     * Relationship with Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
