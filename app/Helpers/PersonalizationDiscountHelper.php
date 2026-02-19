<?php

namespace App\Helpers;

use App\Models\OrderSublimation;
use App\Models\PersonalizationSetting;
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

        // Agrupar por tipo de personalização (usa configurações)
        $grouped = [];
        foreach ($itemCustomizations as $index => $custom) {
            $typeKey = strtoupper(trim($custom['personalization_name'] ?? ''));
            if ($typeKey === '') {
                continue;
            }
            $grouped[$typeKey][$index] = $custom;
        }

        foreach ($grouped as $typeKey => $items) {
            $setting = PersonalizationSetting::findByType($typeKey);
            if (!$setting) {
                continue;
            }
            $items = self::applySessionTypeDiscounts($items, $setting);
            foreach ($items as $index => $custom) {
                $customizations[$index] = $custom;
            }
        }

        return $customizations;
    }

    /**
     * Aplicar descontos por tipo (sessão), usando configurações
     */
    private static function applySessionTypeDiscounts(array $items, PersonalizationSetting $setting): array
    {
        // Ordenar por unit_price (maior para menor)
        uasort($items, function($a, $b) {
            return ($b['unit_price'] ?? 0) <=> ($a['unit_price'] ?? 0);
        });

        $result = [];

        $applicationNumber = 1;
        foreach ($items as $originalIndex => $item) {
            $unitPrice = $item['unit_price'] ?? 0;
            $quantity = $item['quantity'] ?? 1;
            $discountPercent = $setting->getDiscountForApplication($applicationNumber);
            $discountPercent = max(0, min(100, (float) $discountPercent));

            $item['discount_applied'] = $discountPercent;
            $item['final_price'] = ($unitPrice * $quantity) * (1 - ($discountPercent / 100));
            $result[$originalIndex] = $item;

            $applicationNumber++;
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

        // Agrupar por tipo de aplicação e aplicar descontos conforme configuração
        $grouped = $personalizations->groupBy(function($p) {
            return strtoupper(trim($p->application_type ?? ''));
        });

        foreach ($grouped as $typeKey => $group) {
            if ($typeKey === '') {
                continue;
            }
            $setting = PersonalizationSetting::findByType($typeKey);
            if (!$setting) {
                continue;
            }
            self::applyTypeDiscounts($group, $setting);
        }

        // Recalcular total do item
        self::recalculateItemTotal($itemId);
    }

    /**
     * Aplicar descontos por tipo, usando configuração
     */
    private static function applyTypeDiscounts(Collection $personalizations, PersonalizationSetting $setting)
    {
        $sorted = $personalizations->sortByDesc('unit_price')->values();

        foreach ($sorted as $index => $personalization) {
            $applicationNumber = $index + 1;
            $discountPercent = $setting->getDiscountForApplication($applicationNumber);
            $discountPercent = max(0, min(100, (float) $discountPercent));
            $priceWithDiscount = $personalization->unit_price * (1 - ($discountPercent / 100));
            $personalization->update([
                'discount_percent' => $discountPercent,
                'final_price' => $priceWithDiscount * $personalization->quantity,
            ]);
        }

        \Log::info(' Descontos aplicados por tipo', [
            'type' => $setting->personalization_type,
            'total' => $personalizations->count(),
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

        \Log::info(' Total do item recalculado', [
            'item_id' => $itemId,
            'base_price' => $basePrice,
            'personalizations_total' => $totalPersonalizations,
            'new_total' => $newTotalPrice,
        ]);
    }
}
