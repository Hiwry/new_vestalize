<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricPieceTransfer extends Model
{
    protected $fillable = [
        'fabric_piece_id',
        'from_store_id',
        'to_store_id',
        'requested_by',
        'approved_by',
        'received_by',
        'status',
        'requested_at',
        'approved_at',
        'shipped_at',
        'received_at',
        'request_notes',
        'approval_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected $appends = ['status_label', 'status_color'];

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'aprovada' => 'Aprovada',
            'em_transito' => 'Em Trânsito',
            'recebida' => 'Recebida',
            'cancelada' => 'Cancelada',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'aprovada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'em_transito' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            'recebida' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'cancelada' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Relacionamentos

    public function fabricPiece(): BelongsTo
    {
        return $this->belongsTo(FabricPiece::class);
    }

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Métodos de ação

    /**
     * Aprovar transferência
     */
    public function approve(?string $notes = null): bool
    {
        $this->update([
            'status' => 'aprovada',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Marcar como em trânsito
     */
    public function ship(): bool
    {
        $this->update([
            'status' => 'em_transito',
            'shipped_at' => now(),
        ]);

        return true;
    }

    /**
     * Confirmar recebimento
     */
    public function receive(): bool
    {
        $this->update([
            'status' => 'recebida',
            'received_by' => auth()->id(),
            'received_at' => now(),
        ]);

        // Atualizar a peça para a nova loja
        $this->fabricPiece->update([
            'store_id' => $this->to_store_id,
            'status' => 'fechada', // Volta ao status normal
        ]);

        return true;
    }

    /**
     * Cancelar transferência
     */
    public function cancel(?string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelada',
            'approval_notes' => $reason,
        ]);

        // Voltar peça ao status anterior
        $this->fabricPiece->update(['status' => 'fechada']);

        return true;
    }
}
