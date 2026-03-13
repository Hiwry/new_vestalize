<?php
/**
 * Script para recalcular totais de um pedido:
 * - Sincroniza quantity das OrderSublimation com a quantity real do item
 * - Recalcula final_price de cada sublimação proporcional à nova qty
 * - Recalcula total_price de cada item (costura + personalizações)
 * - Recalcula subtotal/total do pedido
 *
 * Uso: php fix_order_totals.php <order_id>
 *   ou via browser: /fix_order_totals.php?order_id=<order_id>
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = $argv[1] ?? $_GET['order_id'] ?? null;

if (!$orderId) {
    die("Informe o ID do pedido. Ex: php fix_order_totals.php 123\n");
}

$order = \App\Models\Order::with(['items.sublimations'])->find($orderId);

if (!$order) {
    die("Pedido #{$orderId} não encontrado.\n");
}

echo "=== Corrigindo Pedido #{$order->id} ===\n\n";

foreach ($order->items as $item) {
    $correctQty = $item->quantity;
    $sewingTotal = $item->unit_price * $correctQty;
    $personalizationTotal = 0;

    echo "Item #{$item->id} | qty={$correctQty} | unit_price={$item->unit_price}\n";

    foreach ($item->sublimations as $sub) {
        $oldQty = $sub->quantity ?: 1;
        $oldFinal = $sub->final_price;

        if ($sub->application_type === 'sub. total') {
            // SUB. TOTAL: quantidade não muda pelo item, manter
            $personalizationTotal += $sub->final_price;
            echo "  Sub (sub.total) #{$sub->id}: mantido final_price={$sub->final_price}\n";
            continue;
        }

        if ($oldQty == $correctQty) {
            $personalizationTotal += $sub->final_price;
            echo "  Sub #{$sub->id}: qty já correta ({$correctQty}), final_price={$sub->final_price}\n";
            continue;
        }

        // Recalcular proporcional
        $unitPriceSub = $oldQty > 0 ? ($oldFinal / $oldQty) : $sub->unit_price;
        $newFinal = round($unitPriceSub * $correctQty, 2);

        $sub->update([
            'quantity'    => $correctQty,
            'final_price' => $newFinal,
        ]);

        $personalizationTotal += $newFinal;

        echo "  Sub #{$sub->id}: qty {$oldQty} -> {$correctQty} | final_price {$oldFinal} -> {$newFinal}\n";
    }

    $newItemTotal = round($sewingTotal + $personalizationTotal, 2);
    $oldItemTotal = $item->total_price;

    $item->update(['total_price' => $newItemTotal]);

    echo "  Item total: {$oldItemTotal} -> {$newItemTotal}\n\n";
}

// Recalcular subtotal e total do pedido
$order->load('items');
$newSubtotal = round($order->items->sum('total_price'), 2);
$oldSubtotal = $order->subtotal;

$order->update(['subtotal' => $newSubtotal]);

echo "Subtotal do pedido: {$oldSubtotal} -> {$newSubtotal}\n";
echo "\n=== Concluido! ===\n";
