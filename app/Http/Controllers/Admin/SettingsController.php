<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Exibir a página de configurações (apenas lista de sub-lojas)
     */
    public function index()
    {
        $mainStore = \App\Models\Store::where('is_main', true)->first();
        $subStores = $mainStore ? $mainStore->subStores()->orderBy('name')->get() : collect();
        
        return view('admin.settings', compact('mainStore', 'subStores'));
    }

    /**
     * Atualizar as configurações
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_city' => 'nullable|string|max:255',
            'company_state' => 'nullable|string|max:2',
            'company_zip' => 'nullable|string|max:10',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_cnpj' => 'nullable|string|max:18',
            'bank_name' => 'nullable|string|max:255',
            'bank_agency' => 'nullable|string|max:10',
            'bank_account' => 'nullable|string|max:20',
            'pix_key' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Buscar ou criar configurações da loja principal (store_id = null)
        $settings = CompanySetting::whereNull('store_id')->first();
        if (!$settings) {
            $settings = new CompanySetting();
            $settings->store_id = null;
        }

        // Upload do logo
        if ($request->hasFile('logo')) {
            // Deletar logo antigo
            if ($settings->logo_path && file_exists(public_path($settings->logo_path))) {
                unlink(public_path($settings->logo_path));
            }

            // Salvar novo logo
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['logo_path'] = 'images/' . $filename;
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()->route('admin.company.settings')
            ->with('success', 'Configurações da empresa atualizadas com sucesso!');
    }

    /**
     * Deletar o logo
     */
    public function deleteLogo()
    {
        $settings = CompanySetting::first();
        
        if ($settings && $settings->logo_path) {
            if (file_exists(public_path($settings->logo_path))) {
                unlink(public_path($settings->logo_path));
            }
            
            $settings->logo_path = null;
            $settings->save();
            
            return response()->json(['success' => true, 'message' => 'Logo removido com sucesso!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Nenhum logo encontrado.'], 404);
    }
}

