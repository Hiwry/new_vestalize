<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Tecido;
use App\Models\Personalizacao;
use App\Models\Modelo;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * Verificar se as tabelas do catálogo existem
     */
    private function tablesExist(): bool
    {
        return Schema::hasTable('products') && 
               Schema::hasTable('tecidos') && 
               Schema::hasTable('personalizacoes') &&
               Schema::hasTable('modelos');
    }

    public function index(): View
    {
        if (!$this->tablesExist()) {
            return view('admin.products.index', ['products' => collect([])])
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $products = Product::with(['tecido', 'personalizacao', 'modelo', 'images'])
                ->orderBy('order')
                ->orderBy('title')
                ->get();
        } catch (\Exception $e) {
            $products = collect([]);
        }
        
        return view('admin.products.index', compact('products'));
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $categories = Category::where('active', true)->orderBy('name')->get();
            $subcategories = Subcategory::where('active', true)->orderBy('name')->get();
            $tecidos = Tecido::where('active', true)->orderBy('name')->get();
            $personalizacoes = Personalizacao::where('active', true)->orderBy('name')->get();
            $modelos = Modelo::where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
            $subcategories = collect([]);
            $tecidos = collect([]);
            $personalizacoes = collect([]);
            $modelos = collect([]);
        }
        
        return view('admin.products.create', compact('categories', 'subcategories', 'tecidos', 'personalizacoes', 'modelos'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'tecido_id' => 'nullable|exists:tecidos,id',
            'personalizacao_id' => 'nullable|exists:personalizacoes,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'price' => 'nullable|numeric|min:0',
            'sale_type' => 'required|in:unidade,kg,metro',
            'allow_application' => 'boolean',
            'application_types' => 'nullable|array',
            'application_types.*' => 'in:sublimacao_local,dtf',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        $validated['allow_application'] = $request->has('allow_application') ? true : false;
        $validated['order'] = $validated['order'] ?? (Product::max('order') ?? 0) + 1;
        
        // Se não permitir aplicação, limpar tipos de aplicação
        if (!$validated['allow_application']) {
            $validated['application_types'] = null;
        }

        $product = Product::create($validated);

        // Upload de imagens
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product): View|RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $product->load('images');
            $categories = Category::where('active', true)->orderBy('name')->get();
            $subcategories = Subcategory::where('active', true)->orderBy('name')->get();
            $tecidos = Tecido::where('active', true)->orderBy('name')->get();
            $personalizacoes = Personalizacao::where('active', true)->orderBy('name')->get();
            $modelos = Modelo::where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
        
        return view('admin.products.edit', compact('product', 'categories', 'subcategories', 'tecidos', 'personalizacoes', 'modelos'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if (!$this->tablesExist()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'tecido_id' => 'nullable|exists:tecidos,id',
            'personalizacao_id' => 'nullable|exists:personalizacoes,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'price' => 'nullable|numeric|min:0',
            'sale_type' => 'required|in:unidade,kg,metro',
            'allow_application' => 'boolean',
            'application_types' => 'nullable|array',
            'application_types.*' => 'in:sublimacao_local,dtf',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        $validated['allow_application'] = $request->has('allow_application') ? true : false;
        
        // Se não permitir aplicação, limpar tipos de aplicação
        if (!$validated['allow_application']) {
            $validated['application_types'] = null;
        }

        $product->update($validated);

        // Upload de novas imagens
        if ($request->hasFile('images')) {
            $maxOrder = $product->images()->max('order') ?? 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'order' => ++$maxOrder,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Deletar imagens
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()
            ->with('success', 'Imagem excluída com sucesso!');
    }

    public function setPrimaryImage(ProductImage $image): RedirectResponse
    {
        // Remover primary de todas as imagens do produto
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        // Definir esta como primary
        $image->update(['is_primary' => true]);

        return redirect()->back()
            ->with('success', 'Imagem principal definida com sucesso!');
    }
}

