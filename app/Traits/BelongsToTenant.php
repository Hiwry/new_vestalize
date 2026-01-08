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
        // Global scope to filter by tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Prevent scope from running in console (artisan commands, migrations)
            if (app()->runningInConsole()) {
                return;
            }

            // Safer check to avoid infinite recursion (User -> scope -> User)
            // Use hasUser() to check if user is already loaded. If not (e.g. during login), 
            // we skip the scope to allow the user to be retrieved safely without looping.
            if (app()->bound('auth') && Auth::guard()->hasUser()) {
                $user = Auth::user();
                
                if ($user && isset($user->tenant_id) && $user->tenant_id !== null) {
                    $builder->where($builder->getQuery()->from . '.tenant_id', $user->tenant_id);
                }
            }
        });

        // Automatically assign tenant_id when creating
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id !== null) {
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
