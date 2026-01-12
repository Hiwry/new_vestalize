<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SublimationAddon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SublimationAddonController extends Controller
{
    /**
     * Atualizar adicionais de sublimação
     */
    public function updateAddons(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'addons' => 'required|array',
            'addons.*.id' => 'nullable|exists:sublimation_addons,id',
            'addons.*.name' => 'required|string|max:255',
            'addons.*.description' => 'nullable|string|max:500',
            'addons.*.price_adjustment' => 'required|numeric',
        ]);

        try {
            foreach ($validated['addons'] as $addonData) {
                if (!empty($addonData['id'])) {
                    // Atualizar adicional existente
                    $addon = SublimationAddon::find($addonData['id']);
                    if ($addon) {
                        $addon->update([
                            'name' => $addonData['name'],
                            'description' => $addonData['description'],
                            'price_adjustment' => $addonData['price_adjustment'],
                        ]);
                    }
                } else {
                    // Criar novo adicional
                    SublimationAddon::create([
                        'name' => $addonData['name'],
                        'description' => $addonData['description'],
                        'price_adjustment' => $addonData['price_adjustment'],
                        'active' => true,
                        'order' => SublimationAddon::max('order') + 1,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Adicionais atualizados com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar adicionais: ' . $e->getMessage());
        }
    }
}