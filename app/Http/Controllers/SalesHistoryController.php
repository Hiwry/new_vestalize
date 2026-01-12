<?php

namespace App\Http\Controllers;

use App\Models\SalesHistory;
use App\Models\User;
use App\Models\Store;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesHistoryController extends Controller
{
    /**
     * Listar histórico de vendas
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            return view('sales-history.index', [
                'history' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'vendors' => collect([]),
                'stores' => collect([]),
                'statuses' => collect([]),
                'stats' => [
                    'total_sales' => 0,
                    'total_revenue' => 0,
                    'total_paid' => 0,
                    'avg_ticket' => 0,
                ],
                'search' => null,
                'userId' => null,
                'storeId' => null,
                'statusId' => null,
                'startDate' => null,
                'endDate' => null,
                'isPdv' => null,
                'isSuperAdmin' => true
            ]);
        }

        $search = $request->get('search');
        $userId = $request->get('user_id');
        $storeId = $request->get('store_id');
        $statusId = $request->get('status_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $isPdv = $request->get('is_pdv');

        $query = SalesHistory::with(['order', 'user', 'store', 'client', 'status']);

        // Filtro por vendedor
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($user->isVendedor()) {
            $query->where('user_id', $user->id);
        }

        // Filtro por loja
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Filtro por status
        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        // Filtro por tipo (PDV ou não)
        if ($isPdv !== null) {
            $query->where('is_pdv', $isPdv);
        }

        // Filtro por data
        if ($startDate) {
            $query->where('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('sale_date', '<=', $endDate . ' 23:59:59');
        }

        // Busca
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $history = $query->orderBy('sale_date', 'desc')->paginate(20);

        // Estatísticas
        $stats = [
            'total_sales' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total'),
            'total_paid' => (clone $query)->sum('total_paid'),
            'avg_ticket' => (clone $query)->count() > 0 
                ? (clone $query)->sum('total') / (clone $query)->count() 
                : 0,
        ];

        // Vendedores para filtro
        $vendors = \App\Models\User::where('role', 'vendedor')
            ->orWhereHas('stores', function($q) {
                $q->wherePivot('role', 'vendedor');
            })
            ->orderBy('name')
            ->get();

        // Lojas para filtro
        $stores = \App\Helpers\StoreHelper::getAvailableStores();

        // Status para filtro
        $statuses = Status::orderBy('position')->get();

        return view('sales-history.index', compact(
            'history',
            'vendors',
            'stores',
            'statuses',
            'stats',
            'search',
            'userId',
            'storeId',
            'statusId',
            'startDate',
            'endDate',
            'isPdv'
        ));
    }
}
