<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'payment_method',
        'payment_methods',
        'amount',
        'entry_amount',
        'remaining_amount',
        'due_date',
        'payment_date',
        'entry_date',
        'status',
        'notes',
        'receipt_attachment',
        'cash_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'entry_date' => 'date',
        'payment_methods' => 'array',
        'amount' => 'decimal:2',
        'entry_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'cash_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_attachment ? Storage::url($this->receipt_attachment) : null;
    }
}
