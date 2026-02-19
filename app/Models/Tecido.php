<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tecido extends Model
{
    protected $table = 'tecidos';

    protected $fillable = ['name', 'slug', 'description', 'active', 'order'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'tecido_id');
    }
}

