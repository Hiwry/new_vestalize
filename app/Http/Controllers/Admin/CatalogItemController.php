<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use App\Models\CatalogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogItemController extends Controller
{
    public function index()
    {
        $items = CatalogItem::with('category')->orderBy('order')->orderBy('title')->get();
        return view('admin.catalog-items.index', compact('items'));
    }

    public function create()
    {
        $categories = CatalogCategory::orderBy('name')->get();
        return view('admin.catalog-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'catalog_category_id' => 'required|exists:catalog_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'active' => 'sometimes|boolean',
            'order' => 'nullable|integer',
        ]);

        $data['active'] = $request->boolean('active', true);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('catalog', 'public');
            $data['image_path'] = $path;
        }

        CatalogItem::create($data);

        return redirect()->route('admin.catalog-items.index')->with('success', 'Item do catálogo criado com sucesso.');
    }

    public function edit(CatalogItem $catalog_item)
    {
        $categories = CatalogCategory::orderBy('name')->get();
        return view('admin.catalog-items.edit', [
            'item' => $catalog_item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, CatalogItem $catalog_item)
    {
        $data = $request->validate([
            'catalog_category_id' => 'required|exists:catalog_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'active' => 'sometimes|boolean',
            'order' => 'nullable|integer',
        ]);

        $data['active'] = $request->boolean('active', true);

        if ($request->hasFile('image')) {
            if ($catalog_item->image_path) {
                Storage::disk('public')->delete($catalog_item->image_path);
            }
            $path = $request->file('image')->store('catalog', 'public');
            $data['image_path'] = $path;
        }

        $catalog_item->update($data);

        return redirect()->route('admin.catalog-items.index')->with('success', 'Item do catálogo atualizado com sucesso.');
    }

    public function destroy(CatalogItem $catalog_item)
    {
        if ($catalog_item->image_path) {
            Storage::disk('public')->delete($catalog_item->image_path);
        }

        $catalog_item->delete();

        return redirect()->route('admin.catalog-items.index')->with('success', 'Item do catálogo removido com sucesso.');
    }
}
