<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;

class MarketplaceCreditPackage extends Model
{
    protected $table = 'marketplace_credit_packages';

    protected $fillable = [
        'name',
        'credits',
        'price',
        'subscriber_price',
        'badge',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'subscriber_price' => 'decimal:2',
        'is_featured'      => 'boolean',
        'is_active'        => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getDiscountPercentAttribute(): float
    {
        if ($this->price <= 0) return 0;
        return round((1 - ($this->subscriber_price / $this->price)) * 100, 1);
    }

    public function getPricePerCreditAttribute(): float
    {
        if ($this->credits <= 0) return 0;
        return $this->price / $this->credits;
    }
}
