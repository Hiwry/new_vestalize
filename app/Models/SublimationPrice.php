<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SublimationPrice extends Model
{
    protected $fillable = [
        'size_id',
        'quantity_from',
        'quantity_to',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function size()
    {
        return $this->belongsTo(SublimationSize::class, 'size_id');
    }
}
