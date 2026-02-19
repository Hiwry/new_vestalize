<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequestSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'slug',
        'title',
        'description',
        'primary_color',
        'products_json',
        'whatsapp_number',
        'is_active',
    ];

    protected $casts = [
        'products_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
