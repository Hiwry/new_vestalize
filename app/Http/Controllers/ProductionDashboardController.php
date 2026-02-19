<?php

namespace App\Http\Controllers;

use App\Models\OrderStatusTracking;
use App\Models\Status;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductionDashboardController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    /**
     * Dashboard de produção
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants sem selecionar contexto
        if ($this->isSuperAdmin() && !$this->hasSelectedTenant()) {
            $statuses = Status::orderBy('position')->get();
            return $this->emptySuperAdminResponse('production.dashboard', [
                'statuses' => $statuses,
                'ordersByStatus' => [],
                'statusStats' => [],
                'topProducts' => collect([]),
                'productionVolume' => collect([]),
                'totalOrders' => 0,
                'ordersInProduction' => 0,
                'efficiency' => [
                    'current' => 0,
                    'previous' => 0,
                    'variation' => 0,
                ],
                'selectedColumns' => $statuses->pluck('id')->toArray(),
                'period' => 'month',
                'startDate' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'endDate' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'lojas' => collect([]),
                'slowestStatus' => null,
                'avgProductionTime' => null,
                'deliveryOrders' => collect([]),
                'deliveryFilter' => 'today',
                'allStatuses' => $statuses,
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ]);
        }

        // Verificar se é produção ou admin
        if (!$user->isProducao() && !$user->isAdmin()) {
            abort(403, 'Acesso negado. Apenas usuários de produção podem acessar.');
        }

        $period = $request->get('period', 'month'); // day, week, month, quarter, year, custom
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        // Definir datas baseadas no período
        if ($period === 'custom' && $startDateInput && $endDateInput) {
            $start = Carbon::parse($startDateInput);
            $end = Carbon::parse($endDateInput);
        } else {
            switch ($period) {
                case 'day':
                    $start = Carbon::now()->startOfDay();
                    $end = Carbon::now()->endOfDay();
                    break;
                case 'week':
                    $start = Carbon::now()->startOfWeek();
                    $end = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                    break;
                case 'quarter':
                    $start = Carbon::now()->startOfQuarter();
                    $end = Carbon::now()->endOfQuarter();
                    break;
                case 'year':
                    $start = Carbon::now()->startOfYear();
                    $end = Carbon::now()->endOfYear();
                    break;
                default:
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
            }
        }

        // Obter todos os status (colunas do kanban)
        $allStatuses = Status::orderBy('position')->get();
        
        // Obter colunas selecionadas pelo usuário (da sessão ou request)
        // Se o formulário foi submetido, usa o que veio do request (mesmo que vazio)
        if ($request->has('filter_submitted')) {
            $selectedColumns = $request->get('columns', []);
        } else {
            // Se não foi submetido (primeiro acesso), tenta pegar da sessão ou usa os padrões
            $selectedColumns = session('production_dashboard_columns', []);
            if (empty($selectedColumns)) {
                // Colunas padrão específicas do fluxo de produção
                $defaultStatusNames = [
                    'Pendente',
                    'Quando não assina',
                    'Assinado',
                    'Inicio',
                    'Fila Corte',
                    'Cortado',
                    'Costura',
                    'Costurar Novamente',
                    'Personalização',
                    'Limpeza',
                    'Concluído'
                ];
                
                $selectedColumns = $allStatuses->filter(function($status) use ($defaultStatusNames) {
                    return in_array($status->name, $defaultStatusNames);
                })->pluck('id')->toArray();
                
                // Se nenhum status padrão encontrado, usar todos
                if (empty($selectedColumns)) {
                    $selectedColumns = $allStatuses->pluck('id')->toArray();
                }
            }
        }
        
        // Garantir que são inteiros
        $selectedColumns = array_map('intval', $selectedColumns);
        
        // Salvar na sessão
        session(['production_dashboard_columns' => $selectedColumns]);
        
        // Filtrar status baseado na seleção
        $statuses = $allStatuses->whereIn('id', $selectedColumns);

        // Restringir por lojas permitidas para o usuário
        $storeIds = $user->getStoreIds();

        $baseQuery = Order::where('orders.is_draft', false)
            ->where('orders.is_pdv', false)
            ->where('orders.is_cancelled', false);

        if (!empty($storeIds)) {
            $baseQuery = $baseQuery->whereIn('orders.store_id', $storeIds);
        }

        // Estatísticas gerais
        $totalOrders = (clone $baseQuery)
            ->whereBetween('orders.created_at', [$start, $end])
            ->count();

        $ordersInProduction = (clone $baseQuery)
            ->whereHas('status', function($q) {
                $q->where('name', '!=', 'Entregue')
                  ->where('name', '!=', 'Cancelado');
            })
            ->count();

        // Pedidos por status (colunas do kanban) - apenas as selecionadas
        $ordersByStatus = [];
        $statusStats = [];
        
        foreach ($statuses as $status) {
            // Contar pedidos no status atual
            $count = (clone $baseQuery)
                ->where('orders.status_id', $status->id)
                ->whereBetween('orders.created_at', [$start, $end])
                ->count();
            
            $ordersByStatus[$status->id] = $count;

            // Calcular tempo médio por status
            // Buscar todos os pedidos neste status (não apenas do período, para ter mais dados)
            $ordersInStatus = (clone $baseQuery)
                ->where('orders.status_id', $status->id)
                ->get();
            
            $times = [];
            
            foreach ($ordersInStatus as $order) {
                // Buscar tracking para este pedido neste status
                $entry = \App\Models\OrderStatusTracking::where('order_id', $order->id)
                    ->where('status_id', $status->id)
                    ->whereNotNull('entered_at')
                    ->orderBy('entered_at', 'desc')
                    ->first();
                
                if ($entry) {
                    if ($entry->exited_at && $entry->duration_seconds) {
                        // Já saiu do status, usar duração registrada
                        $times[] = $entry->duration_seconds;
                    } else {
                        // Ainda está no status, calcular tempo até agora
                        $seconds = $entry->entered_at->diffInSeconds(now());
                        if ($seconds > 0) {
                            $times[] = $seconds;
                        }
                    }
                } else {
                    // Se não há tracking, usar updated_at do pedido como referência
                    // Assumir que entrou no status quando o status foi atualizado
                    if ($order->updated_at) {
                        $seconds = $order->updated_at->diffInSeconds(now());
                        if ($seconds > 0) {
                            $times[] = $seconds;
                        }
                    }
                }
            }
            
            // Se temos tracking histórico completo no período, usar ele primeiro
            $trackingStats = OrderStatusTracking::getAverageTimeByStatus($status->id, $start, $end);
            
            if (!empty($trackingStats) && isset($trackingStats[0]) && $trackingStats[0]['count'] > 0) {
                $stat = $trackingStats[0];
                $statusStats[] = $stat;
            } elseif (count($times) > 0) {
                // Calcular baseado nos tempos coletados
                $avgSeconds = array_sum($times) / count($times);
                $minSeconds = min($times);
                $maxSeconds = max($times);
                
                $statusStats[] = [
                    'status_id' => $status->id,
                    'status_name' => $status->name,
                    'avg_seconds' => (int) $avgSeconds,
                    'avg_formatted' => $this->formatSeconds((int) $avgSeconds),
                    'min_seconds' => (int) $minSeconds,
                    'min_formatted' => $this->formatSeconds((int) $minSeconds),
                    'max_seconds' => (int) $maxSeconds,
                    'max_formatted' => $this->formatSeconds((int) $maxSeconds),
                    'count' => count($times),
                ];
            } else {
                // Sem dados de tempo, mas mostrar o status com contagem de pedidos
                $statusStats[] = [
                    'status_id' => $status->id,
                    'status_name' => $status->name,
                    'avg_seconds' => 0,
                    'avg_formatted' => 'Sem dados',
                    'min_seconds' => 0,
                    'min_formatted' => 'Sem dados',
                    'max_seconds' => 0,
                    'max_formatted' => 'Sem dados',
                    'count' => $count,
                ];
            }
        }

        // Identificar setor que mais está demorando (apenas com dados válidos)
        $slowestStatus = collect($statusStats)
            ->filter(function($stat) {
                return isset($stat['count']) && $stat['count'] > 0 && isset($stat['avg_seconds']) && $stat['avg_seconds'] > 0;
            })
            ->sortByDesc('avg_seconds')
            ->first();

        // Tempo médio total de produção
        // Calcular baseado no tempo total desde que o pedido foi criado até agora
        $allOrdersQuery = Order::where('is_draft', false)
            ->where('is_pdv', false)
            ->where('is_cancelled', false)
            ->whereBetween('created_at', [$start, $end]);

        if (!empty($storeIds)) {
            $allOrdersQuery->whereIn('store_id', $storeIds);
        }

        $allOrders = $allOrdersQuery->get();
        
        $allTimes = [];
        
        foreach ($allOrders as $order) {
            // Buscar primeiro tracking do pedido (quando entrou em produção)
            $firstEntry = OrderStatusTracking::where('order_id', $order->id)
                ->orderBy('entered_at', 'asc')
                ->first();
            
            if ($firstEntry) {
                // Calcular tempo total desde que entrou no primeiro status até agora
                $totalSeconds = $firstEntry->entered_at->diffInSeconds(now());
                if ($totalSeconds > 0) {
                    $allTimes[] = $totalSeconds;
                }
            } else {
                // Se não há tracking, usar created_at como referência
                if ($order->created_at) {
                    $totalSeconds = $order->created_at->diffInSeconds(now());
                    if ($totalSeconds > 0) {
                        $allTimes[] = $totalSeconds;
                    }
                }
            }
        }
        
        // Calcular média
        $avgProductionTime = !empty($allTimes) ? (array_sum($allTimes) / count($allTimes)) : null;

        // Pedidos por data de entrega para o carrossel
        $deliveryFilter = $request->get('delivery_filter', 'today'); // today, week, month, date
        $deliveryDateInput = $request->get('delivery_date');
        if (!empty($deliveryDateInput)) {
            $deliveryFilter = 'date';
        }
        
        $now = Carbon::now();
        $deliveryOrders = collect();
        
        // Buscar pedidos com delivery_date no período selecionado
        $ordersWithDate = Order::with(['client', 'status', 'items'])
            ->where('is_draft', false)
            ->where('is_pdv', false)
            ->where('is_cancelled', false)
            ->whereNotNull('delivery_date')
            ->whereIn('status_id', $selectedColumns);

        if (!empty($storeIds)) {
            $ordersWithDate->whereIn('store_id', $storeIds);
        }
        
        switch ($deliveryFilter) {
            case 'today':
                // Apenas pedidos com entrega hoje (data exata)
                $ordersWithDate->whereDate('delivery_date', Carbon::today()->toDateString());
                break;
            case 'week':
                // Pedidos de segunda a sexta-feira da semana atual (dias úteis)
                $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY); // Segunda-feira
                $endOfWeek = $now->copy()->endOfWeek(Carbon::FRIDAY); // Sexta-feira
                
                $ordersWithDate->whereBetween('delivery_date', [
                    $startOfWeek->toDateString(),
                    $endOfWeek->toDateString()
                ])
                ->whereRaw('DAYOFWEEK(delivery_date) BETWEEN 2 AND 6'); // 2 = Segunda, 6 = Sexta
                break;
            case 'month':
                $ordersWithDate->whereBetween('delivery_date', [
                    $now->copy()->startOfMonth()->toDateString(),
                    $now->copy()->endOfMonth()->toDateString()
                ]);
                break;
            case 'date':
                if (!empty($deliveryDateInput)) {
                    $selectedDate = Carbon::parse($deliveryDateInput)->toDateString();
                    $ordersWithDate->whereDate('delivery_date', $selectedDate);
                } else {
                    $ordersWithDate->whereDate('delivery_date', Carbon::today()->toDateString());
                }
                break;
        }
        
        // Aplicar o filtro e buscar apenas os pedidos do período selecionado
        $deliveryOrders = $ordersWithDate->orderBy('delivery_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        // Format dates for the view
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        return view('production.dashboard', compact(
            'statusStats',
            'slowestStatus',
            'statuses',
            'allStatuses',
            'selectedColumns',
            'totalOrders',
            'ordersInProduction',
            'ordersByStatus',
            'avgProductionTime',
            'period',
            'startDate',
            'endDate',
            'start',
            'end',
            'deliveryOrders',
            'deliveryFilter',
            'deliveryDateInput'
        ));
    }

    /**
     * Formatar segundos em string legível
     */
    private function formatSeconds(int $seconds): string
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
