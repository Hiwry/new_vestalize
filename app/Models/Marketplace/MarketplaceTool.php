<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceTool extends Model
{
    protected $table = 'marketplace_tools';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'price_credits',
        'file_path',
        'file_type',
        'file_size',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'file_size'   => 'integer',
    ];

    public static array $categoryLabels = [
        'mockup'       => 'Mockup',
        'pack_imagens' => 'Pack de Imagens',
        'template'     => 'Template',
        'brush'        => 'Brush / Pincel',
        'fonte'        => 'Fonte',
        'outros'       => 'Outros',
    ];

    public static array $categoryIcons = [
        'mockup'       => '📦',
        'pack_imagens' => '🖼️',
        'template'     => '📄',
        'brush'        => '🖌️',
        'fonte'        => '🔤',
        'outros'       => '⭐',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MarketplaceToolImage::class, 'marketplace_tool_id')->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'orderable_id')
            ->where('orderable_type', 'tool');
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function getCoverImageAttribute(): ?string
    {
        $cover = $this->images->where('is_cover', true)->first()
            ?? $this->images->first();

        return $cover ? asset('storage/' . $cover->path) : null;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::$categoryLabels[$this->category] ?? $this->category;
    }

    public function getCategoryIconAttribute(): string
    {
        return self::$categoryIcons[$this->category] ?? '⭐';
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        $bytes = $this->file_size;
        if ($bytes < 1024) return "$bytes B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . " KB";
        return round($bytes / 1048576, 1) . " MB";
    }
}
