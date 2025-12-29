<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToTenant;

class Product extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'category_id',
        'subcategory_id',
        'tecido_id',
        'personalizacao_id',
        'modelo_id',
        'price',
        'sale_type',
        'allow_application',
        'application_types',
        'active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'allow_application' => 'boolean',
        'application_types' => 'array',
    ];

    public function tecido(): BelongsTo
    {
        return $this->belongsTo(Tecido::class);
    }

    public function personalizacao(): BelongsTo
    {
        return $this->belongsTo(Personalizacao::class);
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}

