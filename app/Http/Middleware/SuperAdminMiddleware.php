<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Super Admin = user without tenant_id (global admin, not tied to any tenant)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        // Super Admin is a user without tenant_id (global admin)
        if (Auth::user()->tenant_id !== null) {
            return redirect()->route('kanban.index')->with('error', 'Acesso negado. Esta funcionalidade é exclusiva para Super Administradores.');
        }

        return $next($request);
    }
}
