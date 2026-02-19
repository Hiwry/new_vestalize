<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OrderStatusTracking extends Model
{
    protected $table = 'order_status_tracking';

    protected $fillable = [
        'order_id',
        'status_id',
        'user_id',
        'entered_at',
        'exited_at',
        'duration_seconds',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
        'exited_at' => 'datetime',
        'duration_seconds' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relacionamento com pedido
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relacionamento com status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar entrada em um status
     */
    public static function recordEntry(int $orderId, int $statusId, ?int $userId = null): self
    {
        // Fechar tracking anterior se houver
        $previousTracking = self::where('order_id', $orderId)
            ->whereNull('exited_at')
            ->latest()
            ->first();

        if ($previousTracking) {
            $previousTracking->exit();
        }

        return self::create([
            'order_id' => $orderId,
            'status_id' => $statusId,
            'user_id' => $userId ?? auth()->id(),
            'entered_at' => now(),
        ]);
    }

    /**
     * Marcar saída do status e calcular duração
     */
    public function exit(): void
    {
        if ($this->exited_at) {
            return; // Já foi fechado
        }

        $this->exited_at = now();
        $this->duration_seconds = $this->entered_at->diffInSeconds($this->exited_at);
        $this->save();
    }

    /**
     * Obter duração formatada
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return 'Em andamento';
        }

        $days = floor($this->duration_seconds / 86400);
        $hours = floor(($this->duration_seconds % 86400) / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);

        if ($days > 0) {
            return "{$days}d {$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Obter tempo médio por status
     */
    public static function getAverageTimeByStatus(?int $statusId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = self::whereNotNull('duration_seconds');

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        if ($startDate) {
            $query->where('entered_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('entered_at', '<=', $endDate);
        }

        return $query->selectRaw('
                status_id,
                AVG(duration_seconds) as avg_seconds,
                MIN(duration_seconds) as min_seconds,
                MAX(duration_seconds) as max_seconds,
                COUNT(*) as count
            ')
            ->groupBy('status_id')
            ->with('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status_id' => $item->status_id,
                    'status_name' => $item->status->name ?? 'N/A',
                    'avg_seconds' => (int) $item->avg_seconds,
                    'avg_formatted' => self::formatSeconds((int) $item->avg_seconds),
                    'min_seconds' => (int) $item->min_seconds,
                    'min_formatted' => self::formatSeconds((int) $item->min_seconds),
                    'max_seconds' => (int) $item->max_seconds,
                    'max_formatted' => self::formatSeconds((int) $item->max_seconds),
                    'count' => (int) $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Formatar segundos em string legível
     */
    private static function formatSeconds(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($days > 0) {
            return "{$days}d {$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes}m";
        }
    }
}
