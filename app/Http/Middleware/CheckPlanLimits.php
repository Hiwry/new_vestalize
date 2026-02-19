<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Features disponíveis por plano
     */
    protected array $planFeatures = [
        'basic' => ['pdv', 'orders', 'budgets', 'kanban'],
        'pro' => ['pdv', 'orders', 'budgets', 'kanban', 'stock'],
        'premium' => ['*'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Sessão expirada. Por favor, faça login novamente.');
        }

        // Se for Admin (Geral ou Loja) ou Produção, ignora limites de plano para garantir fluxo operacional
        if ($user->isAdminGeral() || $user->isAdminLoja() || $user->isProducao()) {
            return $next($request);
        }

        if (!$user->tenant) {
            return redirect()->route('login')
                ->with('error', 'Sessão expirada ou loja não configurada. Por favor, faça login novamente.');
        }

        $tenant = $user->tenant;

        // Verificar se a assinatura está ativa
        if (!$tenant->isActive()) {
            $expiredDate = $tenant->subscription_ends_at 
                ? $tenant->subscription_ends_at->format('d/m/Y') 
                : 'N/A';
            
            return redirect()->route('dashboard')
                ->with('error', "Sua assinatura expirou em {$expiredDate}. Renove para continuar acessando todas as funcionalidades.");
        }

        // Verificar se o recurso está disponível no plano
        if ($feature && !$tenant->canAccess($feature)) {
            $planName = $tenant->currentPlan ? $tenant->currentPlan->name : 'seu plano atual';

            return redirect()->back()
                ->with('error', "O recurso '{$feature}' não está disponível no plano {$planName}. Faça upgrade para ter acesso.");
        }

        return $next($request);
    }
}
