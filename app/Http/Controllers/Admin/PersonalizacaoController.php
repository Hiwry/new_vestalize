<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personalizacao;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PersonalizacaoController extends Controller
{
    private function tableExists(): bool
    {
        return Schema::hasTable('personalizacoes');
    }

    public function index(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'A tabela personalizacoes ainda não foi criada. Execute: php artisan migrate');
        }

        try {
            $personalizacoes = Personalizacao::orderBy('order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $personalizacoes = collect([]);
        }
        
        return view('admin.personalizacoes.index', compact('personalizacoes'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.personalizacoes.index')
                ->with('error', 'A tabela personalizacoes ainda não foi criada. Execute: php artisan migrate');
        }

        return view('admin.personalizacoes.create');
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
        $validated['order'] = $validated['order'] ?? (Personalizacao::max('order') ?? 0) + 1;

        Personalizacao::create($validated);

        return redirect()->route('admin.personalizacoes.index')
            ->with('success', 'Personalização criada com sucesso!');
    }

    public function edit(Personalizacao $personalizacao): View
    {
        return view('admin.personalizacoes.edit', compact('personalizacao'));
    }

    public function update(Request $request, Personalizacao $personalizacao): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        $validated['order'] = $validated['order'] ?? (Personalizacao::max('order') ?? 0) + 1;

        $personalizacao->update($validated);

        return redirect()->route('admin.personalizacoes.index')
            ->with('success', 'Personalização atualizada com sucesso!');
    }

    public function destroy(Personalizacao $personalizacao): RedirectResponse
    {
        $personalizacao->delete();

        return redirect()->route('admin.personalizacoes.index')
            ->with('success', 'Personalização excluída com sucesso!');
    }
}

