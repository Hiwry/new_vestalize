<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SewingMachine extends Model
{
    protected $fillable = [
        'store_id',
        'worker_name',
        'internal_code',
        'name',
        'brand',
        'model',
        'invoice_number',
        'serial_number',
        'status',
        'purchase_date',
        'purchase_price',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Ativa',
            'maintenance' => 'Manutenção',
            'broken' => 'Quebrada',
            'disposed' => 'Descartada',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'maintenance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'broken' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            'disposed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
