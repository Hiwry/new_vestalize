<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SublimationSize extends Model
{
    protected $fillable = [
        'name',
        'dimensions',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function prices()
    {
        return $this->hasMany(SublimationPrice::class, 'size_id');
    }

    public function getPriceForQuantity($quantity)
    {
        return $this->prices()
            ->where('quantity_from', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
            })
            ->orderBy('quantity_from', 'desc')
            ->first();
    }
}
