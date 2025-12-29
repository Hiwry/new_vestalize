<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsCondition;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TermsConditionController extends Controller
{
    public function index(): View
    {
        $terms = TermsCondition::with('fabricType')->orderBy('created_at', 'desc')->get();
        $activeTerms = TermsCondition::getActive();
        
        return view('admin.terms-conditions.index', compact('terms', 'activeTerms'));
    }

    public function create(): View
    {
        $personalizationTypes = \App\Models\PersonalizationPrice::getPersonalizationTypes();
        $fabricTypes = \App\Models\ProductOption::where('type', 'tecido')
            ->where('active', true)
            ->orderBy('order')
            ->get();
        
        return view('admin.terms-conditions.create', compact('personalizationTypes', 'fabricTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'version' => 'required|string|max:10',
            'title' => 'nullable|string|max:255',
            'personalization_type' => 'nullable|string|in:DTF,SERIGRAFIA,BORDADO,EMBORRACHADO,SUB. LOCAL,SUB. TOTAL',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'active' => 'boolean'
        ]);

        // Se está marcando como ativo, desativar os outros com a mesma combinação
        if ($validated['active'] ?? false) {
            $query = TermsCondition::where('active', true);
            
            if (isset($validated['personalization_type'])) {
                $query->where('personalization_type', $validated['personalization_type']);
            } else {
                $query->whereNull('personalization_type');
            }
            
            if (isset($validated['fabric_type_id'])) {
                $query->where('fabric_type_id', $validated['fabric_type_id']);
            } else {
                $query->whereNull('fabric_type_id');
            }
            
            $query->update(['active' => false]);
        }

        TermsCondition::create($validated);

        return redirect()->route('admin.terms-conditions.index')
            ->with('success', 'Termos e condições criados com sucesso!');
    }

    public function edit(TermsCondition $termsCondition): View
    {
        $personalizationTypes = \App\Models\PersonalizationPrice::getPersonalizationTypes();
        $fabricTypes = \App\Models\ProductOption::where('type', 'tecido')
            ->where('active', true)
            ->orderBy('order')
            ->get();
        
        return view('admin.terms-conditions.edit', compact('termsCondition', 'personalizationTypes', 'fabricTypes'));
    }

    public function update(Request $request, TermsCondition $termsCondition): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'version' => 'required|string|max:10',
            'title' => 'nullable|string|max:255',
            'personalization_type' => 'nullable|string|in:DTF,SERIGRAFIA,BORDADO,EMBORRACHADO,SUB. LOCAL,SUB. TOTAL',
            'fabric_type_id' => 'nullable|exists:product_options,id',
            'active' => 'boolean'
        ]);

        // Se está marcando como ativo, desativar os outros com a mesma combinação
        if ($validated['active'] ?? false) {
            $query = TermsCondition::where('active', true)
                ->where('id', '!=', $termsCondition->id);
            
            if (isset($validated['personalization_type'])) {
                $query->where('personalization_type', $validated['personalization_type']);
            } else {
                $query->whereNull('personalization_type');
            }
            
            if (isset($validated['fabric_type_id'])) {
                $query->where('fabric_type_id', $validated['fabric_type_id']);
            } else {
                $query->whereNull('fabric_type_id');
            }
            
            $query->update(['active' => false]);
        }

        $termsCondition->update($validated);

        return redirect()->route('admin.terms-conditions.index')
            ->with('success', 'Termos e condições atualizados com sucesso!');
    }

    public function destroy(TermsCondition $termsCondition): RedirectResponse
    {
        $termsCondition->delete();

        return redirect()->route('admin.terms-conditions.index')
            ->with('success', 'Termos e condições removidos com sucesso!');
    }

    public function activate(TermsCondition $termsCondition): RedirectResponse
    {
        // Desativar todos os outros
        TermsCondition::where('active', true)->update(['active' => false]);
        
        // Ativar o selecionado
        $termsCondition->update(['active' => true]);

        return redirect()->route('admin.terms-conditions.index')
            ->with('success', 'Termos e condições ativados com sucesso!');
    }
}