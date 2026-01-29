<?php

namespace App\Http\Controllers;

use App\Helpers\StoreHelper;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        $period = $request->input('period', 'month');
        $selectedStoreId = $request->input('store_id');
        $storeIds = StoreHelper::getStoreIds($selectedStoreId);

        // Adjust dates based on period if not custom
        if ($period === 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($period === 'month' && !$request->has('start_date')) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($period === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // Super Admin without selected tenant now sees all data
        // Filter is managed via StoreHelper::getStoreIds and the base query below


        // Base query conditions
        $baseQuery = OrderItem::select('order_items.*')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->where('orders.is_draft', false)
            // ->where('payments.cash_approved', true) // Apenas aprovados
            // ->where('payments.remaining_amount', 0) // Apenas totalmente pagos
            ->whereBetween('orders.created_at', [$startDate, $endDate]);

        // Apply store filter - users should only see their tenant's data
        if (!empty($storeIds)) {
            $baseQuery->whereIn('orders.store_id', $storeIds);
        } else {
            // If no specific storeIds, filter by user's tenant stores
            $tenantId = $user->tenant_id ?? session('selected_tenant_id');
            if ($tenantId) {
                $tenantStoreIds = \App\Models\Store::where('tenant_id', $tenantId)->pluck('id')->toArray();
                if (!empty($tenantStoreIds)) {
                    $baseQuery->whereIn('orders.store_id', $tenantStoreIds);
                } else {
                    // No stores for this tenant, return nothing
                    $baseQuery->whereRaw('1 = 0');
                }
            }
            // Super admin without tenant sees all (no filter applied)
        }

        // Metrics
        $totalRevenue = (clone $baseQuery)->sum('order_items.total_price');
        $totalCost = (clone $baseQuery)->sum('order_items.total_cost');
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Daily Chart Data
        $dailyData = (clone $baseQuery)
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('SUM(order_items.total_cost) as cost'),
                DB::raw('SUM(order_items.total_price - order_items.total_cost) as profit')
            )
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date')
            ->get();

        // Top 5 Products by Profit
        $topProducts = (clone $baseQuery)
            ->select(
                'order_items.print_type',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total_price) as revenue'),
                DB::raw('SUM(order_items.total_cost) as cost'),
                DB::raw('SUM(order_items.total_price - order_items.total_cost) as profit')
            )
            ->groupBy('order_items.print_type')
            ->orderBy('profit', 'desc')
            ->limit(5)
            ->get();

        // Items Report (Paginated)
        $itemsReport = (clone $baseQuery)
            ->select(
                'order_items.*', 
                'orders.id as order_code', 
                'orders.created_at as order_date',
                'orders.client_id' 
            )
            ->with(['order.client'])
            ->orderBy('orders.created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());

        $stores = StoreHelper::getUserStores();

        return view('dashboard.financeiro', compact(
            'totalRevenue',
            'totalCost',
            'grossProfit',
            'profitMargin',
            'dailyData',
            'topProducts',
            'itemsReport',
            'startDate',
            'endDate',
            'period',
            'stores',
            'selectedStoreId'
        ));
    }
}
