<?php

namespace App\Http\Controllers;

use App\Models\Uniform;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UniformController extends Controller
{
    public function index(Request $request)
    {
        // 1. Stores for columns
        $stores = Store::orderBy('name')->get();

        // 2. Base query
        $query = Uniform::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 3. Get distinct products (definitions) to paginate
        // Group by Name, Type, Color, Gender, Size
        $products = $query->select('name', 'type', 'color', 'gender', 'size')
            ->distinct()
            ->orderBy('name')
            ->orderBy('size') // Order by size is tricky with strings ("38", "40", "GG"), but better than nothing
            ->paginate(15)
            ->withQueryString();

        // 4. Fetch ALL stock entries for these products
        $pageProducts = $products->items();
        $stockData = [];
        
        if (count($pageProducts) > 0) {
            $detailsQuery = Uniform::query();
            
            $detailsQuery->where(function($q) use ($pageProducts) {
                foreach ($pageProducts as $prod) {
                    $q->orWhere(function($sub) use ($prod) {
                        $sub->where('name', $prod->name)
                            ->where('type', $prod->type);
                        
                        // Handle nullables
                        $prod->color === null ? $sub->whereNull('color') : $sub->where('color', $prod->color);
                        $prod->gender === null ? $sub->whereNull('gender') : $sub->where('gender', $prod->gender);
                        $prod->size === null ? $sub->whereNull('size') : $sub->where('size', $prod->size);
                    });
                }
            });

            $details = $detailsQuery->with('store')->get();

            // Map details to the matrix structure
            foreach ($details as $detail) {
                $key = $detail->name . '|' . $detail->type . '|' . $detail->color . '|' . $detail->gender . '|' . $detail->size;
                $stockData[$key][$detail->store_id] = $detail;
            }
        }

        $types = Uniform::select('type')->distinct()->pluck('type');
        $genders = Uniform::select('gender')->whereNotNull('gender')->distinct()->pluck('gender');

        return view('uniforms.index', compact('products', 'stores', 'types', 'genders', 'stockData'));
    }

    public function create(): View
    {
        $stores = Store::all();
        return view('uniforms.create', compact('stores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Uniform::create($validated);

        return redirect()->route('uniforms.index')
            ->with('success', 'Item cadastrado com sucesso.');
    }

    public function edit(Uniform $uniform): View
    {
        $stores = Store::all();
        return view('uniforms.edit', compact('uniform', 'stores'));
    }

    public function update(Request $request, Uniform $uniform): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $uniform->update($validated);

        return redirect()->route('uniforms.index')
            ->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(Uniform $uniform): RedirectResponse
    {
        $uniform->delete();

        return redirect()->route('uniforms.index')
            ->with('success', 'Item removido com sucesso.');
    }
}
