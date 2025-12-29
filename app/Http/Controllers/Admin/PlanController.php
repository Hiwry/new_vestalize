<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $availableFeatures = Plan::AVAILABLE_FEATURES;
        return view('admin.plans.create', compact('availableFeatures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'array',
            'limits_users' => 'required|integer|min:1',
            'limits_stores' => 'required|integer|min:1',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Prepare limits structure
        $validated['limits'] = [
            'users' => $request->limits_users,
            'stores' => $request->limits_stores,
        ];
        
        $validated['features'] = $request->input('features', []);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        $availableFeatures = Plan::AVAILABLE_FEATURES;
        return view('admin.plans.edit', compact('plan', 'availableFeatures'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'array',
            'limits_users' => 'required|integer|min:1',
            'limits_stores' => 'required|integer|min:1',
        ]);

        // Don't limit slug update if you want to keep permalinks, but for now update it.
        $validated['slug'] = Str::slug($validated['name']);
        
        $validated['limits'] = [
            'users' => $request->limits_users,
            'stores' => $request->limits_stores,
        ];
        
        $validated['features'] = $request->input('features', []);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        
        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano removido com sucesso!');
    }
}
