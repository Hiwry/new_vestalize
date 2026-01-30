<?php

namespace App\Http\Controllers;

use App\Helpers\StoreHelper;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\StockRequest;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockDashboardController extends Controller
{
    /**
     * Display the stock dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (!$user || (!$user->isAdminGeral() && !$user->isEstoque())) {
            abort(403, 'Acesso negado. Apenas admin geral ou estoque podem acessar o dashboard de estoque.');
        }

        $userStoreIds = StoreHelper::getUserStoreIds();
        
        // Filtros Globais
        $selectedStoreId = $request->get('store_id');
        
        // Queries Base
        $stockQuery = Stock::query();
        $requestQuery = StockRequest::query();
        $historyQuery = StockHistory::query();

        // Aplicar restrição de lojas do usuário
        if (!empty($userStoreIds)) {
            $stockQuery->whereIn('store_id', $userStoreIds);
            $requestQuery->where(function($q) use ($userStoreIds) {
                $q->whereIn('requesting_store_id', $userStoreIds)
                  ->orWhereIn('target_store_id', $userStoreIds);
            });
            $historyQuery->whereIn('store_id', $userStoreIds);
        }

        // Aplicar filtro de loja selecionada
        if ($selectedStoreId) {
            $stockQuery->where('store_id', $selectedStoreId);
            $requestQuery->where(function($q) use ($selectedStoreId) {
                $q->where('requesting_store_id', $selectedStoreId)
                  ->orWhere('target_store_id', $selectedStoreId);
            });
            $historyQuery->where('store_id', $selectedStoreId);
        }

        // --- Cards Principais ---
        
        // Total de Itens (Quantidade bruta total de peças)
        $totalQuantidade = (clone $stockQuery)->sum('quantity');
        
        // Total de Itens em Estoque (Quantidade de registros/SKUs)
        $totalItensEstoque = (clone $stockQuery)->count();
        
        // Total Reservado
        $totalReservado = (clone $stockQuery)->sum('reserved_quantity');
        
        // Total Disponível (Quantidade - Reservado)
        // Como o cálculo é feito registro a registro, fazemos via PHP ou SUM(quantity - reserved_quantity)
        $totalDisponivel = (clone $stockQuery)->selectRaw('SUM(quantity - reserved_quantity) as total')->value('total') ?? 0;
        
        // Estoque Baixo
        $estoqueBaixo = (clone $stockQuery)->whereRaw('quantity <= min_stock')->count();
        
        // --- Solicitações ---
        $solicitacoesPendentes = (clone $requestQuery)->where('status', 'pendente')->count();
        $solicitacoesHoje = (clone $requestQuery)->whereDate('created_at', Carbon::today())->count();
        $solicitacoesAprovadas = (clone $requestQuery)->where('status', 'aprovado')->count();
        $solicitacoesEmTransferencia = (clone $requestQuery)->where('status', 'em_transferencia')->count();

        // --- Listas e Tabelas ---

        // Estoque por Loja
        // Se usuário filtrou por uma loja específica, só mostra ela. Se não, mostra todas (respeitando permissões)
        $storesQuery = Store::query();
        if (!empty($userStoreIds)) {
            $storesQuery->whereIn('id', $userStoreIds);
        }
        $stores = $storesQuery->get();
        
        $estoquePorLoja = $stores->map(function($store) use ($stockQuery) {
            // Nota: Se houver filtro de loja global selecionado, a query $stockQuery já está filtrada.
            // Aqui queremos mostrar status geral das lojas, então idealmente seria uma query fresca ou adaptada.
            // Para "Ver todas as lojas" fazer sentido mesmo com filtro aplicado, talvez devêssemos usar uma query separada.
            // Mas seguindo a lógica do filtro: se filtrou loja X, a tabela mostra apenas X ou dados filtrados.
            // Vamos fazer uma query específica por loja para garantir dados corretos de cada linha.
            
            $storeStock = Stock::where('store_id', $store->id)->get();
            
            return (object) [
                'name' => $store->name,
                'total_itens' => $storeStock->count(),
                'total_quantidade' => $storeStock->sum('quantity'),
                'total_disponivel' => $storeStock->sum('available_quantity')
            ];
        });

        // Se houver filtro de loja, filtramos a lista visual também (opcional, mas faz sentido na UI)
        if ($selectedStoreId) {
            $estoquePorLoja = $estoquePorLoja->filter(function($item) use ($selectedStoreId, $stores) {
                $store = $stores->firstWhere('name', $item->name);
                return $store && $store->id == $selectedStoreId;
            });
        }

        // Produtos Mais Solicitados (últimos 30 dias)
        $produtosMaisSolicitados = (clone $requestQuery)
            ->select(
                'fabric_id',
                'fabric_type_id',
                'color_id',
                'cut_type_id',
                'size',
                DB::raw('SUM(requested_quantity) as total_solicitado')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('fabric_id', 'fabric_type_id', 'color_id', 'cut_type_id', 'size')
            ->orderByDesc('total_solicitado')
            ->limit(5)
            ->get();

        $optionIds = $produtosMaisSolicitados
            ->flatMap(fn($item) => [
                $item->fabric_id,
                $item->fabric_type_id,
                $item->color_id,
                $item->cut_type_id,
            ])
            ->filter()
            ->unique()
            ->values();

        $optionsMap = \App\Models\ProductOption::whereIn('id', $optionIds)
            ->get()
            ->keyBy('id');

        $produtosMaisSolicitados = $produtosMaisSolicitados->map(function ($item) use ($optionsMap) {
            return (object) [
                'fabric' => $optionsMap->get($item->fabric_id),
                'fabricType' => $optionsMap->get($item->fabric_type_id),
                'color' => $optionsMap->get($item->color_id),
                'cutType' => $optionsMap->get($item->cut_type_id),
                'size' => $item->size,
                'total_solicitado' => $item->total_solicitado,
            ];
        });

        $solicitacoesRecentes = (clone $requestQuery)
            ->with(['requestingStore', 'targetStore', 'fabric', 'color', 'cutType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Movimentações Recentes (últimas 24h ou últimas 10 gerais)
        // A view diz "Movimentações Recentes (24h)" mas mostra lista. Vamos pegar as últimas gerais para garantir dados.
        $movimentacoesRecentes = (clone $historyQuery)
            ->with(['stock.fabric', 'stock.color', 'stock.cutType', 'user'])
            ->orderBy('action_date', 'desc')
            ->limit(10)
            ->get();

        // --- Gráficos ---

        // Solicitações por Status (Geral)
        $solicitacoesPorStatusQuery = StockRequest::query();
        if (!empty($userStoreIds)) {
            $solicitacoesPorStatusQuery->where(function($q) use ($userStoreIds) {
                $q->whereIn('requesting_store_id', $userStoreIds)
                  ->orWhereIn('target_store_id', $userStoreIds);
            });
        }
        if ($selectedStoreId) {
             $solicitacoesPorStatusQuery->where(function($q) use ($selectedStoreId) {
                $q->where('requesting_store_id', $selectedStoreId)
                  ->orWhere('target_store_id', $selectedStoreId);
            });
        }
        
        $solicitacoesPorStatus = $solicitacoesPorStatusQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Movimentações por Dia (Últimos 30 dias)
        $movimentacoesPorDia = (clone $historyQuery)
            ->select(DB::raw('DATE(action_date) as dia'), DB::raw('count(*) as total_movimentacoes'))
            ->where('action_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return view('dashboard.estoque', compact(
            'stores',
            'selectedStoreId',
            'totalItensEstoque',
            'totalQuantidade',
            'totalReservado',
            'totalDisponivel',
            'estoqueBaixo',
            'solicitacoesPendentes',
            'solicitacoesHoje',
            'solicitacoesAprovadas',
            'solicitacoesEmTransferencia',
            'estoquePorLoja',
            'produtosMaisSolicitados',
            'solicitacoesRecentes',
            'movimentacoesRecentes',
            'solicitacoesPorStatus',
            'movimentacoesPorDia'
        ));
    }
}
