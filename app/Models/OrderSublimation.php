<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSublimation extends Model
{
    protected $fillable = [
        'order_item_id',
        'application_type',
        'art_name',
        'size_id',
        'size_name',
        'location_id',
        'location_name',
        'quantity',
        'color_count',
        'has_neon',
        'neon_surcharge',
        'unit_price',
        'discount_percent',
        'final_price',
        'application_image',
        'seller_notes',
        'color_details',
        'addons',
        'regata_discount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'final_price' => 'decimal:2',
        'neon_surcharge' => 'decimal:2',
        'has_neon' => 'boolean',
        'addons' => 'array',
        'regata_discount' => 'boolean',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function size()
    {
        return $this->belongsTo(SublimationSize::class, 'size_id');
    }

    public function location()
    {
        return $this->belongsTo(SublimationLocation::class, 'location_id');
    }

    public function files()
    {
        return $this->hasMany(OrderSublimationFile::class, 'order_sublimation_id');
    }

    /**
     * Get the URL for the application image
     * This method ensures consistent URL generation for all application images
     */
    public function getApplicationImageUrlAttribute(): ?string
    {
        if (!$this->application_image) {
            return null;
        }

        // Extrair o nome do arquivo do caminho salvo no banco
        // O caminho pode estar em diferentes formatos:
        // - orders/applications/filename.jpg
        // - filename.jpg
        // - /orders/applications/filename.jpg
        $imagePath = trim($this->application_image);
        $imagePath = ltrim($imagePath, '/'); // Remover barra inicial se houver
        
        // Extrair apenas o nome do arquivo (basename)
        // Isso garante que mesmo se o caminho completo estiver salvo, pegamos só o filename
        $basename = basename($imagePath);
        
        // Limpar o basename de caracteres perigosos (manter apenas alfanuméricos, pontos, hífens e underscores)
        $basename = preg_replace('/[^a-zA-Z0-9._-]/', '', $basename);
        
        if (empty($basename)) {
            return null;
        }
        
        // Gerar URL usando a rota específica /imagens-aplicacao/
        // Esta rota é mais confiável pois não depende de .htaccess ou symlink
        try {
            return route('application.image', ['filename' => $basename]);
        } catch (\Exception $e) {
            // Fallback se a rota não existir
            return url('/imagens-aplicacao/' . urlencode($basename));
        }
    }
}
