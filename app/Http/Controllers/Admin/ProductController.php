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
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Verificar se as tabelas do catálogo existem
     */
    private function tablesExist(): bool
    {
        return Schema::hasTable('products') && 
               Schema::hasTable('product_options');
    }

    public function index(): View
    {
        $templates = $this->getTemplates();

        if (!$this->tablesExist()) {
            return view('admin.products.index', ['products' => collect([]), 'templates' => $templates])
                ->with('error', 'As tabelas do catálogo ainda não foram criadas. Execute: php artisan migrate');
        }

        try {
            $products = Product::with(['tecido', 'personalizacao', 'modelo', 'images'])
                ->orderBy('order')
                ->orderBy('title')
                ->get();
            
            // Carregar tipos de corte para o filtro de modelos sugeridos
            $cutTypes = \App\Models\ProductOption::where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $products = collect([]);
            $cutTypes = collect([]);
        }
        
        return view('admin.products.index', compact('products', 'templates', 'cutTypes'));
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
            $cutTypes = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
            $subcategories = collect([]);
            $tecidos = collect([]);
            $personalizacoes = collect([]);
            $modelos = collect([]);
            $cutTypes = collect([]);
        }
        
        return view('admin.products.create', compact('categories', 'subcategories', 'tecidos', 'personalizacoes', 'modelos', 'cutTypes'));
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
            'tecido_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'personalizacao_id' => 'nullable|exists:product_options,id',
            'modelo_id' => 'nullable|exists:product_options,id',
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
        
        // Atributos baseados em ProductOption (Sincronizado com tela de Opções de Produtos)
        $tecidos = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'tecido')->where('active', true)->orderBy('name')->get();
        $personalizacoes = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'personalizacao')->where('active', true)->orderBy('name')->get();
        $modelos = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'detalhe')->where('active', true)->orderBy('name')->get();
        
        $cutTypes = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
    } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
        
        return view('admin.products.edit', compact('product', 'categories', 'subcategories', 'tecidos', 'personalizacoes', 'modelos', 'cutTypes'));
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
            'tecido_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'personalizacao_id' => 'nullable|exists:product_options,id',
            'modelo_id' => 'nullable|exists:product_options,id',
            'price' => 'nullable|numeric|min:0',
            'sale_type' => 'required|in:unidade,kg,metro',
            'allow_application' => 'boolean',
            'application_types' => 'nullable|array',
            'application_types.*' => 'in:sublimacao_local,dtf',
            'available_sizes' => 'nullable|array',
            'available_sizes.*' => 'string|max:10',
            'available_colors' => 'nullable|string',
            'track_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        $validated['allow_application'] = $request->has('allow_application') ? true : false;
        $validated['track_stock'] = $request->has('track_stock') ? true : false;
        
        // Se não permitir aplicação, limpar tipos de aplicação
        if (!$validated['allow_application']) {
            $validated['application_types'] = null;
        }

        // Processar cores (vem como JSON string do frontend)
        if (!empty($validated['available_colors'])) {
            $validated['available_colors'] = json_decode($validated['available_colors'], true);
        } else {
            $validated['available_colors'] = null;
        }

        // Processar tamanhos
        if (empty($validated['available_sizes'])) {
            $validated['available_sizes'] = null;
        }

        // Se não acompanhar estoque, limpar quantidade
        if (!$validated['track_stock']) {
            $validated['stock_quantity'] = null;
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

    public function duplicate(Product $product): RedirectResponse
    {
        try {
            $newProduct = $product->replicate();
            $newProduct->title = $product->title . ' (Cópia)';
            if ($product->sku) {
                // Tentar gerar um SKU único se possível, ou apenas adicionar sufixo
                $newProduct->sku = $product->sku . '-COPY-' . strtoupper(Str::random(4));
            }
            $newProduct->order = (Product::max('order') ?? 0) + 1;
            $newProduct->save();

            // Duplicar imagens fisicamente e no banco
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    $oldPath = $image->image_path;
                    $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newFileName = 'products/' . Str::random(40) . '.' . $extension;
                    
                    if (Storage::disk('public')->copy($oldPath, $newFileName)) {
                        ProductImage::create([
                            'product_id' => $newProduct->id,
                            'image_path' => $newFileName,
                            'is_primary' => $image->is_primary,
                            'order' => $image->order,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.products.edit', $newProduct)
                ->with('success', 'Produto duplicado com sucesso! Agora você pode editar os detalhes da cópia.');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Erro ao duplicar produto: ' . $e->getMessage());
        }
    }

    public function importTemplate(Request $request): RedirectResponse
    {
        $templateId = $request->input('template_id');
        $templates = $this->getTemplates();

        if (!isset($templates[$templateId])) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Template não encontrado.');
        }

        $template = $templates[$templateId];

        try {
            // 1. Garantir que a categoria existe
            $tenantId = auth()->user()->tenant_id;
            $baseSlug = Str::slug($template['category']);
            
            // Buscar dentro do tenant atual (com o scope ativo)
            $category = Category::where('name', $template['category'])->first();
            
            if (!$category) {
                // Gerar slug único globalmente (slug pode já existir em outro tenant)
                $slug = $baseSlug;
                $counter = 1;
                while (\DB::table('categories')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $category = Category::create([
                    'tenant_id' => $tenantId,
                    'name' => $template['category'],
                    'slug' => $slug,
                    'active' => true,
                ]);
            }

            // 2. Tentar encontrar atributos pelos nomes (Case-insensitive) nas Opções de Produto
            $tecido = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'tecido')->where('name', 'like', '%' . ($template['tecido_keyword'] ?? '---') . '%')->first();
            $personalizacao = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'personalizacao')->where('name', 'like', '%' . ($template['personalizacao_keyword'] ?? '---') . '%')->first();
            $modelo = \App\Models\ProductOption::withoutGlobalScopes()->where('type', 'detalhe')->where('name', 'like', '%' . ($template['modelo_keyword'] ?? '---') . '%')->first();

            // 3. Criar o produto
            $product = Product::create([
                'tenant_id' => auth()->user()->tenant_id,
                'title' => $template['title'],
                'description' => $template['description'],
                'category_id' => $category->id,
                'tecido_id' => $tecido?->id,
                'personalizacao_id' => $personalizacao?->id,
                'modelo_id' => $modelo?->id,
                'cut_type_id' => $request->input('cut_type_id'),
                'price' => $template['default_price'],
                'sale_type' => 'unidade',
                'allow_application' => $template['allow_application'] ?? false,
                'application_types' => ($template['allow_application'] ?? false) ? ['sublimacao_local', 'dtf'] : [],
                'active' => true,
                'show_in_catalog' => true,
                'order' => (Product::max('order') ?? 0) + 1,
            ]);

            return redirect()->route('admin.products.edit', $product)
                ->with('success', "Modelo '{$template['title']}' importado com sucesso! Agora você pode ajustar os detalhes e adicionar imagens.");
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Erro ao importar modelo: ' . $e->getMessage());
        }
    }

    private function getTemplates(): array
    {
        return [
            'camisa_basica' => [
                'title' => 'Camisa Básica PP',
                'description' => 'Camiseta clássica gola redonda, ideal para personalização e uniformes.',
                'category' => 'Camisetas',
                'tecido_keyword' => 'Algodão',
                'default_price' => 18.67,
                'icon' => 'fa-shirt',
                'compatible_cuts' => ['basica', 'babylook', 'infantil', 'longline', 'manga-curta'],
                'allow_application' => true,
            ],
            'manga_longa' => [
                'title' => 'Camisa Manga Longa Algodão',
                'description' => 'Camiseta manga longa ideal para climas amenos ou uniformização.',
                'category' => 'Camisetas',
                'tecido_keyword' => 'Malha',
                'default_price' => 45.00,
                'icon' => 'fa-shirt',
                'compatible_cuts' => ['manga-longa', 'basica'],
                'allow_application' => true,
            ],
            'polo_classic' => [
                'title' => 'Camisa Polo Piquet',
                'description' => 'Camisa polo com peitilho e gola em ribana. Visual executivo e profissional.',
                'category' => 'Polos',
                'tecido_keyword' => 'Piquet',
                'default_price' => 55.00,
                'icon' => 'fa-shirt',
                'compatible_cuts' => ['polo'],
                'allow_application' => true,
            ],
            'avental_tradicional' => [
                'title' => 'Avental Tradicional',
                'description' => 'Avental com regulagem no pescoço e bolsos frontais. Ideal para cozinha e eventos.',
                'category' => 'Acessórios',
                'tecido_keyword' => 'Oxford',
                'default_price' => 35.00,
                'icon' => 'fa-vest',
                'compatible_cuts' => ['avental'],
                'allow_application' => true,
            ],
            'calca_brim' => [
                'title' => 'Calça Profissional Brim',
                'description' => 'Calça resistente para trabalho pesado, com bolsos reforçados.',
                'category' => 'Calças',
                'tecido_keyword' => 'Brim',
                'default_price' => 75.00,
                'icon' => 'fa-user-tie',
                'compatible_cuts' => ['calca', 'bata'],
                'allow_application' => false,
            ],
            'bata_profissional' => [
                'title' => 'Bata ou Jaleco Profissional',
                'description' => 'Vestimenta para área de saúde, limpeza ou operacional. Com bolsos e botões.',
                'category' => 'Uniformes',
                'tecido_keyword' => 'Brim',
                'default_price' => 58.00,
                'icon' => 'fa-user-doctor',
                'compatible_cuts' => ['bata', 'jaleco'],
                'allow_application' => true,
            ],
            'bermuda_esportiva' => [
                'title' => 'Bermuda Esportiva Dry',
                'description' => 'Bermuda leve e confortável para prática de esportes e atividades físicas.',
                'category' => 'Esportivo',
                'tecido_keyword' => 'Dry',
                'default_price' => 32.00,
                'icon' => 'fa-person-running',
                'compatible_cuts' => ['bermuda', 'calcao', 'shorts'],
                'allow_application' => true,
            ],
            'colete_identificacao' => [
                'title' => 'Colete de Identificação',
                'description' => 'Colete leve para staff, eventos ou identificação rápida de equipes.',
                'category' => 'Eventos',
                'tecido_keyword' => 'Poliéster',
                'default_price' => 25.00,
                'icon' => 'fa-vest',
                'compatible_cuts' => ['colete'],
                'allow_application' => true,
            ],
            'moletom_canguru' => [
                'title' => 'Moletom Canguru com Capuz',
                'description' => 'Blusa de moletom com bolso frontal e capuz ajustável. Conforto térmico.',
                'category' => 'Moletons',
                'tecido_keyword' => 'Moletom',
                'default_price' => 120.00,
                'icon' => 'fa-vest',
                'compatible_cuts' => ['moletom', 'basica'],
                'allow_application' => true,
            ],
            'camisa_uv' => [
                'title' => 'Camisa Proteção UV',
                'description' => 'Camiseta com tratamento UV, ideal para trabalho ao ar livre ou esportes.',
                'category' => 'Esportivo',
                'tecido_keyword' => 'Poliamida',
                'default_price' => 65.00,
                'icon' => 'fa-sun',
                'compatible_cuts' => ['uv', 'manga-longa', 'manga-curta'],
                'allow_application' => true,
            ],
        ];
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
    public function getCutTypeStock(Request $request): \Illuminate\Http\JsonResponse
    {
        $cutTypeId = $request->query('cut_type_id');
        if (!$cutTypeId) {
            return response()->json(['success' => false, 'message' => 'ID não fornecido']);
        }

        try {
            // Buscar itens no estoque para este tipo de corte
            $stockItems = \App\Models\Stock::where('cut_type_id', $cutTypeId)
                ->with(['color' => function($query) {
                    $query->withoutGlobalScopes();
                }])
                ->get();

            $availableSizes = $stockItems->pluck('size')->unique()->filter()->values()->toArray();
            
            $availableColors = $stockItems->whereNotNull('color_id')
                ->groupBy('color_id')
                ->map(function ($items) {
                    $color = $items->first()->color;
                    return $color ? [
                        'name' => $color->name,
                        'hex' => $color->color_hex ?? '#666666'
                    ] : null;
                })
                ->filter()
                ->values()
                ->toArray();

            $totalQty = $stockItems->sum('quantity');

            return response()->json([
                'success' => true,
                'sizes' => $availableSizes,
                'colors' => $availableColors,
                'total_qty' => $totalQty
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

