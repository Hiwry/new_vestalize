<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Helpers\StoreHelper;

class StoreMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifica se o usuário tem acesso à loja solicitada
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Se houver store_id na requisição, verificar acesso
        $storeId = $request->route('store') ?? $request->input('store_id');
        
        if ($storeId && !StoreHelper::canAccessStore($storeId)) {
            abort(403, 'Você não tem permissão para acessar esta loja.');
        }

        return $next($request);
    }
}
