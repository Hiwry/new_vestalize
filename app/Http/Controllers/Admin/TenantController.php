<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TenantController extends Controller
{
    /**
     * List all tenants.
     */
    public function index(): View
    {
        $tenants = Tenant::with(['affiliate', 'currentPlan'])
            ->withCount(['users', 'stores'])
            ->orderByRaw("CASE 
                WHEN status = 'suspended' THEN 1 
                WHEN (trial_ends_at < NOW() AND status = 'active') THEN 2
                WHEN (subscription_ends_at < NOW() AND status = 'active') THEN 3
                ELSE 4 
            END ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Resumo de afiliados
        $affiliates = Affiliate::withCount('tenants')
            ->orderBy('tenants_count', 'desc')
            ->get();

        return view('admin.tenants.index', compact('tenants', 'affiliates'));
    }

    /**
     * Show form to create a new tenant.
     */
    public function create(): View
    {
        $plans = \App\Models\Plan::all();
        return view('admin.tenants.create', compact('plans'));
    }

    /**
     * Store a new tenant.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // User email must be unique
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,suspended,cancelled',
            'affiliate_code' => 'nullable|string|max:8',
        ]);

        $validated['store_code'] = Tenant::generateStoreCode();
        
        // Verificar código de afiliado
        $affiliateId = null;
        if (!empty($validated['affiliate_code'])) {
            $affiliate = Affiliate::active()->byCode($validated['affiliate_code'])->first();
            if ($affiliate) {
                $affiliateId = $affiliate->id;
            }
        }
        unset($validated['affiliate_code']);
        
        $tenant = Tenant::create(array_merge($validated, [
            'subscription_ends_at' => now()->addMonth(),
            'affiliate_id' => $affiliateId,
        ]));

        // Create Admin User for Tenant
        $password = \Illuminate\Support\Str::password(10);
        
        $user = \App\Models\User::create([
            'name' => $tenant->name,
            'email' => $tenant->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'store_name' => 'Matriz',
        ]);
        // SEGURANÇA: role e tenant_id atribuídos explicitamente
        $user->role = 'admin';
        $user->tenant_id = $tenant->id;
        $user->save();

        // Seed Default Store for Tenant
        $store = \App\Models\Store::create([
            'tenant_id' => $tenant->id,
            'name' => 'Matriz',
            'is_main' => true,
            'is_active' => true,
        ]);

        // Seed Default Statuses for Tenant
        $defaultStatuses = [
            ['name' => 'Pendente', 'color' => '#f59e0b', 'position' => 1],
            ['name' => 'Em Produção', 'color' => '#3b82f6', 'position' => 2],
            ['name' => 'Concluído', 'color' => '#10b981', 'position' => 3],
            ['name' => 'Cancelado', 'color' => '#ef4444', 'position' => 4],
        ];

        foreach ($defaultStatuses as $ds) {
            \App\Models\Status::withoutGlobalScopes()->updateOrCreate(
                [
                    'name' => $ds['name'],
                    'tenant_id' => $tenant->id,
                    'type' => 'production',
                ],
                array_merge($ds, [
                    'tenant_id' => $tenant->id,
                    'type' => 'production',
                ])
            );
        }

        // Send Welcome Email
        try {
            \Illuminate\Support\Facades\Mail::to($tenant->email)->send(new \App\Mail\TenantWelcomeMail($tenant, $user, $password));
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::error('Erro ao enviar email de boas-vindas: ' . $e->getMessage());
            return redirect()->route('admin.tenants.index')
                ->with('success', 'Cliente criado com sucesso, mas houve um erro ao enviar o email. Senha temporária: ' . $password);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Cliente criado com sucesso! Email de acesso enviado.');
    }

    /**
     * Show form to edit tenant.
     */
    public function edit(Tenant $tenant): View
    {
        $plans = \App\Models\Plan::all();
        return view('admin.tenants.edit', compact('tenant', 'plans'));
    }

    /**
     * Update tenant details.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,suspended,cancelled',
            'trial_ends_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ]);

        $tenant->update(\Illuminate\Support\Arr::except($validated, ['password']));

        // Handle Password Update if provided
        $message = 'Dados da assinatura atualizados com sucesso.';
        if (!empty($request->password)) {
            $user = \App\Models\User::where('tenant_id', $tenant->id)->first();
            
            if ($user) {
                // Update User Password
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $user->save();
                $message .= ' Senha de acesso atualizada.';

                // Send Email if requested
                if ($request->boolean('send_password_email')) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($tenant->email)->send(new \App\Mail\TenantWelcomeMail($tenant, $user, $request->password));
                        $message .= ' Email enviado com a nova senha.';
                    } catch (\Exception $e) {
                         \Illuminate\Support\Facades\Log::error('Erro ao enviar email de atualização de senha: ' . $e->getMessage());
                         $message .= ' (Erro ao enviar email: ' . $e->getMessage() . ')';
                    }
                }
            } else {
                 $message .= ' (Aviso: Usuário admin não encontrado para atualizar a senha)';
            }
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', $message);
    }

    /**
     * Simulate sending access email (Resend).
     */
    public function resendAccess(Tenant $tenant): RedirectResponse
    {
        \Illuminate\Support\Facades\Log::info("ResendAccess: Iniciando para tenant {$tenant->id}");
        
        // Buscar qualquer usuário admin do tenant
        $user = \App\Models\User::where('tenant_id', $tenant->id)->first();
        
        \Illuminate\Support\Facades\Log::info("ResendAccess: User encontrado? " . ($user ? 'Sim - ' . $user->email : 'Não'));

        $password = \Illuminate\Support\Str::password(10);

        if (!$user) {
            // Criar usuário se não existir
            \Illuminate\Support\Facades\Log::info("ResendAccess: Criando novo usuário para tenant {$tenant->id}");
            $user = \App\Models\User::create([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'store_name' => 'Matriz',
            ]);
            // SEGURANÇA: role e tenant_id atribuídos explicitamente
            $user->role = 'admin';
            $user->tenant_id = $tenant->id;
            $user->save();
        } else {
            // Apenas redefinir senha
            $user->password = \Illuminate\Support\Facades\Hash::make($password);
            $user->save();
        }

        try {
            \Illuminate\Support\Facades\Log::info("ResendAccess: Sending email to {$tenant->email}");
            \Illuminate\Support\Facades\Mail::to($tenant->email)->send(new \App\Mail\TenantWelcomeMail($tenant, $user, $password));
            \Illuminate\Support\Facades\Log::info("ResendAccess: Email sent successfully");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ResendAccess: Error sending email: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
        
        return redirect()->back()
            ->with('success', 'Credenciais redefinidas e enviadas para ' . $tenant->email);
    }
}
