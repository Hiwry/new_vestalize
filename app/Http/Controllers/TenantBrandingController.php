<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;

class TenantBrandingController extends Controller
{
    /**
     * Mostrar formulário de personalização de marca
     */
    public function edit()
    {
        $tenant = Auth::user()->tenant;
        
        if (!$tenant) {
            return redirect()->route('dashboard')->with('error', 'Apenas usuários vinculados a uma empresa podem personalizar a marca.');
        }

        return view('settings.branding', compact('tenant'));
    }

    /**
     * Atualizar logo e cores do tenant
     */
    public function update(Request $request)
    {
        $tenant = Auth::user()->tenant;
        
        if (!$tenant) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        if ($request->hasFile('logo')) {
            // Remover logo antigo se existir
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $tenant->logo_path = $path;
        }

        $tenant->primary_color = $request->input('primary_color', '#4f46e5');
        $tenant->secondary_color = $request->input('secondary_color', '#7c3aed');
        $tenant->save();

        return redirect()->back()->with('success', 'Configurações de marca atualizadas com sucesso!');
    }
}
