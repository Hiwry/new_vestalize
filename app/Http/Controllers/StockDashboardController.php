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
        $dateStart = Carbon::now()->subDays(30);
        $dateEnd = Carbon::now();

        // Base Query Scope
        $stockQuery = Stock::query();
        $historyQuery = StockHistory::query();
        $requestQuery = StockRequest::query();

        if (!empty($userStoreIds)) {
            $stockQuery->whereIn('store_id', $userStoreIds);
            $historyQuery->whereIn('store_id', $userStoreIds);
            $requestQuery->whereIn('target_store_id', $userStoreIds);
        }

        // 1. KPI Cards
        $totalItems = (clone $stockQuery)->sum('quantity');
        $lowStockCount = (clone $stockQuery)->whereRaw('quantity <= min_stock')->count();
        $pendingRequests = (clone $requestQuery)->where('status', 'pending')->count();
        // Estimated Value (assuming cost price exists on products, but simplistic here. 
        // If no cost available, maybe count distinct SKUs)
        $totalSKUs = (clone $stockQuery)->count();

        // 2. Charts Data
        // Stock by Store
        $stockByStore = (clone $stockQuery)
            ->join('stores', 'stocks.store_id', '=', 'stores.id')
            ->select('stores.name', DB::raw('SUM(stocks.quantity) as total'))
            ->groupBy('stores.name')
            ->pluck('total', 'name')
            ->toArray();

        // Recent Movements (Last 7 Days)
        $movementsData = $this->getMovementChartData($historyQuery);

        // 3. Lists
        // Top Low Stock Items (Priority)
        $lowStockItems = (clone $stockQuery)
            ->with(['store', 'fabric', 'cutType', 'color'])
            ->whereRaw('quantity <= min_stock')
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();

        // Recent Activity
        $recentActivity = (clone $historyQuery)
            ->with(['user', 'store', 'cutType'])
            ->orderBy('action_date', 'desc')
            ->limit(10)
            ->get();

        return view('stocks.dashboard', compact(
            'totalItems',
            'lowStockCount',
            'pendingRequests',
            'totalSKUs',
            'stockByStore',
            'movementsData',
            'lowStockItems',
            'recentActivity'
        ));
    }

    private function getMovementChartData($query)
    {
        $days = [];
        $entries = [];
        $exits = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $label = Carbon::now()->subDays($i)->format('d/m');
            $days[] = $label;

            $entries[] = (clone $query)
                ->whereDate('action_date', $date)
                ->whereIn('action_type', ['entrada', 'devolucao', 'ajuste']) // Positive flows
                ->where('quantity_change', '>', 0)
                ->sum('quantity_change');

            $exits[] = abs((clone $query)
                ->whereDate('action_date', $date)
                ->whereIn('action_type', ['saida', 'perda', 'ajuste']) // Negative flows
                ->where('quantity_change', '<', 0)
                ->sum('quantity_change'));
        }

        return [
            'labels' => $days,
            'entries' => $entries,
            'exits' => $exits
        ];
    }
}
