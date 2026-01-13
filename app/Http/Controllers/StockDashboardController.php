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
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            return view('stocks.dashboard', [
                'totalItems' => 0,
                'lowStockCount' => 0,
                'pendingRequests' => 0,
                'totalSKUs' => 0,
                'stockByStore' => [],
                'movementsData' => [
                    'labels' => [],
                    'entries' => [],
                    'exits' => [],
                ],
                'lowStockItems' => collect([]),
                'recentActivity' => collect([]),
                'isSuperAdmin' => true
            ]);
        }

        if (!$user || (!$user->isAdminGeral() && !$user->isEstoque())) {
            abort(403, 'Acesso negado. Apenas admin geral ou estoque podem acessar o dashboard de estoque.');
        }

        $userStoreIds = StoreHelper::getUserStoreIds();
        
        // Filtros de Período
        $period = $request->get('period', 'month');
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
                    $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
                } else {
                    $startDate = Carbon::now()->subDays(30)->startOfDay();
                    $endDate = Carbon::now();
                }
                break;
            default: // month
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Outros Filtros
        $vendorId = $request->get('vendor_id');
        $fabricId = $request->get('fabric_id');
        $colorId = $request->get('color_id');

        // Base Query Scope
        $stockQuery = Stock::query();
        $historyQuery = StockHistory::whereBetween('action_date', [$startDate, $endDate]);
        $requestQuery = StockRequest::query();

        if (!empty($userStoreIds)) {
            $stockQuery->whereIn('store_id', $userStoreIds);
            $historyQuery->whereIn('store_id', $userStoreIds);
            $requestQuery->where(function($q) use ($userStoreIds) {
                $q->whereIn('requesting_store_id', $userStoreIds)
                  ->orWhereIn('target_store_id', $userStoreIds);
            });
        }

        // Aplicar Filtros Avançados
        if ($vendorId) {
            $historyQuery->where('user_id', $vendorId);
            $requestQuery->where('requested_by', $vendorId);
        }

        if ($fabricId) {
            $stockQuery->where('fabric_id', $fabricId);
            $historyQuery->whereHas('stock', function($q) use ($fabricId) { $q->where('fabric_id', $fabricId); });
            $requestQuery->where('fabric_id', $fabricId);
        }

        if ($colorId) {
            $stockQuery->where('color_id', $colorId);
            $historyQuery->whereHas('stock', function($q) use ($colorId) { $q->where('color_id', $colorId); });
            $requestQuery->where('color_id', $colorId);
        }

        // 1. KPI Cards
        $totalItems = (clone $stockQuery)->sum('quantity');
        $lowStockCount = (clone $stockQuery)->whereRaw('quantity <= min_stock')->count();
        $pendingRequests = (clone $requestQuery)->where('status', 'pendente')->count();
        $totalSKUs = (clone $stockQuery)->count();

        // 2. Charts Data
        $stockByStore = (clone $stockQuery)
            ->join('stores', 'stocks.store_id', '=', 'stores.id')
            ->select('stores.name', DB::raw('SUM(stocks.quantity) as total'))
            ->groupBy('stores.name')
            ->pluck('total', 'name')
            ->toArray();

        $movementsData = $this->getMovementChartData($historyQuery, $startDate, $endDate);

        // 3. Lists
        $lowStockItems = (clone $stockQuery)
            ->with(['store', 'fabric', 'cutType', 'color'])
            ->whereRaw('quantity <= min_stock')
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();

        $recentActivity = (clone $historyQuery)
            ->with(['user', 'store', 'cutType'])
            ->orderBy('action_date', 'desc')
            ->limit(10)
            ->get();

        // Data for Filters
        $vendedores = \App\Models\User::whereIn('role', ['vendedor', 'admin_loja', 'admin'])->get();
        $fabrics = \App\Models\ProductOption::where('type', 'tecido')->get();
        $colors = \App\Models\ProductOption::where('type', 'cor')->get();

        return view('stocks.dashboard', compact(
            'totalItems',
            'lowStockCount',
            'pendingRequests',
            'totalSKUs',
            'stockByStore',
            'movementsData',
            'lowStockItems',
            'recentActivity',
            'period',
            'startDate',
            'endDate',
            'vendedores',
            'fabrics',
            'colors',
            'vendorId',
            'fabricId',
            'colorId'
        ));
    }

    private function getMovementChartData($query, $startDate, $endDate)
    {
        $days = [];
        $entries = [];
        $exits = [];

        // Determine how many days to show (up to 7 or the range)
        $diffDays = $startDate->diffInDays($endDate);
        if ($diffDays > 30) $diffDays = 30; // Cap at 30 days for chart readability

        for ($i = $diffDays; $i >= 0; $i--) {
            $currentDate = (clone $endDate)->subDays($i);
            $dateStr = $currentDate->format('Y-m-d');
            $label = $currentDate->format('d/m');
            $days[] = $label;

            $entries[] = (clone $query)
                ->whereDate('action_date', $dateStr)
                ->whereIn('action_type', ['entrada', 'devolucao', 'ajuste'])
                ->where('quantity_change', '>', 0)
                ->sum('quantity_change');

            $exits[] = abs((clone $query)
                ->whereDate('action_date', $dateStr)
                ->whereIn('action_type', ['saida', 'perda', 'ajuste'])
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
