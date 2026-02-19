<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogItem extends Model
{
    protected $fillable = [
        'catalog_category_id',
        'title',
        'subtitle',
        'image_path',
        'active',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'catalog_category_id');
    }
}
