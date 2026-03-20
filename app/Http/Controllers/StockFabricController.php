<?php

namespace App\Http\Controllers;

use App\Models\ProductOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StockFabricController extends Controller
{
    private function normalizePrice(?string $price): ?string
    {
        if ($price === null || $price === '') {
            return null;
        }

        return str_replace(',', '.', $price);
    }

    private function fabricQuery()
    {
        return ProductOption::query()->where('type', 'tipo_tecido');
    }

    private function findFabricOrFail(int|string $id): ProductOption
    {
        return $this->fabricQuery()->with('parent')->findOrFail($id);
    }

    private function parentFabricOptions()
    {
        return ProductOption::query()
            ->where('type', 'tecido')
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    public function index(): View
    {
        $fabrics = $this->fabricQuery()
            ->with(['parent'])
            ->withCount('fabricTypePieces')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $groupedFabrics = $fabrics
            ->sortBy(function (ProductOption $fabric) {
                $parentName = mb_strtolower($fabric->parent?->name ?? 'sem tecido');
                $name = mb_strtolower($fabric->name);

                return $parentName . '|' . str_pad((string) $fabric->order, 10, '0', STR_PAD_LEFT) . '|' . $name;
            })
            ->groupBy(fn (ProductOption $fabric) => $fabric->parent?->name ?? 'Sem tecido pai');

        return view('admin.stock-fabrics.index', compact('groupedFabrics'));
    }

    public function create(): View
    {
        $parentFabrics = $this->parentFabricOptions();

        return view('admin.stock-fabrics.create', compact('parentFabrics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:product_options,id',
            'price' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'type' => 'tipo_tecido',
            'name' => $validated['name'],
            'parent_type' => 'tecido',
            'parent_id' => $validated['parent_id'],
            'price' => $validated['price'] ?? 0,
            'active' => $request->boolean('active', true),
            'order' => $validated['order'] ?? ((int) $this->fabricQuery()->max('order') + 1),
        ];

        if (Schema::hasColumn('product_options', 'tenant_id')) {
            $data['tenant_id'] = Auth::user()?->tenant_id;
        }

        ProductOption::create($data);

        return redirect()->route('stock-fabrics.index')
            ->with('success', 'Tipo de tecido criado com sucesso!');
    }

    public function edit(int|string $id): View
    {
        $fabric = $this->findFabricOrFail($id);
        $parentFabrics = $this->parentFabricOptions();

        return view('admin.stock-fabrics.edit', compact('fabric', 'parentFabrics'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $fabric = $this->findFabricOrFail($id);

        $request->merge([
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:product_options,id',
            'price' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $newPrice = (float) ($validated['price'] ?? 0);
        $priceChanged = abs((float) $fabric->price - $newPrice) > 0.00001;
        $syncedPieces = 0;

        DB::transaction(function () use ($fabric, $validated, $request, $newPrice, $priceChanged, &$syncedPieces) {
            $fabric->update([
                'name' => $validated['name'],
                'parent_type' => 'tecido',
                'parent_id' => $validated['parent_id'],
                'price' => $newPrice,
                'active' => $request->boolean('active'),
                'order' => $validated['order'] ?? $fabric->order,
            ]);

            if ($priceChanged) {
                $syncedPieces = $fabric->fabricTypePieces()
                    ->active()
                    ->update(['sale_price' => $newPrice]);
            }
        });

        $successMessage = 'Tipo de tecido atualizado com sucesso!';

        if ($priceChanged) {
            $successMessage .= ' ' . $syncedPieces . ' peça(s) ativa(s) tiveram o preço por unidade sincronizado.';
        }

        return redirect()->route('stock-fabrics.index')
            ->with('success', $successMessage);
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $fabric = $this->findFabricOrFail($id);
        $fabric->delete();

        return redirect()->route('stock-fabrics.index')
            ->with('success', 'Tipo de tecido excluído com sucesso!');
    }
}