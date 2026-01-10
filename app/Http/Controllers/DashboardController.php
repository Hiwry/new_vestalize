<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Store;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\StockHistory;
use App\Helpers\StoreHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    /**
     * Obtém os IDs das lojas baseado no filtro selecionado
     */
    private function getStoreIds(?int $selectedStoreId): array
    {
        if ($selectedStoreId) {
            $store = Store::with('subStores')->find($selectedStoreId);
            return $store ? $store->getAllStoreIds() : [];
        }
        
        return StoreHelper::getUserStoreIds();
    }

    /**
     * Aplica filtros de loja e vendedor na query
     */
    private function applyFilters($query, ?int $selectedStoreId): void
    {
        $storeIds = $this->getStoreIds($selectedStoreId);
        
        if (!empty($storeIds)) {
            $query->whereIn('store_id', $storeIds);
        } elseif (!$selectedStoreId) {
            StoreHelper::applyStoreFilter($query);
        }
        
        if (Auth::user()->isVendedor()) {
            $query->where('user_id', Auth::id());
        }
    }

    /**
     * Calcula o range de datas baseado no período selecionado
     */
    private function getDateRange(string $period, ?string $customStart = null, ?string $customEnd = null): array
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $previousStart = $now->copy()->subDay()->startOfDay();
                $previousEnd = $now->copy()->subDay()->endOfDay();
                break;
                
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $previousStart = $now->copy()->subWeek()->startOfWeek();
                $previousEnd = $now->copy()->subWeek()->endOfWeek();
                break;
                
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $previousStart = $now->copy()->subMonth()->startOfMonth();
                $previousEnd = $now->copy()->subMonth()->endOfMonth();
                break;
                
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $previousStart = $now->copy()->subYear()->startOfYear();
                $previousEnd = $now->copy()->subYear()->endOfYear();
                break;
                
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : $now->copy()->startOfMonth();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : $now->copy()->endOfMonth();
                $daysDiff = $start->diffInDays($end);
                $previousStart = $start->copy()->subDays($daysDiff + 1);
                $previousEnd = $start->copy()->subDay()->endOfDay();
                break;
                
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $previousStart = $now->copy()->subMonth()->startOfMonth();
                $previousEnd = $now->copy()->subMonth()->endOfMonth();
        }
        
        return [
            'start' => $start,
            'end' => $end,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants sem selecionar contexto
        if ($this->isSuperAdmin() && !$this->hasSelectedTenant()) {
            return $this->emptySuperAdminResponse('dashboard.admin-geral', [
                'period' => 'month',
                'startDate' => Carbon::now()->startOfMonth(),
                'endDate' => Carbon::now()->endOfMonth(),
                'previousStartDate' => Carbon::now()->subMonth()->startOfMonth(),
                'previousEndDate' => Carbon::now()->subMonth()->endOfMonth(),
                'totalPedidos' => 0,
                'totalFaturamento' => 0,
                'pedidosHoje' => 0,
                'variacaoPedidos' => 0,
                'variacaoFaturamento' => 0,
                'ticketMedio' => 0,
                'variacaoTicketMedio' => 0,
                'vendasPDV' => 0,
                'vendasPDVValor' => 0,
                'pedidosOnline' => 0,
                'pedidosOnlineValor' => 0,
                'totalClientes' => 0,
                'pedidosPorStatus' => collect([]),
                'faturamentoDiario' => collect([]),
                'pedidosRecentes' => collect([]),
                'topClientes' => collect([]),
                'pagamentosPendentes' => collect([]),
                'totalPendente' => 0,
                'pedidosPorMes' => collect([]),
                'faturamentoPorLoja' => collect([]),
                'distribuicaoPagamento' => collect([]),
                'topVendedores' => collect([]),
                'produtosMaisVendidos' => collect([]),
                'clientesAtendidos' => 0,
                'metas' => [
                    'faturamento' => ['valor' => 0, 'meta' => 0, 'percentual' => 0],
                    'pedidos' => ['valor' => 0, 'meta' => 0, 'percentual' => 0],
                    'ticket_medio' => ['valor' => 0, 'meta' => 0, 'percentual' => 0],
                    'novos_clientes' => ['valor' => 0, 'meta' => 0, 'percentual' => 0],
                ],
                'fluxoFinanceiro' => collect([]),
                'resumoMensal' => collect([]),
                'stores' => collect([]),
                'selectedStoreId' => null,
            ]);
        }

        // Filtros de período
        $period = $request->get('period', 'month'); // today, week, month, year, custom
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        
        // Filtro de loja (apenas para admin geral) - DEVE SER DEFINIDO ANTES DE USAR
        $selectedStoreId = null;
        if ($user->isAdminGeral() && $request->has('store_id') && $request->get('store_id')) {
            $selectedStoreId = $request->get('store_id');
        }
        
        // Calcular datas baseado no período
        $dateRange = $this->getDateRange($period, $startDateInput, $endDateInput);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Período anterior para comparação
        $previousStartDate = $dateRange['previous_start'];
        $previousEndDate = $dateRange['previous_end'];
        
        // Query para período anterior (comparação)
        $previousBaseQuery = Order::where('is_draft', false)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate]);
        $this->applyFilters($previousBaseQuery, $selectedStoreId);
        
        // Query base para pedidos
        $baseQuery = Order::where('is_draft', false)
            ->whereBetween('created_at', [$startDate, $endDate]);
        $this->applyFilters($baseQuery, $selectedStoreId);
        
        // Estatísticas gerais
        $totalPedidos = (clone $baseQuery)->count();
        
        // Faturamento: apenas pedidos totalmente pagos e aprovados pelo caixa
        $totalFaturamento = (clone $baseQuery)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->sum('total');
        
        $pedidosHoje = (clone $baseQuery)->whereDate('created_at', Carbon::today())->count();
        
        // Comparação com período anterior
        $totalPedidosAnterior = (clone $previousBaseQuery)->count();
        $totalFaturamentoAnterior = (clone $previousBaseQuery)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->sum('total');
        $variacaoPedidos = $totalPedidosAnterior > 0 
            ? (($totalPedidos - $totalPedidosAnterior) / $totalPedidosAnterior) * 100 
            : 0;
        $variacaoFaturamento = $totalFaturamentoAnterior > 0 
            ? (($totalFaturamento - $totalFaturamentoAnterior) / $totalFaturamentoAnterior) * 100 
            : 0;
        
        // Ticket médio (baseado apenas em faturamento aprovado)
        $pedidosFaturados = (clone $baseQuery)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->count();
        $ticketMedio = $pedidosFaturados > 0 ? $totalFaturamento / $pedidosFaturados : 0;
        
        $pedidosFaturadosAnterior = (clone $previousBaseQuery)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->count();
        $ticketMedioAnterior = $pedidosFaturadosAnterior > 0 ? $totalFaturamentoAnterior / $pedidosFaturadosAnterior : 0;
        $variacaoTicketMedio = $ticketMedioAnterior > 0 
            ? (($ticketMedio - $ticketMedioAnterior) / $ticketMedioAnterior) * 100 
            : 0;
        
        // Vendas PDV vs Pedidos Online (valores apenas de faturamento aprovado)
        $vendasPDV = (clone $baseQuery)->where('is_pdv', true)->count();
        $vendasPDVValor = (clone $baseQuery)
            ->where('is_pdv', true)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->sum('total');
        $pedidosOnline = (clone $baseQuery)->where('is_pdv', false)->count();
        $pedidosOnlineValor = (clone $baseQuery)
            ->where('is_pdv', false)
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->sum('total');
        
        // Aplicar filtro de loja em clientes
        $clientQuery = Client::query();
        $storeIds = $this->getStoreIds($selectedStoreId);
        if (!empty($storeIds)) {
            $clientQuery->whereIn('store_id', $storeIds);
        } elseif (!$selectedStoreId) {
            StoreHelper::applyStoreFilter($clientQuery);
        }
        $totalClientes = $clientQuery->count();
        
        // Pedidos por status (otimizado com join)
        // Nota: precisamos clonar a query base mas remover o whereBetween para aplicar manualmente
        // porque o join pode causar ambiguidade na coluna created_at
        $pedidosPorStatusQuery = Order::where('orders.is_draft', false)
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        $this->applyFilters($pedidosPorStatusQuery, $selectedStoreId);
        $pedidosPorStatus = $pedidosPorStatusQuery
            ->join('statuses', 'orders.status_id', '=', 'statuses.id')
            ->select('statuses.id', 'statuses.name', 'statuses.color', DB::raw('count(*) as total'))
            ->groupBy('statuses.id', 'statuses.name', 'statuses.color')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $item->name ?? 'Sem Status',
                    'color' => $item->color ?? '#9ca3af',
                    'total' => (int)($item->total ?? 0)
                ];
            });

        // Faturamento diário (últimos 30 dias do período selecionado) - apenas aprovados pelo caixa
        $faturamentoDiarioStart = $endDate->copy()->subDays(30);
        if ($faturamentoDiarioStart->lt($startDate)) {
            $faturamentoDiarioStart = $startDate->copy();
        }
        
        $faturamentoDiarioQuery = Order::where('orders.is_draft', false)
            ->whereBetween('orders.created_at', [$faturamentoDiarioStart, $endDate])
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            });
        $this->applyFilters($faturamentoDiarioQuery, $selectedStoreId);
        $faturamentoDiario = $faturamentoDiarioQuery
            ->select(
                DB::raw('DATE(orders.created_at) as dia'),
                DB::raw('SUM(orders.total) as total'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('dia', 'asc')
            ->get();

        // Pedidos e vendas recentes (incluindo vendas do PDV)
        $pedidosRecentes = (clone $baseQuery)
            ->with(['client', 'status', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top 5 clientes (ou 10 para admin geral) - gasto baseado apenas em faturamento aprovado
        $topClientesLimit = $user->isAdminGeral() ? 10 : 5;
        $topClientesQuery = Client::select(
                'clients.id',
                'clients.name',
                'clients.phone_primary',
                'clients.phone_secondary',
                'clients.email',
                'clients.cpf_cnpj',
                'clients.address',
                'clients.city',
                'clients.state',
                'clients.zip_code',
                'clients.category',
                'clients.created_at',
                'clients.updated_at',
                DB::raw('COUNT(orders.id) as total_pedidos'),
                DB::raw('SUM(orders.total) as total_gasto')
            )
            ->join('orders', 'clients.id', '=', 'orders.client_id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.is_draft', false)
            ->where('payments.cash_approved', true)
            ->where('payments.remaining_amount', 0)
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        
        // Aplicar filtros - NOTA: Não usar StoreHelper aqui para evitar ambiguidade de store_id
        if (!empty($storeIds)) {
            $topClientesQuery->whereIn('clients.store_id', $storeIds)
                            ->whereIn('orders.store_id', $storeIds);
        } elseif (!$selectedStoreId) {
            // Aplicar filtro de loja manualmente com prefixo de tabela
            $userStoreIds = StoreHelper::getUserStoreIds();
            if (!empty($userStoreIds)) {
                $topClientesQuery->whereIn('clients.store_id', $userStoreIds)
                                ->whereIn('orders.store_id', $userStoreIds);
            }
        }
        
        if ($user->isVendedor()) {
            $topClientesQuery->where('orders.user_id', $user->id);
        }
        
        $topClientes = $topClientesQuery
            ->groupBy(
                'clients.id',
                'clients.name',
                'clients.phone_primary',
                'clients.phone_secondary',
                'clients.email',
                'clients.cpf_cnpj',
                'clients.address',
                'clients.city',
                'clients.state',
                'clients.zip_code',
                'clients.category',
                'clients.created_at',
                'clients.updated_at'
            )
            ->orderBy('total_gasto', 'desc')
            ->limit($topClientesLimit)
            ->get();

        // Pagamentos pendentes
        $paymentStoreIds = $this->getStoreIds($selectedStoreId);
        $paymentQuery = function($query) use ($paymentStoreIds, $user) {
            $query->where('is_draft', false);
            
            if (!empty($paymentStoreIds)) {
                $query->whereIn('store_id', $paymentStoreIds);
            }
            
            if ($user->isVendedor()) {
                $query->where('user_id', $user->id);
            }
        };
        
        $pagamentosPendentes = Payment::where('remaining_amount', '>', 0)
            ->whereHas('order', $paymentQuery)
            ->with('order.client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Total de valores pendentes
        $totalPendente = Payment::where('remaining_amount', '>', 0)
            ->whereHas('order', $paymentQuery)
            ->sum('remaining_amount');

        // Pedidos por mês (últimos 12 meses) - faturamento apenas de aprovados pelo caixa
        $pedidosPorMes = Order::where('is_draft', false)
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->whereHas('payment', function($q) {
                $q->where('cash_approved', true)
                  ->where('remaining_amount', 0);
            })
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total) as faturamento')
            );
        $this->applyFilters($pedidosPorMes, $selectedStoreId);
        $pedidosPorMes = $pedidosPorMes
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('mes', 'asc')
            ->get();
        
        // Faturamento por loja (apenas para admin geral) - apenas aprovados pelo caixa
        $faturamentoPorLoja = collect();
        if ($user->isAdminGeral()) {
            $faturamentoPorLoja = Store::active()
                ->withCount(['orders' => function($query) use ($startDate, $endDate) {
                    $query->where('is_draft', false)
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->whereHas('payment', function($q) {
                              $q->where('cash_approved', true)
                                ->where('remaining_amount', 0);
                          });
                }])
                ->withSum(['orders' => function($query) use ($startDate, $endDate) {
                    $query->where('is_draft', false)
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->whereHas('payment', function($q) {
                              $q->where('cash_approved', true)
                                ->where('remaining_amount', 0);
                          });
                }], 'total')
                ->orderBy('is_main', 'desc')
                ->orderBy('name')
                ->get()
                ->map(function($store) {
                    return [
                        'name' => $store->name,
                        'total_pedidos' => $store->orders_count ?? 0,
                        'total_faturamento' => $store->orders_sum_total ?? 0
                    ];
                });
        }
        
        // Distribuição por forma de pagamento
        $paymentStoreIds = $this->getStoreIds($selectedStoreId);
        $distribuicaoPagamento = Payment::whereHas('order', function($query) use ($paymentStoreIds, $user, $startDate, $endDate, $selectedStoreId) {
            $query->where('is_draft', false)
                  ->whereBetween('created_at', [$startDate, $endDate]);
            
            if (!empty($paymentStoreIds)) {
                $query->whereIn('store_id', $paymentStoreIds);
            } elseif (!$selectedStoreId) {
                StoreHelper::applyStoreFilter($query);
            }
            
            if ($user->isVendedor()) {
                $query->where('user_id', $user->id);
            }
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get()
        ->flatMap(function($payment) {
            $methods = is_array($payment->payment_methods) 
                ? $payment->payment_methods 
                : json_decode($payment->payment_methods, true) ?? [];
            
            if (empty($methods)) {
                return collect();
            }
            
            return collect($methods)->map(function($method) {
                return [
                    'method' => $method['method'] ?? 'desconhecido',
                    'amount' => $method['amount'] ?? 0
                ];
            });
        })
        ->groupBy('method')
        ->map(function($group, $method) {
            return [
                'method' => $method,
                'total' => $group->sum('amount'),
                'count' => $group->count()
            ];
        })
        ->values();
        
        // Top vendedores (apenas para admin geral e admin loja) - faturamento apenas aprovado pelo caixa
        $topVendedores = collect();
        if (!$user->isVendedor()) {
            $topVendedoresQuery = User::select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COUNT(orders.id) as total_pedidos'),
                    DB::raw('SUM(orders.total) as total_faturamento')
                )
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->join('payments', 'orders.id', '=', 'payments.order_id')
                ->where('orders.is_draft', false)
                ->where('payments.cash_approved', true)
                ->where('payments.remaining_amount', 0)
                ->whereBetween('orders.created_at', [$startDate, $endDate]);
            
            $storeIds = $this->getStoreIds($selectedStoreId);
            if (!empty($storeIds)) {
                $topVendedoresQuery->whereIn('orders.store_id', $storeIds);
            } elseif (!$selectedStoreId) {
                StoreHelper::applyStoreFilter($topVendedoresQuery, 'orders');
            }
            
            $topVendedores = $topVendedoresQuery
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderBy('total_faturamento', 'desc')
                ->limit(10)
                ->get();
        }
        
        // Produtos mais vendidos
        $produtosMaisVendidos = OrderItem::select(
                'order_items.print_type',
                DB::raw('SUM(order_items.quantity) as total_vendido'),
                DB::raw('SUM(order_items.total_price) as total_faturamento')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.is_draft', false)
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        
        $storeIds = $this->getStoreIds($selectedStoreId);
        if (!empty($storeIds)) {
            $produtosMaisVendidos->whereIn('orders.store_id', $storeIds);
        } elseif (!$selectedStoreId) {
            StoreHelper::applyStoreFilter($produtosMaisVendidos, 'orders');
        }
        
        if ($user->isVendedor()) {
            $produtosMaisVendidos->where('orders.user_id', $user->id);
        }
        
        $produtosMaisVendidos = $produtosMaisVendidos
            ->groupBy('order_items.print_type')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();
        
        // Clientes únicos atendidos (para vendedor)
        $clientesAtendidos = 0;
        if ($user->isVendedor()) {
            $clientesAtendidos = (clone $baseQuery)
                ->whereNotNull('client_id')
                ->distinct('client_id')
                ->count('client_id');
        }

        // Lojas disponíveis para filtro
        $stores = collect();
        
        if ($user->isAdminGeral()) {
            $stores = Store::active()->orderBy('is_main', 'desc')->orderBy('name')->get();
        } elseif ($user->isAdmin() || $user->isAdminLoja()) {
            $userStoreIds = $user->getStoreIds();
            if (!empty($userStoreIds)) {
                $stores = Store::whereIn('id', $userStoreIds)
                    ->active()
                    ->orderBy('is_main', 'desc')
                    ->orderBy('name')
                    ->get();
            }
        }
        
        // Fallback para debug (apenas para admins)
        if ($stores->isEmpty() && !$user->isVendedor()) {
            $allStores = Store::active()->get();
            if ($allStores->isNotEmpty()) {
                $stores = $allStores;
            }
        }
        
        // Se for usuário de produção, redirecionar para dashboard de produção
        if ($user->isProducao()) {
            return redirect()->route('production.dashboard');
        }
        
        // Se for usuário de estoque, calcular estatísticas de estoque
        if ($user->isEstoque()) {
            return $this->stockDashboard($request);
        }
        
        // Determinar qual view usar baseado no tipo de usuário
        $viewName = 'dashboard';
        if ($user->isAdminGeral()) {
            $viewName = 'dashboard.admin-geral';
        } elseif ($user->isAdminLoja()) {
            $viewName = 'dashboard.admin-loja';
        } elseif ($user->isVendedor()) {
            $viewName = 'dashboard.vendedor';
        }
        
        return view($viewName, compact(
            'totalPedidos',
            'totalClientes',
            'totalFaturamento',
            'pedidosHoje',
            'pedidosPorStatus',
            'faturamentoDiario',
            'pedidosRecentes',
            'topClientes',
            'pagamentosPendentes',
            'totalPendente',
            'pedidosPorMes',
            'stores',
            'selectedStoreId',
            'variacaoPedidos',
            'variacaoFaturamento',
            'ticketMedio',
            'variacaoTicketMedio',
            'vendasPDV',
            'vendasPDVValor',
            'pedidosOnline',
            'pedidosOnlineValor',
            'faturamentoPorLoja',
            'distribuicaoPagamento',
            'topVendedores',
            'produtosMaisVendidos',
            'clientesAtendidos',
            'period',
            'startDate',
            'endDate',
            'previousStartDate',
            'previousEndDate'
        ));
    }

    /**
     * Dashboard específico para usuário de estoque
     */
    private function stockDashboard(Request $request)
    {
        $user = Auth::user();
        
        // Filtro de loja
        $selectedStoreId = $request->get('store_id');
        $storeQuery = Stock::query();
        
        if ($selectedStoreId) {
            $storeQuery->where('store_id', $selectedStoreId);
        }
        
        // Estatísticas gerais de estoque
        $totalItensEstoque = (clone $storeQuery)->count();
        $totalQuantidade = (clone $storeQuery)->sum('quantity');
        $totalReservado = (clone $storeQuery)->sum('reserved_quantity');
        $totalDisponivel = $totalQuantidade - $totalReservado;
        
        // Estoque baixo (abaixo do mínimo)
        $estoqueBaixo = (clone $storeQuery)
            ->whereRaw('(quantity - reserved_quantity) < min_stock')
            ->whereRaw('min_stock > 0')
            ->count();
        
        // Solicitações pendentes
        $solicitacoesPendentes = StockRequest::where('status', 'pendente')->count();
        
        // Solicitações aprovadas aguardando transferência
        $solicitacoesAprovadas = StockRequest::where('status', 'aprovado')->count();
        
        // Solicitações em transferência
        $solicitacoesEmTransferencia = StockRequest::where('status', 'em_transferencia')->count();
        
        // Total de solicitações hoje
        $solicitacoesHoje = StockRequest::whereDate('created_at', Carbon::today())->count();
        
        // Movimentações recentes (últimas 24 horas)
        $movimentacoesRecentes = StockHistory::where('action_date', '>=', Carbon::now()->subDay())
            ->orderBy('action_date', 'desc')
            ->limit(10)
            ->with(['stock.store', 'stock.fabric', 'stock.color', 'stock.cutType', 'user', 'order'])
            ->get();
        
        // Estoque por loja
        $estoquePorLoja = Stock::select(
                'stores.id',
                'stores.name',
                DB::raw('COUNT(stocks.id) as total_itens'),
                DB::raw('SUM(stocks.quantity) as total_quantidade'),
                DB::raw('SUM(stocks.reserved_quantity) as total_reservado'),
                DB::raw('SUM(stocks.quantity - stocks.reserved_quantity) as total_disponivel')
            )
            ->join('stores', 'stocks.store_id', '=', 'stores.id')
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('stores.name')
            ->get();
        
        // Solicitações por status
        $solicitacoesPorStatus = StockRequest::select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->status => $item->total];
            });
        
        // Top produtos mais solicitados (últimos 30 dias)
        $produtosMaisSolicitados = StockRequest::select(
                'fabric_id',
                'color_id',
                'cut_type_id',
                'size',
                DB::raw('SUM(requested_quantity) as total_solicitado')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('fabric_id')
            ->groupBy('fabric_id', 'color_id', 'cut_type_id', 'size')
            ->orderBy('total_solicitado', 'desc')
            ->limit(10)
            ->with(['fabric', 'color', 'cutType'])
            ->get();
        
        // Histórico de movimentações por dia (últimos 30 dias)
        $movimentacoesPorDia = StockHistory::select(
                DB::raw('DATE(action_date) as dia'),
                DB::raw('COUNT(*) as total_movimentacoes'),
                DB::raw('SUM(ABS(quantity_change)) as total_quantidade')
            )
            ->where('action_date', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(action_date)'))
            ->orderBy('dia', 'asc')
            ->get();
        
        // Solicitações recentes
        $solicitacoesRecentes = StockRequest::with([
                'order',
                'requestingStore',
                'targetStore',
                'fabric',
                'color',
                'cutType',
                'requestedBy'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Lojas disponíveis para filtro
        $stores = Store::active()->orderBy('name')->get();
        
        return view('dashboard.estoque', compact(
            'totalItensEstoque',
            'totalQuantidade',
            'totalReservado',
            'totalDisponivel',
            'estoqueBaixo',
            'solicitacoesPendentes',
            'solicitacoesAprovadas',
            'solicitacoesEmTransferencia',
            'solicitacoesHoje',
            'movimentacoesRecentes',
            'estoquePorLoja',
            'solicitacoesPorStatus',
            'produtosMaisSolicitados',
            'movimentacoesPorDia',
            'solicitacoesRecentes',
            'stores',
            'selectedStoreId'
        ));
    }

    /**
     * Exibir página de links rápidos
     */
    public function links(\Illuminate\Http\Request $request)
    {
        $category = $request->get('category', 'geral'); // geral, vendedor, estoque, caixa, admin, producao
        
        return view('links.index', compact('category'));
    }
}
