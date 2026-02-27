<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class MarketplaceOrder extends Model
{
    protected $table = 'marketplace_orders';

    protected $fillable = [
        'order_number',
        'buyer_id',
        'orderable_type',
        'orderable_id',
        'designer_id',
        'price_credits',
        'credits_to_designer',
        'status',
        'buyer_instructions',
        'delivery_message',
        'delivery_file',
        'deadline_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'deadline_at'   => 'datetime',
        'delivered_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    public static array $statusLabels = [
        'pending_payment'    => 'Aguardando Pagamento',
        'in_progress'        => 'Em Andamento',
        'delivered'          => 'Entregue',
        'revision_requested' => 'Revisão Solicitada',
        'completed'          => 'Concluído',
        'cancelled'          => 'Cancelado',
        'refunded'           => 'Reembolsado',
    ];

    public static array $statusColors = [
        'pending_payment'    => 'yellow',
        'in_progress'        => 'blue',
        'delivered'          => 'purple',
        'revision_requested' => 'orange',
        'completed'          => 'green',
        'cancelled'          => 'red',
        'refunded'           => 'gray',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(DesignerProfile::class, 'designer_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(MarketplaceReview::class, 'marketplace_order_id');
    }

    /**
     * Resolve o produto (serviço ou ferramenta)
     */
    public function orderable()
    {
        if ($this->orderable_type === 'service') {
            return $this->belongsTo(MarketplaceService::class, 'orderable_id');
        }
        return $this->belongsTo(MarketplaceTool::class, 'orderable_id');
    }

    public function getOrderableModelAttribute()
    {
        if ($this->orderable_type === 'service') {
            return MarketplaceService::find($this->orderable_id);
        }
        return MarketplaceTool::find($this->orderable_id);
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeForBuyer($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeForDesigner($query, int $designerProfileId)
    {
        return $query->where('designer_id', $designerProfileId);
    }

    // ─── Helpers ───────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        return 'MKT-' . strtoupper(Str::random(8));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'gray';
    }

    public function isService(): bool
    {
        return $this->orderable_type === 'service';
    }

    public function isTool(): bool
    {
        return $this->orderable_type === 'tool';
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'completed' && !$this->review;
    }
}
