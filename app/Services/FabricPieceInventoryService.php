<?php

namespace App\Services;

use App\Models\FabricPiece;
use App\Models\FabricPieceSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FabricPieceInventoryService
{
    public function openPiece(FabricPiece $piece, ?float $initialQuantity = null): FabricPiece
    {
        if ($piece->status !== 'fechada') {
            throw new RuntimeException('Apenas peças fechadas podem ser abertas.');
        }

        $quantity = $initialQuantity ?? $piece->initial_quantity;

        if ($quantity <= 0) {
            throw new RuntimeException('Informe uma quantidade inicial válida para abrir a peça.');
        }

        $this->syncCurrentQuantity($piece, $quantity);

        $piece->forceFill([
            'status' => 'aberta',
            'opened_at' => now(),
        ])->save();

        return $piece->fresh();
    }

    public function sell(FabricPiece $piece, array $data = []): array
    {
        $quantity = (float) ($data['quantity'] ?? 0);
        $unit = (string) ($data['unit'] ?? $piece->control_unit);
        $unitPrice = (float) ($data['unit_price'] ?? $piece->sale_price ?? 0);
        $orderId = $data['order_id'] ?? null;
        $catalogOrderId = $data['catalog_order_id'] ?? null;
        $orderItemId = $data['order_item_id'] ?? null;
        $channel = (string) ($data['channel'] ?? 'manual');
        $soldBy = $data['sold_by'] ?? Auth::id();
        $notes = $data['notes'] ?? null;

        if ($quantity <= 0) {
            throw new RuntimeException('Informe uma quantidade válida.');
        }

        if ($piece->status === 'vendida' || $piece->available_quantity <= 0) {
            throw new RuntimeException('Esta peça não possui saldo disponível.');
        }

        if ($unit !== $piece->control_unit) {
            throw new RuntimeException('A unidade informada não corresponde ao controle da peça.');
        }

        return DB::transaction(function () use ($piece, $quantity, $unit, $unitPrice, $orderId, $catalogOrderId, $orderItemId, $channel, $soldBy, $notes) {
            $piece = FabricPiece::query()->lockForUpdate()->findOrFail($piece->id);

            if ($piece->status === 'fechada') {
                $this->openPiece($piece, $piece->initial_quantity);
                $piece->refresh();
            }

            if ($quantity > $piece->available_quantity + 0.0001) {
                throw new RuntimeException('Quantidade insuficiente para esta peça.');
            }

            $remaining = max(0, (float) $piece->available_quantity - $quantity);

            $sale = FabricPieceSale::create([
                'fabric_piece_id' => $piece->id,
                'store_id' => $piece->store_id,
                'order_id' => $orderId,
                'catalog_order_id' => $catalogOrderId,
                'order_item_id' => $orderItemId,
                'sold_by' => $soldBy,
                'channel' => $channel,
                'quantity' => $quantity,
                'unit' => $unit,
                'unit_price' => $unitPrice,
                'total_price' => round($unitPrice * $quantity, 2),
                'notes' => $notes,
            ]);

            $this->syncCurrentQuantity($piece, $remaining);

            $piece->forceFill([
                'status' => $remaining > 0.0001 ? 'aberta' : 'vendida',
                'sold_at' => now(),
                'sold_by' => $soldBy,
                'order_id' => $remaining > 0.0001 ? $piece->order_id : $orderId,
            ])->save();

            return [
                'piece' => $piece->fresh(),
                'sale' => $sale,
                'remaining' => $remaining,
            ];
        });
    }

    public function sellEntirePiece(FabricPiece $piece, array $data = []): array
    {
        if ($piece->available_quantity <= 0) {
            throw new RuntimeException('Esta peça não possui saldo disponível.');
        }

        return $this->sell($piece, array_merge($data, [
            'quantity' => $piece->available_quantity,
            'unit' => $piece->control_unit,
        ]));
    }

    public function restoreSale(FabricPieceSale $sale, ?string $reason = null, ?int $userId = null): FabricPiece
    {
        return DB::transaction(function () use ($sale, $reason, $userId) {
            $sale = FabricPieceSale::query()->lockForUpdate()->findOrFail($sale->id);

            if ($sale->reverted_at) {
                return $sale->fabricPiece()->firstOrFail();
            }

            $piece = FabricPiece::query()->lockForUpdate()->findOrFail($sale->fabric_piece_id);
            $restoredQuantity = (float) $piece->available_quantity + (float) $sale->quantity;

            $this->syncCurrentQuantity($piece, $restoredQuantity);

            $piece->forceFill([
                'status' => $restoredQuantity > 0.0001 ? 'aberta' : $piece->status,
                'sold_at' => $restoredQuantity > 0.0001 ? null : $piece->sold_at,
                'sold_by' => $restoredQuantity > 0.0001 ? null : $piece->sold_by,
                'order_id' => $sale->order_id && $piece->order_id == $sale->order_id ? null : $piece->order_id,
            ])->save();

            $sale->update([
                'reverted_at' => now(),
                'reverted_by' => $userId ?? Auth::id(),
                'revert_reason' => $reason,
            ]);

            return $piece->fresh();
        });
    }

    public function buildOrderItemPayload(FabricPiece $piece, float $quantity): array
    {
        return [
            'id' => $piece->id,
            'label' => $piece->display_name,
            'quantity' => $quantity,
            'unit' => $piece->control_unit,
            'store_id' => $piece->store_id,
            'store_name' => $piece->store?->name,
        ];
    }

    public function extractOrderItemPayload(array|string|null $printDesc): ?array
    {
        $decoded = is_array($printDesc)
            ? $printDesc
            : (is_string($printDesc) ? json_decode($printDesc, true) : null);

        if (!is_array($decoded)) {
            return null;
        }

        $payload = $decoded['fabric_piece'] ?? null;

        return is_array($payload) && !empty($payload['id']) ? $payload : null;
    }

    private function syncCurrentQuantity(FabricPiece $piece, float $quantity): void
    {
        $normalized = max(0, $quantity);

        if ($piece->control_unit === 'metros') {
            $piece->meters_current = $normalized;
            return;
        }

        $piece->weight_current = $normalized;
    }
}
