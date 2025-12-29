<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToTenant;

class Budget extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'store_id',
        'budget_number',
        'order_number',
        'order_id',
        'valid_until',
        'subtotal',
        'discount',
        'discount_type',
        'total',
        'observations',
        'admin_notes',
        'status',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    /**
     * Generate unique budget number
     */
    public static function generateBudgetNumber(): string
    {
        $year = date('Y');
        $lastBudget = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $number = $lastBudget ? (int)substr($lastBudget->budget_number, -4) + 1 : 1;
        
        return 'ORC-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
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
}
