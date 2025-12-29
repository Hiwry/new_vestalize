<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalizationSetting;
use App\Models\PersonalizationSpecialOption;
use App\Models\SublimationLocation;
use App\Models\PersonalizationPrice;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonalizationSettingController extends Controller
{
    /**
     * Exibir formulário de edição de configurações
     */
    public function edit(string $type): View
    {
        $setting = PersonalizationSetting::where('personalization_type', $type)->firstOrFail();
        
        $specialOptions = PersonalizationSpecialOption::where('personalization_type', $type)
            ->orderBy('order')
            ->get();
        
        $locations = SublimationLocation::orderBy('order')->get();
        
        $sizes = PersonalizationPrice::getSizesForType($type);
        
        return view('admin.personalization-prices.settings', compact('setting', 'specialOptions', 'locations', 'sizes'));
    }

    /**
     * Atualizar configurações
     */
    public function update(Request $request, string $type): RedirectResponse
    {
        $setting = PersonalizationSetting::where('personalization_type', $type)->firstOrFail();

        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'charge_by_color' => 'boolean',
            'color_price_per_unit' => 'nullable|numeric|min:0',
            'color_cost_per_unit' => 'nullable|numeric|min:0',
            'min_colors' => 'nullable|integer|min:1',
            'max_colors' => 'nullable|integer|min:1',
            'discount_2nd_application' => 'nullable|numeric|min:0|max:100',
            'discount_3rd_application' => 'nullable|numeric|min:0|max:100',
            'discount_4th_plus_application' => 'nullable|numeric|min:0|max:100',
            'has_sizes' => 'boolean',
            'has_locations' => 'boolean',
            'has_special_options' => 'boolean',
            'active' => 'boolean',
        ]);

        // Processar checkboxes
        $validated['charge_by_color'] = $request->has('charge_by_color');
        $validated['has_sizes'] = $request->has('has_sizes');
        $validated['has_locations'] = $request->has('has_locations');
        $validated['has_special_options'] = $request->has('has_special_options');
        $validated['active'] = $request->has('active');

        // Valores padrão para campos vazios
        $validated['color_price_per_unit'] = $validated['color_price_per_unit'] ?? 0;
        $validated['color_cost_per_unit'] = $validated['color_cost_per_unit'] ?? 0;
        $validated['min_colors'] = $validated['min_colors'] ?? 1;
        $validated['discount_2nd_application'] = $validated['discount_2nd_application'] ?? 0;
        $validated['discount_3rd_application'] = $validated['discount_3rd_application'] ?? 0;
        $validated['discount_4th_plus_application'] = $validated['discount_4th_plus_application'] ?? 0;

        $setting->update($validated);

        return redirect()
            ->route('admin.personalization-settings.edit', $type)
            ->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Criar nova opção especial
     */
    public function storeSpecialOption(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'charge_type' => 'required|in:percentage,fixed',
            'charge_value' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $maxOrder = PersonalizationSpecialOption::where('personalization_type', $type)->max('order') ?? 0;

        PersonalizationSpecialOption::create([
            'personalization_type' => $type,
            'name' => $validated['name'],
            'charge_type' => $validated['charge_type'],
            'charge_value' => $validated['charge_value'],
            'cost' => $validated['cost'],
            'description' => $validated['description'] ?? null,
            'active' => true,
            'order' => $maxOrder + 1,
        ]);

        return redirect()
            ->route('admin.personalization-settings.edit', $type)
            ->with('success', 'Opção especial criada com sucesso!');
    }

    /**
     * Atualizar opção especial
     */
    public function updateSpecialOption(Request $request, string $type, int $option): RedirectResponse
    {
        $specialOption = PersonalizationSpecialOption::where('personalization_type', $type)
            ->where('id', $option)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'charge_type' => 'required|in:percentage,fixed',
            'charge_value' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $specialOption->update($validated);

        return redirect()
            ->route('admin.personalization-settings.edit', $type)
            ->with('success', 'Opção especial atualizada com sucesso!');
    }

    /**
     * Excluir opção especial
     */
    public function destroySpecialOption(string $type, int $option): RedirectResponse
    {
        $specialOption = PersonalizationSpecialOption::where('personalization_type', $type)
            ->where('id', $option)
            ->firstOrFail();

        $specialOption->delete();

        return redirect()
            ->route('admin.personalization-settings.edit', $type)
            ->with('success', 'Opção especial removida com sucesso!');
    }

    /**
     * Toggle ativo/inativo de opção especial
     */
    public function toggleSpecialOption(Request $request, string $type, int $option): RedirectResponse
    {
        $specialOption = PersonalizationSpecialOption::where('personalization_type', $type)
            ->where('id', $option)
            ->firstOrFail();

        $specialOption->update(['active' => !$specialOption->active]);

        return redirect()
            ->route('admin.personalization-settings.edit', $type)
            ->with('success', 'Status da opção atualizado!');
    }
}
