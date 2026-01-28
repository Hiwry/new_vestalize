<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubLocalProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubLocalProductController extends Controller
{
    public function index()
    {
        $products = SubLocalProduct::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.sub-local-products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.sub-local-products.create');
    }

    public function store(Request $request)
    {
        // Converter vírgula para ponto nos campos de preço
        $request->merge([
            'price' => str_replace(',', '.', $request->input('price', '0')),
            'cost' => $request->input('cost') ? str_replace(',', '.', $request->input('cost')) : null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:vestuario,canecas,acessorios,diversos',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048', // max 2MB
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'available_sizes' => 'nullable|array',
            'available_sizes.*' => 'string|max:10',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sub-local-products', 'public');
            $validated['image'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_customization'] = $request->has('requires_customization');
        $validated['requires_size'] = $request->has('requires_size');
        $validated['available_sizes'] = $request->has('requires_size') ? $request->input('available_sizes', []) : null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        // Novos campos: preços por quantidade e edição de preço
        $validated['has_quantity_pricing'] = $request->has('has_quantity_pricing');
        $validated['allow_price_edit'] = $request->has('allow_price_edit');
        
        // Processar tabela de preços por quantidade
        if ($request->has('has_quantity_pricing') && $request->input('quantity_pricing')) {
            $quantityPricing = json_decode($request->input('quantity_pricing'), true);
            $validated['quantity_pricing'] = is_array($quantityPricing) ? $quantityPricing : null;
        } else {
            $validated['quantity_pricing'] = null;
        }
        
        SubLocalProduct::create($validated);

        return redirect()->route('admin.sub-local-products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function edit(SubLocalProduct $subLocalProduct)
    {
        return view('admin.sub-local-products.edit', compact('subLocalProduct'));
    }

    public function update(Request $request, SubLocalProduct $subLocalProduct)
    {
        // Converter vírgula para ponto nos campos de preço
        $request->merge([
            'price' => str_replace(',', '.', $request->input('price', '0')),
            'cost' => $request->input('cost') ? str_replace(',', '.', $request->input('cost')) : null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:vestuario,canecas,acessorios,diversos',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'available_sizes' => 'nullable|array',
            'available_sizes.*' => 'string|max:10',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($subLocalProduct->image) {
                Storage::disk('public')->delete($subLocalProduct->image);
            }
            $path = $request->file('image')->store('sub-local-products', 'public');
            $validated['image'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['requires_customization'] = $request->has('requires_customization');
        $validated['requires_size'] = $request->has('requires_size');
        $validated['available_sizes'] = $request->has('requires_size') ? $request->input('available_sizes', []) : null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        // Novos campos: preços por quantidade e edição de preço
        $validated['has_quantity_pricing'] = $request->has('has_quantity_pricing');
        $validated['allow_price_edit'] = $request->has('allow_price_edit');
        
        // Processar tabela de preços por quantidade
        if ($request->has('has_quantity_pricing') && $request->input('quantity_pricing')) {
            $quantityPricing = json_decode($request->input('quantity_pricing'), true);
            $validated['quantity_pricing'] = is_array($quantityPricing) ? $quantityPricing : null;
        } else {
            $validated['quantity_pricing'] = null;
        }
        
        $subLocalProduct->update($validated);

        return redirect()->route('admin.sub-local-products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(SubLocalProduct $subLocalProduct)
    {
        if ($subLocalProduct->image) {
            Storage::disk('public')->delete($subLocalProduct->image);
        }
        $subLocalProduct->delete();

        return redirect()->route('admin.sub-local-products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function storeAddon(Request $request, SubLocalProduct $subLocalProduct)
    {
        // Converter vírgula
        $request->merge([
            'price' => str_replace(',', '.', $request->input('price', '0')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $subLocalProduct->addons()->create($validated);

        return back()->with('success', 'Adicional adicionado!');
    }

    public function destroyAddon(SubLocalProduct $subLocalProduct, \App\Models\SubLocalProductAddon $addon)
    {
        $addon->delete();
        return back()->with('success', 'Adicional removido!');
    }
}
