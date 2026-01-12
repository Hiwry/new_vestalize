<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Observers\AuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Observers para Pedidos e Auditoria Global
        Order::observe(OrderObserver::class);
        Order::observe(AuditObserver::class);
        
        \App\Models\Client::observe(AuditObserver::class);
        \App\Models\Payment::observe(AuditObserver::class);
        \App\Models\CashTransaction::observe(AuditObserver::class);
        \App\Models\User::observe(AuditObserver::class);
        \App\Models\Store::observe(AuditObserver::class);
        \App\Models\Tenant::observe(AuditObserver::class);
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        if ($this->app->environment('production') && config('app.debug')) {
            \Log::warning('APP_DEBUG está habilitado em produção!');
        }

        // --- RATE LIMITING ---
        
        // Limite para login (5 tentativas por minuto por IP/Email)
        RateLimiter::for('login', function (Request $request) {
            $key = $request->input('email') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json([
                    'message' => 'Muitas tentativas de login. Tente novamente em 1 minuto.'
                ], 429);
            });
        });

        // Limite para API Global (60 requisições por minuto)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Limite para Busca Rápida (mais permissivo)
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}
