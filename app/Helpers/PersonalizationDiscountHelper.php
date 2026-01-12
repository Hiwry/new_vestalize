<?php

namespace App\Helpers;

use App\Models\OrderSublimation;
use Illuminate\Support\Collection;

class PersonalizationDiscountHelper
{
    /**
     * Aplicar descontos automáticos em personalizações de sessão (orçamentos)
     * 
     * @param array $customizations Array de personalizações da sessão
     * @param int $itemIndex Índice do item que acabou de receber personalização
     * @return array Array atualizado com descontos aplicados
     */
    public static function applySessionDiscounts(array $customizations, int $itemIndex): array
    {
        // Filtrar personalizações do item específico
        $itemCustomizations = array_filter($customizations, function($c) use ($itemIndex) {
            return ($c['item_index'] ?? 0) == $itemIndex;
        });

        if (empty($itemCustomizations)) {
            return $customizations;
        }

        // Separar por tipo
        $serigraphy = [];
        $sublimationLocal = [];

        foreach ($itemCustomizations as $index => $custom) {
            $type = strtoupper($custom['personalization_name'] ?? '');
            
            // SERIGRAFIA ou EMBORRACHADO
            if (in_array($type, ['SERIGRAFIA', 'EMBORRACHADO'])) {
                $serigraphy[$index] = $custom;
            }
            // SUBLIMAÇÃO LOCAL (tem location)
            elseif (!empty($custom['location'])) {
                $sublimationLocal[$index] = $custom;
            }
        }

        // Aplicar descontos em SERIGRAFIA/EMBORRACHADO
        if (count($serigraphy) >= 3) {
            $serigraphy = self::applySessionSerigraphyDiscounts($serigraphy);
            foreach ($serigraphy as $index => $custom) {
                $customizations[$index] = $custom;
            }
        }

        // Aplicar descontos em SUBLIMAÇÃO LOCAL
        if (count($sublimationLocal) >= 2) {
            $sublimationLocal = self::applySessionSublimationDiscounts($sublimationLocal);
            foreach ($sublimationLocal as $index => $custom) {
                $customizations[$index] = $custom;
            }
        }

        return $customizations;
    }

    /**
     * Aplicar descontos em SERIGRAFIA/EMBORRACHADO (sessão)
     */
    private static function applySessionSerigraphyDiscounts(array $items): array
    {
        // Ordenar por unit_price (maior para menor)
        uasort($items, function($a, $b) {
            return ($b['unit_price'] ?? 0) <=> ($a['unit_price'] ?? 0);
        });

        $sorted = array_values($items);
        $originalKeys = array_keys($items);
        $result = [];

        foreach ($sorted as $i => $item) {
            $originalIndex = $originalKeys[$i];
            
            if ($i < 2) {
                // Manter as 2 primeiras sem desconto
                $item['discount_applied'] = 0;
                $result[$originalIndex] = $item;
            } else {
                // Aplicar 50% de desconto
                $unitPrice = $item['unit_price'] ?? 0;
                $quantity = $item['quantity'] ?? 1;
                
                $item['discount_applied'] = 50;
                $item['final_price'] = ($unitPrice * $quantity) * 0.5;
                
                $result[$originalIndex] = $item;
            }
        }

        return $result;
    }

    /**
     * Aplicar descontos em SUBLIMAÇÃO LOCAL (sessão)
     */
    private static function applySessionSublimationDiscounts(array $items): array
    {
        // Ordenar por unit_price (maior para menor)
        uasort($items, function($a, $b) {
            return ($b['unit_price'] ?? 0) <=> ($a['unit_price'] ?? 0);
        });

        $sorted = array_values($items);
        $originalKeys = array_keys($items);
        $result = [];

        foreach ($sorted as $i => $item) {
            $originalIndex = $originalKeys[$i];
            
            if ($i === 0) {
                // Manter a primeira sem desconto
                $item['discount_applied'] = 0;
                $result[$originalIndex] = $item;
            } else {
                // Aplicar 50% de desconto
                $unitPrice = $item['unit_price'] ?? 0;
                $quantity = $item['quantity'] ?? 1;
                
                $item['discount_applied'] = 50;
                $item['final_price'] = ($unitPrice * $quantity) * 0.5;
                
                $result[$originalIndex] = $item;
            }
        }

        return $result;
    }

    /**
     * Aplicar descontos automáticos em personalizações de um item
     * 
     * REGRAS:
     * - SERIGRAFIA/EMBORRACHADO: 50% desconto a partir da 3ª aplicação (mantém 2 de maior valor)
     * - SUBLIMAÇÃO LOCAL: 50% desconto a partir da 2ª aplicação (mantém 1 de maior valor)
     * 
     * @param int $itemId
     * @return void
     */
    public static function applyDiscounts($itemId)
    {
        // Buscar todas as personalizações do item
        $personalizations = OrderSublimation::where('order_item_id', $itemId)
            ->orderBy('unit_price', 'desc')
            ->get();

        if ($personalizations->isEmpty()) {
            return;
        }

        // Separar por tipo (usando application_type do novo schema)
        $serigraphyTypes = ['SERIGRAFIA', 'EMBORRACHADO'];
        
        // Agrupar personalizações por tipo
        $serigraphy = $personalizations->filter(function($p) use ($serigraphyTypes) {
            $appType = strtoupper($p->application_type ?? '');
            return in_array($appType, $serigraphyTypes);
        });

        $sublimationLocal = $personalizations->filter(function($p) {
            // Sublimação LOCAL é quando tem location_id (diferente de SUB. TOTAL que não tem)
            return !empty($p->location_id) || !empty($p->location_name);
        });

        // Aplicar descontos em SERIGRAFIA/EMBORRACHADO
        if ($serigraphy->count() >= 3) {
            self::applySerigraphyDiscounts($serigraphy);
        }

        // Aplicar descontos em SUBLIMAÇÃO LOCAL
        if ($sublimationLocal->count() >= 2) {
            self::applySublimationLocalDiscounts($sublimationLocal);
        }

        // Recalcular total do item
        self::recalculateItemTotal($itemId);
    }

    /**
     * Aplicar descontos em SERIGRAFIA/EMBORRACHADO
     * Mantém 2 de maior valor, aplica 50% nas demais
     */
    private static function applySerigraphyDiscounts(Collection $personalizations)
    {
        $sorted = $personalizations->sortByDesc('unit_price')->values();

        foreach ($sorted as $index => $personalization) {
            if ($index < 2) {
                // Manter as 2 primeiras (maior valor) sem desconto
                $personalization->update([
                    'discount_percent' => 0,
                    'final_price' => $personalization->unit_price * $personalization->quantity,
                ]);
            } else {
                // Aplicar 50% de desconto nas demais
                $priceWithDiscount = $personalization->unit_price * 0.5;
                $personalization->update([
                    'discount_percent' => 50,
                    'final_price' => $priceWithDiscount * $personalization->quantity,
                ]);
            }
        }

        \Log::info('✅ Descontos SERIGRAFIA/EMBORRACHADO aplicados', [
            'total' => $personalizations->count(),
            'com_desconto' => $personalizations->count() - 2,
        ]);
    }

    /**
     * Aplicar descontos em SUBLIMAÇÃO LOCAL
     * Mantém 1 de maior valor, aplica 50% nas demais
     */
    private static function applySublimationLocalDiscounts(Collection $personalizations)
    {
        $sorted = $personalizations->sortByDesc('unit_price')->values();

        foreach ($sorted as $index => $personalization) {
            if ($index === 0) {
                // Manter a primeira (maior valor) sem desconto
                $personalization->update([
                    'discount_percent' => 0,
                    'final_price' => $personalization->unit_price * $personalization->quantity,
                ]);
            } else {
                // Aplicar 50% de desconto nas demais
                $priceWithDiscount = $personalization->unit_price * 0.5;
                $personalization->update([
                    'discount_percent' => 50,
                    'final_price' => $priceWithDiscount * $personalization->quantity,
                ]);
            }
        }

        \Log::info('✅ Descontos SUBLIMAÇÃO LOCAL aplicados', [
            'total' => $personalizations->count(),
            'com_desconto' => $personalizations->count() - 1,
        ]);
    }

    /**
     * Recalcular o total do item após aplicar descontos
     */
    private static function recalculateItemTotal($itemId)
    {
        $item = \App\Models\OrderItem::find($itemId);
        
        if (!$item) {
            return;
        }

        // Calcular total de personalizações
        $totalPersonalizations = OrderSublimation::where('order_item_id', $itemId)
            ->sum('final_price');
        
        // Calcular novo total do item (preço base + personalizações)
        $basePrice = $item->unit_price * $item->quantity;
        $newTotalPrice = $basePrice + $totalPersonalizations;
        
        $item->update([
            'total_price' => $newTotalPrice
        ]);

        // Atualizar subtotal do pedido
        $order = $item->order;
        if ($order) {
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
            ]);
        }

        \Log::info('✅ Total do item recalculado', [
            'item_id' => $itemId,
            'base_price' => $basePrice,
            'personalizations_total' => $totalPersonalizations,
            'new_total' => $newTotalPrice,
        ]);
    }
}

