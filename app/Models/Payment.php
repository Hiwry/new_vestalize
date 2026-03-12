<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'receipt_attachments',
        'cash_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'entry_date' => 'date',
        'payment_methods' => 'array',
        'receipt_attachments' => 'array',
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
        $path = $this->primary_receipt_attachment_path;

        return $path ? Storage::url($path) : null;
    }

    public function getReceiptAttachmentsListAttribute(): array
    {
        $attachments = $this->receipt_attachments;

        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }

        $normalized = [];

        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_string($attachment) && $attachment !== '') {
                    $normalized[] = [
                        'path' => $attachment,
                        'name' => basename($attachment),
                        'uploaded_at' => null,
                    ];
                    continue;
                }

                if (!is_array($attachment) || empty($attachment['path'])) {
                    continue;
                }

                $normalized[] = [
                    'path' => $attachment['path'],
                    'name' => $attachment['name'] ?? basename($attachment['path']),
                    'uploaded_at' => $attachment['uploaded_at'] ?? null,
                ];
            }
        }

        if (empty($normalized) && $this->receipt_attachment) {
            $normalized[] = [
                'path' => $this->receipt_attachment,
                'name' => basename($this->receipt_attachment),
                'uploaded_at' => null,
            ];
        }

        return $normalized;
    }

    public function getPrimaryReceiptAttachmentPathAttribute(): ?string
    {
        return $this->receipt_attachments_list[0]['path'] ?? $this->receipt_attachment;
    }
}
