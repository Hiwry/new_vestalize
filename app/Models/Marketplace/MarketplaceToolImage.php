<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceToolImage extends Model
{
    protected $table = 'marketplace_tool_images';

    protected $fillable = [
        'marketplace_tool_id',
        'path',
        'caption',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MarketplaceTool::class, 'marketplace_tool_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
