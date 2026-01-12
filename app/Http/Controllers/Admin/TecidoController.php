<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tecido;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TecidoController extends Controller
{
    private function tableExists(): bool
    {
        return Schema::hasTable('tecidos');
    }

    public function index(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'A tabela tecidos ainda não foi criada. Execute: php artisan migrate');
        }

        try {
            $tecidos = Tecido::orderBy('order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $tecidos = collect([]);
        }
        
        return view('admin.tecidos.index', compact('tecidos'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.tecidos.index')
                ->with('error', 'A tabela tecidos ainda não foi criada. Execute: php artisan migrate');
        }

        return view('admin.tecidos.create');
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
        $validated['order'] = $validated['order'] ?? (Tecido::max('order') ?? 0) + 1;

        Tecido::create($validated);

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido criado com sucesso!');
    }

    public function edit(Tecido $tecido): View
    {
        return view('admin.tecidos.edit', compact('tecido'));
    }

    public function update(Request $request, Tecido $tecido): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        $validated['order'] = $validated['order'] ?? (Tecido::max('order') ?? 0) + 1;

        $tecido->update($validated);

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido atualizado com sucesso!');
    }

    public function destroy(Tecido $tecido): RedirectResponse
    {
        $tecido->delete();

        return redirect()->route('admin.tecidos.index')
            ->with('success', 'Tecido excluído com sucesso!');
    }
}

