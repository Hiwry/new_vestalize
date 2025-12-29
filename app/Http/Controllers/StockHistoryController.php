<?php

namespace App\Http\Controllers;

use App\Helpers\StoreHelper;
use App\Models\StockHistory;
use App\Models\Store;
use App\Models\User;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockHistoryController extends Controller
{
    /**
     * Display a listing of the stock history.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdminGeral() && !$user->isEstoque())) {
            abort(403, 'Acesso negado. Apenas admin geral ou estoque podem acessar o histórico de estoque.');
        }

        $query = StockHistory::with(['store', 'user', 'fabric', 'fabric.parent', 'color', 'cutType', 'stock', 'order', 'stockRequest', 'stockRequest.order'])
            ->orderBy('action_date', 'desc');

        // Filter by Store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        } else {
            // Apply permission filter if no specific store selected
            $userStoreIds = StoreHelper::getUserStoreIds();
            if (!empty($userStoreIds)) {
                $query->whereIn('store_id', $userStoreIds);
            }
        }

        // Filter by Date Range
        if ($request->filled('date_start')) {
            $query->whereDate('action_date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('action_date', '<=', $request->date_end);
        }

        // Filter by User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action Type
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Search by Product (Fabric/Color/Cut)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('fabric', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('color', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('cutType', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $history = $query->paginate(20)->withQueryString();
        
        // Data for filters
        $stores = StoreHelper::getAvailableStores();
        $users = User::orderBy('name')->get();
        // Use distinct actions from DB or defaults
        $actions = ['entrada', 'saida', 'transferencia', 'reserva', 'liberacao', 'edicao', 'devolucao', 'perda', 'ajuste'];

        return view('stocks.history', compact('history', 'stores', 'users', 'actions'));
    }
}
