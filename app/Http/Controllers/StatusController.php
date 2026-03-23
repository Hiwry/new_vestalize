<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{
    public function index(Request $request): View
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }

        $viewType = $request->get('type', 'production');
        if (!in_array($viewType, ['production', 'personalized'])) {
            $viewType = 'production';
        }

        $statuses = Status::withCount(['orders' => function ($query) use ($viewType) {
            $query->notDrafts()->where('is_cancelled', false);
            StoreHelper::applyStoreFilter($query);
            if ($viewType === 'personalized') {
                $query->where('origin', 'personalized');
            } else {
                $query->where(function ($q) {
                    $q->where('origin', '!=', 'personalized')->orWhereNull('origin');
                });
            }
        }])->where('type', $viewType)->orderBy('position')->get();

        $nameCounts = $statuses->countBy(function ($status) {
            return trim((string) $status->name);
        });

        $statuses->transform(function ($status) use ($nameCounts) {
            $baseName = trim((string) $status->name);
            $hasDuplicates = ($nameCounts[$baseName] ?? 0) > 1;
            $suffix = $status->position ? ' (Pos. ' . $status->position . ')' : ' (#' . $status->id . ')';
            $status->display_name = $hasDuplicates ? $baseName . $suffix : $baseName;

            return $status;
        });

        $dashboardSelectedColumns = collect(session('production_dashboard_columns', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $dashboardStatuses = $statuses
            ->whereIn('id', $dashboardSelectedColumns)
            ->values();

        $totalColumns = $statuses->count();
        $dashboardColumnCount = $dashboardStatuses->count();
        $totalOrdersInColumns = (int) $statuses->sum('orders_count');
        $emptyColumnsCount = (int) $statuses->where('orders_count', 0)->count();
        $avgOrdersPerColumn = $totalColumns > 0
            ? round($totalOrdersInColumns / $totalColumns, 1)
            : 0;

        $busiestStatus = $statuses
            ->sortByDesc('orders_count')
            ->first();

        $columnLoadSeries = $statuses->map(function ($status) use ($totalOrdersInColumns, $dashboardSelectedColumns) {
            $ordersCount = (int) ($status->orders_count ?? 0);

            return [
                'id' => $status->id,
                'label' => $status->display_name,
                'orders' => $ordersCount,
                'position' => (int) ($status->position ?? 0),
                'color' => $status->color ?: '#7c3aed',
                'share' => $totalOrdersInColumns > 0
                    ? round(($ordersCount / $totalOrdersInColumns) * 100, 1)
                    : 0,
                'in_dashboard' => $dashboardSelectedColumns->contains((int) $status->id),
            ];
        })->values();

        return view('kanban.columns.index', compact(
            'statuses',
            'dashboardStatuses',
            'totalColumns',
            'dashboardColumnCount',
            'totalOrdersInColumns',
            'emptyColumnsCount',
            'avgOrdersPerColumn',
            'busiestStatus',
            'columnLoadSeries',
            'viewType'
        ));
    }

    public function create(): View
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
        return view('kanban.columns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
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
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
        return view('kanban.columns.edit', compact('status'));
    }

    public function update(Request $request, Status $status): RedirectResponse
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
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
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
        // Verificar se há pedidos nesta coluna
        $ordersCount = $status->orders()->count();
        
        if ($ordersCount > 0) {
            return redirect()->route('kanban.columns.index')
                ->with('error', "Não é possível excluir a coluna '{$status->name}' pois existem {$ordersCount} pedido(s) nela. Mova os pedidos para outra coluna primeiro.");
        }

        // Reordenar posições das colunas restantes
        Status::where('position', '>', $status->position)
            ->decrement('position');

        $status->delete();

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna excluída com sucesso!');
    }

    public function reorder(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }
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
        if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
            abort(403, 'Acesso negado.');
        }
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
