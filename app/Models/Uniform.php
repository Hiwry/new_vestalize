<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uniform extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'type',
        'color',
        'size',
        'gender',
        'quantity',
        'min_stock',
        'notes',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
