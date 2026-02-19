<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\SublimationSize;
use App\Models\ProductOption;
use App\Models\Setting;
use App\Models\CashTransaction;
use App\Models\DeliveryRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->tenant_id !== null) {
            return redirect()->route('dashboard');
        }

        // Se não houver tenant selecionado, mostra estado vazio
        if (!$this->hasSelectedTenant()) {
            return view('admin.dashboard', [
                'stats' => [
                    'total_orders' => 0,
                    'total_users' => 0,
                    'total_sizes' => 0,
                    'total_product_options' => 0,
                    'total_settings' => 0,
                    'pending_delivery_requests' => 0,
                    'pending_edits' => 0,
                    'pending_cancellations' => 0,
                    'pending_edit_requests' => 0,
                    'total_cash_transactions' => 0,
                    'total_stores' => 0,
                    'total_sub_stores' => 0,
                ],
                'recent_orders' => collect([]),
                'recent_users' => collect([]),
                'pending_delivery_requests' => collect([]),
                'recent_cash_transactions' => collect([]),
                'stores' => collect([]),
                'isSuperAdmin' => true
            ]);
        }

        $activeTenantId = $this->getSelectedTenantId();

        // Estatísticas filtradas pelo tenant selecionado
        $stats = [
            'total_orders' => Order::whereHas('store', fn($q) => $q->where('tenant_id', $activeTenantId))->where('is_draft', false)->count(),
            'total_users' => User::whereHas('stores', fn($q) => $q->where('tenant_id', $activeTenantId))->count(),
            'total_sizes' => SublimationSize::where('active', true)->count(), // Geral? Ou por tenant?
            'total_product_options' => ProductOption::count(), // Geral?
            'total_settings' => Setting::count(), // Geral?
            'pending_delivery_requests' => DeliveryRequest::where('status', 'pending')
                ->whereHas('order.store', fn($q) => $q->where('tenant_id', $activeTenantId))
                ->count(),
            'pending_edits' => \App\Models\OrderEditRequest::where('status', 'pending')
                 ->whereHas('order.store', fn($q) => $q->where('tenant_id', $activeTenantId))
                 ->count(),
            'pending_cancellations' => \App\Models\OrderCancellation::where('status', 'pending')
                 ->whereHas('order.store', fn($q) => $q->where('tenant_id', $activeTenantId))
                 ->count(),
            'pending_edit_requests' => \App\Models\OrderEditRequest::where('status', 'pending')
                 ->whereHas('order.store', fn($q) => $q->where('tenant_id', $activeTenantId))
                 ->count(),
            'total_cash_transactions' => CashTransaction::whereHas('store', fn($q) => $q->where('tenant_id', $activeTenantId))->count(),
            'total_stores' => Store::where('tenant_id', $activeTenantId)->active()->count(),
            'total_sub_stores' => Store::where('tenant_id', $activeTenantId)->whereNotNull('parent_id')->active()->count(),
        ];

        // Pedidos recentes
        $recent_orders = Order::with(['client', 'status'])
            ->whereHas('store', fn($q) => $q->where('tenant_id', $activeTenantId))
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Usuários recentes
        $recent_users = User::whereHas('stores', fn($q) => $q->where('tenant_id', $activeTenantId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Solicitações de entrega pendentes
        $pending_delivery_requests = DeliveryRequest::with(['order.client'])
            ->where('status', 'pending')
            ->whereHas('order.store', fn($q) => $q->where('tenant_id', $activeTenantId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Transações de caixa recentes
        $recent_cash_transactions = CashTransaction::with(['user'])
            ->whereHas('store', fn($q) => $q->where('tenant_id', $activeTenantId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Lojas e sub-lojas
        $stores = Store::with(['parent', 'subStores', 'users'])
            ->where('tenant_id', $activeTenantId)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'recent_users',
            'pending_delivery_requests',
            'recent_cash_transactions',
            'stores'
        ));
    }
}
