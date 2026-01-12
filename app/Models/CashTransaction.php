<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    protected $fillable = [
        'store_id',
        'type',
        'category',
        'description',
        'amount',
        'payment_method',
        'payment_methods',
        'status',
        'transaction_date',
        'order_id',
        'user_id',
        'user_name',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'payment_methods' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // Escopo para entradas
    public function scopeEntradas($query)
    {
        return $query->where('type', 'entrada');
    }

    // Escopo para saídas
    public function scopeSaidas($query)
    {
        return $query->where('type', 'saida');
    }

    // Escopo para transações confirmadas
    public function scopeConfirmadas($query)
    {
        return $query->where('status', 'confirmado');
    }

    // Escopo para transações pendentes
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    // Calcular saldo atual (apenas confirmadas)
    public static function getSaldoAtual()
    {
        $entradas = self::where('type', 'entrada')->where('status', 'confirmado')->sum('amount');
        $saidas = self::where('type', 'saida')->where('status', 'confirmado')->sum('amount');
        return $entradas - $saidas;
    }

    // Calcular saldo geral (apenas confirmadas)
    public static function getSaldoGeral()
    {
        $entradas = self::where('type', 'entrada')->where('status', 'confirmado')->sum('amount');
        $saidas = self::where('type', 'saida')->where('status', 'confirmado')->sum('amount');
        return $entradas - $saidas;
    }

    // Calcular saldo pendente (apenas entradas pendentes)
    public static function getSaldoPendente()
    {
        return self::where('type', 'entrada')->where('status', 'pendente')->sum('amount');
    }

    // Total de saídas
    public static function getTotalSaidas()
    {
        return self::where('type', 'saida')->sum('amount');
    }

    public function scopeByStore($query, $storeIds)
    {
        if (empty($storeIds)) {
            return $query;
        }
        
        if (is_array($storeIds)) {
            return $query->whereIn('store_id', $storeIds);
        }
        
        return $query->where('store_id', $storeIds);
    }
}
