<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    /**
     * Show subscription dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->route('kanban.index')->with('error', 'Você não está associado a nenhuma assinatura.');
        }
        
        $currentPlan = $tenant->currentPlan;
        $allPlans = Plan::orderBy('price')->get();
        $subscriptionPayments = SubscriptionPayment::with('plan')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        
        return view('subscription.index', compact('tenant', 'currentPlan', 'allPlans', 'subscriptionPayments'));
    }
    
    /**
     * Request upgrade to a different plan
     */
    public function requestUpgrade(Plan $plan)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant não encontrado.');
        }
        
        // Send email to admin (super admin email)
        try {
            $adminEmail = config('mail.admin_email', 'admin@vestalize.com');
            
            Mail::raw("
Solicitação de Upgrade de Plano

Empresa: {$tenant->name}
Email: {$tenant->email}
Código da Loja: {$tenant->store_code}

Plano Atual: " . ($tenant->currentPlan?->name ?? 'Nenhum') . "
Plano Solicitado: {$plan->name} (R$ " . number_format($plan->price, 2, ',', '.') . "/mês)

Por favor, entre em contato com o cliente para formalizar o upgrade.
            ", function ($message) use ($adminEmail, $tenant) {
                $message->to($adminEmail)
                       ->subject("Solicitação de Upgrade - {$tenant->name}");
            });
            
            return redirect()->back()->with('success', 'Sua solicitação de upgrade foi enviada! Entraremos em contato em breve.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao enviar solicitação: ' . $e->getMessage());
        }
    }
    
    /**
     * Request trial of a premium plan
     */
    public function requestTrial(Plan $plan)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant não encontrado.');
        }
        
        // Check if already on trial or has better plan
        if ($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
            return redirect()->back()->with('error', 'Você já está em período de teste.');
        }
        
        try {
            // Ativação automática do teste por 7 dias
            $tenant->update([
                'plan_id' => $plan->id,
                'trial_ends_at' => now()->addDays(7),
                'status' => 'active',
            ]);

            // Ainda enviar e-mail para o admin ficar sabendo
            $adminEmail = config('mail.admin_email', 'hiwry@hotmail.com');
            $tenantAdmin = $tenant->users()->where('role', 'admin')->first();
            
            try {
                Mail::to($adminEmail)->send(new \App\Mail\AdminNewTenantNotification($tenant, $tenantAdmin));
            } catch (\Exception $e) {
                \Log::error('Erro ao enviar e-mail de teste para admin: ' . $e->getMessage());
            }
            
            return redirect()->back()->with('success', 'Seu período de teste de 7 dias foi ativado com sucesso! Aproveite todos os recursos do plano ' . $plan->name . '.');
        } catch (\Exception $e) {
            \Log::error('Trial activation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao ativar período de teste. Nossa equipe já foi notificada.');
        }
    }
    
    /**
     * Request subscription renewal
     */
    public function renewRequest()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant não encontrado.');
        }
        
        try {
            $adminEmail = config('mail.admin_email', 'admin@vestalize.com');
            
            Mail::raw("
Solicitação de Renovação de Assinatura

Empresa: {$tenant->name}
Email: {$tenant->email}
Código da Loja: {$tenant->store_code}

Plano Atual: " . ($tenant->currentPlan?->name ?? 'Nenhum') . "
Vencimento Atual: " . ($tenant->subscription_ends_at?->format('d/m/Y') ?? 'N/A') . "

O cliente deseja renovar sua assinatura.
Por favor, entre em contato para formalizar a renovação.
            ", function ($message) use ($adminEmail, $tenant) {
                $message->to($adminEmail)
                       ->subject("Solicitação de Renovação - {$tenant->name}");
            });
            
            return redirect()->back()->with('success', 'Sua solicitação de renovação foi enviada! Entraremos em contato em breve.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao enviar solicitação: ' . $e->getMessage());
        }
    }
}
