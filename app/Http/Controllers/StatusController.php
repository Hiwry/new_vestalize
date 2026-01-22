<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Helpers\StoreHelper;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // Determinar o tenant a ser usado
        $activeTenantId = $user->tenant_id;
        if ($activeTenantId === null) {
            $activeTenantId = session('selected_tenant_id');
        }
        if ($activeTenantId === null) {
            // Fallback: usar tenant_id da primeira loja encontrada ou tenant_id = 1
            $firstStore = \App\Models\Store::first();
            $activeTenantId = $firstStore ? $firstStore->tenant_id : 1;
        }
        
        // Buscar status do tenant com contagem de pedidos filtrada por loja e tenant
        $statuses = Status::where('tenant_id', $activeTenantId)
            ->withCount(['orders' => function($query) use ($activeTenantId) {
                $query->kanbanVisible()
                      ->where('tenant_id', $activeTenantId); // Garantir filtro de tenant literal
                // Aplicar filtro de loja adicional
                StoreHelper::applyStoreFilter($query);
            }])
            ->orderBy('position')
            ->get();
            
        return view('kanban.columns.index', compact('statuses'));
    }

    public function create(): View
    {
        return view('kanban.columns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        // Definir posição como a última
        $maxPosition = Status::max('position') ?? 0;
        $validated['position'] = $maxPosition + 1;

        Status::create($validated);

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna criada com sucesso!');
    }

    public function edit(Status $status): View
    {
        return view('kanban.columns.edit', compact('status'));
    }

    public function update(Request $request, Status $status): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $status->update($validated);

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna atualizada com sucesso!');
    }

    public function destroy(Status $status): RedirectResponse
    {
        // 1. Verificar se há pedidos que o usuário PODE ver
        $ordersQuery = $status->orders()->kanbanVisible();
        StoreHelper::applyStoreFilter($ordersQuery);
        $visibleOrdersCount = $ordersQuery->count();
        
        if ($visibleOrdersCount > 0) {
            return redirect()->route('kanban.columns.index')
                ->with('error', "Não é possível excluir a coluna '{$status->name}' pois existem {$visibleOrdersCount} pedido(s) visíveis nela. Mova os pedidos para outra coluna primeiro.");
        }

        // 2. Resolver pedidos remanescentes (invisíveis, rascunhos ou de outras lojas do tenant)
        // Procurar um status de fallback para o mesmo tenant (o primeiro da lista que não seja o atual)
        $fallbackStatus = Status::where('tenant_id', $status->tenant_id)
            ->where('id', '!=', $status->id)
            ->orderBy('position')
            ->first();

        if (!$fallbackStatus) {
            return redirect()->route('kanban.columns.index')
                ->with('error', "Não é possível excluir a única coluna do Kanban.");
        }

        // Mover TODOS os pedidos deste status (do tenant) para o fallback para evitar erro de FK
        \App\Models\Order::where('status_id', $status->id)
            ->where('tenant_id', $status->tenant_id)
            ->update(['status_id' => $fallbackStatus->id]);

        // Reordenar posições das colunas restantes
        Status::where('tenant_id', $status->tenant_id)
            ->where('position', '>', $status->position)
            ->decrement('position');

        $status->delete();

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna excluída com sucesso!');
    }

    public function reorder(Request $request)
    {
        try {
            $validated = $request->validate([
                'statuses' => 'required|array',
                'statuses.*' => 'required|integer|exists:statuses,id',
            ]);

            // Verificar se todos os IDs existem
            $existingIds = Status::whereIn('id', $validated['statuses'])->pluck('id')->toArray();
            if (count($existingIds) !== count($validated['statuses'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Um ou mais IDs de coluna não foram encontrados.'
                ], 422);
            }

            // Atualizar posições usando transação
            DB::beginTransaction();
            try {
                foreach ($validated['statuses'] as $index => $statusId) {
                    Status::where('id', $statusId)->update(['position' => $index + 1]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ordem das colunas atualizada com sucesso!'
                ]);
            }

            return redirect()->route('kanban.columns.index')
                ->with('success', 'Ordem das colunas atualizada com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos: ' . implode(', ', $e->errors()['statuses'] ?? ['Erro de validação'])
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro ao reordenar colunas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar ordem: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('kanban.columns.index')
                ->with('error', 'Erro ao atualizar ordem das colunas: ' . $e->getMessage());
        }
    }

    public function moveOrders(Request $request, Status $status): RedirectResponse
    {
        $validated = $request->validate([
            'target_status_id' => 'required|integer|exists:statuses,id',
        ]);

        $targetStatus = Status::findOrFail($validated['target_status_id']);
        
        // Mover todos os pedidos desta coluna para a coluna de destino
        $movedCount = $status->orders()->update(['status_id' => $targetStatus->id]);

        return redirect()->route('kanban.columns.index')
            ->with('success', "{$movedCount} pedido(s) movido(s) para '{$targetStatus->name}' com sucesso!");
    }
}
