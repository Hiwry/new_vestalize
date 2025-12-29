<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Order;
use App\Observers\OrderObserver;

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
        // Registrar Observer para liberar estoque ao cancelar pedido
        Order::observe(OrderObserver::class);
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        if ($this->app->environment('production') && config('app.debug')) {
            \Log::warning('APP_DEBUG está habilitado em produção!');
        }
    }
}
