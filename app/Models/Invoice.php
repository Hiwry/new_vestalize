<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'ref',
        'numero',
        'serie',
        'chave_nfe',
        'protocolo',
        'data_emissao',
        'valor_produtos',
        'valor_frete',
        'valor_desconto',
        'valor_total',
        'status',
        'status_sefaz',
        'motivo_sefaz',
        'xml_path',
        'pdf_path',
        'cancelled_at',
        'cancel_protocol',
        'cancel_reason',
        'attempts',
        'last_attempt_at',
        'error_log',
        'ibs_cbs_base_calculo',
        'ibs_valor_total',
        'cbs_valor_total',
        'is_valor_total',
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'error_log' => 'array',
        'valor_produtos' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Status possíveis
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DENIED = 'denied';
    const STATUS_ERROR = 'error';

    /**
     * Relacionamento com Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relacionamento com Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com Itens
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Verifica se a nota está autorizada
     */
    public function isAuthorized(): bool
    {
        return $this->status === self::STATUS_AUTHORIZED;
    }

    /**
     * Verifica se a nota pode ser cancelada
     */
    public function canBeCancelled(): bool
    {
        if ($this->status !== self::STATUS_AUTHORIZED) {
            return false;
        }
        
        // NF-e só pode ser cancelada em até 24 horas
        if ($this->data_emissao) {
            return $this->data_emissao->diffInHours(now()) <= 24;
        }
        
        return false;
    }

    /**
     * Retorna o status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PROCESSING => 'Processando',
            self::STATUS_AUTHORIZED => 'Autorizada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_DENIED => 'Rejeitada',
            self::STATUS_ERROR => 'Erro',
            default => 'Desconhecido',
        };
    }

    /**
     * Retorna a cor do status para badges
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_AUTHORIZED => 'green',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_DENIED => 'red',
            self::STATUS_ERROR => 'red',
            default => 'gray',
        };
    }

    /**
     * Gera a referência única
     */
    public static function generateRef(int $tenantId, int $orderId): string
    {
        return "T{$tenantId}-O{$orderId}-" . now()->format('YmdHis');
    }

    /**
     * URL do DANFE (PDF)
     */
    public function getDanfeUrlAttribute(): ?string
    {
        if (empty($this->pdf_path)) {
            return null;
        }
        return asset('storage/' . $this->pdf_path);
    }

    /**
     * Scope para notas autorizadas
     */
    public function scopeAuthorized($query)
    {
        return $query->where('status', self::STATUS_AUTHORIZED);
    }

    /**
     * Scope para notas com erro
     */
    public function scopeWithErrors($query)
    {
        return $query->whereIn('status', [self::STATUS_ERROR, self::STATUS_DENIED]);
    }
}
