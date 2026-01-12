<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantContextController extends Controller
{
    /**
     * Define o tenant atual na sessão para o Super Admin
     */
    public function setContext(Request $request)
    {
        $user = Auth::user();
        
        if ($user->tenant_id !== null) {
            abort(403, 'Apenas Super Admins podem alternar o contexto de tenant.');
        }

        $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id'
        ]);

        if ($request->tenant_id) {
            session(['selected_tenant_id' => $request->tenant_id]);
            $tenant = Tenant::find($request->tenant_id);
            return redirect()->back()->with('success', "Contexto alterado para: {$tenant->name}");
        }

        session()->forget('selected_tenant_id');
        return redirect()->back()->with('success', 'Contexto de Super Admin restaurado (visão global vazia).');
    }
}
