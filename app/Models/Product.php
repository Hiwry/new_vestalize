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
        'sku',
        'description',
        'catalog_description',
        'category_id',
        'subcategory_id',
        'tecido_id',
        'cut_type_id',
        'personalizacao_id',
        'modelo_id',
        'price',
        'wholesale_price',
        'wholesale_min_qty',
        'sale_type',
        'allow_application',
        'application_types',
        'available_sizes',
        'available_colors',
        'track_stock',
        'stock_quantity',
        'active',
        'show_in_catalog',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'active' => 'boolean',
        'show_in_catalog' => 'boolean',
        'allow_application' => 'boolean',
        'application_types' => 'array',
        'available_sizes' => 'array',
        'available_colors' => 'array',
        'track_stock' => 'boolean',
    ];

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        if (!$this->track_stock) {
            return true; // Not tracking stock = always available
        }
        return ($this->stock_quantity ?? 0) > 0;
    }

    /**
     * Get stock status label
     */
    public function getStockStatusAttribute(): string
    {
        if (!$this->track_stock) {
            return 'available';
        }
        if (($this->stock_quantity ?? 0) > 0) {
            return 'in_stock';
        }
        return 'out_of_stock';
    }


    /**
     * Scope: products visible in the public catalog
     */
    public function scopeCatalogVisible($query)
    {
        return $query->where('active', true)
                     ->where('show_in_catalog', true);
    }

    /**
     * Get the effective price based on quantity
     */
    public function getPriceForQuantity(int $quantity): float
    {
        if ($this->wholesale_price && $quantity >= $this->wholesale_min_qty) {
            return (float) $this->wholesale_price;
        }
        return (float) $this->price;
    }

    /**
     * Check if wholesale price is available
     */
    public function hasWholesalePrice(): bool
    {
        return $this->wholesale_price !== null && $this->wholesale_price > 0;
    }

    public function tecido(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'tecido_id');
    }

    public function cutType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'cut_type_id');
    }

    public function personalizacao(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'personalizacao_id');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'modelo_id');
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

    /**
     * Get the primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        if ($primary && $primary->image_path) {
            if (str_starts_with($primary->image_path, 'http')) {
                return $primary->image_path;
            }
            return asset('storage/' . $primary->image_path);
        }
        return null;
    }
}


