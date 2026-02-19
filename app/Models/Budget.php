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
        // Quick budget fields
        'is_quick',
        'contact_name',
        'contact_phone',
        'deadline_days',
        'product_internal',
        'technique',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_quick' => 'boolean',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Check if this is a quick budget
     */
    public function isQuick(): bool
    {
        return (bool) $this->is_quick;
    }

    /**
     * Get contact info (from client or contact fields for quick budgets)
     */
    public function getContactInfo(): array
    {
        if ($this->isQuick() && !$this->client_id) {
            return [
                'name' => $this->contact_name,
                'phone' => $this->contact_phone,
                'email' => null,
            ];
        }
        
        return [
            'name' => $this->client?->name,
            'phone' => $this->client?->phone_primary,
            'email' => $this->client?->email,
        ];
    }

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
        $prefix = 'ORC-' . $year . '-';

        $maxNumber = self::withoutGlobalScopes()
            ->where('budget_number', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(budget_number, '-', -1) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $nextNumber = ((int) $maxNumber) + 1;

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create budget with a unique budget_number (retries on duplicate)
     */
    public static function createWithUniqueNumber(array $attributes, int $maxAttempts = 5): self
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            $attributes['budget_number'] = self::generateBudgetNumber();

            try {
                return self::create($attributes);
            } catch (\Illuminate\Database\QueryException $e) {
                if (!self::isDuplicateBudgetNumberException($e)) {
                    throw $e;
                }

                $lastException = $e;
                $attempt++;
                usleep(50000);
            }
        }

        throw $lastException ?? new \RuntimeException('Falha ao gerar número de orçamento único.');
    }

    private static function isDuplicateBudgetNumberException(\Illuminate\Database\QueryException $e): bool
    {
        $errorInfo = $e->errorInfo ?? [];
        $errorCode = isset($errorInfo[1]) ? (int) $errorInfo[1] : null;
        $message = $e->getMessage();

        return $errorCode === 1062
            || str_contains($message, 'budgets_budget_number_unique')
            || str_contains($message, 'Duplicate entry');
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

