<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublimationProductPrice extends Model
{
    protected $fillable = [
        'tenant_id',
        'product_type',
        'size_key',
        'tecido_id',
        'quantity_from',
        'quantity_to',
        'price',
        'cost',
    ];

    protected $casts = [
        'quantity_from' => 'integer',
        'quantity_to' => 'integer',
        'tecido_id' => 'integer',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * Relação com Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relação com Tecido
     */
    public function tecido(): BelongsTo
    {
        return $this->belongsTo(Tecido::class);
    }

    /**
     * Label da faixa de quantidade
     */
    public function getRangeLabelAttribute(): string
    {
        if ($this->quantity_to) {
            return "{$this->quantity_from} - {$this->quantity_to}";
        }
        return "{$this->quantity_from}+";
    }

    /**
     * Buscar preço para um tipo e quantidade específicos
     * Prioriza preços do tenant, depois busca preços globais (tenant_id = null)
     */
    public static function getPriceFor(string $productType, int $quantity, ?int $tenantId = null, ?int $tecidoId = null): ?float
    {
        // Primeiro, tentar buscar preço específico do tenant
        // Exclui registros com size_key (preços por dimensão/modelo, ex: Bandeira),
        // que não devem ser usados na precificação por quantidade.
        if ($tenantId) {
            $query = static::where('product_type', $productType)
                ->where('tenant_id', $tenantId)
                ->whereNull('size_key')
                ->where('quantity_from', '<=', $quantity)
                ->where(function($q) use ($quantity) {
                    $q->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
                });

            if ($tecidoId) {
                $query->where('tecido_id', $tecidoId);
            }

            $price = $query->orderBy('quantity_from', 'desc')->first();
            
            if ($price) {
                return (float) $price->price;
            }
        }
        
        // Se não encontrou preço do tenant, buscar preço global
        $globalQuery = static::where('product_type', $productType)
            ->whereNull('tenant_id')
            ->whereNull('size_key')
            ->where('quantity_from', '<=', $quantity)
            ->where(function($q) use ($quantity) {
                $q->whereNull('quantity_to')
                  ->orWhere('quantity_to', '>=', $quantity);
            });

        if ($tecidoId) {
            $globalQuery->where('tecido_id', $tecidoId);
        }

        $globalPrice = $globalQuery->orderBy('quantity_from', 'desc')->first();
        
        return $globalPrice ? (float) $globalPrice->price : null;
    }

    /**
     * Buscar preço para um tipo e tamanho específicos (ex: bandeira)
     */
    public static function getPriceForSize(string $productType, string $sizeKey, ?int $tenantId = null): ?float
    {
        if ($tenantId) {
            $price = static::where('product_type', $productType)
                ->where('tenant_id', $tenantId)
                ->where('size_key', $sizeKey)
                ->first();

            if ($price) {
                return (float) $price->price;
            }
        }

        $price = static::where('product_type', $productType)
            ->whereNull('tenant_id')
            ->where('size_key', $sizeKey)
            ->first();

        return $price ? (float) $price->price : null;
    }

    /**
     * Retorna mapa size_key => price para um tipo de produto
     */
    public static function getSizePricesFor(string $productType, ?int $tenantId = null): array
    {
        $query = static::where('product_type', $productType)
            ->whereNotNull('size_key');

        if ($tenantId) {
            $query->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            });
        } else {
            $query->whereNull('tenant_id');
        }

        $rows = $query->get();

        // Tenant rows override global rows
        $map = [];
        foreach ($rows->sortBy('tenant_id') as $row) {
            if ($row->tenant_id !== null || !isset($map[$row->size_key])) {
                $map[$row->size_key] = (float) $row->price;
            }
        }

        return $map;
    }
}
