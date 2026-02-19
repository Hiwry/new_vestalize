<?php

namespace App\Http\Controllers;

use App\Models\ProductionSupply;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductionSupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Stores for columns
        $stores = Store::orderBy('name')->get();

        // 2. Base query
        $query = ProductionSupply::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Get distinct products (definitions) to paginate
        $products = $query->select('name', 'type', 'color', 'unit')
            ->distinct()
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // 4. Fetch ALL stock entries for these products
        $pageProducts = $products->items();
        $stockData = [];
        
        if (count($pageProducts) > 0) {
            $detailsQuery = ProductionSupply::query();
            
            $detailsQuery->where(function($q) use ($pageProducts) {
                foreach ($pageProducts as $prod) {
                    $q->orWhere(function($sub) use ($prod) {
                        $sub->where('name', $prod->name)
                            ->where('type', $prod->type)
                            ->where('unit', $prod->unit);
                        
                        if ($prod->color === null) {
                            $sub->whereNull('color');
                        } else {
                            $sub->where('color', $prod->color);
                        }
                    });
                }
            });

            $details = $detailsQuery->with('store')->get();

            // Map details to the matrix structure
            foreach ($details as $detail) {
                // Key to match the grouping in the view
                $key = $detail->name . '|' . $detail->type . '|' . $detail->color . '|' . $detail->unit;
                $stockData[$key][$detail->store_id] = $detail;
            }
        }

        $types = ProductionSupply::select('type')->distinct()->pluck('type');

        return view('production-supplies.index', compact('products', 'stores', 'types', 'stockData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::all();
        return view('production-supplies.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        ProductionSupply::create($validated);

        return redirect()->route('production-supplies.index')
            ->with('success', 'Material cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionSupply $productionSupply): View
    {
        $stores = Store::all();
        return view('production-supplies.edit', compact('productionSupply', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionSupply $productionSupply): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $productionSupply->update($validated);

        return redirect()->route('production-supplies.index')
            ->with('success', 'Material atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductionSupply $productionSupply): RedirectResponse
    {
        $productionSupply->delete();

        return redirect()->route('production-supplies.index')
            ->with('success', 'Material removido com sucesso.');
    }
}
