<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\ImageHelper;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_number',
        'fabric',
        'color',
        'collar',
        'model',
        'detail',
        'print_type',
        'print_desc',
        'art_name',
        'cover_image',
        'corel_file_path',
        'sizes',
        'quantity',
        'unit_price',
        'total_price',
        'unit_cost',
        'total_cost',
        'is_pinned',
        'is_sublimation_total',
        'sublimation_type',
        'sublimation_addons',
        'art_notes',
    ];

    protected $casts = [
        'sizes' => 'array',
        'sublimation_addons' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'is_pinned' => 'boolean',
        'is_sublimation_total' => 'boolean',
    ];
    protected $appends = ['cover_image_url'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sublimations()
    {
        return $this->hasMany(OrderSublimation::class, 'order_item_id');
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class, 'order_item_id');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return ImageHelper::resolveCoverImageUrl($this->cover_image);
    }

    // Accessors para converter IDs em nomes automaticamente
    public function getFabricAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        $option = ProductOption::where('id', $value)->where('type', 'tecido')->first();
        return $option ? $option->name : $value;
    }

    public function getColorAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        $option = ProductOption::where('id', $value)->where('type', 'cor')->first();
        return $option ? $option->name : $value;
    }

    public function getCollarAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        $option = ProductOption::where('id', $value)->where('type', 'gola')->first();
        return $option ? $option->name : $value;
    }

    public function getModelAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        $option = ProductOption::where('id', $value)->where('type', 'tipo_corte')->first();
        return $option ? $option->name : $value;
    }

    public function getDetailAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        $option = ProductOption::where('id', $value)->where('type', 'detalhe')->first();
        return $option ? $option->name : $value;
    }

    public function getPrintTypeAttribute($value)
    {
        if (!$value || !is_numeric($value)) {
            return $value;
        }
        
        // Tentar buscar em ProductOption primeiro (type='personalizacao')
        $option = ProductOption::where('id', $value)->where('type', 'personalizacao')->first();
        if ($option) {
            return $option->name;
        }
        
        // Fallback: tentar PersonalizationPrice
        $personalization = \App\Models\PersonalizationPrice::where('id', $value)->first();
        return $personalization ? $personalization->personalization_type : $value;
    }
}
