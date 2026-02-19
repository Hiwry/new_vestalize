<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SublimationSize;
use App\Models\SublimationPrice;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SublimationPriceController extends Controller
{
    public function index(): View
    {
        $sizes = SublimationSize::with('prices')
            ->where('active', true)
            ->orderBy('order')
            ->get();

        return view('admin.sublimation-prices.index', compact('sizes'));
    }

    public function edit(SublimationSize $size): View
    {
        $prices = $size->prices()->orderBy('quantity_from')->get();
        return view('admin.sublimation-prices.edit', compact('size', 'prices'));
    }

    public function update(Request $request, SublimationSize $size): RedirectResponse
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.quantity_from' => 'required|integer|min:1',
            'prices.*.quantity_to' => 'nullable|integer|min:1|gte:prices.*.quantity_from',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        // Deletar preços existentes
        $size->prices()->delete();

        // Criar novos preços
        foreach ($validated['prices'] as $priceData) {
            if (!empty($priceData['quantity_from']) && !empty($priceData['price'])) {
                SublimationPrice::create([
                    'size_id' => $size->id,
                    'quantity_from' => $priceData['quantity_from'],
                    'quantity_to' => $priceData['quantity_to'] ?: null,
                    'price' => $priceData['price'],
                ]);
            }
        }

        return redirect()->route('admin.sublimation-prices.index')
            ->with('success', 'Preços atualizados com sucesso!');
    }

    public function addPriceRow(Request $request): View
    {
        $index = $request->get('index', 0);
        return view('admin.sublimation-prices.partials.price-row', compact('index'));
    }
}
