<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalizationPrice;
use App\Models\SublimationLocation;
use App\Models\SublimationProductType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonalizationPriceController extends Controller
{
    public function index(): View
    {
        $types = PersonalizationPrice::getPersonalizationTypes();
        // Remover LISAS pois não há configuração de preços para este tipo
        if (isset($types['LISAS'])) {
            unset($types['LISAS']);
        }
        $pricesByType = [];
        $locations = SublimationLocation::orderBy('order')->get();
        
        // Carregar configurações de personalização existentes
        $settings = \App\Models\PersonalizationSetting::all()->keyBy('personalization_type');
        
        // Auto-criar settings para tipos que não existem ainda
        foreach ($types as $key => $label) {
            if (!$settings->has($key)) {
                $newSetting = \App\Models\PersonalizationSetting::create([
                    'personalization_type' => $key,
                    'display_name' => $label,
                    'description' => null,
                    'charge_by_color' => false,
                    'color_price_per_unit' => 0,
                    'min_colors' => 1,
                    'max_colors' => null,
                    'discount_2nd_application' => 0,
                    'discount_3rd_application' => 0,
                    'discount_4th_plus_application' => 0,
                    'has_sizes' => true,
                    'has_locations' => true,
                    'has_special_options' => false,
                    'active' => true,
                    'order' => 0,
                ]);
                $settings->put($key, $newSetting);
            }
        }
        
        foreach ($types as $key => $label) {
            $setting = $settings->get($key);
            
            $pricesByType[$key] = [
                'label' => $label,
                'sizes' => PersonalizationPrice::getSizesForType($key),
                'total_ranges' => PersonalizationPrice::where('personalization_type', $key)
                    ->where('active', true)
                    ->count(),
                'setting' => $setting,
                'charge_by_color' => $setting?->charge_by_color ?? false,
                'color_price' => $setting?->color_price_per_unit ?? 0,
                'discount_2nd' => $setting?->discount_2nd_application ?? 0,
                'special_options_count' => $setting ? \App\Models\PersonalizationSpecialOption::where('personalization_type', $key)->where('active', true)->count() : 0,
            ];
        }

        return view('admin.personalization-prices.index', compact('types', 'pricesByType', 'locations', 'settings'));
    }

    public function edit($type): View
    {
        $types = PersonalizationPrice::getPersonalizationTypes();
        
        if (!array_key_exists($type, $types)) {
            abort(404, 'Tipo de personalização não encontrado');
        }

        // Se for SERIGRAFIA, usar view especializada
        if ($type === 'SERIGRAFIA') {
            // Carregar preços de tamanhos (exceto cores)
            $sizes = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', '!=', 'COR')
                ->select('size_name')
                ->distinct()
                ->orderBy('size_name')
                ->get()
                ->pluck('size_name');
                
            $prices = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', '!=', 'COR')
                ->orderBy('size_name')
                ->orderBy('quantity_from')
                ->get()
                ->groupBy('size_name');
            
            // Carregar preços de cores separadamente
            $colorPrices = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', 'COR')
                ->orderBy('quantity_from')
                ->get();

            return view('admin.personalization-prices.edit-serigraphy', compact('type', 'types', 'sizes', 'prices', 'colorPrices'));
        }

        if ($type === 'EMBORRACHADO') {
            // Carregar preços de tamanhos (exceto cores)
            $sizes = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', '!=', 'COR')
                ->select('size_name')
                ->distinct()
                ->orderBy('size_name')
                ->get()
                ->pluck('size_name');
                
            $prices = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', '!=', 'COR')
                ->orderBy('size_name')
                ->orderBy('quantity_from')
                ->get()
                ->groupBy('size_name');
            
            // Carregar preços de cores separadamente
            $colorPrices = PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', 'COR')
                ->orderBy('quantity_from')
                ->get();

            return view('admin.personalization-prices.edit-emborrachado', compact('type', 'types', 'sizes', 'prices', 'colorPrices'));
        }

        if ($type === 'SUB. TOTAL') {
            $tenantId = auth()->user()->tenant_id;

            // Carregar precos base (CACHARREL)
            $sizes = PersonalizationPrice::where('personalization_type', $type)
                ->select('size_name')
                ->distinct()
                ->orderBy('size_name')
                ->get()
                ->pluck('size_name');

            $prices = PersonalizationPrice::where('personalization_type', $type)
                ->orderBy('size_name')
                ->orderBy('quantity_from')
                ->get()
                ->groupBy('size_name');

            // Carregar adicionais
            $addons = \App\Models\SublimationAddon::getActiveAddons();

            // Carregar tipos de produto de SUB. TOTAL (camisa, conjunto, bandeira, etc.)
            $productTypes = SublimationProductType::getForTenant($tenantId);

            return view('admin.personalization-prices.edit-sub-total', compact('type', 'types', 'sizes', 'prices', 'addons', 'productTypes'));
        }
        $sizes = PersonalizationPrice::getSizesForType($type);
        $prices = PersonalizationPrice::where('personalization_type', $type)
            ->orderBy('quantity_from')
            ->orderBy('size_name')
            ->get();

        // Debug: log dos dados carregados

        return view('admin.personalization-prices.edit', compact('type', 'types', 'sizes', 'prices'));
    }

    public function update(Request $request, $type): RedirectResponse
    {
        // Lógica unificada para todos os tipos

        // Validação específica para SUB. TOTAL
        if ($type === 'SUB. TOTAL' && $request->has('base_prices')) {

            if (empty($request->base_prices)) {
                return redirect()->back()->with('error', 'Nenhum preço base foi enviado');
            }
            
            $validBasePrices = array_filter($request->base_prices, function($priceData) {
                return !empty($priceData['quantity_from']) && !empty($priceData['price']) && 
                       is_numeric($priceData['quantity_from']) && is_numeric($priceData['price']);
            });
            
            if (empty($validBasePrices)) {
                return redirect()->back()->with('error', 'Nenhuma faixa de preço válida foi encontrada');
            }
            
            // Validação simples sem regras complexas
            $validated = $request->validate([
                'base_prices' => 'required|array',
                'base_prices.*.quantity_from' => 'nullable|integer',
                'base_prices.*.quantity_to' => 'nullable|integer',
                'base_prices.*.price' => 'nullable|numeric',
            ]);
        }
        
        // Detectar formato dos dados (novo ou antigo)
        $isNewFormat = isset($request->prices[0]['quantity_from']);
        
        // Só processar validação padrão se não for SUB. TOTAL com base_prices
        if (!($type === 'SUB. TOTAL' && $request->has('base_prices'))) {
            if ($isNewFormat) {
                // Novo formato: quantity_from, quantity_to, tamanhos dinâmicos
                $validated = $request->validate([
                    'prices' => 'required|array|min:1',
                    'prices.*.quantity_from' => 'required|integer|min:1',
                    'prices.*.quantity_to' => 'nullable|integer|min:1',
                ]);
                $validated['prices'] = $request->prices;
            } else {
                // Formato antigo: from, to, ESCUDO, A4, A3
                $validated = $request->validate([
                    'prices' => 'required|array|min:1',
                    'prices.*.from' => 'required|integer|min:1',
                    'prices.*.to' => 'nullable|integer|min:1',
                ]);
                $validated['prices'] = $request->prices;
            }
        }
        
        // Processar preços base se for SUB. TOTAL
        if ($type === 'SUB. TOTAL' && $request->has('base_prices')) {
            try {
                // Deletar preços existentes para SUB. TOTAL
                $deleted = PersonalizationPrice::where('personalization_type', $type)->delete();
                
                // Criar novos preços base usando apenas faixas válidas
                foreach ($validBasePrices as $priceData) {
                    PersonalizationPrice::create([
                        'personalization_type' => $type,
                        'size_name' => 'CACHARREL',
                        'size_dimensions' => null,
                        'quantity_from' => $priceData['quantity_from'],
                        'quantity_to' => $priceData['quantity_to'] ?: null,
                        'price' => $priceData['price'],
                        'cost' => $priceData['cost'] ?? 0,
                        'active' => true,
                        'order' => 0,
                    ]);
                }
                
                return redirect()->back()->with('success', 'Preços base atualizados com sucesso!');
            } catch (\Exception $e) {
                \Log::error('Error saving SUB. TOTAL base prices:', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Erro ao salvar preços base: ' . $e->getMessage());
            }
        }

        // Processar preços de cores separadas PRIMEIRO se for SERIGRAFIA ou EMBORRACHADO
        if (in_array($type, ['SERIGRAFIA', 'EMBORRACHADO']) && $request->has('color_prices')) {
            // Limpar preços de cor existentes para este tipo
            PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', 'COR')
                ->delete();
            
            // Processar cada linha de preço de cor
            foreach ($request->color_prices as $index => $colorData) {
                if (!empty($colorData['from']) && !empty($colorData['to']) && !empty($colorData['price'])) {
                    // Criar novo preço de cor
                    PersonalizationPrice::create([
                        'personalization_type' => $type,
                        'size_name' => 'COR',
                        'size_dimensions' => null,
                        'quantity_from' => $colorData['from'],
                        'quantity_to' => $colorData['to'],
                        'price' => $colorData['price'],
                        'cost' => $colorData['cost'] ?? 0,
                    ]);
                }
            }
        }

        // Preservar size_dimensions antes de deletar
        $existingSizeDimensions = [];
        $existingPrices = PersonalizationPrice::where('personalization_type', $type)->get();
        foreach ($existingPrices as $existingPrice) {
            if ($existingPrice->size_dimensions) {
                $existingSizeDimensions[$existingPrice->size_name] = $existingPrice->size_dimensions;
            }
        }

        // Deletar preços existentes para este tipo (exceto cores se for SERIGRAFIA ou EMBORRACHADO)
        if (in_array($type, ['SERIGRAFIA', 'EMBORRACHADO'])) {
            // Para serigrafia e emborrachado, deletar apenas preços de tamanhos, não de cores
            PersonalizationPrice::where('personalization_type', $type)
                ->where('size_name', '!=', 'COR')
                ->delete();
        } else {
            // Para outros tipos, deletar todos os preços
            PersonalizationPrice::where('personalization_type', $type)->delete();
        }

        // Criar novos preços para cada faixa de quantidade e cada tamanho
        foreach ($validated['prices'] as $priceData) {
            if ($isNewFormat) {
                // Novo formato: quantity_from, quantity_to, tamanhos dinâmicos
                $quantityFrom = $priceData['quantity_from'];
                $quantityTo = $priceData['quantity_to'] ?? null;
                
                // Processar todos os campos que não são quantity_from ou quantity_to
                foreach ($priceData as $key => $value) {
                    if ($key !== 'quantity_from' && $key !== 'quantity_to' && !empty($value)) {
                        $sizeName = strtoupper($key);
                        
                        // Handle new array format [price, cost] or old scalar format
                        $priceValue = is_array($value) ? ($value['price'] ?? null) : $value;
                        $costValue = is_array($value) ? ($value['cost'] ?? 0) : 0;
                        
                        // Skip if price is empty
                        if ($priceValue === null || $priceValue === '') {
                            continue;
                        }

                        $newPrice = PersonalizationPrice::create([
                            'personalization_type' => $type,
                            'size_name' => $sizeName,
                            'size_dimensions' => $existingSizeDimensions[$sizeName] ?? null,
                            'quantity_from' => $quantityFrom,
                            'quantity_to' => $quantityTo,
                            'price' => $priceValue,
                            'cost' => $costValue,
                        ]);
                    }
                }
            } else {
                // Formato antigo: from, to, ESCUDO, A4, A3
                $quantityFrom = $priceData['from'];
                $quantityTo = $priceData['to'] ?? null;
                
                // Processar tamanhos fixos
                $sizes = ['ESCUDO', 'A4', 'A3'];
                foreach ($sizes as $size) {
                    if (isset($priceData[$size]) && !empty($priceData[$size])) {
                        PersonalizationPrice::create([
                            'personalization_type' => $type,
                            'size_name' => $size,
                            'size_dimensions' => $existingSizeDimensions[$size] ?? null,
                            'quantity_from' => $quantityFrom,
                            'quantity_to' => $quantityTo,
                            'price' => $priceData[$size],
                        ]);
                    }
                }
            }
        }
        
        return redirect()->route('admin.personalization-prices.edit', $type)
            ->with('success', 'Preços atualizados com sucesso!');
    }


    public function addPriceRow(Request $request): View
    {
        $type = $request->get('type');
        $sizeName = $request->get('size_name');
        $index = $request->get('index', 0);
        $price = null; // Nova linha de preço sempre começa vazia
        
        return view('admin.personalization-prices.partials.price-row', compact('type', 'sizeName', 'index', 'price'));
    }

    public function getSizesForType(Request $request)
    {
        $type = $request->get('type');
        $sizes = PersonalizationPrice::getSizesForType($type);
        
        return response()->json($sizes);
    }
}
