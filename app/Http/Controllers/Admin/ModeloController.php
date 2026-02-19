<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModeloController extends Controller
{
    private function tableExists(): bool
    {
        return Schema::hasTable('modelos');
    }

    public function index(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'A tabela modelos ainda não foi criada. Execute: php artisan migrate');
        }

        try {
            $modelos = Modelo::orderBy('order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $modelos = collect([]);
        }
        
        return view('admin.modelos.index', compact('modelos'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.modelos.index')
                ->with('error', 'A tabela modelos ainda não foi criada. Execute: php artisan migrate');
        }

        return view('admin.modelos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        $validated['order'] = $validated['order'] ?? (Modelo::max('order') ?? 0) + 1;

        Modelo::create($validated);

        return redirect()->route('admin.modelos.index')
            ->with('success', 'Modelo criado com sucesso!');
    }

    public function edit(Modelo $modelo): View
    {
        return view('admin.modelos.edit', compact('modelo'));
    }

    public function update(Request $request, Modelo $modelo): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        $validated['order'] = $validated['order'] ?? (Modelo::max('order') ?? 0) + 1;

        $modelo->update($validated);

        return redirect()->route('admin.modelos.index')
            ->with('success', 'Modelo atualizado com sucesso!');
    }

    public function destroy(Modelo $modelo): RedirectResponse
    {
        $modelo->delete();

        return redirect()->route('admin.modelos.index')
            ->with('success', 'Modelo excluído com sucesso!');
    }
}

