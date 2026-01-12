<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SublimationProductType;
use App\Models\SublimationProductPrice;
use App\Models\SublimationProductAddon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SublimationProductController extends Controller
{
    /**
     * Lista todos os tipos de produto com cards
     */
    public function index(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $tenant = auth()->user()->tenant;
        
        // Super Admin
        if (!$tenant) {
            $tenant = (object) [
                'sublimation_total_enabled' => false,
                'id' => null,
            ];
        }
        
        // Buscar tipos dinâmicos
        $types = SublimationProductType::getForTenant($tenantId);
        
        // Montar dados para cada tipo
        $productTypes = [];
        foreach ($types as $type) {
            $prices = SublimationProductPrice::where('tenant_id', $tenantId)
                ->where('product_type', $type->slug)
                ->get();
            
            $addonsCount = SublimationProductAddon::where('tenant_id', $tenantId)
                ->where('product_type', $type->slug)
                ->count();
            
            $productTypes[] = [
                'slug' => $type->slug,
                'label' => $type->name,
                'icon' => $type->icon,
                'prices_count' => $prices->count(),
                'addons_count' => $addonsCount,
                'min_price' => $prices->min('price'),
            ];
        }

        return view('admin.sublimation-products.index', compact('productTypes', 'tenant'));
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

        // Verificar se já existe
        $exists = SublimationProductType::where('slug', $slug)
            ->where(function($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Este tipo já existe!');
        }

        $maxOrder = SublimationProductType::where(function($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        })->max('order') ?? 0;

        SublimationProductType::create([
            'tenant_id' => $tenantId,
            'slug' => $slug,
            'name' => $validated['name'],
            'order' => $maxOrder + 1,
        ]);

        return redirect()
            ->route('admin.sublimation-products.index')
            ->with('success', "Tipo '{$validated['name']}' adicionado!");
    }

    /**
     * Excluir tipo de produto
     */
    public function destroyType(SublimationProductType $type): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        // Só pode excluir tipos do próprio tenant
        if ($type->tenant_id !== $tenantId) {
            return redirect()
                ->back()
                ->with('error', 'Não é possível excluir tipos padrão.');
        }

        // Excluir preços e adicionais relacionados
        SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type->slug)
            ->delete();
        
        SublimationProductAddon::where('tenant_id', $tenantId)
            ->where('product_type', $type->slug)
            ->delete();

        $name = $type->name;
        $type->delete();

        return redirect()
            ->route('admin.sublimation-products.index')
            ->with('success', "Tipo '{$name}' removido!");
    }

    /**
     * Editar preços de um tipo de produto
     */
    public function editType(string $type): View
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Buscar o tipo
        $productType = SublimationProductType::where('slug', $type)
            ->where(function($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->firstOrFail();
        
        $prices = SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->orderBy('quantity_from')
            ->get();

        // Adicionais deste tipo
        $addons = SublimationProductAddon::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->orderBy('order')
            ->get();

        // Tecidos disponíveis
        $tecidos = \App\Models\Tecido::where('active', true)->orderBy('name')->get();

        return view('admin.sublimation-products.edit-type', [
            'type' => $type,
            'productType' => $productType,
            'typeLabel' => $productType->name,
            'typeIcon' => $productType->icon,
            'prices' => $prices,
            'addons' => $addons,
            'tecidos' => $tecidos,
        ]);
    }

    /**
     * Atualizar preços de um tipo de produto
     */
    public function updateType(Request $request, string $type): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        // Atualizar tecido padrão do tipo
        $productType = SublimationProductType::where('slug', $type)
            ->where(function($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->firstOrFail();
            
        $productType->update([
            'tecido_id' => $request->tecido_id
        ]);
        
        // Deletar preços existentes deste tipo
        SublimationProductPrice::where('tenant_id', $tenantId)
            ->where('product_type', $type)
            ->delete();
        
        // Criar novos preços
        $pricesData = $request->input('prices', []);
        foreach ($pricesData as $priceData) {
            if (empty($priceData['quantity_from']) || empty($priceData['price'])) {
                continue;
            }
            
            SublimationProductPrice::create([
                'tenant_id' => $tenantId,
                'product_type' => $type,
                'quantity_from' => $priceData['quantity_from'],
                'quantity_to' => $priceData['quantity_to'] ?: null,
                'price' => $priceData['price'],
            ]);
        }

        return redirect()
            ->route('admin.sublimation-products.edit-type', $type)
            ->with('success', 'Preços salvos com sucesso!');
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
                ->with('error', 'Super Admin não pode ativar/desativar SUB. TOTAL.');
        }
        
        $tenant->sublimation_total_enabled = !$tenant->sublimation_total_enabled;
        $tenant->save();

        $status = $tenant->sublimation_total_enabled ? 'habilitada' : 'desabilitada';

        return redirect()
            ->back()
            ->with('success', "SUB. TOTAL {$status}!");
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
            ->with('success', 'Adicional adicionado!');
    }

    /**
     * Excluir addon
     */
    public function destroyAddon(SublimationProductAddon $addon): RedirectResponse
    {
        $type = $addon->product_type;
        $addon->delete();

        return redirect()
            ->route('admin.sublimation-products.edit-type', $type)
            ->with('success', 'Adicional removido!');
    }
}
