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

        // Enriquecer itens com contexto de agrupamento (Pedido, Venda, Catálogo)
        $previousGroupKey = null;
        $groupSequence = 0;
        $history->setCollection(
            $history->getCollection()->map(function (StockHistory $item) use (&$previousGroupKey, &$groupSequence) {
                $context = $this->resolveMovementContext($item);
                $groupKey = $context['key'];
                $isGroupedContext = $context['type'] !== 'none';
                $isGroupStart = $isGroupedContext && $groupKey !== $previousGroupKey;

                if ($isGroupStart) {
                    $groupSequence++;
                }

                $item->setAttribute('history_context_type', $context['type']);
                $item->setAttribute('history_context_key', $groupKey);
                $item->setAttribute('history_context_label', $context['label']);
                $item->setAttribute('history_context_badge', $context['badge']);
                $item->setAttribute('history_group_start', $isGroupStart);
                $item->setAttribute('history_group_seq', $isGroupedContext ? $groupSequence : null);

                $previousGroupKey = $groupKey;
                return $item;
            })
        );
        
        // Data for filters
        $stores = StoreHelper::getAvailableStores();
        $users = User::orderBy('name')->get();
        // Use distinct actions from DB or defaults
        $actions = ['entrada', 'saida', 'transferencia', 'reserva', 'liberacao', 'edicao', 'devolucao', 'perda', 'ajuste'];

        return view('stocks.history', compact('history', 'stores', 'users', 'actions'));
    }

    /**
     * Resolve contexto de agrupamento para uma movimentação.
     */
    private function resolveMovementContext(StockHistory $item): array
    {
        $order = $item->order ?: optional($item->stockRequest)->order;

        if ($order) {
            $paddedId = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            $origin = strtolower((string) ($order->origin ?? ''));

            if ((bool) ($order->is_pdv ?? false) || $origin === 'pdv') {
                return [
                    'type' => 'sale',
                    'key' => "sale_{$order->id}",
                    'label' => "Venda PDV #{$paddedId}",
                    'badge' => 'Venda',
                ];
            }

            if ($origin === 'catalogo') {
                return [
                    'type' => 'catalog_order',
                    'key' => "catalog_order_{$order->id}",
                    'label' => "Pedido Catálogo Convertido #{$paddedId}",
                    'badge' => 'Catálogo',
                ];
            }

            return [
                'type' => 'order',
                'key' => "order_{$order->id}",
                'label' => "Pedido #{$paddedId}",
                'badge' => 'Pedido',
            ];
        }

        $catalogCode = $this->extractCatalogCode($item->stockRequest->request_notes ?? null)
            ?? $this->extractCatalogCode($item->notes ?? null);

        if ($catalogCode) {
            return [
                'type' => 'catalog',
                'key' => "catalog_{$catalogCode}",
                'label' => "Pedido Catálogo {$catalogCode}",
                'badge' => 'Catálogo',
            ];
        }

        return [
            'type' => 'none',
            'key' => "history_{$item->id}",
            'label' => null,
            'badge' => null,
        ];
    }

    /**
     * Extrai o código CAT-XXXX de uma string.
     */
    private function extractCatalogCode(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        if (preg_match('/(CAT-[A-Za-z0-9]+)/i', $text, $matches)) {
            return strtoupper($matches[1]);
        }

        return null;
    }
}
