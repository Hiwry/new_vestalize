<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    /**
     * Verificar se a tabela categories existe
     */
    private function tableExists(): bool
    {
        return Schema::hasTable('categories');
    }

    public function index(): View
    {
        if (!$this->tableExists()) {
            return view('admin.categories.index', ['categories' => collect([])])
                ->with('error', 'A tabela categories ainda não foi criada. Execute: php artisan migrate');
        }

        try {
            $categories = Category::orderBy('order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tableExists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'A tabela categories ainda não foi criada. Execute: php artisan migrate');
        }

        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? true : false;
        $validated['order'] = $validated['order'] ?? (Category::max('order') ?? 0) + 1;

        $category = Category::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category' => $category
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? true : false;

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Verificar se há produtos associados
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Não é possível excluir a categoria pois existem produtos associados.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}

