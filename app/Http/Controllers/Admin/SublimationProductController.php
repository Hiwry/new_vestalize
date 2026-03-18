<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SublimationProductAddon;
use App\Models\SublimationProductPrice;
use App\Models\SublimationProductType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SublimationProductController extends Controller
{
    private function resolveProductTypeForTenant(string $type, ?int $tenantId): SublimationProductType
    {
        return SublimationProductType::with('tecido')
            ->where('slug', $type)
            ->where(function ($query) use ($tenantId) {
                $query->whereNull('tenant_id');

                if ($tenantId) {
                    $query->orWhere('tenant_id', $tenantId);
                }
            })
            ->orderByRaw(
                'CASE
                    WHEN tenant_id = ? THEN 0
                    WHEN tenant_id IS NULL THEN 1
                    ELSE 2
                END',
                [$tenantId]
            )
            ->firstOrFail();
    }

    /**
     * Lista todos os tipos de produto com cards
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.personalization-prices.edit', ['type' => 'SUB. TOTAL']);
    }

    /**
     * Adicionar novo tipo de produto
     */
    public function storeType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $slug = Str::slug($validated['name']);

        $exists = SublimationProductType::where('slug', $slug)
            ->where(function ($query) use ($tenantId) {
                $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Este tipo ja existe.');
        }

        $maxOrder = SublimationProductType::where(function ($query) use ($tenantId) {
            $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->max('order') ?? 0;

        SublimationProductType::create([
            'tenant_id' => $tenantId,
            'slug' => $slug,
            'name' => $validated['name'],
            'order' => $maxOrder + 1,
        ]);

        return redirect()
            ->back()
            ->with('success', "Tipo '{$validated['name']}' adicionado.");
    }

    /**
     * Excluir tipo de produto
     */
    public function destroyType(SublimationProductType $type): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        if ($type->tenant_id !== $tenantId) {
            return redirect()
                ->back()
                ->with('error', 'Nao e possivel excluir tipos padrao.');
        }

        SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type->slug)
            ->delete();

        SublimationProductAddon::where('tenant_id', $tenantId)
            ->where('product_type', $type->slug)
            ->delete();

        $name = $type->name;
        $type->delete();

        return redirect()
            ->back()
            ->with('success', "Tipo '{$name}' removido.");
    }

    public function editType(Request $request, string $type): View
    {
        $tenantId = auth()->user()->tenant_id;
        $productType = $this->resolveProductTypeForTenant($type, $tenantId);

        // Se veio um tecido_id na query string, usa ele. Caso contrrio vincula ao tecido atual do produto.
        $selectedTecidoId = $request->query('tecido_id', $productType->tecido_id);

        $prices = SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->where('tecido_id', $selectedTecidoId)
            ->orderBy('quantity_from')
            ->get();

        $addons = SublimationProductAddon::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->orderBy('order')
            ->get();

        $tecidos = \App\Models\Tecido::where('active', true)->orderBy('name')->get();

        return view('admin.sublimation-products.edit-type', [
            'type' => $type,
            'productType' => $productType,
            'selectedTecidoId' => $selectedTecidoId,
            'typeLabel' => $productType->name,
            'typeIcon' => $productType->icon,
            'prices' => $prices,
            'addons' => $addons,
            'tecidos' => $tecidos,
        ]);
    }

    /**
     * Atualizar precos de um tipo de produto
     */
    public function updateType(Request $request, string $type): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $validated = $request->validate([
            'tecido_id' => 'required|exists:tecidos,id',
            'prices' => 'nullable|array',
            'prices.*.id' => 'nullable|integer',
            'prices.*.quantity_from' => 'nullable|integer|min:1',
            'prices.*.quantity_to' => 'nullable|integer|min:1',
            'prices.*.price' => 'nullable|numeric|min:0',
        ]);

        $productType = $this->resolveProductTypeForTenant($type, $tenantId);
        $productType->update([
            'tecido_id' => $validated['tecido_id'],
        ]);

        SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->where('tecido_id', $validated['tecido_id'])
            ->delete();

        $pricesData = $validated['prices'] ?? [];
        foreach ($pricesData as $priceData) {
            if (empty($priceData['quantity_from']) || empty($priceData['price'])) {
                continue;
            }

            SublimationProductPrice::create([
                'tenant_id' => $tenantId,
                'product_type' => $type,
                'tecido_id' => $validated['tecido_id'],
                'quantity_from' => $priceData['quantity_from'],
                'quantity_to' => $priceData['quantity_to'] ?: null,
                'price' => $priceData['price'],
            ]);
        }

        return redirect()
            ->route('admin.sublimation-products.edit-type', ['type' => $type, 'tecido_id' => $validated['tecido_id']])
            ->with('success', 'Configuracao salva com sucesso.');
    }

    /**
     * Toggle SUB. TOTAL habilitado para o tenant
     */
    public function toggleEnabled(Request $request): RedirectResponse
    {
        $tenant = auth()->user()->tenant;

        if (!$tenant) {
            return redirect()
                ->back()
                ->with('error', 'Super Admin nao pode ativar ou desativar SUB. TOTAL.');
        }

        $tenant->sublimation_total_enabled = !$tenant->sublimation_total_enabled;
        $tenant->save();

        $status = $tenant->sublimation_total_enabled ? 'habilitada' : 'desabilitada';

        return redirect()
            ->back()
            ->with('success', "SUB. TOTAL {$status}.");
    }

    /**
     * Atualizar modelos de um tipo de produto
     */
    public function updateModels(Request $request, string $type): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $productType = $this->resolveProductTypeForTenant($type, $tenantId);

        // Se o tenant não é dono do tipo, cria uma cópia para o tenant
        if ($productType->tenant_id !== $tenantId) {
            $productType = $productType->replicate();
            $productType->tenant_id = $tenantId;
            $productType->save();
        }

        $models = $request->input('models', []);
        // Normalizar: uppercase e remover vazios
        $models = array_values(array_filter(array_map(function ($m) {
            return strtoupper(trim($m));
        }, $models)));

        $productType->update(['models' => $models]);

        return redirect()
            ->route('admin.sublimation-products.edit-type', $type)
            ->with('success', 'Modelos atualizados com sucesso.');
    }

    /**
     * Adicionar addon para um tipo de produto
     */
    public function storeAddon(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $tenantId = auth()->user()->tenant_id;

        SublimationProductAddon::create([
            'tenant_id' => $tenantId,
            'product_type' => $type,
            'name' => strtoupper($validated['name']),
            'price' => $validated['price'],
            'order' => SublimationProductAddon::where('tenant_id', $tenantId)
                ->where('product_type', $type)
                ->count(),
        ]);

        return redirect()
            ->route('admin.sublimation-products.edit-type', $type)
            ->with('success', 'Adicional adicionado.');
    }

    /**
     * Atualizar addon
     */
    public function updateAddon(Request $request, SublimationProductAddon $addon): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        if ((int) $addon->tenant_id !== (int) $tenantId) {
            return redirect()
                ->route('admin.sublimation-products.edit-type', $addon->product_type)
                ->with('error', 'Nao e possivel alterar este adicional.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $addon->update([
            'name' => strtoupper($validated['name']),
            'price' => $validated['price'],
        ]);

        return redirect()
            ->route('admin.sublimation-products.edit-type', $addon->product_type)
            ->with('success', 'Adicional atualizado.');
    }

    /**
     * Excluir addon
     */
    public function destroyAddon(SublimationProductAddon $addon): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        if ((int) $addon->tenant_id !== (int) $tenantId) {
            return redirect()
                ->route('admin.sublimation-products.edit-type', $addon->product_type)
                ->with('error', 'Nao e possivel remover este adicional.');
        }

        $type = $addon->product_type;
        $addon->delete();

        return redirect()
            ->route('admin.sublimation-products.edit-type', $type)
            ->with('success', 'Adicional removido.');
    }
}
