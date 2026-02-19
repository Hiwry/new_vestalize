<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SubLocalProductAddon extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sub_local_product_id',
        'name',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(SubLocalProduct::class, 'sub_local_product_id');
    }
}
