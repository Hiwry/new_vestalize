<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerigraphyColor extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_neon',
        'order',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_neon' => 'boolean',
        'active' => 'boolean',
    ];
}
