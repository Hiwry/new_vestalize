<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEditRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'changes',
        'order_snapshot_before',
        'order_snapshot_after',
        'admin_notes',
        'approved_by',
        'approved_at',
        'completed_at'
    ];

    protected $casts = [
        'changes' => 'array',
        'order_snapshot_before' => 'array',
        'order_snapshot_after' => 'array',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
