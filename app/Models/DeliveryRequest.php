<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRequest extends Model
{
    protected $fillable = [
        'order_id',
        'current_delivery_date',
        'requested_delivery_date',
        'reason',
        'status',
        'requested_by',
        'requested_by_name',
        'reviewed_by',
        'reviewed_by_name',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'current_delivery_date' => 'date',
        'requested_delivery_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendente');
    }
}
