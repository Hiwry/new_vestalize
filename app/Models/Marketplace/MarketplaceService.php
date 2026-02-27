<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceService extends Model
{
    protected $table = 'marketplace_services';

    protected $fillable = [
        'designer_profile_id',
        'title',
        'description',
        'category',
        'price_credits',
        'delivery_days',
        'requirements',
        'revisions',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    public static array $categoryLabels = [
        'logo'              => 'Logo & Marca',
        'vetorizacao'       => 'Vetorização',
        'estampa'           => 'Estampa',
        'mockup'            => 'Mockup',
        'identidade_visual' => 'Identidade Visual',
        'outros'            => 'Outros',
    ];

    public static array $categoryIcons = [
        'logo'              => '🎨',
        'vetorizacao'       => '✏️',
        'estampa'           => '👕',
        'mockup'            => '📦',
        'identidade_visual' => '🏢',
        'outros'            => '⭐',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function designer(): BelongsTo
    {
        return $this->belongsTo(DesignerProfile::class, 'designer_profile_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MarketplaceServiceImage::class, 'marketplace_service_id')->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'orderable_id')
            ->where('orderable_type', 'service');
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
}
