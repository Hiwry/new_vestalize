<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderComment;
use App\Models\OrderLog;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Notification;
use App\Helpers\StoreHelper;
use App\Helpers\ImageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class KanbanController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        // Super Admin visibility is now handled by StoreHelper filtering
        $search = $request->get('search');
        $personalizationType = $request->get('personalization_type');
        $deliveryDateFilter = $request->get('delivery_date');
        $viewType = $request->get('type', 'production'); // 'production' or 'personalized'
        if (!$viewType || !in_array($viewType, ['production', 'personalized'])) {
            $viewType = 'production';
        }
        $period = $request->get('period', 'week'); // Default: semana

        // Date Logic
        if ($period === 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
        } else {
            $startDate = null;
            $endDate = null;
        }

        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'day':
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
                case 'week':
                    $startDate = $now->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
                    $endDate = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = $now->startOfMonth()->format('Y-m-d');
                    $endDate = $now->endOfMonth()->format('Y-m-d');
                    break;
                default: // 'all'
                    $startDate = null; // Or some default far past date if needed for calendar init, but null handles 'all' usually
                    // For calendar init in blade: x-data="kanbanBoardProd(..., '{{ $startDate }}')"
                    // If start date is null, let's default to today for the calendar Initial view
                    if (!$startDate) $startDate = $now->format('Y-m-d');
                    $endDate = null;
                    break;
            }
        }
        
        // Determinar o tenant a ser usado para status
        $activeTenantId = $user->tenant_id;
        if ($activeTenantId === null) {
            $activeTenantId = session('selected_tenant_id');
        }
        if ($activeTenantId === null) {
            $firstStore = \App\Models\Store::first();
            $activeTenantId = $firstStore ? $firstStore->tenant_id : 1;
        }
        
        // Filter statuses by type
        $statuses = Status::where('tenant_id', $activeTenantId)
            ->where('type', $viewType)
            ->withCount(['orders' => function($query) use ($personalizationType, $deliveryDateFilter, $viewType) {
            $query->notDrafts()
                  ->where('is_cancelled', false);
            
            // Filter by origin based on view type
            if ($viewType === 'personalized') {
                $query->where('origin', 'personalized');
            } else {
                $query->where(function($q) {
                    $q->where('origin', '!=', 'personalized')
                      ->orWhereNull('origin');
                });
            }

            if (Auth::user()->isVendedor()) {
                $query->byUser(Auth::id());
            }
            // Aplicar filtro de loja
            StoreHelper::applyStoreFilter($query);
            
            // Aplicar filtro de personalização na contagem também
            if ($personalizationType) {
                // ... same logic ...
                 $query->where(function($q) use ($personalizationType) {
                    $q->whereHas('items', function($itemQuery) use ($personalizationType) {
                        $itemQuery->where(function($subQuery) use ($personalizationType) {
                            $subQuery->where('print_type', 'like', "%{$personalizationType}%")
                                    ->orWhereHas('sublimations', function($sublimationQuery) use ($personalizationType) {
                                        $sublimationQuery->where('application_type', 'like', "%{$personalizationType}%");
                                    });
                        });
                    });
                });
            }

            if ($deliveryDateFilter) {
                $query->whereDate('delivery_date', $deliveryDateFilter);
            }
        }])->orderBy('position')->get();
        
        $query = Order::with([
            'client', 
            'user', 
            'store',
            'items.files',
            'items.sublimations.location',
            'items.sublimations',
            'pendingCancellation', 
            'pendingEditRequest'
        ])->notDrafts() 
          ->where('is_cancelled', false);

        // Filter by origin based on view type
        if ($viewType === 'personalized') {
            $query->where('origin', 'personalized');
        } else {
            $query->where(function($q) {
                $q->where('origin', '!=', 'personalized')
                  ->orWhereNull('origin');
            });
        }

        // Aplicar filtro de loja
        StoreHelper::applyStoreFilter($query);

        // Se for vendedor, mostrar apenas os pedidos que ele criou
        if (Auth::user()->isVendedor()) {
            $query->byUser(Auth::id());
        }
        
        // Filtrar vendas do PDV: excluir vendas PDV que não têm sublimação local
        // ONLY APPLY THIS LOGIC FOR PRODUCTION VIEW? Or both?
        // Let's assume personalized module items are always relevant if in personalized status.
        // But for consistency:
        if ($viewType === 'production') {
             $query->where(function($q) {
                $q->where('is_pdv', false) 
                  ->orWhere(function($subQ) {
                      $subQ->where('is_pdv', true)
                           ->whereHas('items', function($itemQuery) {
                               $itemQuery->whereHas('sublimations', function($sublimationQuery) {
                                   $sublimationQuery->where(function($locQuery) {
                                       $locQuery->whereNotNull('location_id')
                                               ->orWhereNotNull('location_name');
                                   });
                               });
                           });
                  });
            });
        }
        
        // Aplicar filtro de personalização
        if ($personalizationType) {
             $query->where(function($q) use ($personalizationType) {
                $q->whereHas('items', function($itemQuery) use ($personalizationType) {
                    $itemQuery->where(function($subQuery) use ($personalizationType) {
                        $subQuery->where('print_type', 'like', "%{$personalizationType}%")
                                ->orWhere(function($idQuery) use ($personalizationType) {
                                    $personalizationOption = \App\Models\ProductOption::where('type', 'personalizacao')
                                        ->where('name', 'like', "%{$personalizationType}%")
                                        ->pluck('id');
                                    if ($personalizationOption->isNotEmpty()) {
                                        $idQuery->whereIn('print_type', $personalizationOption->toArray());
                                    }
                                })
                                ->orWhereHas('sublimations', function($sublimationQuery) use ($personalizationType) {
                                    $sublimationQuery->where('application_type', 'like', "%{$personalizationType}%");
                                });
                    });
                });
            });
        }

        if ($deliveryDateFilter) {
            $query->whereDate('delivery_date', $deliveryDateFilter);
        }
        
        // Aplicar busca usando scope otimizado
        if ($search) {
            $query->search($search);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // Verificar existência das imagens de capa
        foreach ($orders as $order) {
            $firstItem = $order->items->first();
            $coverImageUrl = $order->cover_image_url ?: $firstItem?->cover_image_url;

            $order->cover_image_exists = (bool) $coverImageUrl;
            $order->cover_image_url = $coverImageUrl;
        }
        
        $ordersByStatus = $orders->groupBy('status_id');
        
        // Se houver pesquisa e apenas um resultado, passar o ID do pedido para abrir automaticamente
        $autoOpenOrderId = null;
        if ($search && $orders->count() === 1) {
            $autoOpenOrderId = $orders->first()->id;
        } elseif ($search && $orders->count() > 1) {
            $autoOpenOrderId = $orders->first()->id;
        }

        // Buscar tipos de personalização disponíveis
        $personalizationTypes = \App\Models\PersonalizationPrice::getPersonalizationTypes();
        
        $ordersForCalendar = $orders;
        
        return view('kanban.index', compact('statuses', 'ordersByStatus', 'search', 'autoOpenOrderId', 'personalizationType', 'personalizationTypes', 'deliveryDateFilter', 'ordersForCalendar', 'viewType', 'period', 'startDate', 'endDate'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        // Apenas administradores podem atualizar status
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem alterar status dos pedidos.'
            ], 403);
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $order = Order::with('status')->findOrFail($validated['order_id']);
        $oldStatus = $order->status;
        $newStatus = Status::findOrFail($validated['status_id']);
        
        $order->update(['status_id' => $validated['status_id']]);

        // Registrar tracking de mudança de status
        try {
            // Fechar tracking anterior se houver
            $previousTracking = \App\Models\OrderStatusTracking::where('order_id', $order->id)
                ->whereNull('exited_at')
                ->latest()
                ->first();

            if ($previousTracking) {
                $previousTracking->exit();
            }

            // Registrar entrada no novo status
            \App\Models\OrderStatusTracking::recordEntry($order->id, $newStatus->id, Auth::id());
        } catch (\Exception $e) {
            \Log::warning('Erro ao registrar tracking de status', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }

        // Criar log de mudança de status
        $user = Auth::user();
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'action' => 'status_changed',
            'description' => "Status alterado de '{$oldStatus->name}' para '{$newStatus->name}'",
            'old_value' => ['status' => $oldStatus->name],
            'new_value' => ['status' => $newStatus->name],
        ]);

        // Criar notificação para o criador do pedido
        if ($order->user_id && $order->user_id != Auth::id()) {
            Notification::createOrderMoved(
                $order->user_id,
                $order->id,
                $order->order_number,
                $oldStatus->name,
                $newStatus->name
            );
        }

        // Se o novo status for "Pronto" ou "Entregue", confirmar transações do caixa
        if (in_array(strtolower($newStatus->name), ['pronto', 'entregue'])) {
            \App\Models\CashTransaction::where('order_id', $order->id)
                ->where('status', 'pendente')
                ->update(['status' => 'confirmado']);
                
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? 'Sistema',
                'action' => 'cash_confirmed',
                'description' => "Valores do pedido confirmados no caixa (Pedido movido para {$newStatus->name})",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
        ]);
    }

    public function getOrderDetails($id): JsonResponse
    {
        $order = Order::with([
            'client',
            'user',
            'store',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.sublimations.files',
            'items.files',
            'comments.user',
            'logs.user',
            'pendingDeliveryRequest',
            'payments'
        ])->findOrFail($id);

        // Verificar se o pedido está cancelado
        if ($order->is_cancelled) {
            return response()->json([
                'error' => 'Este pedido foi cancelado e não pode ser visualizado no Kanban.'
            ], 403);
        }

        // Verificar existência das imagens de capa dos itens
        foreach ($order->items as $item) {
            $item->cover_image_exists = (bool) $item->cover_image_url;
        }

        // Retornar todos os pagamentos como array
        $payments = $order->payments;

        return response()->json([
            'id' => $order->id,
            'client' => $order->client,
            'user' => $order->user,
            'store' => $order->store,
            'items' => $order->items,
            'payments' => $payments,
            'entry_date' => $order->entry_date,
            'comments' => $order->comments,
            'logs' => $order->logs,
            'total' => $order->total,
            'created_at' => $order->created_at,
            'delivery_date' => $order->delivery_date ? $order->delivery_date->format('Y-m-d') : null,
            'pending_delivery_request' => $order->pendingDeliveryRequest,
            'seller' => $order->seller,
        ]);
    }

    public function addComment(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $order = Order::findOrFail($id);
        $user = Auth::user();
        $userName = $user ? $user->name : 'Anônimo';

        $comment = OrderComment::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $userName,
            'comment' => $validated['comment'],
        ]);

        // Criar log
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $userName,
            'action' => 'comment_added',
            'description' => 'Comentário adicionado',
            'new_value' => ['comment' => $validated['comment']],
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comentário adicionado com sucesso',
        ]);
    }

    public function uploadItemFile(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'file' => 'required|file|max:51200', // Max 50MB
        ]);

        try {
            $item = \App\Models\OrderItem::findOrFail($request->item_id);
            
            // Check authorization (if user can edit this order)
            $order = $item->order;
            // Add authorization check logic here if needed (e.g., store isolation)
            
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Store file
            $path = $file->store('orders/items/files', 'public');
            
            // Create OrderFile record
            $orderFile = \App\Models\OrderFile::create([
                'order_item_id' => $item->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo enviado com sucesso!',
                'file' => $orderFile
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar arquivo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadCostura($id)
    {
        try {
            // Aumentar limite de memória para processamento de PDF
            ini_set('memory_limit', '1024M');
            
            \Log::info('Iniciando download de costura para pedido: ' . $id);
            
            $order = Order::with(['client', 'items'])->findOrFail($id);
            \Log::info('Pedido carregado com sucesso', ['order_id' => $order->id, 'items_count' => $order->items->count()]);

            // Array para armazenar dados de imagem de cada item (evita conflito com accessors)
            $itemImages = [];

            // Processar imagens dos itens com otimizações para evitar problemas de memória
            foreach ($order->items as $item) {
                // Inicializar dados da imagem
                $itemImages[$item->id] = [
                    'hasCoverImage' => false,
                    'coverImageInfo' => null,
                    'coverImageUrl' => null,
                    'coverImageBase64' => false,
                ];
                
                if ($item->cover_image) {
                    // Normalizar o caminho
                    $normalizedPath = ImageHelper::normalizePath($item->cover_image);
                    
                    // Primeiro, verificar se a imagem está em public/images (novo local, sem symlink)
                    $publicImagesPath = public_path('images/' . $normalizedPath);
                    $actualPath = null;
                    
                    if (file_exists($publicImagesPath)) {
                        $actualPath = $publicImagesPath;
                        \Log::info('Imagem encontrada em public/images (costura)', ['path' => $publicImagesPath]);
                    } else {
                        // Se não encontrou em public/images, tentar em storage/app/public (compatibilidade)
                        $relativePath = ImageHelper::resolveRelativePath($item->cover_image, [
                            'orders/covers',
                            'orders/items/covers',
                            'orders/items',
                            'orders',
                        ]);
                        
                        if ($relativePath) {
                            $storagePath = Storage::disk('public')->path($relativePath);
                            if (file_exists($storagePath)) {
                                $actualPath = $storagePath;
                                \Log::info('Imagem encontrada em storage/app/public (costura)', ['path' => $storagePath]);
                            }
                        }
                    }
                    
                    if ($actualPath && file_exists($actualPath)) {
                        // Verificar se o arquivo não é muito grande (limite de 2MB para economizar memória)
                        $fileSize = filesize($actualPath);
                        if ($fileSize > 2 * 1024 * 1024) {
                            // Tentar criar uma versão otimizada da imagem
                            $optimizedPath = $this->optimizeImageForPDF($actualPath);
                            if ($optimizedPath && file_exists($optimizedPath)) {
                                $actualPath = $optimizedPath;
                                $fileSize = filesize($actualPath);
                                \Log::info('Imagem otimizada criada', [
                                    'original' => $actualPath,
                                    'otimizada' => $optimizedPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            } else {
                                \Log::warning('Imagem muito grande e não foi possível otimizar, usando original', [
                                    'path' => $actualPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            }
                        }
                            
                        $itemImages[$item->id]['hasCoverImage'] = true;
                            $itemImages[$item->id]['coverImageInfo'] = [
                                'name' => basename($actualPath),
                                'size' => $this->formatFileSize($fileSize),
                                'extension' => strtoupper(pathinfo($actualPath, PATHINFO_EXTENSION)),
                                'path' => $item->cover_image
                            ];
                            
                            // Tentar múltiplas abordagens para garantir que a imagem apareça no PDF
                            // Abordagem 1: Base64 (mais confiável)
                            try {
                                $imageData = file_get_contents($actualPath);
                                if ($imageData && strlen($imageData) > 0) {
                                    $imageBase64 = base64_encode($imageData);
                                    $mimeType = mime_content_type($actualPath) ?: 'image/jpeg';
                                    
                                    // Limitar tamanho do base64 para evitar problemas de memória
                                    if (strlen($imageBase64) < 5 * 1024 * 1024) { // 5MB
                                        // Armazenar no array separado para evitar conflito com accessors
                                        $base64DataUrl = 'data:' . $mimeType . ';base64,' . $imageBase64;
                                        $itemImages[$item->id]['coverImageUrl'] = $base64DataUrl;
                                        $itemImages[$item->id]['coverImageBase64'] = true;
                                        
                                        // Verificar se o valor foi salvo corretamente
                                        $savedValue = $itemImages[$item->id]['coverImageUrl'] ?? null;
                                        
                                        \Log::info('Imagem costura processada (base64)!', [
                                            'original_path' => $actualPath,
                                            'size' => ($itemImages[$item->id]['coverImageInfo'] ?? [])['size'] ?? 'N/A',
                                            'base64_length' => strlen($imageBase64),
                                            'mime_type' => $mimeType,
                                            'data_url_length' => strlen($base64DataUrl),
                                            'saved_value_preview' => $savedValue ? (str_starts_with($savedValue, 'data:image') ? substr($savedValue, 0, 50) . '...' : $savedValue) : null,
                                            'saved_value_length' => $savedValue ? strlen($savedValue) : 0
                                        ]);
                                    } else {
                                        throw new \Exception('Imagem muito grande para base64');
                                    }
                                } else {
                                    throw new \Exception('Arquivo vazio ou não pode ser lido');
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Erro ao converter imagem para base64, usando caminho:', [
                                    'error' => $e->getMessage(),
                                    'path' => $actualPath
                                ]);
                                
                                // Fallback: usar caminho relativo ao chroot
                                $publicChrootPath = realpath(public_path());
                                $storageChrootPath = realpath(storage_path('app/public'));
                                
                                if ($publicChrootPath && str_starts_with($actualPath, $publicChrootPath)) {
                                    // Caminho relativo ao chroot (public/)
                                    $pdfPath = str_replace($publicChrootPath, '', $actualPath);
                                    $pdfPath = str_replace('\\', '/', $pdfPath);
                                    $pdfPath = ltrim($pdfPath, '/\\');
                                    $pdfPath = '/' . $pdfPath;
                                } elseif ($storageChrootPath && str_starts_with($actualPath, $storageChrootPath)) {
                                    // Caminho relativo ao chroot (storage/app/public)
                                    $pdfPath = str_replace($storageChrootPath, '', $actualPath);
                                    $pdfPath = str_replace('\\', '/', $pdfPath);
                                    $pdfPath = ltrim($pdfPath, '/\\');
                                    $pdfPath = '/storage/' . $pdfPath;
                                } else {
                                    // Caminho absoluto (fallback)
                                    if (DIRECTORY_SEPARATOR === '\\') {
                                        // Windows: usar caminho absoluto com barras normais
                                        $pdfPath = str_replace('\\', '/', $actualPath);
                                        // Adicionar barra inicial se não tiver
                                        if (!str_starts_with($pdfPath, '/')) {
                                            $pdfPath = '/' . $pdfPath;
                                        }
                                        // Converter C:/ para /C:/
                                        if (preg_match('/^\/[A-Z]:\//', $pdfPath)) {
                                            $pdfPath = preg_replace('/^\/([A-Z]):\//', '/$1:/', $pdfPath);
                                        }
                                    } else {
                                        // Linux/Unix: usar caminho absoluto normal
                                        $pdfPath = $actualPath;
                                    }
                                }
                                
                                $itemImages[$item->id]['coverImageUrl'] = $pdfPath;
                                $itemImages[$item->id]['coverImageBase64'] = false;
                                
                                \Log::info('Imagem costura usando caminho de arquivo:', [
                                    'pdf_path' => $pdfPath,
                                    'original_path' => $actualPath
                                ]);
                            }
                        } else {
                            \Log::warning('Arquivo não existe após verificar caminhos:', [
                                'original_path' => $item->cover_image,
                                'normalized_path' => $normalizedPath,
                                'public_images_path' => $publicImagesPath,
                                'public_images_exists' => file_exists($publicImagesPath)
                            ]);
                        }
                } else {
                    \Log::info('Item sem imagem de capa');
                }
            }

            // Log das imagens antes de renderizar
            foreach ($order->items as $item) {
                $imageData = $itemImages[$item->id] ?? [];
                $coverImageUrlValue = $imageData['coverImageUrl'] ?? null;
                $coverImageUrlPreview = $coverImageUrlValue ? (
                    str_starts_with($coverImageUrlValue, 'data:image') 
                        ? substr($coverImageUrlValue, 0, 50) . '...' 
                        : $coverImageUrlValue
                ) : null;
                
                \Log::info('Item antes de renderizar PDF costura:', [
                    'item_id' => $item->id,
                    'has_cover_image' => $imageData['hasCoverImage'] ?? false,
                    'cover_image_url_preview' => $coverImageUrlPreview,
                    'cover_image_url_length' => $coverImageUrlValue ? strlen($coverImageUrlValue) : 0,
                    'cover_image_base64' => $imageData['coverImageBase64'] ?? false,
                    'cover_image_path' => $item->cover_image ?? null,
                    'cover_image_info' => $imageData['coverImageInfo'] ?? null
                ]);
            }
            
            // Buscar configurações da empresa da loja do pedido
            $storeId = $order->store_id;
            if (!$storeId) {
                $mainStore = \App\Models\Store::where('is_main', true)->first();
                $storeId = $mainStore ? $mainStore->id : null;
            }
            $companySettings = \App\Models\CompanySetting::getSettings($storeId);
            
            // Processar imagem de capa do pedido (Order Cover Image)
            $orderCoverImage = [
                'hasCoverImage' => false,
                'coverImageUrl' => null,
            ];
            
            if ($order->cover_image) {
                // Lógica similar à de itens para resolver imagem do pedido
                $normalizedPath = \App\Helpers\ImageHelper::normalizePath($order->cover_image);
                $publicImagesPath = public_path('images/' . $normalizedPath);
                $actualPath = null;

                if (file_exists($publicImagesPath)) {
                    $actualPath = $publicImagesPath;
                } else {
                    $relativePath = \App\Helpers\ImageHelper::resolveRelativePath($order->cover_image, ['orders/covers', 'orders']);
                    if ($relativePath) {
                        $storagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($relativePath);
                        if (file_exists($storagePath)) {
                            $actualPath = $storagePath;
                        }
                    }
                }

                if ($actualPath && file_exists($actualPath)) {
                     // Tentar otimizar se for muito grande
                     $fileSize = filesize($actualPath);
                     if ($fileSize > 2 * 1024 * 1024) {
                         $optimizedPath = $this->optimizeImageForPDF($actualPath);
                         if ($optimizedPath && file_exists($optimizedPath)) {
                             $actualPath = $optimizedPath;
                         }
                     }
                     
                     // Converter para Base64
                     try {
                        $imageData = file_get_contents($actualPath);
                        if ($imageData) {
                            $imageBase64 = base64_encode($imageData);
                            $mimeType = mime_content_type($actualPath) ?: 'image/jpeg';
                            $orderCoverImage['coverImageUrl'] = 'data:' . $mimeType . ';base64,' . $imageBase64;
                            $orderCoverImage['hasCoverImage'] = true;
                            \Log::info('Imagem de capa do pedido processada com sucesso');
                        }
                     } catch (\Exception $e) {
                         \Log::error('Erro ao processar imagem de capa do pedido: ' . $e->getMessage());
                     }
                }
            }

            \Log::info('Iniciando renderização da view');
            $html = view('kanban.pdf.costura', compact('order', 'itemImages', 'companySettings', 'orderCoverImage'))->render();
            \Log::info('View renderizada com sucesso', ['html_length' => strlen($html)]);
            
            // Verificar se a imagem está no HTML
            $hasDataImage = strpos($html, 'data:image') !== false;
            $hasFileProtocol = strpos($html, 'file://') !== false;
            $hasImgTag = strpos($html, '<img') !== false;
            
            if ($hasDataImage) {
                \Log::info('HTML contém imagem base64');
                // Extrair um trecho do HTML onde está a imagem
                $imgPos = strpos($html, 'data:image');
                if ($imgPos !== false) {
                    $snippet = substr($html, max(0, $imgPos - 50), 200);
                    \Log::info('Trecho do HTML com imagem:', ['snippet' => $snippet]);
                }
            } elseif ($hasFileProtocol) {
                \Log::info('HTML contém caminho de arquivo');
            } else {
                \Log::warning('HTML não contém referência de imagem!', [
                    'has_img_tag' => $hasImgTag,
                    'html_length' => strlen($html)
                ]);
            }
            
            // Limpar memória antes de criar o PDF
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            \Log::info('Iniciando criação do PDF');
            
            // Configurar DomPDF diretamente
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false);
            $options->set('isHtml5ParserEnabled', false);
            $options->set('isPhpEnabled', false);
            $options->set('isJavascriptEnabled', false);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('debugLayoutLines', false);
            $options->set('debugLayoutBlocks', false);
            $options->set('debugLayoutInline', false);
            $options->set('debugLayoutPaddingBox', false);
            $options->set('fontDir', storage_path('fonts'));
            $options->set('fontCache', storage_path('fonts'));
            $options->set('tempDir', sys_get_temp_dir());
            // Configurar chroot para permitir acesso às imagens
            // No Windows, usar o diretório raiz do sistema ou não usar chroot
            if (DIRECTORY_SEPARATOR === '\\') {
                // Windows: usar o diretório raiz (C:\) para permitir acesso a todos os arquivos
                $rootPath = realpath(storage_path('app/public'));
                if ($rootPath) {
                    $options->set('chroot', $rootPath);
                }
            } else {
                // Linux/Unix: usar base_path como chroot
                $options->set('chroot', realpath(base_path()));
            }
            $options->set('logOutputFile', null);
            $options->set('defaultMediaType', 'screen');
            $options->set('isImageEnabled', true); // Habilitar imagens para mostrar capas
            $options->set('defaultPaperSize', 'a4');
            $options->set('defaultPaperOrientation', 'portrait');
            $options->set('enable_font_subsetting', true);
            $options->set('isFontSubsettingEnabled', true);
            
            $pdf = new Dompdf($options);
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            
            \Log::info('Iniciando download do PDF');
            
            $filename = "pedido_{$order->id}_costura.pdf";
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de costura', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPersonalizacao($id)
    {
        try {
            // Aumentar limite de memória para processamento de PDF
            ini_set('memory_limit', '1024M');
            
            \Log::info('Iniciando download de personalização para pedido: ' . $id);
            
            $order = Order::with(['client', 'items.sublimations.size', 'items.sublimations.location'])->findOrFail($id);
            \Log::info('Pedido carregado com sucesso', ['order_id' => $order->id, 'items_count' => $order->items->count()]);

            // Array para armazenar dados de imagem de cada item (evita conflito com accessors)
            $itemImages = [];

            // Processar imagens dos itens com otimizações para evitar problemas de memória
            foreach ($order->items as $item) {
                // Inicializar dados da imagem
                $itemImages[$item->id] = [
                    'hasCoverImage' => false,
                    'coverImageInfo' => null,
                    'coverImageUrl' => null,
                    'coverImageBase64' => false,
                ];
                
                if ($item->cover_image) {
                    // Normalizar o caminho
                    $normalizedPath = ImageHelper::normalizePath($item->cover_image);
                    
                    // Primeiro, verificar se a imagem está em public/images (novo local, sem symlink)
                    $publicImagesPath = public_path('images/' . $normalizedPath);
                    $actualPath = null;
                    
                    if (file_exists($publicImagesPath)) {
                        $actualPath = $publicImagesPath;
                        \Log::info('Imagem encontrada em public/images (personalização)', ['path' => $publicImagesPath]);
                    } else {
                        // Se não encontrou em public/images, tentar em storage/app/public (compatibilidade)
                        $relativePath = ImageHelper::resolveRelativePath($item->cover_image, [
                            'orders/covers',
                            'orders/items/covers',
                            'orders/items',
                            'orders',
                        ]);
                        
                        if ($relativePath) {
                            $storagePath = Storage::disk('public')->path($relativePath);
                            if (file_exists($storagePath)) {
                                $actualPath = $storagePath;
                                \Log::info('Imagem encontrada em storage/app/public (personalização)', ['path' => $storagePath]);
                            }
                        }
                    }
                    
                    if ($actualPath && file_exists($actualPath)) {
                        // Verificar se o arquivo não é muito grande (limite de 2MB para economizar memória)
                        $fileSize = filesize($actualPath);
                        if ($fileSize > 2 * 1024 * 1024) {
                            // Tentar criar uma versão otimizada da imagem
                            $optimizedPath = $this->optimizeImageForPDF($actualPath);
                            if ($optimizedPath && file_exists($optimizedPath)) {
                                $actualPath = $optimizedPath;
                                $fileSize = filesize($actualPath);
                                \Log::info('Imagem personalização otimizada criada', [
                                    'original' => $actualPath,
                                    'otimizada' => $optimizedPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            } else {
                                \Log::warning('Imagem personalização muito grande e não foi possível otimizar, usando original', [
                                    'path' => $actualPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            }
                        }
                            
                            $itemImages[$item->id]['hasCoverImage'] = true;
                            $itemImages[$item->id]['coverImageInfo'] = [
                                'name' => basename($actualPath),
                                'size' => $this->formatFileSize($fileSize),
                                'extension' => strtoupper(pathinfo($actualPath, PATHINFO_EXTENSION)),
                                'path' => $item->cover_image
                            ];
                            
                            // Tentar múltiplas abordagens para garantir que a imagem apareça no PDF
                            // Abordagem 1: Base64 (mais confiável)
                            try {
                                $imageData = file_get_contents($actualPath);
                                if ($imageData && strlen($imageData) > 0) {
                                    $imageBase64 = base64_encode($imageData);
                                    $mimeType = mime_content_type($actualPath) ?: 'image/jpeg';
                                    
                                    // Limitar tamanho do base64 para evitar problemas de memória
                                    if (strlen($imageBase64) < 5 * 1024 * 1024) { // 5MB
                                        // Armazenar no array separado para evitar conflito com accessors
                                        $base64DataUrl = 'data:' . $mimeType . ';base64,' . $imageBase64;
                                        $itemImages[$item->id]['coverImageUrl'] = $base64DataUrl;
                                        $itemImages[$item->id]['coverImageBase64'] = true;
                                        
                                        // Verificar se o valor foi salvo corretamente
                                        $savedValue = $itemImages[$item->id]['coverImageUrl'] ?? null;
                                        
                                        \Log::info('Imagem personalização processada (base64)!', [
                                            'original_path' => $actualPath,
                                            'size' => ($itemImages[$item->id]['coverImageInfo'] ?? [])['size'] ?? 'N/A',
                                            'base64_length' => strlen($imageBase64),
                                            'mime_type' => $mimeType,
                                            'data_url_length' => strlen($base64DataUrl),
                                            'saved_value_preview' => $savedValue ? (str_starts_with($savedValue, 'data:image') ? substr($savedValue, 0, 50) . '...' : $savedValue) : null,
                                            'saved_value_length' => $savedValue ? strlen($savedValue) : 0
                                        ]);
                                    } else {
                                        throw new \Exception('Imagem muito grande para base64');
                                    }
                                } else {
                                    throw new \Exception('Arquivo vazio ou não pode ser lido');
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Erro ao converter imagem para base64, usando caminho:', [
                                    'error' => $e->getMessage(),
                                    'path' => $actualPath
                                ]);
                                
                                // Fallback: usar caminho relativo ao chroot
                                $chrootPath = realpath(storage_path('app/public'));
                                if ($chrootPath && str_starts_with($actualPath, $chrootPath)) {
                                    // Caminho relativo ao chroot (storage/app/public)
                                    $pdfPath = str_replace($chrootPath, '', $actualPath);
                                    $pdfPath = str_replace('\\', '/', $pdfPath);
                                    $pdfPath = ltrim($pdfPath, '/\\');
                                    $pdfPath = '/' . $pdfPath;
                                } else {
                                    // Caminho absoluto (fallback)
                                    if (DIRECTORY_SEPARATOR === '\\') {
                                        // Windows: usar caminho absoluto com barras normais
                                        $pdfPath = str_replace('\\', '/', $actualPath);
                                        // Adicionar barra inicial se não tiver
                                        if (!str_starts_with($pdfPath, '/')) {
                                            $pdfPath = '/' . $pdfPath;
                                        }
                                        // Converter C:/ para /C:/
                                        if (preg_match('/^\/[A-Z]:\//', $pdfPath)) {
                                            $pdfPath = preg_replace('/^\/([A-Z]):\//', '/$1:/', $pdfPath);
                                        }
                                    } else {
                                        // Linux/Unix: usar caminho absoluto normal
                                        $pdfPath = $actualPath;
                                    }
                                }
                                
                                $itemImages[$item->id]['coverImageUrl'] = $pdfPath;
                                $itemImages[$item->id]['coverImageBase64'] = false;
                                
                                \Log::info('Imagem personalização usando caminho de arquivo:', [
                                    'pdf_path' => $pdfPath,
                                    'original_path' => $actualPath
                                ]);
                            }
                        } else {
                            \Log::warning('Arquivo não existe após verificar caminhos (personalização):', [
                                'original_path' => $item->cover_image,
                                'normalized_path' => $normalizedPath,
                                'public_images_path' => $publicImagesPath,
                                'public_images_exists' => file_exists($publicImagesPath)
                            ]);
                        }
                } else {
                    \Log::info('Item sem imagem de capa (personalização)');
                }
            }

            // Log das imagens antes de renderizar
            foreach ($order->items as $item) {
                $imageData = $itemImages[$item->id] ?? [];
                $coverImageUrlValue = $imageData['coverImageUrl'] ?? null;
                $coverImageUrlPreview = $coverImageUrlValue ? (
                    str_starts_with($coverImageUrlValue, 'data:image') 
                        ? substr($coverImageUrlValue, 0, 50) . '...' 
                        : $coverImageUrlValue
                ) : null;
                
                \Log::info('Item antes de renderizar PDF personalização:', [
                    'item_id' => $item->id,
                    'has_cover_image' => $imageData['hasCoverImage'] ?? false,
                    'cover_image_url_preview' => $coverImageUrlPreview,
                    'cover_image_url_length' => $coverImageUrlValue ? strlen($coverImageUrlValue) : 0,
                    'cover_image_base64' => $imageData['coverImageBase64'] ?? false,
                    'cover_image_path' => $item->cover_image ?? null,
                    'cover_image_info' => $imageData['coverImageInfo'] ?? null
                ]);
            }
            
            // Buscar configurações da empresa da loja do pedido
            $storeId = $order->store_id;
            if (!$storeId) {
                $mainStore = \App\Models\Store::where('is_main', true)->first();
                $storeId = $mainStore ? $mainStore->id : null;
            }
            $companySettings = \App\Models\CompanySetting::getSettings($storeId);

            // Processar imagem de capa do pedido (Order Cover Image)
            $orderCoverImage = [
                'hasCoverImage' => false,
                'coverImageUrl' => null,
            ];
            
            if ($order->cover_image) {
               try {
                   $normalizedPath = \App\Helpers\ImageHelper::normalizePath($order->cover_image);
                   $publicImagesPath = public_path('images/' . $normalizedPath);
                   $actualPath = null;
                   
                   if (file_exists($publicImagesPath)) {
                       $actualPath = $publicImagesPath;
                   } else {
                       $relativePath = \App\Helpers\ImageHelper::resolveRelativePath($order->cover_image, ['orders/covers', 'orders']);
                       if ($relativePath) {
                           $storagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($relativePath);
                           if (file_exists($storagePath)) { $actualPath = $storagePath; }
                       }
                   }

                   if ($actualPath && file_exists($actualPath)) {
                        $fileSize = filesize($actualPath);
                        if ($fileSize > 2 * 1024 * 1024) {
                            $optimizedPath = $this->optimizeImageForPDF($actualPath);
                            if ($optimizedPath && file_exists($optimizedPath)) { $actualPath = $optimizedPath; }
                        }
                        
                        $imageData = file_get_contents($actualPath);
                        if ($imageData) {
                            $imageBase64 = base64_encode($imageData);
                            $mimeType = mime_content_type($actualPath) ?: 'image/jpeg';
                            $orderCoverImage['coverImageUrl'] = 'data:' . $mimeType . ';base64,' . $imageBase64;
                            $orderCoverImage['hasCoverImage'] = true;
                        }
                   }
               } catch (\Exception $e) {
                   \Log::error('Erro ao processar imagem de capa do pedido: ' . $e->getMessage());
               }
            }
            
            \Log::info('Iniciando renderização da view de personalização');
            $html = view('kanban.pdf.personalizacao', compact('order', 'itemImages', 'companySettings', 'orderCoverImage'))->render();
            \Log::info('View de personalização renderizada com sucesso', ['html_length' => strlen($html)]);
            
            // Verificar se a imagem está no HTML
            $hasDataImage = strpos($html, 'data:image') !== false;
            $hasFileProtocol = strpos($html, 'file://') !== false;
            $hasImgTag = strpos($html, '<img') !== false;
            
            if ($hasDataImage) {
                \Log::info('HTML contém imagem base64');
                // Extrair um trecho do HTML onde está a imagem
                $imgPos = strpos($html, 'data:image');
                if ($imgPos !== false) {
                    $snippet = substr($html, max(0, $imgPos - 50), 200);
                    \Log::info('Trecho do HTML com imagem:', ['snippet' => $snippet]);
                }
            } elseif ($hasFileProtocol) {
                \Log::info('HTML contém caminho de arquivo');
            } else {
                \Log::warning('HTML não contém referência de imagem!', [
                    'has_img_tag' => $hasImgTag,
                    'html_length' => strlen($html)
                ]);
            }
            
            // Limpar memória antes de criar o PDF
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            \Log::info('Iniciando criação do PDF de personalização');
            
            // Configurar DomPDF diretamente
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false);
            $options->set('isHtml5ParserEnabled', false);
            $options->set('isPhpEnabled', false);
            $options->set('isJavascriptEnabled', false);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('debugLayoutLines', false);
            $options->set('debugLayoutBlocks', false);
            $options->set('debugLayoutInline', false);
            $options->set('debugLayoutPaddingBox', false);
            $options->set('fontDir', storage_path('fonts'));
            $options->set('fontCache', storage_path('fonts'));
            $options->set('tempDir', sys_get_temp_dir());
            // Configurar chroot para permitir acesso às imagens
            // No Windows, usar o diretório raiz do sistema ou não usar chroot
            if (DIRECTORY_SEPARATOR === '\\') {
                // Windows: usar o diretório raiz (C:\) para permitir acesso a todos os arquivos
                $rootPath = realpath(storage_path('app/public'));
                if ($rootPath) {
                    $options->set('chroot', $rootPath);
                }
            } else {
                // Linux/Unix: usar base_path como chroot
                $options->set('chroot', realpath(base_path()));
            }
            $options->set('logOutputFile', null);
            $options->set('defaultMediaType', 'screen');
            $options->set('isImageEnabled', true); // Habilitar imagens para mostrar capas
            $options->set('defaultPaperSize', 'a4');
            $options->set('defaultPaperOrientation', 'portrait');
            $options->set('enable_font_subsetting', true);
            $options->set('isFontSubsettingEnabled', true);
            
            $pdf = new Dompdf($options);
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();
            
            \Log::info('Iniciando download do PDF de personalização');
            
            $filename = "pedido_{$order->id}_personalizacao.pdf";
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de personalização', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Formatar tamanho do arquivo em formato legível
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Otimizar imagem para PDF reduzindo tamanho
     */
    private function optimizeImageForPDF($imagePath)
    {
        try {
            // Verificar se a extensão GD está disponível
            if (!extension_loaded('gd')) {
                \Log::warning('Extensão GD não disponível para otimização de imagem - retornando imagem original');
                return $imagePath; // Retornar o caminho original em vez de null
            }

            // Criar diretório temporário se não existir
            $tempDir = storage_path('app/temp/optimized');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Nome do arquivo otimizado
            $filename = basename($imagePath);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $optimizedPath = $tempDir . DIRECTORY_SEPARATOR . 'opt_' . $filename;

            // Carregar imagem original
            $image = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                default:
                    \Log::warning('Formato de imagem não suportado para otimização: ' . $extension);
                    return null;
            }

            if (!$image) {
                \Log::warning('Não foi possível carregar a imagem para otimização');
                return null;
            }

            // Obter dimensões originais
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calcular novas dimensões (máximo 800px de largura, mantendo proporção)
            $maxWidth = 800;
            $maxHeight = 600;
            
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                $newWidth = intval($originalWidth * $ratio);
                $newHeight = intval($originalHeight * $ratio);
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Criar nova imagem redimensionada
            $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preservar transparência para PNG
            if ($extension === 'png') {
                imagealphablending($optimizedImage, false);
                imagesavealpha($optimizedImage, true);
                $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                imagefill($optimizedImage, 0, 0, $transparent);
            }

            // Redimensionar imagem
            imagecopyresampled($optimizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Salvar imagem otimizada
            $success = false;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $success = imagejpeg($optimizedImage, $optimizedPath, 85); // Qualidade 85%
                    break;
                case 'png':
                    $success = imagepng($optimizedImage, $optimizedPath, 8); // Compressão 8
                    break;
            }

            // Limpar memória
            imagedestroy($image);
            imagedestroy($optimizedImage);

            if ($success && file_exists($optimizedPath)) {
                \Log::info('Imagem otimizada com sucesso', [
                    'original' => $imagePath,
                    'otimizada' => $optimizedPath,
                    'dimensões_originais' => $originalWidth . 'x' . $originalHeight,
                    'dimensões_otimizadas' => $newWidth . 'x' . $newHeight,
                    'tamanho_original' => $this->formatFileSize(filesize($imagePath)),
                    'tamanho_otimizado' => $this->formatFileSize(filesize($optimizedPath))
                ]);
                return $optimizedPath;
            } else {
                \Log::warning('Falha ao salvar imagem otimizada');
                return null;
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao otimizar imagem', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function downloadFiles($id)
    {
        $order = Order::with(['items.sublimations.files', 'items.files'])->findOrFail($id);
        
        // Coletar todos os arquivos de todas as personalizações de todos os itens
        $allFiles = collect();
        foreach ($order->items as $item) {
            // Adicionar arquivos da sublimação
            foreach ($item->sublimations as $sublimation) {
                if ($sublimation->files->isNotEmpty()) {
                    $allFiles = $allFiles->merge($sublimation->files);
                }
            }
            
            // Adicionar arquivos do item
            if ($item->files->isNotEmpty()) {
                $allFiles = $allFiles->merge($item->files);
            }
        }

        if ($allFiles->isEmpty()) {
            return back()->with('error', 'Nenhum arquivo encontrado para este pedido.');
        }

        // Se for apenas um arquivo, fazer download direto
        if ($allFiles->count() === 1) {
            $file = $allFiles->first();
            $filePath = storage_path('app/public/' . $file->file_path);
            
            if (file_exists($filePath)) {
                return response()->download($filePath, $file->file_name);
            } else {
                return back()->with('error', 'Arquivo não encontrado.');
            }
        }

        // Se forem múltiplos arquivos, criar um ZIP
        $zipFileName = "pedido_{$order->id}_arquivos_arte.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");
        
        // Criar diretório temp se não existir
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($allFiles as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->file_name);
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function addPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:dinheiro,pix,cartao,transferencia,boleto',
            'payment_date' => 'required|date',
        ]);

        $order = Order::with('client')->findOrFail($id);
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        // Verificar se o valor não excede o restante
        if ($validated['amount'] > $payment->remaining_amount) {
            return response()->json([
                'success' => false,
                'message' => 'O valor informado excede o valor restante.'
            ], 400);
        }

        // Calcular novos totais
        $currentPaid = $payment->entry_amount;
        $newEntryAmount = $currentPaid + $validated['amount'];
        $newRemainingAmount = $payment->remaining_amount - $validated['amount'];

        // Adicionar novo método de pagamento ao array
        $paymentMethods = $payment->payment_methods ?? [];
        $paymentMethods[] = [
            'id' => time() . rand(1000, 9999),
            'method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'date' => $validated['payment_date'],
        ];

        // Atualizar o pagamento existente
        $payment->update([
            'entry_amount' => $newEntryAmount,
            'remaining_amount' => $newRemainingAmount,
            'payment_methods' => $paymentMethods,
            'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
        ]);

        // Registrar no caixa como "pendente" até o pedido ser entregue
        $user = Auth::user();
        \App\Models\CashTransaction::create([
            'type' => 'entrada',
            'category' => 'Venda',
            'description' => "Pagamento Adicional do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . $order->client->name,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pendente',
            'transaction_date' => $validated['payment_date'],
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'notes' => 'Pagamento adicional registrado via Kanban',
        ]);

        // Criar log
        \App\Models\OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'action' => 'payment_added',
            'description' => "Pagamento adicional de R$ " . number_format($validated['amount'], 2, ',', '.') . " registrado via " . $validated['payment_method'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento registrado com sucesso!'
        ]);
    }
}
