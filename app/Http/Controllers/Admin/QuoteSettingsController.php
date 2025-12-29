<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequestSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuoteSettingsController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        
        // Ensure user has access
        if (!$tenant || !$tenant->canAccess('external_quote')) {
            abort(403, 'Seu plano não permite acesso a esta funcionalidade.');
        }

        $settings = QuoteRequestSetting::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'slug' => Str::slug($tenant->name . '-' . Str::random(4)),
                'title' => 'Orçamento Gratuito',
                'primary_color' => '#4f46e5',
                'is_active' => true,
                'products_json' => [
                    [
                        'icon' => 't-shirt', 
                        'name' => 'Camiseta Tradicional', 
                        'description' => 'Camisetas de alta qualidade com personalização completa.'
                    ],
                    [
                        'icon' => 'polo', 
                        'name' => 'Polo Personalizada', 
                        'description' => 'Polos elegantes e profissionais para empresas e eventos.'
                    ]
                ]
            ]
        );

        return view('admin.quote_settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $tenant = Auth::user()->tenant;
        
        if (!$tenant || !$tenant->canAccess('external_quote')) {
            abort(403);
        }

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:quote_request_settings,slug,' . $request->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'primary_color' => 'required|string|max:7',
            'products' => 'nullable|array',
            'products.*.name' => 'required|string',
            'products.*.icon' => 'required|string',
            'products.*.description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $settings = QuoteRequestSetting::where('tenant_id', $tenant->id)->firstOrFail();
        
        $settings->update([
            'slug' => Str::slug($validated['slug']),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'primary_color' => $validated['primary_color'],
            'products_json' => $validated['products'] ?? [],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.quote-settings.index')
            ->with('success', 'Configurações de orçamento atualizadas com sucesso!');
    }
}
