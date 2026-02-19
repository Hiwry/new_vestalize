<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'order_item_id',
        'codigo',
        'descricao',
        'ncm',
        'cfop',
        'unidade',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'origem',
        'csosn',
        'ibs_cbs_base_calculo',
        'ibs_valor',
        'cbs_valor',
        'pIBS',
        'pCBS',
        'cst_ibs_cbs',
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Relacionamento com Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relacionamento com OrderItem
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
