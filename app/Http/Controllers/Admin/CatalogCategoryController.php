<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogCategoryController extends Controller
{
    public function index()
    {
        $categories = CatalogCategory::orderBy('name')->get();
        return view('admin.catalog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.catalog-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['name']);

        CatalogCategory::create($data);

        return redirect()->route('admin.catalog-categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(CatalogCategory $catalog_category)
    {
        return view('admin.catalog-categories.edit', ['category' => $catalog_category]);
    }

    public function update(Request $request, CatalogCategory $catalog_category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $catalog_category->update($data);

        return redirect()->route('admin.catalog-categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(CatalogCategory $catalog_category)
    {
        $catalog_category->delete();

        return redirect()->route('admin.catalog-categories.index')
            ->with('success', 'Categoria removida com sucesso.');
    }
}
