<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'cache' => \App\Http\Middleware\CacheMiddleware::class,
            'cash' => \App\Http\Middleware\CashMiddleware::class,
            'plan' => \App\Http\Middleware\CheckPlanLimits::class,
        ]);

        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        $middleware->validateCsrfTokens(except: [
            'logout',
            'stripe/*',
        ]);

        $middleware->removeFromGroup('api', \Illuminate\Routing\Middleware\SubstituteBindings::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
