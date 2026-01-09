<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLocalProduct extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'description',
        'image',
        'price',
        'is_active',
        'sort_order',
        'requires_customization',
        'available_sizes',
        'requires_size',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'requires_customization' => 'boolean',
        'available_sizes' => 'array',
        'requires_size' => 'boolean',
    ];
}
