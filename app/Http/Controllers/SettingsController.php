<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Página de configurações organizadas por categoria
     */
    public function index(Request $request): View
    {
        $category = $request->get('category', 'geral'); // geral, vendas, administracao, etc.

        return view('settings.index', compact('category'));
    }

    public function personalizations(): View
    {
        return view('settings.personalizations');
    }

    /**
     * Página de configurações da empresa (Dados, Marca, Termos)
     */
    public function company(): View
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        // Se usuário não tem tenant (super admin global sem tenant?), cria objeto vazio ou trata erro
        if (!$tenant) {
            abort(403, 'Usuário não vinculado a uma empresa.');
        }

        // Buscar configurações da loja principal do tenant (assumindo single store por enquanto ou pegando a principal)
        // O método getSettings já lida com a busca
        $store = $tenant->stores()->where('is_main', true)->first();
        $storeId = $store ? $store->id : null;
        $settings = CompanySetting::getSettings($storeId);

        return view('settings.company', compact('tenant', 'settings'));
    }

    /**
     * Atualizar configurações da empresa
     */
    public function updateCompany(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Ação não permitida.');
        }

        $validated = $request->validate([
            // Dados da Empresa
            'company_name' => 'nullable|string|max:255',
            'company_cnpj' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:255',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:2',
            'company_zip' => 'nullable|string|max:10',
            'company_website' => 'nullable|url|max:255',
            
            // Marca
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'primary_color_light' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'primary_color_dark' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color_light' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color_dark' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            
            // Textos
            'terms_conditions' => 'nullable|string',
        ]);

        // 1. Atualizar Tenant (Branding básico)
        if ($request->hasFile('logo')) {
            if ($tenant->logo_path && file_exists(public_path($tenant->logo_path))) {
                @unlink(public_path($tenant->logo_path));
            }
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $path = 'images/' . $filename;
            $tenant->logo_path = $path;
        }
        
        // Cores para tema claro (salvo em primary/secondary_color para compatibilidade)
        if ($request->filled('primary_color_light')) {
            $tenant->primary_color = $request->input('primary_color_light');
        }
        if ($request->filled('secondary_color_light')) {
            $tenant->secondary_color = $request->input('secondary_color_light');
        }
        // Cores para tema escuro (novos campos)
        if ($request->filled('primary_color_dark')) {
            $tenant->primary_color_dark = $request->input('primary_color_dark');
        }
        if ($request->filled('secondary_color_dark')) {
            $tenant->secondary_color_dark = $request->input('secondary_color_dark');
        }
        
        $tenant->save();

        // 2. Atualizar CompanySettings
        $store = $tenant->stores()->where('is_main', true)->first();
        $storeId = $store ? $store->id : null;
        
        // Tenta buscar existente ou cria nova instância
        $settings = CompanySetting::where('store_id', $storeId)->first();
        if (!$settings) {
            $settings = new CompanySetting();
            $settings->store_id = $storeId;
        }

        $settings->fill([
            'company_name' => $validated['company_name'] ?? $settings->company_name,
            'company_cnpj' => $validated['company_cnpj'] ?? $settings->company_cnpj,
            'company_email' => $validated['company_email'] ?? $settings->company_email,
            'company_phone' => $validated['company_phone'] ?? $settings->company_phone,
            'company_address' => $validated['company_address'] ?? $settings->company_address,
            'company_city' => $validated['company_city'] ?? $settings->company_city,
            'company_state' => $validated['company_state'] ?? $settings->company_state,
            'company_zip' => $validated['company_zip'] ?? $settings->company_zip,
            'company_website' => $validated['company_website'] ?? $settings->company_website,
            'terms_conditions' => $validated['terms_conditions'] ?? $settings->terms_conditions,
        ]);
        
        // Sincronizar logo também em CompanySettings se necessário (ou deixar nulo para usar do tenant)
        // Por enquanto, vou salvar o caminho do logo no CompanySettings também para consistência com lógica antiga se houver
        if (isset($path)) {
            $settings->logo_path = $path;
        }

        $settings->save();

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
