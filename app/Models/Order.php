<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Helpers\ImageHelper;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'store_id',
        'contract_type',
        'order_date',
        'delivery_date',
        'seller',
        'nt',
        'status_id',
        'total_items',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'notes',
        'cover_image',
        'is_draft',
        'is_pdv',
        'terms_accepted',
        'terms_accepted_at',
        'terms_version',
        'client_token',
        'client_confirmed',
        'client_confirmed_at',
        'client_confirmation_notes',
        'is_editing',
        'edit_requested_at',
        'edit_notes',
        'edit_completed_at',
        'edit_status',
        'edit_approved_at',
        'edit_rejected_at',
        'edit_rejection_reason',
        'edit_approved_by',
        'is_modified',
        'last_modified_at',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason',
        'has_pending_edit',
        'has_pending_cancellation',
        'last_updated_at',
        'is_event',
        'stock_status',
    ];

    /**
     * Garantir isolamento por tenant via store_id
     * Super Admin (tenant_id = null) nao ve pedidos de assinantes
     */
    protected static function booted()
    {
        // Trait handle basic tenant isolation. 
        // We keep local store filtering logic if needed, but tenant isolation is now global via trait.
    }

    protected $casts = [
        'is_draft' => 'boolean',
        'client_confirmed' => 'boolean',
        'client_confirmed_at' => 'datetime',
        'is_editing' => 'boolean',
        'edit_requested_at' => 'datetime',
        'edit_completed_at' => 'datetime',
        'edit_approved_at' => 'datetime',
        'edit_rejected_at' => 'datetime',
        'is_modified' => 'boolean',
        'last_modified_at' => 'datetime',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime',
        'has_pending_edit' => 'boolean',
        'has_pending_cancellation' => 'boolean',
        'last_updated_at' => 'datetime',
        'is_event' => 'boolean',
        'order_date' => 'date',
        'delivery_date' => 'date',
        'terms_accepted_at' => 'datetime',
    ];

    /**
     * Relacionamentos que devem ser sempre carregados
     */
    protected $with = ['status'];
    protected $appends = ['cover_image_url'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Accessor para retornar o nome do vendedor automaticamente
     * Se o campo seller estiver vazio, retorna o nome do usuário que criou o pedido
     */
    public function getSellerAttribute($value)
    {
        if (empty($value) && $this->user) {
            return $this->user->name;
        }
        return $value;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(OrderComment::class)->orderBy('created_at', 'desc');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->orderBy('created_at', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function editHistory(): HasMany
    {
        return $this->hasMany(OrderEditHistory::class);
    }

    public function editApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_approved_by');
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return ImageHelper::resolveCoverImageUrl($this->cover_image);
    }

    public function deliveryRequests(): HasMany
    {
        return $this->hasMany(DeliveryRequest::class);
    }

    public function pendingDeliveryRequest()
    {
        return $this->hasOne(DeliveryRequest::class)->where('status', 'pendente');
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(OrderCancellation::class)->orderBy('created_at', 'desc');
    }

    public function editRequests(): HasMany
    {
        return $this->hasMany(OrderEditRequest::class)->orderBy('created_at', 'desc');
    }

    public function pendingCancellation()
    {
        return $this->hasOne(OrderCancellation::class)->where('status', 'pending');
    }

    public function pendingEditRequest()
    {
        return $this->hasOne(OrderEditRequest::class)->where('status', 'pending');
    }

    public function isCancelled(): bool
    {
        return $this->is_cancelled;
    }

    public function hasPendingCancellation(): bool
    {
        return $this->has_pending_cancellation;
    }

    public function hasPendingEdit(): bool
    {
        return $this->has_pending_edit;
    }

    /**
     * Scopes para melhorar queries
     */
    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }

    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }

    public function scopeNotDrafts($query)
    {
        return $query->where('is_draft', false);
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            // Se a busca contém apenas dígitos (ex: '7', '000007', '0007')
            // Converter para inteiro para buscar pelo ID exato (ignora zeros à esquerda)
            if (preg_match('/^\d+$/', $search)) {
                $numericId = (int)$search;
                $q->where('id', $numericId);
            } else {
                // Buscar em outros campos apenas se não for um número
                $q->where('nt', 'like', "%{$search}%")
                    ->orWhereHas('client', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                           ->orWhere('phone_primary', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function($q3) use ($search) {
                        $q3->where('art_name', 'like', "%{$search}%");
                    });
            }
        });
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByStore($query, $storeIds)
    {
        if (empty($storeIds)) {
            return $query;
        }
        
        if (is_array($storeIds)) {
            return $query->whereIn('store_id', $storeIds);
        }
        
        return $query->where('store_id', $storeIds);
    }

    /**
     * Scope para pedidos visíveis no Kanban
     * Exclui rascunhos, cancelados e vendas PDV sem personalização/sublimação local
     */
    public function scopeKanbanVisible($query)
    {
        return $query->notDrafts()
            ->where('is_cancelled', false)
            ->where(function($q) {
                $q->where('is_pdv', false)
                  ->orWhere(function($subQ) {
                      $subQ->where('is_pdv', true)
                           ->whereHas('items', function($itemQuery) {
                               $itemQuery->whereHas('sublimations', function($sublimationQuery) {
                                   $sublimationQuery->where(function($locQuery) {
                                       $locQuery->whereNotNull('location_id')
                                               ->orWhereNotNull('location_name');
                                   });
                               });
                           });
                  });
            });
    }
}
