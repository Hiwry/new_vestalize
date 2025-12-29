<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSublimationFile extends Model
{
    protected $fillable = [
        'order_sublimation_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function sublimation()
    {
        return $this->belongsTo(OrderSublimation::class, 'order_sublimation_id');
    }

    /**
     * Retorna o tamanho do arquivo formatado
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Retorna a extensão do arquivo
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
