<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip security headers for storage files (images, etc.)
        // Verificar tanto a URI quanto a rota nomeada
        $isStorageRequest = $request->is('storage/*') || 
            $request->routeIs('storage.serve') || 
            $request->routeIs('storage.local') ||
            $request->path() === 'storage' ||
            str_starts_with($request->path(), 'storage/');
            
        if ($isStorageRequest) {
            $response = $next($request);
            // Garantir que não aplicamos headers restritivos
            $response->headers->remove('X-Frame-Options');
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('X-Content-Type-Options');
            return $response;
        }

        $response = $next($request);

        // Headers de segurança básicos
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Strict-Transport-Security (apenas em HTTPS)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy
        $csp = $this->buildCSP();
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Build Content Security Policy header
     */
    private function buildCSP(): string
    {
        $isProduction = app()->environment('production');
        
        $directives = [
            "default-src 'self'",
            // Stripe precisa estar liberado para o checkout funcionar
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' 'wasm-unsafe-eval' 'inline-speculation-rules' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.bunny.net https://js.stripe.com https://unpkg.com http://localhost:5173 http://[::1]:5173 https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/",
            "script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://js.stripe.com https://unpkg.com http://localhost:5173 http://[::1]:5173 https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://unpkg.com http://localhost:5173 http://[::1]:5173",
            "font-src 'self' https://fonts.bunny.net https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com data: http://localhost:5173 http://[::1]:5173",
            "img-src 'self' data: https: blob: http://127.0.0.1:* http://localhost:* http://[::1]:*",
            // Stripe endpoints para requisicoes do JS
            "connect-src 'self' http://localhost:* http://127.0.0.1:* http://[::1]:* ws://localhost:* ws://127.0.0.1:* ws://[::1]:* https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://js.stripe.com https://api.stripe.com https://m.stripe.network https://q.stripe.com https://unpkg.com",
            "frame-src 'self' https://js.stripe.com https://www.google.com/recaptcha/ https://recaptcha.google.com/",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ];
        
        // Apenas adicionar upgrade-insecure-requests em produção
        if ($isProduction) {
            $directives[] = "upgrade-insecure-requests";
        }

        return implode('; ', $directives);
    }
}

