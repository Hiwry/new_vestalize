<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class QuickProductController extends Controller
{
    /**
     * Exibir página de cadastro rápido
     */
    public function index(): View
    {
        return view('admin.quick-products.index');
    }

    /**
     * Cadastrar malha/tecido
     */
    public function storeFabric(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('products')) {
            return redirect()->route('admin.quick-products.index')
                ->with('error', 'A tabela de produtos ainda não foi criada. Execute: php artisan migrate');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_type' => 'required|in:kg,metro',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        
        // Verificar e criar coluna sale_type se não existir
        if (!Schema::hasColumn('products', 'sale_type')) {
            try {
                DB::statement("ALTER TABLE products ADD COLUMN sale_type ENUM('unidade', 'kg', 'metro') DEFAULT 'unidade' AFTER price");
            } catch (\Exception $e) {
                return redirect()->route('admin.quick-products.index')
                    ->with('error', 'Erro ao criar coluna sale_type. Execute: php artisan migrate');
            }
        }
        
        // Verificar e criar coluna order se não existir
        if (!Schema::hasColumn('products', 'order')) {
            try {
                DB::statement("ALTER TABLE products ADD COLUMN `order` INT DEFAULT 0 AFTER active");
            } catch (\Exception $e) {
                // Se falhar, não é crítico, apenas não adiciona o order
            }
        }
        
        $validated['sale_type'] = $validated['sale_type'];
        
        // Adicionar order apenas se a coluna existir
        if (Schema::hasColumn('products', 'order')) {
            $validated['order'] = (Product::max('order') ?? 0) + 1;
        }

        // Filtrar apenas campos que existem na tabela
        $columns = Schema::getColumnListing('products');
        $validated = array_intersect_key($validated, array_flip($columns));

        Product::create($validated);

        return redirect()->route('admin.quick-products.index')
            ->with('success', 'Malha/Tecido cadastrado com sucesso!');
    }

    /**
     * Cadastrar produto por unidade
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('products')) {
            return redirect()->route('admin.quick-products.index')
                ->with('error', 'A tabela de produtos ainda não foi criada. Execute: php artisan migrate');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        
        // Verificar e criar coluna sale_type se não existir
        if (!Schema::hasColumn('products', 'sale_type')) {
            try {
                DB::statement("ALTER TABLE products ADD COLUMN sale_type ENUM('unidade', 'kg', 'metro') DEFAULT 'unidade' AFTER price");
            } catch (\Exception $e) {
                return redirect()->route('admin.quick-products.index')
                    ->with('error', 'Erro ao criar coluna sale_type. Execute: php artisan migrate');
            }
        }
        
        // Verificar e criar coluna order se não existir
        if (!Schema::hasColumn('products', 'order')) {
            try {
                DB::statement("ALTER TABLE products ADD COLUMN `order` INT DEFAULT 0 AFTER active");
            } catch (\Exception $e) {
                // Se falhar, não é crítico, apenas não adiciona o order
            }
        }
        
        $validated['sale_type'] = 'unidade';
        
        // Adicionar order apenas se a coluna existir
        if (Schema::hasColumn('products', 'order')) {
            $validated['order'] = (Product::max('order') ?? 0) + 1;
        }

        // Filtrar apenas campos que existem na tabela
        $columns = Schema::getColumnListing('products');
        $validated = array_intersect_key($validated, array_flip($columns));

        Product::create($validated);

        return redirect()->route('admin.quick-products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }
}

