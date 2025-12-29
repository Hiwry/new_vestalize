<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToTenant;

class Stock extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'fabric_id',
        'fabric_type_id',
        'color_id',
        'cut_type_id',
        'size',
        'shelf',
        'quantity',
        'reserved_quantity',
        'min_stock',
        'max_stock',
        'notes',
    ];

    /**
     * Garantir isolamento por tenant via store_id
     */
    protected static function booted()
    {
        // Isolation handled by BelongsToTenant trait
    }

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
    ];

    /**
     * Relacionamento com loja
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relacionamento com tecido (Categoria Pai, ex: Algodão)
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_id');
    }

    /**
     * Relacionamento com tipo de tecido (Específico, ex: Cedromix)
     */
    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_type_id');
    }

    /**
     * Relacionamento com cor
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'color_id');
    }

    /**
     * Relacionamento com tipo de corte
     */
    public function cutType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'cut_type_id');
    }

    /**
     * Obter quantidade disponível (calculada)
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /**
     * Verificar se está abaixo do estoque mínimo
     */
    public function isBelowMinimum(): bool
    {
        return $this->available_quantity < $this->min_stock;
    }

    /**
     * Verificar se tem estoque disponível
     */
    public function hasStock(int $quantity): bool
    {
        return $this->available_quantity >= $quantity;
    }

    /**
     * Reservar quantidade
     */
    public function reserve(int $quantity, ?int $userId = null, ?int $orderId = null, ?int $stockRequestId = null, ?string $notes = null): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }

        $this->increment('reserved_quantity', $quantity);
        
        // Registrar histórico (após incrementar para ter valores corretos)
        try {
            \App\Models\StockHistory::recordMovement(
                'reserva',
                $this,
                $quantity, // Quantidade reservada
                $userId ?? auth()->id(),
                $orderId,
                $stockRequestId,
                $notes
            );
        } catch (\Exception $e) {
            \Log::warning('Erro ao registrar histórico de estoque', [
                'error' => $e->getMessage(),
                'stock_id' => $this->id,
            ]);
        }
        
        return true;
    }

    /**
     * Liberar quantidade reservada
     */
    public function release(int $quantity, ?int $userId = null, ?int $orderId = null, ?int $stockRequestId = null, ?string $notes = null): void
    {
        $quantityToRelease = max(0, min($quantity, $this->reserved_quantity));
        $this->decrement('reserved_quantity', $quantityToRelease);
        
        // Registrar histórico (após decrementar para ter valores corretos)
        try {
            \App\Models\StockHistory::recordMovement(
                'liberacao',
                $this,
                -$quantityToRelease, // Quantidade liberada (negativa)
                $userId ?? auth()->id(),
                $orderId,
                $stockRequestId,
                $notes
            );
        } catch (\Exception $e) {
            \Log::warning('Erro ao registrar histórico de estoque', [
                'error' => $e->getMessage(),
                'stock_id' => $this->id,
            ]);
        }
    }

    /**
     * Baixar estoque (usar quantidade)
     */
    public function use(int $quantity, ?int $userId = null, ?int $orderId = null, ?int $stockRequestId = null, ?string $notes = null): bool
    {
        // Verificar se há estoque disponível
        $availableQuantity = $this->available_quantity;
        
        if ($availableQuantity <= 0) {
            \Log::warning('Tentativa de usar estoque sem quantidade disponível', [
                'stock_id' => $this->id,
                'available' => $availableQuantity,
                'requested' => $quantity,
            ]);
            return false;
        }
        
        // Se a quantidade solicitada for maior que a disponível, usar apenas o disponível
        $quantityToUse = min($quantity, $availableQuantity);
        
        if ($quantityToUse < $quantity) {
            \Log::warning('Quantidade solicitada maior que disponível - usando apenas o disponível', [
                'stock_id' => $this->id,
                'available' => $availableQuantity,
                'requested' => $quantity,
                'will_use' => $quantityToUse,
            ]);
        }

        $oldQuantity = $this->quantity;
        $oldReserved = $this->reserved_quantity;
        $reservedToRelease = max(0, min($quantityToUse, $this->reserved_quantity));
        
        // Decrementar quantidade e quantidade reservada
        $this->decrement('quantity', $quantityToUse);
        $this->decrement('reserved_quantity', $reservedToRelease);
        
        // Recarregar para garantir que temos os valores atualizados
        $this->refresh();
        
        \Log::info('Estoque usado', [
            'stock_id' => $this->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $this->quantity,
            'old_reserved' => $oldReserved,
            'new_reserved' => $this->reserved_quantity,
            'quantity_used' => $quantityToUse,
            'reserved_released' => $reservedToRelease,
        ]);
        
        // Registrar histórico
        try {
            \App\Models\StockHistory::recordMovement(
                'saida',
                $this,
                -$quantityToUse,
                $userId ?? auth()->id(),
                $orderId,
                $stockRequestId,
                $notes
            );
        } catch (\Exception $e) {
            \Log::warning('Erro ao registrar histórico de estoque', [
                'error' => $e->getMessage(),
                'stock_id' => $this->id,
            ]);
        }
        
        // Verificar estoque baixo após uso
        $this->checkLowStockAfterChange();
        
        return true;
    }

    /**
     * Adicionar ao estoque
     */
    public function add(int $quantity, ?int $userId = null, ?int $orderId = null, ?int $stockRequestId = null, ?string $notes = null): void
    {
        $this->increment('quantity', $quantity);
        
        // Registrar histórico
        try {
            \App\Models\StockHistory::recordMovement(
                'entrada',
                $this,
                $quantity,
                $userId ?? auth()->id(),
                $orderId,
                $stockRequestId,
                $notes
            );
        } catch (\Exception $e) {
            \Log::warning('Erro ao registrar histórico de estoque', [
                'error' => $e->getMessage(),
                'stock_id' => $this->id,
            ]);
        }
        
        // Verificar estoque baixo após adição (pode ter saído do estado de baixo)
        $this->checkLowStockAfterChange();
    }

    /**
     * Verificar estoque baixo após mudança
     */
    private function checkLowStockAfterChange()
    {
        $this->refresh();
        $availableQuantity = $this->available_quantity;
        
        // Verificar se está abaixo de 30 peças
        if ($availableQuantity < 30) {
            $this->load(['store', 'fabric', 'color', 'cutType']);
            
            // Verificar se já existe notificação recente (últimas 24 horas) para evitar spam
            $recentNotification = \App\Models\Notification::where('type', 'low_stock')
                ->where('data->store_name', $this->store->name ?? 'N/A')
                ->where('data->product_info', ($this->fabric->name ?? 'N/A') . ' - ' . ($this->color->name ?? 'N/A'))
                ->where('data->size', $this->size)
                ->where('created_at', '>=', now()->subDay())
                ->exists();
            
            if (!$recentNotification) {
                $productInfo = ($this->fabric->name ?? 'N/A') . ' - ' . ($this->color->name ?? 'N/A');
                $storeName = $this->store->name ?? 'N/A';
                
                // Criar notificação para usuários de estoque
                $estoqueUsers = \App\Models\User::where('role', 'estoque')->orWhere('role', 'admin')->get();
                foreach ($estoqueUsers as $estoqueUser) {
                    \App\Models\Notification::createLowStock(
                        $estoqueUser->id,
                        $storeName,
                        $productInfo,
                        $this->size,
                        $availableQuantity
                    );
                }
            }
        }
    }

    /**
     * Buscar estoque por parâmetros
     * NOTA: fabricId e fabricTypeId são opcionais - se null, ignora este filtro
     */
    public static function findByParams(int $storeId, ?int $fabricId, ?int $fabricTypeId, ?int $colorId, ?int $cutTypeId, string $size): ?self
    {
        return self::where('store_id', $storeId)
            ->when($fabricId !== null, fn($q) => $q->where('fabric_id', $fabricId))
            ->when($fabricTypeId !== null, fn($q) => $q->where('fabric_type_id', $fabricTypeId))
            ->where('color_id', $colorId)
            ->where('cut_type_id', $cutTypeId)
            ->where('size', $size)
            ->first();
    }

    /**
     * Criar ou atualizar estoque
     */
    public static function createOrUpdateStock(array $data): self
    {
        return self::updateOrCreate(
            [
                'store_id' => $data['store_id'],
                'fabric_id' => $data['fabric_id'],
                'fabric_type_id' => $data['fabric_type_id'] ?? null,
                'color_id' => $data['color_id'],
                'cut_type_id' => $data['cut_type_id'],
                'size' => $data['size'],
            ],
            [
                'quantity' => $data['quantity'] ?? 0,
                'min_stock' => $data['min_stock'] ?? 0,
                'max_stock' => $data['max_stock'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );
    }

    /**
     * Verificar se uma loja tem estoque COMPLETO para todos os tamanhos solicitados
     * 
     * @param int $storeId
     * @param int|null $fabricId
     * @param int|null $fabricTypeId
     * @param int|null $colorId
     * @param int|null $cutTypeId
     * @param array $sizes Array associativo ['PP' => 10, 'M' => 20, ...]
     * @return bool True se a loja tem estoque suficiente para TODOS os tamanhos
     */
    public static function checkCompleteAvailability(
        int $storeId,
        ?int $fabricId,
        ?int $fabricTypeId,
        ?int $colorId,
        ?int $cutTypeId,
        array $sizes
    ): bool {
        foreach ($sizes as $size => $quantity) {
            if ($quantity <= 0) continue;
            
            $stock = self::findByParams($storeId, $fabricId, $fabricTypeId, $colorId, $cutTypeId, strtoupper($size));
            
            if (!$stock || $stock->available_quantity < $quantity) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Encontrar a melhor loja que tem estoque COMPLETO para todos os tamanhos
     * 
     * @param int|null $fabricId
     * @param int|null $fabricTypeId
     * @param int|null $colorId
     * @param int|null $cutTypeId
     * @param array $sizes Array associativo ['PP' => 10, 'M' => 20, ...]
     * @param int|null $preferredStoreId Loja preferida (geralmente a loja do pedido)
     * @return int|null ID da loja que tem estoque completo, ou null se nenhuma tiver
     */
    public static function findBestStoreWithCompleteStock(
        ?int $fabricId,
        ?int $fabricTypeId,
        ?int $colorId,
        ?int $cutTypeId,
        array $sizes,
        ?int $preferredStoreId = null
    ): ?int {
        // Primeiro, verificar a loja preferida
        if ($preferredStoreId) {
            $hasComplete = self::checkCompleteAvailability(
                $preferredStoreId,
                $fabricId,
                $fabricTypeId,
                $colorId,
                $cutTypeId,
                $sizes
            );
            
            if ($hasComplete) {
                return $preferredStoreId;
            }
        }
        
        // Buscar todas as lojas que têm pelo menos um dos produtos
        $storeIds = self::query()
            ->when($fabricId, fn($q) => $q->where('fabric_id', $fabricId))
            ->when($colorId, fn($q) => $q->where('color_id', $colorId))
            ->when($cutTypeId, fn($q) => $q->where('cut_type_id', $cutTypeId))
            ->when($fabricTypeId, fn($q) => $q->where(function($q2) use ($fabricTypeId) {
                $q2->where('fabric_type_id', $fabricTypeId)->orWhereNull('fabric_type_id');
            }))
            ->where('quantity', '>', 0)
            ->distinct()
            ->pluck('store_id')
            ->filter(fn($id) => $id !== $preferredStoreId) // Já verificamos a preferida
            ->toArray();
        
        // Verificar cada loja
        foreach ($storeIds as $storeId) {
            $hasComplete = self::checkCompleteAvailability(
                $storeId,
                $fabricId,
                $fabricTypeId,
                $colorId,
                $cutTypeId,
                $sizes
            );
            
            if ($hasComplete) {
                \Log::info('🏪 Stock::findBestStoreWithCompleteStock encontrou loja alternativa', [
                    'store_id' => $storeId,
                    'sizes' => $sizes,
                ]);
                return $storeId;
            }
        }
        
        return null;
    }
}
