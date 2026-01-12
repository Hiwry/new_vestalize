<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use App\Models\Store;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PublicRegistrationController extends Controller
{
    /**
     * Mostrar formulário de registro
     */
    public function show()
    {
        $plans = Plan::all();
        return view('auth.registro', compact('plans'));
    }

    /**
     * Processar o registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'plan_id' => 'required|exists:plans,id',
        ]);

        // 1. Criar o Tenant com o Plano selecionado pelo usuário e 7 Dias de Teste
        $planId = $request->plan_id;

        $tenant = Tenant::create([
            'name' => $request->company_name,
            'email' => $request->email,
            'plan_id' => $planId,
            'store_code' => Tenant::generateStoreCode(),
            'status' => 'active',
            'trial_ends_at' => now()->addDays(7),
            'subscription_ends_at' => null, // Trial apenas
        ]);

        // 2. Criar Loja Principal Padrão
        $store = Store::create([
            'name' => 'Loja Principal',
            'tenant_id' => $tenant->id,
            'is_main' => true,
            'active' => true,
        ]);

        // 3. Criar Status Padrão
        $defaultStatuses = [
            ['name' => 'Pendente', 'color' => '#f59e0b', 'position' => 1],
            ['name' => 'Em Produção', 'color' => '#3b82f6', 'position' => 2],
            ['name' => 'Concluído', 'color' => '#10b981', 'position' => 3],
            ['name' => 'Cancelado', 'color' => '#ef4444', 'position' => 4],
        ];

        foreach ($defaultStatuses as $statusData) {
            Status::create(array_merge($statusData, ['tenant_id' => $tenant->id]));
        }

        // 4. Criar o Usuário Administrador
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // 5. Notificar o Super Admin
        try {
            $adminEmail = config('mail.admin_email', 'hiwry@hotmail.com'); // Usando fallback se não configurado
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\AdminNewTenantNotification($tenant, $user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao notificar admin sobre novo registro: ' . $e->getMessage());
        }

        // 6. Login automático
        Auth::login($user);

        // Associar user à loja principal em store_user table (pivot)
        $user->stores()->attach($store->id, ['role' => 'admin_loja']);

        return redirect()->route('dashboard')->with('success', 'Bem-vindo! Sua conta foi criada e seu teste de 7 dias já está ativo.');
    }
}
