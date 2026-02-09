<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\SizeSurcharge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateOrderSurcharges extends Command
{
    protected $signature = 'orders:recalculate-surcharges {--dry-run : Show what would be changed without actually changing}';
    protected $description = 'Recalculate size surcharges for all existing orders using correct unit price calculation';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info(' DRY RUN MODE - No changes will be made');
        }

        $orders = Order::with('items', 'payments')
            ->where('is_draft', false)
            ->get();

        $this->info("Found {$orders->count()} non-draft orders to process");
        
        $updatedCount = 0;
        $totalDifference = 0;

        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        foreach ($orders as $order) {
            $result = $this->recalculateOrder($order, $dryRun);
            
            if ($result['changed']) {
                $updatedCount++;
                $totalDifference += $result['difference'];
                
                if ($dryRun) {
                    $this->newLine();
                    $this->warn("Order #{$order->id}: {$result['old_total']} → {$result['new_total']} (diff: {$result['difference']})");
                }
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info(" Summary (DRY RUN):");
            $this->info("   Orders that would be updated: {$updatedCount}");
            $this->info("   Total difference: R$ " . number_format($totalDifference, 2, ',', '.'));
            $this->newLine();
            $this->warn("Run without --dry-run to apply changes.");
        } else {
            $this->info(" Recalculation complete!");
            $this->info("   Orders updated: {$updatedCount}");
            $this->info("   Total difference: R$ " . number_format($totalDifference, 2, ',', '.'));
        }

        return Command::SUCCESS;
    }

    private function recalculateOrder(Order $order, bool $dryRun): array
    {
        $largeSizes = ['GG', 'EXG', 'G1', 'G2', 'G3', 'Especial', 'ESPECIAL'];
        $sizeQuantities = [];
        
        // Count sizes across all items
        foreach ($order->items as $item) {
            $model = strtoupper($item->model ?? '');
            $detail = strtoupper($item->detail ?? '');
            $isRestricted = str_contains($model, 'INFANTIL') || str_contains($model, 'BABY LOOK') || 
                            str_contains($detail, 'INFANTIL') || str_contains($detail, 'BABY LOOK');
            
            $printDesc = is_string($item->print_desc) ? json_decode($item->print_desc, true) : $item->print_desc;
            $applySurcharge = filter_var($printDesc['apply_surcharge'] ?? false, FILTER_VALIDATE_BOOLEAN);
            
            if ($isRestricted && !$applySurcharge) continue;
            
            $sizes = is_string($item->sizes) ? json_decode($item->sizes, true) : $item->sizes;
            if (is_array($sizes)) {
                foreach ($sizes as $size => $qty) {
                    if (in_array($size, $largeSizes)) {
                        $sizeQuantities[$size] = ($sizeQuantities[$size] ?? 0) + (int)$qty;
                    }
                }
            }
        }

        // Calculate unit price (subtotal / total pieces)
        $subtotal = $order->subtotal;
        $totalPieces = $order->items->sum('quantity');
        $unitPrice = $totalPieces > 0 ? $subtotal / $totalPieces : $subtotal;
        
        // Calculate surcharges using unit price
        $sizeSurcharges = [];
        foreach ($sizeQuantities as $size => $qty) {
            if ($qty > 0) {
                $surchargeModel = SizeSurcharge::getSurchargeForSize($size, $unitPrice);
                if ($surchargeModel) {
                    $sizeSurcharges[$size] = (float)$surchargeModel->surcharge * $qty;
                }
            }
        }
        
        $totalSurcharges = array_sum($sizeSurcharges);
        
        // Check for ESPECIAL setup fee
        $hasAnyEspecial = false;
        foreach ($order->items as $item) {
            $sizes = is_string($item->sizes) ? json_decode($item->sizes, true) : $item->sizes;
            if (is_array($sizes)) {
                foreach ($sizes as $size => $qty) {
                    if (strtoupper($size) === 'ESPECIAL' && $qty > 0) {
                        $hasAnyEspecial = true;
                        break 2;
                    }
                }
            }
        }
        
        if ($hasAnyEspecial) {
            $setupModel = SizeSurcharge::getSurchargeForSize('ESPECIAL', $unitPrice);
            if ($setupModel) {
                $totalSurcharges += (float)$setupModel->surcharge;
            }
        }
        
        // Calculate new total
        $delivery = (float)($order->delivery_fee ?? 0);
        $discount = (float)($order->discount ?? 0);
        $newTotal = max(0, $subtotal + $totalSurcharges + $delivery - $discount);
        
        $oldTotal = $order->total;
        $difference = $newTotal - $oldTotal;
        
        $changed = abs($difference) > 0.01; // Only consider significant differences
        
        if ($changed && !$dryRun) {
            DB::transaction(function () use ($order, $newTotal, $sizeSurcharges) {
                $order->update(['total' => $newTotal]);
                
                // Update payment remaining amount if exists
                $payment = $order->payments->first();
                if ($payment) {
                    $totalPaid = 0;
                    if ($payment->payment_methods && is_array($payment->payment_methods)) {
                        $totalPaid = array_sum(array_column($payment->payment_methods, 'amount'));
                    } else {
                        $totalPaid = $payment->entry_amount ?? 0;
                    }
                    
                    $payment->update([
                        'amount' => $newTotal,
                        'remaining_amount' => max(0, $newTotal - $totalPaid),
                        'status' => $totalPaid >= $newTotal ? 'pago' : 'pendente',
                    ]);
                }
                
                Log::info('Order surcharge recalculated', [
                    'order_id' => $order->id,
                    'old_total' => $order->total,
                    'new_total' => $newTotal,
                    'size_surcharges' => $sizeSurcharges,
                ]);
            });
        }
        
        return [
            'changed' => $changed,
            'old_total' => number_format($oldTotal, 2, ',', '.'),
            'new_total' => number_format($newTotal, 2, ',', '.'),
            'difference' => $difference,
        ];
    }
}
