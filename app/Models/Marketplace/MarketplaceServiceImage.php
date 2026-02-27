<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceServiceImage extends Model
{
    protected $table = 'marketplace_service_images';

    protected $fillable = [
        'marketplace_service_id',
        'path',
        'caption',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(MarketplaceService::class, 'marketplace_service_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
