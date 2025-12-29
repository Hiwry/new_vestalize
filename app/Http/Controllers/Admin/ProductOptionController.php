<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductOptionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->get('type', 'personalizacao');
        
        $options = ProductOption::with('parents')
            ->where('type', $type)
            ->orderBy('order')
            ->paginate(20);

        $types = [
            'personalizacao' => 'Personalização',
            'tecido' => 'Tecidos',
            'tipo_tecido' => 'Tipos de Tecido',
            'cor' => 'Cores',
            'tipo_corte' => 'Tipos de Corte',
            'detalhe' => 'Detalhes',
            'gola' => 'Golas',
        ];

        return view('admin.product-options.index', compact('options', 'type', 'types'));
    }

    public function create(Request $request): View
    {
        $type = $request->get('type', 'personalizacao');
        
        $types = [
            'personalizacao' => 'Personalização',
            'tecido' => 'Tecidos',
            'tipo_tecido' => 'Tipos de Tecido',
            'cor' => 'Cores',
            'tipo_corte' => 'Tipos de Corte',
            'detalhe' => 'Detalhes',
            'gola' => 'Golas',
        ];

        // Definir pais baseado no tipo
        $parents = [];
        $parentLabel = '';
        
        if ($type === 'tecido') {
            $parents = ProductOption::where('type', 'personalizacao')->where('active', true)->get();
            $parentLabel = 'Personalização';
        } elseif ($type === 'tipo_tecido') {
            $parents = ProductOption::where('type', 'tecido')->where('active', true)->get();
            $parentLabel = 'Tecido';
        } elseif ($type === 'cor') {
            $parents = ProductOption::where('type', 'tipo_tecido')->where('active', true)->get();
            $parentLabel = 'Tipo de Tecido';
        } elseif ($type === 'tipo_corte') {
            $parents = ProductOption::where('type', 'tipo_tecido')->where('active', true)->get();
            $parentLabel = 'Tipo de Tecido';
        } elseif ($type === 'detalhe') {
            $parents = ProductOption::where('type', 'tipo_corte')->where('active', true)->get();
            $parentLabel = 'Tipo de Corte';
        } elseif ($type === 'gola') {
            $parents = ProductOption::where('type', 'tipo_corte')->where('active', true)->get();
            $parentLabel = 'Tipo de Corte';
        }

        return view('admin.product-options.create', compact('type', 'types', 'parents', 'parentLabel'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'parent_ids' => 'nullable|array',
            'parent_ids.*' => [
                Rule::exists('product_options', 'id')->where(function ($query) {
                    if (!Schema::hasColumn('product_options', 'tenant_id')) {
                        return;
                    }
                    $user = Auth::user();
                    if ($user && $user->tenant_id !== null) {
                        $query->where('tenant_id', $user->tenant_id);
                    }
                }),
            ],
            'active' => 'boolean',
            'is_pinned' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['price'] = $validated['price'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['active'] = $request->has('active');
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['order'] = $validated['order'] ?? 0;
        if (Schema::hasColumn('product_options', 'tenant_id')) {
            $validated['tenant_id'] = Auth::user()?->tenant_id;
        }

        // Manter parent_id para compatibilidade (usar o primeiro pai)
        if (!empty($validated['parent_ids'])) {
            $validated['parent_id'] = $validated['parent_ids'][0];
            $parent = ProductOption::find($validated['parent_id']);
            $validated['parent_type'] = $parent->type;
        }

        $parentIds = $validated['parent_ids'] ?? [];
        unset($validated['parent_ids']);

        $option = ProductOption::create($validated);

        // Sincronizar múltiplos pais
        if (!empty($parentIds)) {
            $option->parents()->sync($parentIds);
        }

        return redirect()
            ->route('admin.product-options.index', ['type' => $validated['type']])
            ->with('success', 'Opção criada com sucesso!');
    }

    public function edit(string $id): View
    {
        $option = ProductOption::with('parents')->findOrFail($id);
        
        $types = [
            'personalizacao' => 'Personalização',
            'tecido' => 'Tecidos',
            'tipo_tecido' => 'Tipos de Tecido',
            'cor' => 'Cores',
            'tipo_corte' => 'Tipos de Corte',
            'detalhe' => 'Detalhes',
            'gola' => 'Golas',
        ];

        // Definir pais baseado no tipo
        $parents = [];
        $parentLabel = '';
        
        if ($option->type === 'tecido') {
            $parents = ProductOption::where('type', 'personalizacao')->where('active', true)->get();
            $parentLabel = 'Personalização';
        } elseif ($option->type === 'tipo_tecido') {
            $parents = ProductOption::where('type', 'tecido')->where('active', true)->get();
            $parentLabel = 'Tecido';
        } elseif ($option->type === 'cor') {
            $parents = ProductOption::where('type', 'tipo_tecido')->where('active', true)->get();
            $parentLabel = 'Tipo de Tecido';
        } elseif ($option->type === 'tipo_corte') {
            $parents = ProductOption::where('type', 'tipo_tecido')->where('active', true)->get();
            $parentLabel = 'Tipo de Tecido';
        } elseif ($option->type === 'detalhe') {
            $parents = ProductOption::where('type', 'tipo_corte')->where('active', true)->get();
            $parentLabel = 'Tipo de Corte';
        } elseif ($option->type === 'gola') {
            $parents = ProductOption::where('type', 'tipo_corte')->where('active', true)->get();
            $parentLabel = 'Tipo de Corte';
        }

        return view('admin.product-options.edit', compact('option', 'types', 'parents', 'parentLabel'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $option = ProductOption::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'parent_ids' => 'nullable|array',
            'parent_ids.*' => [
                Rule::exists('product_options', 'id')->where(function ($query) {
                    if (!Schema::hasColumn('product_options', 'tenant_id')) {
                        return;
                    }
                    $user = Auth::user();
                    if ($user && $user->tenant_id !== null) {
                        $query->where('tenant_id', $user->tenant_id);
                    }
                }),
            ],
            'active' => 'boolean',
            'is_pinned' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['price'] = $validated['price'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['active'] = $request->has('active');
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['order'] = $validated['order'] ?? 0;

        // Manter parent_id para compatibilidade (usar o primeiro pai)
        if (!empty($validated['parent_ids'])) {
            $validated['parent_id'] = $validated['parent_ids'][0];
            $parent = ProductOption::find($validated['parent_id']);
            $validated['parent_type'] = $parent->type;
        } else {
            $validated['parent_type'] = null;
            $validated['parent_id'] = null;
        }

        $parentIds = $validated['parent_ids'] ?? [];
        unset($validated['parent_ids']);

        $option->update($validated);

        // Sincronizar múltiplos pais
        $option->parents()->sync($parentIds);

        return redirect()
            ->route('admin.product-options.index', ['type' => $option->type])
            ->with('success', 'Opção atualizada com sucesso!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $option = ProductOption::findOrFail($id);
        $type = $option->type;
        $option->delete();

        return redirect()
            ->route('admin.product-options.index', ['type' => $type])
            ->with('success', 'Opção removida com sucesso!');
    }
}
