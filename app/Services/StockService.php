<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductOption;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\User;
use App\Models\Notification;
use App\Mail\StockTransferRequestMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StockService
{
    /**
     * Check stock availability and create requests for an order
     * Now with AUTO-RESERVATION: finds the best store with complete stock and reserves automatically
     * 
     * @param Order $order
     * @return array ['status' => 'total|partial|none', 'requests_created' => int, 'details' => [...]]
     */
    public static function checkAndReserveForOrder(Order $order): array
    {
        Log::info('🔍 StockService: Verificando estoque para pedido', ['order_id' => $order->id]);
        
        $resolveOptionId = function ($value, string $type, array $fallbackCandidates = []): ?int {
            if ($value === null || $value === '') {
                return null;
            }

            if (is_numeric($value)) {
                return (int) $value;
            }

            $candidates = [];
            $normalized = trim((string) $value);
            $candidates[] = $normalized;

            if (str_contains($normalized, '-')) {
                foreach (explode('-', $normalized) as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $candidates[] = $part;
                    }
                }
            }

            foreach ($fallbackCandidates as $candidate) {
                $candidate = trim((string) $candidate);
                if ($candidate !== '') {
                    $candidates[] = $candidate;
                }
            }

            foreach ($candidates as $candidate) {
                $option = ProductOption::where('type', $type)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($candidate)])
                    ->first();

                if ($option) {
                    return (int) $option->id;
                }
            }

            foreach ($candidates as $candidate) {
                $option = ProductOption::where('type', $type)
                    ->where('name', 'like', '%' . $candidate . '%')
                    ->first();

                if ($option) {
                    return (int) $option->id;
                }
            }

            return null;
        };

        $storeId = $order->store_id;
        $requestsCreated = 0;
        $itemsWithFullStock = 0;
        $itemsWithPartialStock = 0;
        $itemsWithNoStock = 0;
        $details = [];
        $autoReservedCount = 0;
        
        // Coletar itens de transferência para envio de email agregado
        $transferItemsByStore = [];
        
        // Load items if not loaded
        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }
        
        foreach ($order->items as $item) {
            // Get sizes from item
            $sizes = is_array($item->sizes) ? $item->sizes : json_decode($item->sizes, true) ?? [];
            
            // Skip items without sizes
            if (empty($sizes)) {
                $details[] = [
                    'item_id' => $item->id,
                    'status' => 'skipped',
                    'reason' => 'No sizes defined'
                ];
                continue;
            }
            
            // Get fabric, color, cut_type from item
            $fabricId = $item->fabric_id ?? null;
            $fabricTypeId = $item->fabric_type_id ?? null;
            $colorId = $item->color_id ?? null;
            $cutTypeId = $item->cut_type_id ?? null;

            $fabricName = $item->fabric ?? null;
            $fabricTypeName = $item->fabric_type ?? null;
            $fabricNameCandidate = $fabricName;
            $fabricTypeNameCandidate = $fabricTypeName;

            if (!$fabricTypeName && $fabricName && str_contains($fabricName, '-')) {
                [$fabricNameCandidate, $fabricTypeNameCandidate] = array_map('trim', explode('-', $fabricName, 2));
            }

            // Resolve IDs from names when *_id columns are missing on order_items
            $fabricId = $fabricId !== null ? (int)$fabricId : $resolveOptionId($fabricNameCandidate, 'tecido');
            $fabricTypeId = $fabricTypeId !== null ? (int)$fabricTypeId : $resolveOptionId($fabricTypeNameCandidate, 'tipo_tecido', [$fabricNameCandidate]);
            $colorId = $colorId !== null ? (int)$colorId : $resolveOptionId($item->color ?? null, 'cor');
            $cutTypeId = $cutTypeId !== null ? (int)$cutTypeId : $resolveOptionId($item->model ?? null, 'tipo_corte');
            
            // ========== NOVA LÓGICA: BUSCAR LOJA COM ESTOQUE COMPLETO ==========
            $bestStoreId = Stock::findBestStoreWithCompleteStock(
                $fabricId,
                $fabricTypeId,
                $colorId,
                $cutTypeId,
                $sizes,
                $storeId // Preferir a loja do pedido
            );
            
            $hasCompleteStock = $bestStoreId !== null;
            $targetStoreId = $bestStoreId ?? $storeId;
            
            Log::info('📦 StockService: Resultado da busca de loja', [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'preferred_store' => $storeId,
                'best_store' => $bestStoreId,
                'has_complete' => $hasCompleteStock,
            ]);
            
            $itemDetail = [
                'item_id' => $item->id,
                'item_number' => $item->item_number,
                'sizes' => [],
                'has_full_stock' => $hasCompleteStock,
                'has_partial_stock' => false,
                'auto_reserved' => $hasCompleteStock,
                'source_store_id' => $targetStoreId,
            ];

            // Se não há estoque completo em nenhuma loja, não gerar solicitação
            if (!$hasCompleteStock) {
                $itemsWithNoStock++;
                $details[] = array_merge($itemDetail, ['status' => 'no_stock']);
                continue;
            }
            
            foreach ($sizes as $size => $quantity) {
                if ($quantity <= 0) continue;
                
                // Evitar duplicar solicitações para o mesmo pedido/produto/tamanho
                $existingRequest = StockRequest::where('order_id', $order->id)
                    ->where('fabric_id', $fabricId)
                    ->where('color_id', $colorId)
                    ->where('cut_type_id', $cutTypeId)
                    ->where('size', strtoupper($size))
                    ->where(function($q) use ($targetStoreId) {
                        // Verificar se já existe solicitação para esta loja OU com loja null (broadcast)
                        $q->where('target_store_id', $targetStoreId)
                          ->orWhereNull('target_store_id');
                    })
                    ->whereIn('status', ['pendente', 'aprovado', 'em_transferencia', 'concluido'])
                    ->first();

                if ($existingRequest) {
                    Log::info('Solicitação de estoque já existe, pulando criação duplicada', [
                        'order_id' => $order->id,
                        'stock_request_id' => $existingRequest->id,
                        'size' => $size,
                        'target_store_id' => $targetStoreId,
                    ]);
                    continue;
                }
                
                // Se tem estoque completo, RESERVAR e APROVAR automaticamente
                if ($hasCompleteStock) {
                    $stock = Stock::findByParams(
                        $targetStoreId,
                        $fabricId,
                        $fabricTypeId,
                        $colorId,
                        $cutTypeId,
                        strtoupper($size)
                    );
                    
                    if ($stock) {
                        // Reservar o estoque
                        $reserved = $stock->reserve(
                            $quantity,
                            \Auth::id(),
                            $order->id,
                            null, // stock_request_id será preenchido depois
                            "Reserva automática para Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT)
                        );
                        
                        if ($reserved) {
                            // Criar StockRequest PENDENTE (estoque reservado, mas aguarda aprovação manual)
                            $isTransfer = $targetStoreId !== $storeId;
                            
                            $stockRequest = StockRequest::create([
                                'order_id' => $order->id,
                                'requesting_store_id' => $storeId, // Loja do pedido
                                'target_store_id' => $targetStoreId, // Loja que tem o estoque
                                'fabric_id' => $fabricId,
                                'fabric_type_id' => $fabricTypeId,
                                'color_id' => $colorId,
                                'cut_type_id' => $cutTypeId,
                                'size' => strtoupper($size),
                                'requested_quantity' => $quantity,
                                'approved_quantity' => 0, // Aguarda aprovação
                                'status' => 'pendente', // Pendente, mas estoque já reservado
                                'requested_by' => \Auth::id(),
                                'approved_by' => null,
                                'approved_at' => null,
                                'request_notes' => $isTransfer 
                                    ? "Transferência automática - Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " (Estoque reservado)"
                                    : "Reserva automática - Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " (Estoque reservado)",
                            ]);
                            
                            // Notificar usuários do estoque
                            self::notifyStockUsers($stockRequest, $order);
                            
                            $requestsCreated++;
                            $autoReservedCount++;
                            
                            // NOTIFICAÇÃO DE TRANSFERÊNCIA
                            if ($isTransfer) {
                                try {
                                    // Buscar usuários da loja de origem (target_store)
                                    $targetStoreUsers = \App\Models\User::where('store_id', $targetStoreId)->pluck('id');
                                    $storeName = \App\Models\Store::find($targetStoreId)?->name ?? 'Loja';
                                    $requestingStoreName = \App\Models\Store::find($storeId)?->name ?? 'Outra Loja';
                                    
                                    foreach ($targetStoreUsers as $userId) {
                                        \App\Models\Notification::create([
                                            'user_id' => $userId,
                                            'type' => 'stock_transfer_request',
                                            'title' => 'Solicitação de Transferência',
                                            'message' => "Pedido #{$order->id} precisa de estoque da {$storeName}. Tamanho: {$size}, Qtd: {$quantity}. Destino: {$requestingStoreName}.",
                                            'link' => route('stock-requests.index'),
                                            'data' => [
                                                'stock_request_id' => $stockRequest->id,
                                                'order_id' => $order->id,
                                                'size' => $size,
                                                'quantity' => $quantity,
                                            ],
                                        ]);
                                    }
                                    
                                    Log::info('📨 Notificação de transferência enviada', [
                                        'target_store_id' => $targetStoreId,
                                        'users_notified' => $targetStoreUsers->count(),
                                    ]);
                                    
                                    // Coletar item para email agregado
                                    if (!isset($transferItemsByStore[$targetStoreId])) {
                                        $transferItemsByStore[$targetStoreId] = [];
                                    }
                                    $transferItemsByStore[$targetStoreId][] = [
                                        'size' => $size,
                                        'quantity' => $quantity,
                                        'product' => trim(($item->fabric ?? '') . ' - ' . ($item->color ?? '') . ' - ' . ($item->model ?? ''), ' -'),
                                    ];
                                } catch (\Exception $e) {
                                    Log::warning('Erro ao enviar notificação de transferência', [
                                        'error' => $e->getMessage(),
                                    ]);
                                }
                            }
                            
                            Log::info('✅ StockRequest criado com reserva automática (pendente)', [
                                'stock_request_id' => $stockRequest->id,
                                'order_id' => $order->id,
                                'size' => $size,
                                'quantity' => $quantity,
                                'target_store_id' => $targetStoreId,
                                'is_transfer' => $isTransfer,
                                'stock_reserved' => true,
                            ]);
                            
                            $itemDetail['sizes'][] = [
                                'size' => $size,
                                'requested' => $quantity,
                                'available' => $quantity,
                                'fulfilled' => true,
                                'partial' => false,
                                'auto_reserved' => true,
                            ];
                            
                            continue;
                        }
                    }
                }
                
                // Fallback: criar solicitação pendente (comportamento antigo)
                $stocksQuery = Stock::where('size', strtoupper($size));
                if ($fabricId) {
                    $stocksQuery->where('fabric_id', $fabricId);
                }
                if ($colorId) {
                    $stocksQuery->where('color_id', $colorId);
                }
                if ($cutTypeId) {
                    $stocksQuery->where('cut_type_id', $cutTypeId);
                }
                if ($fabricTypeId) {
                    $stocksQuery->where(function ($q) use ($fabricTypeId) {
                        $q->where('fabric_type_id', $fabricTypeId)
                          ->orWhereNull('fabric_type_id');
                    });
                }
                
                $stocks = $stocksQuery->get();
                $stock = $stocks->sortByDesc('available_quantity')->first();
                $availableQuantity = $stock ? $stock->available_quantity : 0;
                $fallbackTargetStoreId = $stock ? $stock->store_id : $storeId;
                
                $canFulfill = $availableQuantity >= $quantity;
                $hasPartial = $availableQuantity > 0 && $availableQuantity < $quantity;
                
                $itemDetail['sizes'][] = [
                    'size' => $size,
                    'requested' => $quantity,
                    'available' => $availableQuantity,
                    'fulfilled' => $canFulfill,
                    'partial' => $hasPartial,
                    'auto_reserved' => false,
                ];
                
                if (!$canFulfill) {
                    $itemDetail['has_full_stock'] = false;
                    if ($hasPartial) {
                        $itemDetail['has_partial_stock'] = true;
                    }
                }
                
                try {
                    $stockRequest = StockRequest::create([
                        'order_id' => $order->id,
                        'requesting_store_id' => $storeId,
                        'target_store_id' => $fallbackTargetStoreId,
                        'fabric_id' => $fabricId,
                        'fabric_type_id' => $fabricTypeId,
                        'color_id' => $colorId,
                        'cut_type_id' => $cutTypeId,
                        'size' => strtoupper($size),
                        'requested_quantity' => $quantity,
                        'approved_quantity' => 0,
                        'status' => 'pendente',
                        'requested_by' => \Auth::id(),
                        'approved_by' => null,
                        'approved_at' => null,
                        'request_notes' => "Solicitacao para Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    ]);
                    
                    // Notificar usuários do estoque
                    self::notifyStockUsers($stockRequest, $order);

                    $requestsCreated++;
                    
                    Log::info('StockRequest criado (pendente)', [
                        'stock_request_id' => $stockRequest->id,
                        'order_id' => $order->id,
                        'size' => $size,
                        'quantity_requested' => $quantity,
                        'target_store_id' => $fallbackTargetStoreId,
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Erro ao criar StockRequest', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Count items by stock status
            if ($itemDetail['has_full_stock']) {
                $itemsWithFullStock++;
            } elseif ($itemDetail['has_partial_stock']) {
                $itemsWithPartialStock++;
            } else {
                $itemsWithNoStock++;
            }
            
            $details[] = $itemDetail;
        }
        
        // ENVIAR EMAIL DE NOTIFICAÇÃO PARA TRANSFERÊNCIAS
        foreach ($transferItemsByStore as $sourceStoreId => $items) {
            try {
                $sourceStore = \App\Models\Store::find($sourceStoreId);
                $destinationStore = \App\Models\Store::find($storeId);
                
                if ($sourceStore && $destinationStore) {
                    // Buscar emails dos usuários da loja de origem
                    $storeUsers = \App\Models\User::where('store_id', $sourceStoreId)
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->get();
                    
                    foreach ($storeUsers as $user) {
                        Mail::to($user->email)->send(
                            new StockTransferRequestMail($order, $sourceStore, $destinationStore, $items)
                        );
                    }
                    
                    Log::info('📧 Email de transferência enviado', [
                        'order_id' => $order->id,
                        'source_store' => $sourceStore->name,
                        'destination_store' => $destinationStore->name,
                        'items_count' => count($items),
                        'users_emailed' => $storeUsers->count(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao enviar email de transferência', [
                    'error' => $e->getMessage(),
                    'source_store_id' => $sourceStoreId,
                ]);
            }
        }
        
        // Determine overall status
        // Se criou solicitações, o status é 'reserved' (aguardando separação)
        // Se tem estoque e nenhuma solicitação pendente, é 'total'
        // Se tem estoque parcial, é 'partial'
        // Se não tem estoque, é 'none'
        $totalItems = $itemsWithFullStock + $itemsWithPartialStock + $itemsWithNoStock;
        $status = 'none';
        
        if ($requestsCreated > 0) {
            // Há solicitações criadas - estoque reservado aguardando separação
            $status = 'reserved';
        } elseif ($totalItems > 0) {
            if ($itemsWithFullStock == $totalItems) {
                $status = 'total';
            } elseif ($itemsWithFullStock > 0 || $itemsWithPartialStock > 0) {
                $status = 'partial';
            }
        }
        
        Log::info('✅ StockService: Verificação concluída', [
            'order_id' => $order->id,
            'status' => $status,
            'requests_created' => $requestsCreated,
            'auto_reserved' => $autoReservedCount,
            'items_full' => $itemsWithFullStock,
            'items_partial' => $itemsWithPartialStock,
            'items_none' => $itemsWithNoStock
        ]);
        
        return [
            'status' => $status,
            'requests_created' => $requestsCreated,
            'auto_reserved' => $autoReservedCount,
            'items_full' => $itemsWithFullStock,
            'items_partial' => $itemsWithPartialStock,
            'items_none' => $itemsWithNoStock,
            'details' => $details
        ];
    }
    
    /**
     * Get stock status label for display
     */
    public static function getStockStatusLabel(?string $status): array
    {
        return match($status) {
            'total' => [
                'label' => 'ESTOQUE TOTAL',
                'color' => 'green',
                'icon' => '✓',
                'bg_class' => 'bg-green-100 text-green-800',
            ],
            'partial' => [
                'label' => 'ESTOQUE PARCIAL',
                'color' => 'yellow',
                'icon' => '⚠',
                'bg_class' => 'bg-yellow-100 text-yellow-800',
            ],
            'none' => [
                'label' => 'SEM ESTOQUE',
                'color' => 'red',
                'icon' => '✗',
                'bg_class' => 'bg-red-100 text-red-800',
            ],
            'reserved' => [
                'label' => 'RESERVADO',
                'color' => 'blue',
                'icon' => '🔒',
                'bg_class' => 'bg-blue-100 text-blue-800',
            ],
            'pending' => [
                'label' => 'VERIFICANDO',
                'color' => 'gray',
                'icon' => '⏳',
                'bg_class' => 'bg-gray-100 text-gray-800',
            ],
            default => [
                'label' => '-',
                'color' => 'gray',
                'icon' => '',
                'bg_class' => 'bg-gray-100 text-gray-600',
            ],
        };
    }
    
    /**
     * Notificar usuários do estoque sobre nova solicitação
     */
    private static function notifyStockUsers(StockRequest $stockRequest, Order $order): void
    {
        try {
            // Buscar usuários com role 'estoque', 'admin' ou 'admin_geral'
            $stockUsers = User::whereIn('role', ['estoque', 'admin', 'admin_geral'])->get();
            
            if ($stockUsers->isEmpty()) {
                Log::warning('Nenhum usuário de estoque encontrado para notificar');
                return;
            }
            
            // Carregar relacionamentos necessários
            $stockRequest->load(['fabric', 'color', 'cutType', 'requestingStore']);
            
            // Construir informações do produto
            $productInfo = sprintf(
                '%s - %s - %s',
                $stockRequest->fabric->name ?? 'N/A',
                $stockRequest->color->name ?? 'N/A',
                $stockRequest->cutType->name ?? 'N/A'
            );
            
            $storeName = $stockRequest->requestingStore->name ?? 'Loja';
            $orderNumber = str_pad($order->id, 6, '0', STR_PAD_LEFT);
            
            foreach ($stockUsers as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'stock_request_created',
                    'title' => 'Nova Solicitação de Estoque',
                    'message' => "Pedido #{$orderNumber} precisa de estoque: {$productInfo} - Tam: {$stockRequest->size} - Qtd: {$stockRequest->requested_quantity}",
                    'link' => route('stock-requests.index'),
                    'data' => [
                        'stock_request_id' => $stockRequest->id,
                        'order_id' => $order->id,
                        'size' => $stockRequest->size,
                        'quantity' => $stockRequest->requested_quantity,
                        'product_info' => $productInfo,
                        'store_name' => $storeName,
                    ],
                ]);
            }
            
            Log::info('📬 Notificações de solicitação de estoque enviadas', [
                'stock_request_id' => $stockRequest->id,
                'order_id' => $order->id,
                'users_notified' => $stockUsers->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Erro ao notificar usuários do estoque', [
                'error' => $e->getMessage(),
                'stock_request_id' => $stockRequest->id ?? null,
            ]);
        }
    }
}
