<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;
use App\Models\ProductOption;
use App\Models\Order;
use App\Models\OrderLog;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\CompanySetting;

class StockRequestController extends Controller
{
    /**
     * Listar solicitações de estoque
     */
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $storeId = $request->get('store_id');

        $query = StockRequest::with([
            'order',
            'requestingStore',
            'targetStore',
            'fabric',
            'color',
            'cutType',
            'requestedBy',
            'approvedBy' // Relacionamento com usuário que aprovou
        ]);

        // Aplicar filtro de loja
        $user = Auth::user();
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            if (!empty($storeIds)) {
                $query->where(function($q) use ($storeIds) {
                    $q->whereIn('requesting_store_id', $storeIds)
                      ->orWhereIn('target_store_id', $storeIds)
                      ->orWhereNull('target_store_id'); // Mostrar solicitações para "Todas as Lojas"
                });
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($storeId) {
            $query->where(function($q) use ($storeId) {
                $q->where('requesting_store_id', $storeId)
                  ->orWhere('target_store_id', $storeId);
            });
        }

        // Ordenar: por data de criação (mais recentes primeiro), independente se tem pedido ou não
        $allRequests = $query->orderBy('created_at', 'desc')
            ->get();
        
        // Agrupar solicitações por pedido e especificações (tecido, cor, corte)
        $groupedRequests = [];
        foreach ($allRequests as $stockRequest) {
            // Criar chave única: order_id + fabric_id + color_id + cut_type_id + status
            $groupKey = sprintf(
                '%s_%s_%s_%s_%s',
                $stockRequest->order_id ?? 'sem_pedido_' . $stockRequest->id,
                $stockRequest->fabric_id ?? '0',
                $stockRequest->color_id ?? '0',
                $stockRequest->cut_type_id ?? '0',
                $stockRequest->status
            );
            
            if (!isset($groupedRequests[$groupKey])) {
                $order = $stockRequest->order;
                // Verificar se é PDV: só é PDV se o pedido existir E tiver is_pdv = true
                $isPdv = false;
                if ($order && isset($order->is_pdv)) {
                    $isPdv = (bool) $order->is_pdv;
                }
                
                $groupedRequests[$groupKey] = [
                    'order_id' => $stockRequest->order_id,
                    'order' => $order,
                    'is_pdv' => $isPdv, // Identificar se é venda PDV ou pedido
                    'fabric' => $stockRequest->fabric,
                    'color' => $stockRequest->color,
                    'cut_type' => $stockRequest->cutType,
                    'requesting_store' => $stockRequest->requestingStore,
                    'target_store' => $stockRequest->targetStore,
                    'status' => $stockRequest->status,
                    'created_at' => $stockRequest->created_at,
                    'approved_by' => $stockRequest->approvedBy, // Usuário que aprovou
                    'approved_at' => $stockRequest->approved_at, // Data/hora da aprovação
                    'request_notes' => $stockRequest->request_notes, // Observações da solicitação
                    'requests' => [],
                    'sizes_summary' => [], // Para armazenar resumo: ['P' => 5, 'M' => 2, 'G' => 4]
                ];
            }
            
            $groupedRequests[$groupKey]['requests'][] = $stockRequest;
            
            // Agrupar tamanhos e quantidades
            $size = $stockRequest->size;
            if (!isset($groupedRequests[$groupKey]['sizes_summary'][$size])) {
                $groupedRequests[$groupKey]['sizes_summary'][$size] = 0;
            }
            $groupedRequests[$groupKey]['sizes_summary'][$size] += $stockRequest->requested_quantity;
        }
        
        // Converter para array e paginar
        $groupedArray = array_values($groupedRequests);
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedGroups = array_slice($groupedArray, $offset, $perPage);
        
        // Criar paginator manual
        $requests = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedGroups,
            count($groupedArray),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stores = StoreHelper::getAvailableStores();
        $statuses = ['pendente', 'aprovado', 'rejeitado', 'em_transferencia', 'concluido', 'cancelado'];
        
        // Dados para os modais de criação de solicitações
        $fabrics = ProductOption::where('type', 'tecido')->where('active', true)->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->where('active', true)->orderBy('name')->get();
        $cutTypes = ProductOption::where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];

        $normalizeOptionValue = static function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            $normalized = trim((string) $value);
            if ($normalized === '') {
                return null;
            }

            $normalized = Str::ascii($normalized);
            $normalized = preg_replace('/[^A-Za-z0-9]+/', ' ', $normalized) ?? '';
            $normalized = strtolower(trim($normalized));

            return $normalized !== '' ? $normalized : null;
        };

        $buildOptionRecords = static function ($options) use ($normalizeOptionValue): array {
            return $options
                ->map(function ($option) use ($normalizeOptionValue) {
                    return [
                        'id' => (int) $option->id,
                        'normalized_name' => $normalizeOptionValue((string) $option->name),
                    ];
                })
                ->values()
                ->all();
        };

        $fabricRecords = $buildOptionRecords($fabrics);
        $colorRecords = $buildOptionRecords($colors);
        $cutTypeRecords = $buildOptionRecords($cutTypes);

        $resolveOptionId = static function (?string $rawValue, array $optionRecords) use ($normalizeOptionValue): ?int {
            if ($rawValue === null) {
                return null;
            }

            $value = trim((string) $rawValue);
            if ($value === '') {
                return null;
            }

            $candidates = [$value];
            if (str_contains($value, '-')) {
                $parts = array_values(array_filter(array_map('trim', explode('-', $value)), static fn ($part) => $part !== ''));
                $candidates = array_merge($candidates, $parts);

                if (count($parts) > 1) {
                    $candidates[] = implode(' ', array_slice($parts, 1));
                }
            }

            $candidates = array_values(array_unique($candidates));

            foreach ($candidates as $candidate) {
                if (!preg_match('/^\d+$/', $candidate)) {
                    continue;
                }

                $candidateId = (int) $candidate;
                foreach ($optionRecords as $optionRecord) {
                    if ($optionRecord['id'] === $candidateId) {
                        return $candidateId;
                    }
                }
            }

            $normalizedCandidates = array_values(array_filter(array_unique(array_map($normalizeOptionValue, $candidates))));
            if (empty($normalizedCandidates)) {
                return null;
            }

            foreach ($normalizedCandidates as $candidateNormalized) {
                foreach ($optionRecords as $optionRecord) {
                    if ($optionRecord['normalized_name'] === $candidateNormalized) {
                        return $optionRecord['id'];
                    }
                }
            }

            $bestScore = -1;
            $bestId = null;

            foreach ($normalizedCandidates as $candidateNormalized) {
                foreach ($optionRecords as $optionRecord) {
                    $optionNormalized = $optionRecord['normalized_name'];
                    if (!$optionNormalized) {
                        continue;
                    }

                    $score = -1;
                    if (str_contains($optionNormalized, $candidateNormalized)) {
                        $score = strlen($candidateNormalized) + 100;
                    } elseif (
                        strlen($optionNormalized) >= 3
                        && str_contains($candidateNormalized, $optionNormalized)
                    ) {
                        $score = strlen($optionNormalized) + 40;
                    }

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestId = $optionRecord['id'];
                    }
                }
            }

            return $bestScore >= 0 ? $bestId : null;
        };
        
        // Pedidos recentes para solicitações vinculadas a pedidos (excluir vendas PDV)
        $recentOrders = Order::where(function($query) {
                $query->where('is_pdv', false)
                      ->orWhereNull('is_pdv');
            })
            ->with(['items' => function ($query) {
                $query->select(['id', 'order_id', 'fabric', 'color', 'model'])
                    ->orderBy('id');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'client_id', 'created_at', 'store_id']);

        $recentOrders->transform(function ($order) use ($resolveOptionId, $fabricRecords, $colorRecords, $cutTypeRecords) {
            $preferredItem = $order->items->first(function ($item) {
                $fabric = trim((string) $item->getRawOriginal('fabric'));
                $color = trim((string) $item->getRawOriginal('color'));
                $model = trim((string) $item->getRawOriginal('model'));

                return $fabric !== '' || $color !== '' || $model !== '';
            }) ?? $order->items->first();

            $normalize = function (?string $value): ?string {
                if ($value === null) {
                    return null;
                }

                $trimmed = trim($value);
                if ($trimmed === '') {
                    return null;
                }

                return $trimmed;
            };

            $rawFabric = $preferredItem ? $normalize((string) $preferredItem->getRawOriginal('fabric')) : null;
            $rawColor = $preferredItem ? $normalize((string) $preferredItem->getRawOriginal('color')) : null;
            $rawCutType = $preferredItem ? $normalize((string) $preferredItem->getRawOriginal('model')) : null;

            $order->default_store_id = $order->store_id;
            $order->default_fabric_id = $resolveOptionId($rawFabric, $fabricRecords);
            $order->default_color_id = $resolveOptionId($rawColor, $colorRecords);
            $order->default_cut_type_id = $resolveOptionId($rawCutType, $cutTypeRecords);

            // Mantem fallback textual para casos em que o cadastro nao tenha correspondencia.
            $order->default_fabric = $rawFabric;
            $order->default_color = $rawColor;
            $order->default_cut_type = $rawCutType;

            // Remover relação para reduzir payload no view
            $order->unsetRelation('items');

            return $order;
        });

        return view('stock-requests.index', compact('requests', 'stores', 'statuses', 'status', 'storeId', 'fabrics', 'colors', 'cutTypes', 'sizes', 'recentOrders'));
    }

    /**
     * Criar solicitação de estoque
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'requesting_store_id' => 'required|exists:stores,id',
            'target_store_id' => 'nullable|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'size' => 'required|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'requested_quantity' => 'required|integer|min:1',
            'request_notes' => 'nullable|string|max:1000',
        ]);

        // Verificar permissão
        if (!StoreHelper::canAccessStore($validated['requesting_store_id'])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar esta loja.'
                ], 403);
            }

            return redirect()->back()->with('error', 'Você não tem permissão para acessar esta loja.');
        }

        $stockRequest = StockRequest::create([
            ...$validated,
            'requested_by' => Auth::id(),
            'status' => 'pendente',
        ]);

        // Se for uma retirada (decremento), tentar reservar o estoque imediatamente
        if (str_contains($stockRequest->request_notes ?? '', '[RETIRADA]')) {
            try {
                $stock = Stock::findByParams(
                    $stockRequest->requesting_store_id,
                    $stockRequest->fabric_id,
                    null,
                    $stockRequest->color_id,
                    $stockRequest->cut_type_id,
                    $stockRequest->size
                );

                if ($stock && $stock->hasStock($stockRequest->requested_quantity)) {
                    $stock->reserve(
                        $stockRequest->requested_quantity, 
                        Auth::id(), 
                        null, 
                        $stockRequest->id, 
                        'Reserva automática para retirada'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Erro ao reservar estoque para retirada', [
                    'error' => $e->getMessage(),
                    'request_id' => $stockRequest->id
                ]);
            }
        }
        
        // Atualizar status de separação do pedido, se houver
        if ($stockRequest->order_id) {
            $order = Order::find($stockRequest->order_id);
            if ($order && $order->stock_separation_status !== 'in_separation') {
                OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'Sistema',
                    'action' => 'ESTOQUE_SEPARACAO',
                    'description' => 'Estoque entrou em separação',
                ]);
            }

            $this->refreshOrderStockSeparationStatus($stockRequest->order_id);
        }
        
        // Notificar lojas
        if ($stockRequest->target_store_id) {
            Notification::createStockRequestCreated($stockRequest->target_store_id, $stockRequest);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Solicitação criada com sucesso!']);
        }

        return redirect()->route('stock-requests.index')
            ->with('success', 'Solicitação de estoque criada com sucesso.');
    }

    /**
     * Aprovar solicitação
     */
    public function approve(Request $request, $id)
    {
        try {
            $stockRequest = StockRequest::with('order')->findOrFail($id);
            \Log::info('Tentando aprovar solicitaçao', ['id' => $id, 'status__atual' => $stockRequest->status, 'target_store' => $stockRequest->target_store_id]);

            $user = Auth::user();

            if ($stockRequest->status !== 'pendente') {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Esta solicitação não está pendente.'], 400);
                }
                return redirect()->back()->with('error', 'Esta solicitação não está pendente.');
            }

            // Validar dados de aprovação
            $validated = $request->validate([
                'fulfilling_store_id' => 'nullable|exists:stores,id',
                'approved_quantity' => 'nullable|integer|min:1',
                'approval_notes' => 'nullable|string|max:1000',
            ]);

            // Se for transferência Avulsa (broadcast), PRECISA selecionar a loja de origem
            // MAS para vendas PDV onde a solicitação é para a própria loja, usar a loja solicitante como padrão
            $storeId = $validated['fulfilling_store_id'] ?? $stockRequest->target_store_id;
            
            // Se ainda não tem store_id, usar a loja solicitante (PDV)
            if (!$storeId) {
                $storeId = $stockRequest->requesting_store_id;
            }
            
            // Se AINDA não tem (cenário extremo), retornar erro
            if (!$storeId) {
                 if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Não foi possível determinar a loja de origem.'], 422);
                }
                return redirect()->back()->with('error', 'Não foi possível determinar a loja de origem.');
            }
            
            if (!StoreHelper::canAccessStore($storeId)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Você não tem permissão na loja de origem.'], 403);
                }
                return redirect()->back()->with('error', 'Você não tem permissão na loja de origem.');
            }

            $approvedQuantity = $validated['approved_quantity'] ?? $stockRequest->requested_quantity;
            $itemsToApprove = $request->get('items'); // Array de ['id' => qty]

            DB::beginTransaction();
            
            // Se o usuário enviou itens específicos (novo modal), aprovar apenas esses
            if (is_array($itemsToApprove) && !empty($itemsToApprove)) {
                $requests = StockRequest::whereIn('id', array_keys($itemsToApprove))
                    ->where('status', 'pendente')
                    ->get();
            } else {
                // Lógica antiga/legada: Agrupar solicitações pendentes do mesmo pedido
                if ($stockRequest->order_id) {
                    $requests = StockRequest::where('order_id', $stockRequest->order_id)
                        ->where('status', 'pendente')
                        ->get();
                } else {
                    $requests = collect([$stockRequest]);
                }
            }

            // Lógica de Distribuição Inteligente (apenas para o fluxo antigo sem itens específicos)
            $batchTotalRequested = $requests->sum('requested_quantity');
            $distributeQuantity = false;
            
            if (!is_array($itemsToApprove) && $requests->count() > 1 && $approvedQuantity == $batchTotalRequested) {
                 $distributeQuantity = true;
            }

            foreach ($requests as $req) {
                if ($req->target_store_id === null && $storeId) {
                    $req->target_store_id = $storeId;
                }
                
                $stock = Stock::findByParams(
                    $req->target_store_id,
                    $req->fabric_id,
                    $req->fabric_type_id ?? null,
                    $req->color_id,
                    $req->cut_type_id,
                    $req->size
                );

                // Quantidade a aprovar para este item
                if (is_array($itemsToApprove)) {
                    $qtyToApprove = (int) ($itemsToApprove[$req->id] ?? 0);
                    if ($qtyToApprove <= 0) continue; // Pular se não informou quantidade ou é 0
                } elseif ($distributeQuantity) {
                    $qtyToApprove = $req->requested_quantity;
                } else {
                    $qtyToApprove = ($req->id === $stockRequest->id) ? $approvedQuantity : $req->requested_quantity;
                }

                if (!$stock || !$stock->hasStock($qtyToApprove)) {
                    // SE for o item principal (que o usuário clicou), lançar erro com detalhes
                    if ($req->id === $stockRequest->id || is_array($itemsToApprove)) {
                        $available = $stock ? $stock->available_quantity : 0;
                        throw new \Exception("Estoque insuficiente para {$req->size}. Disponível: {$available}. Solicitado: {$qtyToApprove}.");
                    } else {
                        // SE for um item irmão (Batch), apenas pular
                        continue; 
                    }
                }

                $success = $stock->use(
                    $qtyToApprove,
                    $user->id,
                    $req->order_id,
                    $req->id,
                    "Aprovação de solicitação #{$req->id}"
                );

                if (!$success) {
                    if ($req->id === $stockRequest->id || is_array($itemsToApprove)) {
                         throw new \Exception("Erro ao deduzir estoque para {$req->size}.");
                    }
                    continue;
                }

                $req->update([
                    'status' => 'aprovado',
                    'approved_quantity' => $qtyToApprove,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'approval_notes' => $validated['approval_notes'] ?? null,
                ]);

                $productInfo = sprintf('%s - %s', $req->fabric->name ?? 'Tecido', $req->color->name ?? 'Cor');
                Notification::createStockRequestApproved(
                    $req->requested_by,
                    $req->id,
                    $user->name,
                    $productInfo,
                    $qtyToApprove
                );
            }
            
             if ($stockRequest->order_id) {
                 $order = Order::find($stockRequest->order_id);
                if ($order) {
                     $pendingCount = StockRequest::where('order_id', $order->id)
                        ->where('status', 'pendente')
                        ->count();
                    $order->update([
                        'stock_status' => $pendingCount > 0 ? 'partial' : 'total',
                    ]);
                    $this->refreshOrderStockSeparationStatus($order->id);

                    OrderLog::create([
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                        'user_name' => Auth::user()->name ?? 'Sistema',
                        'action' => 'ESTOQUE_APROVADO',
                        'description' => sprintf(
                            'Aprovação em lote: %s solicitação(ões) do Pedido #%s',
                            $requests->count(),
                            str_pad($order->id, 6, '0', STR_PAD_LEFT)
                        ),
                    ]);
                }
            }

            DB::commit();


            // Gerar URL para o comprovante
            $receiptUrl = route('stock-requests.receipt', ['id' => $stockRequest->id]);

            $successMsg = 'Solicitação aprovada com sucesso!';
            // Tentar identificar se houve skipped
            $approvedCount = StockRequest::whereIn('id', $requests->pluck('id'))
                                       ->where('status', 'aprovado')
                                       ->count();
            if ($approvedCount < $requests->count()) {
                $successMsg = "Solicitação processada. alguns itens do pedido podem ter ficado pendentes por falta de estoque.";
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => $successMsg,
                    'pdf_url' => $receiptUrl
                ]);
            }

            return redirect()->back()->with('success', $successMsg . ' <a href="'.$receiptUrl.'" target="_blank" class="underline font-bold">Imprimir Comprovante</a>');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
             $errors = $e->errors();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao aprovar solicitação de estoque', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $id,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao aprovar solicitação: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao processar aprovação: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar solicitação
     */
    public function reject(Request $request, $id) 
    {
        try {
            $stockRequest = StockRequest::findOrFail($id);
            
            if ($stockRequest->status !== 'pendente') {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Solicitação não está pendente.'], 400);
                }
                return redirect()->back()->with('error', 'Solicitação não está pendente.');
            }
            
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000'
            ]);
            
            $stockRequest->update([
                'status' => 'rejeitado',
                'rejection_reason' => $validated['rejection_reason'],
                'approved_at' => now(), // rejected at
                'approved_by' => Auth::id() // rejected by
            ]);

            // Liberar reserva de estoque se existir
            $stock = Stock::findByParams(
                $stockRequest->target_store_id ?? $stockRequest->requesting_store_id,
                $stockRequest->fabric_id,
                null,
                $stockRequest->color_id,
                $stockRequest->cut_type_id,
                $stockRequest->size
            );

            if ($stock && $stock->reserved_quantity > 0) {
                $stock->release($stockRequest->requested_quantity, Auth::id(), $stockRequest->order_id, $stockRequest->id, 'Solicitação rejeitada - Liberação de reserva');
            }

            $this->refreshOrderStockSeparationStatus($stockRequest->order_id);
             
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Solicitação rejeitada com sucesso.']);
            }
            return redirect()->back()->with('success', 'Solicitação rejeitada.');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao rejeitar solicitação: ' . $e->getMessage());
             if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao rejeitar: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao rejeitar: ' . $e->getMessage());
        }
    }

    /**
     * Concluir solicitação (recebimento)
     */
    public function complete(Request $request, $id) 
    {
        try {
            $stockRequest = StockRequest::findOrFail($id);
            
            // Lógica simplificada de conclusão
            $stockRequest->update([
                'status' => 'concluido',
                'updated_at' => now()
            ]);

            $this->refreshOrderStockSeparationStatus($stockRequest->order_id);
             
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Solicitação concluída.']);
            }
            return redirect()->back()->with('success', 'Solicitação concluída.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao concluir: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao concluir.' . $e->getMessage());
        }
    }

    /**
     * Recalcula o status de separação do pedido com base nas solicitações de estoque.
     */
    private function refreshOrderStockSeparationStatus(?int $orderId): void
    {
        if (!$orderId) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        $requestsQuery = StockRequest::where('order_id', $orderId);

        $hasRequests = (clone $requestsQuery)->exists();
        $pendingCount = (clone $requestsQuery)->where('status', 'pendente')->count();
        $hasSeparatedItems = (clone $requestsQuery)
            ->whereIn('status', ['aprovado', 'em_transferencia', 'concluido'])
            ->exists();

        if ($pendingCount > 0) {
            $newStatus = 'in_separation';
        } elseif ($hasSeparatedItems) {
            $newStatus = 'completed';
        } elseif ($hasRequests) {
            $newStatus = 'not_required';
        } else {
            $newStatus = 'pending';
        }

        if ($order->stock_separation_status !== $newStatus) {
            $order->stock_separation_status = $newStatus;
            $order->save();
        }
    }

    /**
     * Gerar comprovante de separação em PDF
     */
    public function generateReceipt($id)
    {
        $stockRequest = StockRequest::with(['order.client', 'approvedBy', 'targetStore', 'requestingStore', 'fabric', 'color', 'cutType'])->findOrFail($id);
        
        // Se não estiver aprovado, não gera comprovante
        if ($stockRequest->status !== 'aprovado' && $stockRequest->status !== 'concluido' && $stockRequest->status !== 'em_transferencia') {
            return redirect()->back()->with('error', 'Esta solicitação não está aprovada.');
        }

        // Determinar o grupo de aprovação (Batch)
        $query = StockRequest::with(['fabric', 'color', 'cutType'])
            ->where('approved_by', $stockRequest->approved_by)
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [
                $stockRequest->approved_at->copy()->subSeconds(30), 
                $stockRequest->approved_at->copy()->addSeconds(30)
            ]);

        // Se tiver pedido vinculado, agrupa pelo pedido
        if ($stockRequest->order_id) {
            $query->where('order_id', $stockRequest->order_id);
        } else {
            // Se não tiver pedido (transferência avulsa), agrupa por origem/destino e quem pediu
            $query->whereNull('order_id')
                  ->where('requesting_store_id', $stockRequest->requesting_store_id)
                  ->where('requested_by', $stockRequest->requested_by);
        }
        
        $items = $query->get();
        if ($items->isEmpty()) {
            $items = collect([$stockRequest]);
        }
        
        // Dados para o PDF
        $order = $stockRequest->order;
        $approver = $stockRequest->approvedBy;
        $store = $stockRequest->targetStore; // Quem forneceu o estoque (Origem da saída)
        $targetStore = $stockRequest->requestingStore; // Quem pediu (Destino)
        
        $totalQuantity = $items->sum('approved_quantity');
        $notes = $items->first(fn($i) => !empty($i->approval_notes))->approval_notes ?? null;

        $storeId = $store ? $store->id : null;
        if (!$storeId) {
             $mainStore = Store::where('is_main', true)->first();
             $storeId = $mainStore ? $mainStore->id : null;
        }
        $companySettings = CompanySetting::getSettings($storeId);

        try {
            // Gerar HTML
            $html = view('stock-requests.pdf.receipt', compact(
                'items', 'order', 'approver', 'store', 'targetStore', 'totalQuantity', 'companySettings', 'notes'
            ))->render();
            
            // Configurar DomPDF
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isImageEnabled', true);
            $options->set('chroot', public_path());
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $filename = 'Comprovante_Separacao_' . ($order ? 'Pedido_'.$order->id : 'Transferencia_'.$stockRequest->id) . '.pdf';
            
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de separação: ' . $e->getMessage());
            return response('Erro ao gerar PDF: ' . $e->getMessage(), 500);
        }
    }
}
