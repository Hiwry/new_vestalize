<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SubcategoryController extends Controller
{
    /**
     * Verificar se as tabelas existem
     */
    private function tablesExist(): bool
    {
        return Schema::hasTable('subcategories') && Schema::hasTable('categories');
    }

    public function index(): View
    {
        if (!$this->tablesExist()) {
            return view('admin.subcategories.index', ['subcategories' => collect([])])
                ->with('error', 'As tabelas ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $subcategories = Subcategory::with('category')->orderBy('order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $subcategories = collect([]);
        }
        
        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'As tabelas ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $categories = Category::where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }
        
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? true : false;
        $validated['order'] = $validated['order'] ?? (Subcategory::max('order') ?? 0) + 1;

        Subcategory::create($validated);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoria criada com sucesso!');
    }

    public function edit(Subcategory $subcategory): View|RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'As tabelas ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $categories = Category::where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
        
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active') ? true : false;

        $subcategory->update($validated);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoria atualizada com sucesso!');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        // Verificar se há produtos associados
        if ($subcategory->products()->count() > 0) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Não é possível excluir a subcategoria pois existem produtos associados.');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategoria excluída com sucesso!');
    }
}

