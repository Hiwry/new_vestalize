<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class StockRequest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'requesting_store_id',
        'target_store_id',
        'fabric_id',
        'fabric_type_id',
        'color_id',
        'cut_type_id',
        'size',
        'requested_quantity',
        'approved_quantity',
        'transferred_quantity',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'transferred_at',
        'request_notes',
        'approval_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
        'transferred_quantity' => 'integer',
        'approved_at' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    protected $appends = ['is_stock_reserved', 'is_transfer'];

    /**
     * Verifica se o estoque já está reservado para esta solicitação
     */
    public function getIsStockReservedAttribute(): bool
    {
        // Estoque reservado é indicado pelas notas contendo "(Estoque reservado)"
        return $this->request_notes && str_contains($this->request_notes, '(Estoque reservado)');
    }

    /**
     * Verifica se é uma solicitação de transferência (entre lojas)
     */
    public function getIsTransferAttribute(): bool
    {
        return $this->target_store_id !== null && 
               $this->requesting_store_id !== null && 
               $this->target_store_id !== $this->requesting_store_id;
    }

    /**
     * Relacionamento com pedido
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com loja solicitante
     */
    public function requestingStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'requesting_store_id');
    }

    /**
     * Relacionamento com loja de destino
     */
    public function targetStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'target_store_id');
    }

    /**
     * Relacionamento com tipo de tecido
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_id');
    }

    /**
     * Relacionamento com cor
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'color_id');
    }

    /**
     * Relacionamento com tipo de corte
     */
    public function cutType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'cut_type_id');
    }

    /**
     * Relacionamento com usuário que solicitou
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Relacionamento com usuário que aprovou
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Aprovar solicitação
     */
    public function approve(int $quantity, ?int $approvedBy = null, ?string $notes = null): bool
    {
        if ($this->status !== 'pendente') {
            return false;
        }

        $this->update([
            'status' => 'aprovado',
            'approved_quantity' => min($quantity, $this->requested_quantity),
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Rejeitar solicitação
     */
    public function reject(?int $rejectedBy = null, ?string $reason = null): bool
    {
        if ($this->status !== 'pendente') {
            return false;
        }

        $this->update([
            'status' => 'rejeitado',
            'approved_by' => $rejectedBy ?? auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Devolver estoque à loja solicitante
        // O estoque é descontado da loja solicitante (requesting_store_id) quando a venda é criada no checkout
        // Ao rejeitar, devolvemos o estoque para a loja solicitante
        if ($this->requesting_store_id) {
            $stock = Stock::findByParams(
                $this->requesting_store_id,
                $this->fabric_id,
                null, // fabric_type_id
                $this->color_id,
                $this->cut_type_id,
                $this->size
            );

            // Se o estoque existe, adicionar de volta a quantidade solicitada
            if ($stock) {
                $stock->add($this->requested_quantity, $rejectedBy ?? auth()->id(), $this->order_id, $this->id, 'Devolução - Solicitação rejeitada');
            } else {
                // Se não existe, criar o registro de estoque
                $newStock = Stock::createOrUpdateStock([
                    'store_id' => $this->requesting_store_id,
                    'fabric_id' => $this->fabric_id,
                    'color_id' => $this->color_id,
                    'cut_type_id' => $this->cut_type_id,
                    'size' => $this->size,
                    'quantity' => $this->requested_quantity,
                ]);
                
                // Registrar histórico para estoque recém-criado
                try {
                    \App\Models\StockHistory::recordMovement(
                        'devolucao',
                        $newStock,
                        $this->requested_quantity,
                        $rejectedBy ?? auth()->id(),
                        $this->order_id,
                        $this->id,
                        'Devolução - Solicitação rejeitada - Estoque criado'
                    );
                } catch (\Exception $e) {
                    \Log::warning('Erro ao registrar histórico de estoque', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * Marcar como em transferência
     */
    public function markAsTransferring(): bool
    {
        if ($this->status !== 'aprovado') {
            return false;
        }

        $this->update(['status' => 'em_transferencia']);
        return true;
    }

    /**
     * Concluir transferência
     */
    public function complete(int $transferredQuantity): bool
    {
        if (!in_array($this->status, ['aprovado', 'em_transferencia'])) {
            return false;
        }

        $this->update([
            'status' => 'concluido',
            'transferred_quantity' => min($transferredQuantity, $this->approved_quantity),
            'transferred_at' => now(),
        ]);

            // Atualizar estoque da loja solicitante
            if ($this->target_store_id && $this->requesting_store_id) {
                $stock = Stock::findByParams(
                    $this->requesting_store_id,
                    $this->fabric_id,
                    null, // fabric_type_id
                    $this->color_id,
                    $this->cut_type_id,
                    $this->size
                );

                if ($stock) {
                    $stock->add($transferredQuantity, auth()->id(), $this->order_id, $this->id, 'Transferência concluída');
                } else {
                    $newStock = Stock::createOrUpdateStock([
                        'store_id' => $this->requesting_store_id,
                        'fabric_id' => $this->fabric_id,
                        'color_id' => $this->color_id,
                        'cut_type_id' => $this->cut_type_id,
                        'size' => $this->size,
                        'quantity' => $transferredQuantity,
                    ]);
                    
                    // Registrar histórico para estoque recém-criado
                    try {
                        \App\Models\StockHistory::recordMovement(
                            'transferencia',
                            $newStock,
                            $transferredQuantity,
                            auth()->id(),
                            $this->order_id,
                            $this->id,
                            'Transferência concluída - Estoque criado'
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao registrar histórico de estoque', [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Baixar estoque da loja de origem
                $sourceStock = Stock::findByParams(
                    $this->target_store_id,
                    $this->fabric_id,
                    null, // fabric_type_id
                    $this->color_id,
                    $this->cut_type_id,
                    $this->size
                );

                if ($sourceStock) {
                    $sourceStock->use($transferredQuantity, auth()->id(), $this->order_id, $this->id, 'Transferência concluída');
                }
            }

        return true;
    }
}
