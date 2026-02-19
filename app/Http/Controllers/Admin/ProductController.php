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
        
        // Buscar template no banco
        $template = \App\Models\ProductTemplate::find($templateId);

        if (!$template) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Template não encontrado.');
        }

        try {
            // 1. Garantir que a categoria existe
            $user = auth()->user();
            if (!$user || !$user->tenant_id) {
                throw new \Exception('Usuário sem tenant vinculado.');
            }
            $tenantId = $user->tenant_id;
            
            $baseSlug = Str::slug($template->category);
            
            // Buscar categoria existente ou criar
            $category = Category::where('tenant_id', $tenantId)
                ->where('name', $template->category)
                ->first();
            
            if (!$category) {
                // Gerar slug único
                $slug = $baseSlug;
                $counter = 1;
                while (Category::where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $category = Category::create([
                    'tenant_id' => $tenantId,
                    'name' => $template->category,
                    'slug' => $slug,
                    'active' => true,
                ]);
            }

            // 2. Tentar encontrar atributos pelos nomes (Case-insensitive)
            $tecido = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('type', 'tecido')
                ->where('name', 'like', '%' . ($template->tecido_keyword ?? '---') . '%')
                ->first();
                
            $personalizacao = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('type', 'personalizacao')
                ->where('name', 'like', '%' . ($template->personalizacao_keyword ?? '---') . '%')
                ->first();
                
            $modelo = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('type', 'detalhe')
                ->where('name', 'like', '%' . ($template->modelo_keyword ?? '---') . '%')
                ->first();

            // 2.1 Resolver tipo de corte automaticamente quando não vier do modal
            $requestedCutTypeId = $request->filled('cut_type_id') ? (int) $request->input('cut_type_id') : null;
            $cutTypeQuery = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('type', 'tipo_corte')
                ->where('active', true);

            if (Schema::hasColumn('product_options', 'tenant_id')) {
                $cutTypeQuery->where(function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                });
            }

            $availableCutTypes = $cutTypeQuery->get(['id', 'name']);
            $resolvedCutTypeId = null;

            if ($requestedCutTypeId && $availableCutTypes->contains('id', $requestedCutTypeId)) {
                $resolvedCutTypeId = $requestedCutTypeId;
            }

            if (!$resolvedCutTypeId && is_array($template->compatible_cuts) && !empty($template->compatible_cuts)) {
                $compatibleSlugs = collect($template->compatible_cuts)
                    ->map(static fn ($value) => Str::slug((string) $value))
                    ->filter()
                    ->values()
                    ->all();

                $compatibleMatch = $availableCutTypes->first(function ($cutType) use ($compatibleSlugs) {
                    return in_array(Str::slug((string) $cutType->name), $compatibleSlugs, true);
                });

                if ($compatibleMatch) {
                    $resolvedCutTypeId = (int) $compatibleMatch->id;
                }
            }

            if (!$resolvedCutTypeId && !empty($template->title)) {
                $normalizedTitle = Str::lower(Str::ascii((string) $template->title));
                $bestScore = 0;

                foreach ($availableCutTypes as $cutType) {
                    $normalizedCutName = Str::lower(Str::ascii((string) $cutType->name));
                    if ($normalizedCutName === '') {
                        continue;
                    }

                    $score = 0;
                    if ($normalizedCutName === $normalizedTitle) {
                        $score += 1000;
                    } elseif (str_contains($normalizedTitle, $normalizedCutName) || str_contains($normalizedCutName, $normalizedTitle)) {
                        $score += max(strlen($normalizedCutName), strlen($normalizedTitle));
                    }

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $resolvedCutTypeId = (int) $cutType->id;
                    }
                }
            }

            // 3. Criar o produto
            $product = Product::create([
                'tenant_id' => $tenantId,
                'title' => $template->title,
                'description' => $template->description,
                'category_id' => $category->id,
                'tecido_id' => $tecido?->id,
                'personalizacao_id' => $personalizacao?->id,
                'modelo_id' => $modelo?->id,
                'cut_type_id' => $resolvedCutTypeId,
                'price' => $template->default_price,
                'sale_type' => 'unidade',
                'allow_application' => $template->allow_application,
                'application_types' => $template->allow_application ? ['sublimacao_local', 'dtf'] : [],
                'active' => true,
                'show_in_catalog' => true,
                'order' => (Product::where('tenant_id', $tenantId)->max('order') ?? 0) + 1,
            ]);

            return redirect()->route('admin.products.edit', $product)
                ->with('success', "Modelo '{$template->title}' importado com sucesso! Agora você pode ajustar os detalhes e adicionar imagens.");
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Erro ao importar modelo: ' . $e->getMessage());
        }
    }

    private function getTemplates(): array
    {
        $tenantId = auth()->user()->tenant_id;
        
        $count = \App\Models\ProductTemplate::where('tenant_id', $tenantId)->count();
        \Illuminate\Support\Facades\Log::info("getTemplates called for Tenant: {$tenantId}. Existing templates: {$count}");

        // Se ainda não houver templates no banco para este tenant, popular com os padrões relevantes
        if ($count === 0) {
            \Illuminate\Support\Facades\Log::info("No templates found. Seeding now...");
            $this->seedTenantTemplates($tenantId);
        }

        // Retornar templates exclusivos do tenant
        $templates = \App\Models\ProductTemplate::where('tenant_id', $tenantId)
            ->where('active', true)
            ->get();
            
        \Illuminate\Support\Facades\Log::info("Returning {$templates->count()} templates.");

        return $templates->keyBy('id')->toArray();
    }

    private function seedTenantTemplates(int $tenantId): void
    {
        \Illuminate\Support\Facades\Log::info("Starting seedTenantTemplates for Tenant {$tenantId}");

        // Buscar os tipos de corte que o cliente REALMENTE tem
        $activeCutTypes = \App\Models\ProductOption::where('tenant_id', $tenantId)
            ->where('type', 'tipo_corte')
            ->where('active', true)
            ->get();
            
        \Illuminate\Support\Facades\Log::info("Found {$activeCutTypes->count()} active cut types for tenant.");

        if ($activeCutTypes->isEmpty()) {
            \Illuminate\Support\Facades\Log::warning("No cut types found! Seeding aborted.");
            return;
        }

        foreach ($activeCutTypes as $cut) {
            $cutName = $cut->name;
            $slug = Str::slug($cutName);
            $lowerName = mb_strtolower($cutName);

            \Illuminate\Support\Facades\Log::info("Processing Cut: {$cutName} (Slug: {$slug})");

            // Heurística para determinar Categoria e Ícone
            $category = 'Outros';
            $icon = 'fa-shirt';
            $tecido = null;
            $allowApp = true;

            if (str_contains($lowerName, 'colete')) {
                $category = 'Eventos';
                $icon = 'fa-vest';
            } elseif (str_contains($lowerName, 'calça') || str_contains($lowerName, 'calca')) {
                $category = 'Calças';
                $icon = 'fa-user-tie';
                $allowApp = false;
            } elseif (str_contains($lowerName, 'bermuda') || str_contains($lowerName, 'shorts')) {
                $category = 'Esportivo';
                $icon = 'fa-person-running';
            } elseif (str_contains($lowerName, 'avental')) {
                $category = 'Acessórios';
                $icon = 'fa-vest-patches';
            } elseif (str_contains($lowerName, 'polo')) {
                $category = 'Polos';
                $icon = 'fa-shirt';
            } elseif (str_contains($lowerName, 'moletom')) {
                $category = 'Moletons';
                $icon = 'fa-user-astronaut'; // Icone mais "fechado"
            } elseif (str_contains($lowerName, 'bata') || str_contains($lowerName, 'jaleco')) {
                $category = 'Uniformes';
                $icon = 'fa-user-doctor';
            } elseif (str_contains($lowerName, 'manga longa')) {
                $category = 'Camisetas';
                $icon = 'fa-shirt'; // Poderia ser outro
            } elseif (str_contains($lowerName, 'infantil') || str_contains($lowerName, 'babylook') || str_contains($lowerName, 'básica') || str_contains($lowerName, 'basica')) {
                $category = 'Camisetas';
                $icon = 'fa-shirt';
            }

            // Tentar extrair tecido do nome (ex: "Básica Algodão")
            if (str_contains($lowerName, 'algodão') || str_contains($lowerName, 'algodao')) $tecido = 'Algodão';
            elseif (str_contains($lowerName, 'pv')) $tecido = 'PV';
            elseif (str_contains($lowerName, 'piquet')) $tecido = 'Piquet';
            elseif (str_contains($lowerName, 'dry')) $tecido = 'Dry';
            elseif (str_contains($lowerName, 'poliamida')) $tecido = 'Poliamida';
            elseif (str_contains($lowerName, 'brim')) $tecido = 'Brim';
            elseif (str_contains($lowerName, 'oxford')) $tecido = 'Oxford';
            elseif (str_contains($lowerName, 'cacharrel')) $tecido = 'Cacharrel';

            // Criar o template baseada EXATAMENTE no corte existente
            $template = \App\Models\ProductTemplate::create([
                'tenant_id' => $tenantId,
                'title' => $cutName, // O título do modelo é o próprio nome do corte
                'description' => "Modelo baseado no corte {$cutName}. Ideal para produção sob demanda.",
                'category' => $category,
                'tecido_keyword' => $tecido,
                'default_price' => $cut->price ?? 0.00, // Usa o preço base do corte!
                'icon' => $icon,
                'compatible_cuts' => [$slug], // Compatível apenas com ele mesmo
                'allow_application' => $allowApp,
                'active' => true,
            ]);
            
            \Illuminate\Support\Facades\Log::info("Created Template ID: {$template->id} for '{$cutName}'");
        }
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

    public function suggestCutType(Request $request): \Illuminate\Http\JsonResponse
    {
        $tecidoId = $request->query('tecido_id');
        $personalizacaoId = $request->query('personalizacao_id');
        $modeloId = $request->query('modelo_id');
        $title = trim((string) $request->query('title', ''));

        if (!$tecidoId && !$personalizacaoId && !$modeloId && $title === '') {
            return response()->json([
                'success' => true,
                'cut_type_id' => null,
            ]);
        }

        $normalize = static function (?string $value): string {
            $normalized = Str::lower(Str::ascii((string) $value));
            $normalized = preg_replace('/[^a-z0-9\\s]/', ' ', $normalized);
            $normalized = preg_replace('/\\s+/', ' ', trim((string) $normalized));
            return $normalized ?? '';
        };

        try {
            $cutTypeId = null;

            $availableCutTypes = \App\Models\ProductOption::withoutGlobalScopes()
                ->where('type', 'tipo_corte')
                ->where('active', true)
                ->get(['id', 'name']);

            $availableCutTypeIds = $availableCutTypes->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            if (!empty($tecidoId)) {
                $bestFromStock = \App\Models\Stock::query()
                    ->whereNotNull('cut_type_id')
                    ->where('fabric_id', $tecidoId)
                    ->selectRaw('cut_type_id, SUM(quantity) as total_qty, COUNT(*) as item_count')
                    ->groupBy('cut_type_id')
                    ->orderByDesc('total_qty')
                    ->orderByDesc('item_count')
                    ->first();

                if ($bestFromStock && in_array((int) $bestFromStock->cut_type_id, $availableCutTypeIds, true)) {
                    $cutTypeId = (int) $bestFromStock->cut_type_id;
                }
            }

            if (!$cutTypeId) {
                $candidateOptionIds = collect([$tecidoId, $personalizacaoId, $modeloId])
                    ->filter(static fn ($value) => !empty($value))
                    ->map(static fn ($value) => (int) $value)
                    ->values();

                $keywords = \App\Models\ProductOption::withoutGlobalScopes()
                    ->whereIn('id', $candidateOptionIds)
                    ->pluck('name')
                    ->flatMap(static function ($name) use ($normalize) {
                        $normalized = $normalize($name);
                        return $normalized !== '' ? explode(' ', $normalized) : [];
                    })
                    ->filter(static fn ($word) => strlen((string) $word) >= 3)
                    ->unique()
                    ->values();

                if ($keywords->isNotEmpty()) {
                    $bestScore = 0;
                    $bestId = null;

                    foreach ($availableCutTypes as $cutType) {
                        $normalizedCutName = $normalize($cutType->name);
                        if ($normalizedCutName === '') {
                            continue;
                        }

                        $score = 0;
                        foreach ($keywords as $keyword) {
                            if (str_contains($normalizedCutName, (string) $keyword)) {
                                $score += strlen((string) $keyword);
                            }
                        }

                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $bestId = (int) $cutType->id;
                        }
                    }

                    if (!empty($bestId)) {
                        $cutTypeId = $bestId;
                    }
                }
            }

            if (!$cutTypeId && $title !== '') {
                $normalizedTitle = $normalize($title);
                $titleWords = collect(explode(' ', $normalizedTitle))
                    ->filter(static fn ($word) => strlen((string) $word) >= 3)
                    ->values();

                $bestScore = 0;
                $bestId = null;

                foreach ($availableCutTypes as $cutType) {
                    $normalizedCutName = $normalize($cutType->name);
                    if ($normalizedCutName === '') {
                        continue;
                    }

                    $score = 0;
                    if ($normalizedCutName === $normalizedTitle) {
                        $score += 1000;
                    } elseif (
                        str_contains($normalizedTitle, $normalizedCutName) ||
                        str_contains($normalizedCutName, $normalizedTitle)
                    ) {
                        $score += max(strlen($normalizedCutName), strlen($normalizedTitle));
                    }

                    foreach ($titleWords as $word) {
                        if (str_contains($normalizedCutName, (string) $word)) {
                            $score += strlen((string) $word);
                        }
                    }

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestId = (int) $cutType->id;
                    }
                }

                if (!empty($bestId)) {
                    $cutTypeId = $bestId;
                }
            }

            return response()->json([
                'success' => true,
                'cut_type_id' => $cutTypeId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

