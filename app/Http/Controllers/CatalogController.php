<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\CatalogItem;
use App\Models\CatalogCategory;

class CatalogController extends Controller
{
    /**
     * Exibir catálogo público
     */
    public function index(Request $request): View
    {
        if (!Schema::hasTable('catalog_items') || !Schema::hasTable('catalog_categories')) {
            $items = new LengthAwarePaginator([], 0, 12, 1);
            $categories = collect([]);

            return view('catalog.index', [
                'items' => $items,
                'categories' => $categories,
                'message' => 'O catálogo ainda não foi configurado. As tabelas do banco de dados precisam ser criadas.',
            ]);
        }

        try {
            $query = CatalogItem::with('category');

            if ($request->filled('category')) {
                $query->where('catalog_category_id', $request->category);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('subtitle', 'like', "%{$search}%");
                });
            }

            $items = $query->where('active', true)
                ->orderBy('order')
                ->orderBy('title')
                ->paginate(12)
                ->appends($request->query());

            $categories = CatalogCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            $items = new LengthAwarePaginator([], 0, 12, 1);
            $categories = collect([]);
        }

        return view('catalog.index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    /**
     * Exibir detalhes de um item de catálogo
     */
    public function show($id): View
    {
        if (!Schema::hasTable('catalog_items')) {
            abort(404, 'O catálogo ainda não foi configurado.');
        }

        try {
            $item = CatalogItem::with('category')
                ->where('active', true)
                ->findOrFail($id);
        } catch (\Exception $e) {
            abort(404, 'Item não encontrado.');
        }

        return view('catalog.show', compact('item'));
    }
}

