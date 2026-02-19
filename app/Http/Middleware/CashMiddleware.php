<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CashMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifica se o usuário é admin ou caixa
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar.');
        }

        return $next($request);
    }
}

