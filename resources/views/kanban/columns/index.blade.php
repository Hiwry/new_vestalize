@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
@php
    $dashboardCoverage = $totalColumns > 0 ? round(($dashboardColumnCount / $totalColumns) * 100) : 0;
    $busiestLabel = $busiestStatus?->display_name ?? 'Sem coluna';
    $busiestOrders = (int) ($busiestStatus->orders_count ?? 0);
@endphp

<style>
    .kc-shell {
        --kc-surface: #f8fafc;
        --kc-surface-dark: #111827;
        --kc-card: rgba(255, 255, 255, 0.92);
        --kc-card-dark: rgba(17, 24, 39, 0.92);
        --kc-border: rgba(148, 163, 184, 0.22);
        --kc-text: #0f172a;
        --kc-muted: #64748b;
        --kc-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        background: var(--kc-surface);
        border: 1px solid var(--kc-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: var(--kc-shadow);
    }
    .dark .kc-shell {
        background: var(--kc-surface-dark);
    }
    .kc-topbar, .kc-actions, .kc-card-head, .kc-meta, .kc-list-head, .kc-item-main, .kc-item-top {
        display: flex;
        gap: 14px;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .kc-brand { display: flex; gap: 14px; align-items: center; }
    .kc-logo {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #a855f7;
        background: rgba(168, 85, 247, 0.12);
    }
    .kc-title { font-size: 22px; font-weight: 800; color: var(--kc-text); }
    .kc-subtitle { font-size: 13px; font-weight: 600; color: var(--kc-muted); }
    .dark .kc-title { color: #ffffff !important; }
    .dark .kc-subtitle { color: #e2e8f0 !important; }
    .dark .kc-logo { color: #ffffff !important; background: rgba(255,255,255,0.1); }
    .kc-action {
        height: 40px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #fff !important;
    }
    .kc-action span,
    .kc-action i,
    .kc-action * { color: #fff !important; }
    .kc-action-muted { background: #475569; }
    .kc-action-primary { background: #2563eb; }
    .kc-grid-kpi {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 22px;
    }
    .kc-grid-2 {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(340px, 0.85fr);
        gap: 14px;
        margin-top: 14px;
    }
    .kc-card, .kc-item {
        background: var(--kc-card);
        border: 1px solid var(--kc-border);
        border-radius: 18px;
        box-shadow: var(--kc-shadow);
    }
    .dark .kc-card, .dark .kc-item {
        background: var(--kc-card-dark);
    }
    .kc-card { padding: 18px; }
    .kc-kpi {
        min-height: 142px;
        position: relative;
        overflow: hidden;
    }
    .kc-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 14px;
    }
    .kc-kpi-label {
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--kc-muted);
        font-weight: 800;
    }
    .kc-kpi-value {
        font-size: 30px;
        line-height: 1;
        font-weight: 900;
        color: var(--kc-text);
        margin-top: 8px;
    }
    .kc-kpi-note {
        font-size: 12px;
        font-weight: 600;
        color: var(--kc-muted);
        margin-top: 10px;
    }
    .dark .kc-kpi-value { color: #ffffff !important; }
    .dark .kc-kpi-label { color: #e2e8f0 !important; }
    .dark .kc-kpi-note { color: #cbd5e1 !important; }
    .kc-chart-box { height: 320px; }
    .kc-chart-box-sm { height: 280px; }
    .kc-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.09);
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 800;
    }
    .dark .kc-pill {
        background: rgba(59, 130, 246, 0.18);
        color: #bfdbfe;
    }
    .kc-list {
        margin-top: 14px;
        display: grid;
        gap: 12px;
    }
    .kc-item {
        padding: 16px 18px;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .kc-item:hover {
        transform: translateY(-1px);
        border-color: rgba(59, 130, 246, 0.25);
    }
    .kc-item-handle {
        color: #94a3b8;
        cursor: move;
        padding: 6px;
        border-radius: 10px;
    }
    .kc-item-color {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        border: 2px solid rgba(255, 255, 255, 0.85);
        box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.25);
    }
    .kc-item-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--kc-text);
    }
    .kc-item-subtitle {
        font-size: 12px;
        font-weight: 600;
        color: var(--kc-muted);
        margin-top: 2px;
    }
    .dark .kc-item-title { color: #ffffff !important; }
    .dark .kc-item-subtitle { color: #e2e8f0 !important; }
    .dark .kc-item-handle { color: #e2e8f0 !important; }
    .kc-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .kc-badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        background: #e2e8f0;
        color: #334155;
    }
    .dark .kc-badge {
        background: rgba(51, 65, 85, 0.95);
        color: #cbd5e1;
    }
    .kc-badge-success {
        background: rgba(16, 185, 129, 0.14);
        color: #047857;
    }
    .dark .kc-badge-success {
        background: rgba(16, 185, 129, 0.18);
        color: #a7f3d0;
    }
    .kc-progress {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.18);
        overflow: hidden;
        margin-top: 14px;
    }
    .kc-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
    }
    .kc-item-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 14px;
    }
    .kc-btn {
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 800;
        border: 0;
    }
    .kc-btn-edit { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
    .kc-btn-move { background: #d97706; color: #fff !important; }
    .kc-btn-delete { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    .kc-btn-delete[disabled] { opacity: .5; cursor: not-allowed; }
    .kc-save-wrap { margin-top: 18px; display: flex; justify-content: center; }
    .kc-save-btn {
        min-width: 260px;
        height: 46px;
        border-radius: 14px;
        border: 0;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        background: #059669;
    }
    @media (max-width: 1200px) {
        .kc-grid-kpi, .kc-grid-2 { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 820px) {
        .kc-shell { padding: 16px; }
        .kc-grid-kpi, .kc-grid-2 { grid-template-columns: 1fr; }
        .kc-actions { width: 100%; }
        .kc-action { width: 100%; justify-content: center; }
        .kc-item-top, .kc-item-main, .kc-card-head { align-items: flex-start; }
    }
</style>

@if(session('success'))
<div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

<section class="kc-shell">
    <div class="kc-topbar">
        <div class="kc-brand">
            <span class="kc-logo"><i class="fa-solid fa-table-columns"></i></span>
            <div>
                <h1 class="kc-title">Colunas do Kanban</h1>
                <p class="kc-subtitle">Visão operacional das colunas e carga atual por etapa.</p>
            </div>
        </div>
        <div class="kc-actions">
            <a href="{{ route('kanban.index') }}" class="kc-action kc-action-muted">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar ao Kanban</span>
            </a>
            <a href="{{ route('kanban.columns.create') }}" class="kc-action kc-action-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Coluna</span>
            </a>
        </div>
    </div>

    <div style="display:flex;gap:8px;margin:18px 0 22px;">
        <a href="{{ route('kanban.columns.index', ['type'=>'production']) }}"
           class="kc-action {{ $viewType === 'production' ? 'kc-action-primary' : 'kc-action-muted' }}">
            <i class="fa-solid fa-shirt"></i>
            <span>Produção</span>
        </a>
        <a href="{{ route('kanban.columns.index', ['type'=>'personalized']) }}"
           class="kc-action {{ $viewType === 'personalized' ? 'kc-action-primary' : 'kc-action-muted' }}">
            <i class="fa-solid fa-paint-brush"></i>
            <span>Personalizados</span>
        </a>
    </div>

    {{-- View-type label --}}
    <div style="margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        @if($viewType === 'personalized')
            <span style="display:inline-flex;align-items:center;gap:7px;padding:6px 14px;border-radius:999px;background:rgba(168,85,247,0.13);color:#a855f7;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;">
                <i class="fa-solid fa-paint-brush"></i> Colunas Personalizadas
            </span>
        @else
            <span style="display:inline-flex;align-items:center;gap:7px;padding:6px 14px;border-radius:999px;background:rgba(37,99,235,0.11);color:#3b82f6;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;">
                <i class="fa-solid fa-shirt"></i> Colunas de Produção
            </span>
        @endif
    </div>

    <div class="kc-grid-kpi">
        <article class="kc-card kc-kpi">
            <span class="kc-kpi-icon" style="background: #0d9488;"><i class="fa-solid fa-layer-group"></i></span>
            <div class="kc-kpi-label">Total de Colunas</div>
            <div class="kc-kpi-value">{{ $totalColumns }}</div>
            <div class="kc-kpi-note">Todas as etapas disponíveis no Kanban.</div>
        </article>
        <article class="kc-card kc-kpi">
            <span class="kc-kpi-icon" style="background: #7c3aed;"><i class="fa-solid fa-boxes-stacked"></i></span>
            <div class="kc-kpi-label">Pedidos nas Colunas</div>
            <div class="kc-kpi-value">{{ $totalOrdersInColumns }}</div>
            <div class="kc-kpi-note">Média de {{ number_format($avgOrdersPerColumn, 1, ',', '.') }} por coluna.</div>
        </article>
        <article class="kc-card kc-kpi">
            <span class="kc-kpi-icon" style="background: #dc2626;"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div class="kc-kpi-label">Maior Carga</div>
            <div class="kc-kpi-value">{{ $busiestOrders }}</div>
            <div class="kc-kpi-note">{{ $busiestLabel }} lidera a fila atual.</div>
        </article>
    </div>

    <section class="kc-card">
        <div class="kc-card-head">
            <div>
                <div class="kc-title" style="font-size: 18px;">Pedidos por Coluna — {{ $viewType === 'personalized' ? 'Personalizados' : 'Produção' }}</div>
                <div class="kc-subtitle">Comparativo direto da quantidade de pedidos em cada etapa.</div>
            </div>
            <span class="kc-pill"><i class="fa-solid fa-filter"></i>{{ $emptyColumnsCount }} coluna(s) vazia(s)</span>
        </div>
        <div class="kc-chart-box"><canvas id="columnLoadChart"></canvas></div>
    </section>

    @if($statuses->count() > 0)
    <section class="kc-card" style="margin-top: 14px;">
        <div class="kc-list-head">
            <div>
                <div class="kc-title" style="font-size: 18px;">Lista — {{ $viewType === 'personalized' ? 'Personalizados' : 'Produção' }}</div>
                <div class="kc-subtitle">Arraste para reordenar. Cada card mostra posição, carga, participação e se a coluna está no dashboard.</div>
            </div>
            <span class="kc-pill"><i class="fa-solid fa-arrow-up-wide-short"></i>Ordenação manual</span>
        </div>

        <div id="sortable-columns" class="kc-list">
            @foreach($statuses as $status)
            @php
                $ordersCount = (int) ($status->orders_count ?? 0);
                $share = $totalOrdersInColumns > 0 ? round(($ordersCount / $totalOrdersInColumns) * 100, 1) : 0;
                $inDashboard = ($columnLoadSeries->firstWhere('id', $status->id)['in_dashboard'] ?? false) === true;
            @endphp
            <article class="kc-item" data-status-id="{{ $status->id }}">
                <div class="kc-item-top">
                    <div class="kc-item-main">
                        <button type="button" class="kc-item-handle" aria-label="Arrastar coluna">
                            <i class="fa-solid fa-grip-lines"></i>
                        </button>
                        <span class="kc-item-color" style="background-color: {{ $status->color }}"></span>
                        <div>
                            <div class="kc-item-title">{{ $status->display_name ?? $status->name }}</div>
                            <div class="kc-item-subtitle">Posição {{ $status->position }} no fluxo principal</div>
                        </div>
                    </div>
                    <div class="kc-badges">
                        <span class="kc-badge">{{ $ordersCount }} pedido(s)</span>
                        <span class="kc-badge">{{ number_format($share, 1, ',', '.') }}% da fila</span>
                        @if($inDashboard)
                        <span class="kc-badge kc-badge-success">No dashboard</span>
                        @else
                        <span class="kc-badge">Fora do dashboard</span>
                        @endif
                    </div>
                </div>

                <div class="kc-progress" aria-hidden="true">
                    <span style="width: {{ min(100, $share > 0 ? $share : ($ordersCount > 0 ? 6 : 0)) }}%; background: {{ $status->color }};"></span>
                </div>

                <div class="kc-item-actions">
                    <a href="{{ route('kanban.columns.edit', $status) }}" class="kc-btn kc-btn-edit">Editar</a>

                    @if($ordersCount > 0)
                    <button type="button" onclick="window.openMoveModal && window.openMoveModal({{ $status->id }}, @js($status->display_name ?? $status->name), {{ $ordersCount }})" class="kc-btn kc-btn-move">
                        Mover Pedidos
                    </button>
                    @endif

                    <form id="delete-form-{{ $status->id }}" method="POST" action="{{ route('kanban.columns.destroy', $status) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                onclick="window.openDeleteModal && window.openDeleteModal({{ $status->id }}, @js($status->display_name ?? $status->name))"
                                class="kc-btn kc-btn-delete"
                                {{ $ordersCount > 0 ? 'disabled' : '' }}>
                            Excluir
                        </button>
                    </form>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @else
    <section class="kc-card" style="margin-top: 14px; text-align: center; padding: 48px 24px;">
        <div class="kc-title" style="font-size: 18px;">Nenhuma coluna encontrada</div>
        <p class="kc-subtitle" style="margin-top: 8px;">Crie a primeira etapa para começar a usar o Kanban.</p>
        <div style="margin-top: 18px;">
            <a href="{{ route('kanban.columns.create') }}" class="kc-action kc-action-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Criar primeira coluna</span>
            </a>
        </div>
    </section>
    @endif

    @if($statuses->count() > 1)
    <div class="kc-save-wrap">
        <button onclick="window.saveOrder && window.saveOrder()" class="kc-save-btn">Salvar Ordem das Colunas</button>
    </div>
    @endif
</section>

<div id="moveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/25 max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium dark:text-gray-100">Mover Pedidos</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Mover <span id="moveCount">0</span> pedido(s) da coluna
                    <strong id="moveFromColumn">-</strong> para:
                </p>
                <form id="moveForm" method="POST">
                    @csrf
                    <select name="target_status_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        <option value="">Selecione a coluna de destino</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->display_name ?? $status->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end space-x-3">
                <button onclick="window.closeMoveModal && window.closeMoveModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Cancelar
                </button>
                <button onclick="window.submitMove && window.submitMove()" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                    Mover Pedidos
                </button>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/25 max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium dark:text-gray-100">Confirmar Exclusão</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-300">
                    Tem certeza que deseja excluir a coluna <strong id="deleteColumnName">-</strong>?
                </p>
                <p class="text-sm text-red-500 mt-2">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end space-x-3">
                <button onclick="window.closeDeleteModal && window.closeDeleteModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Cancelar
                </button>
                <button onclick="window.confirmDelete && window.confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition font-bold">
                    Sim, Excluir
                </button>
            </div>
        </div>
    </div>
</div>

@php
    $columnChartPayload = $columnLoadSeries->map(fn ($item) => [
        'label' => $item['label'],
        'orders' => $item['orders'],
        'share' => $item['share'],
        'color' => $item['color'],
        'in_dashboard' => $item['in_dashboard'],
    ])->values();
@endphp

<script>
    (function initKanbanColumns() {
        const sortableContainer = document.getElementById('sortable-columns');
        if (sortableContainer) {
            if (typeof Sortable !== 'undefined') {
                if (!sortableContainer.hasAttribute('data-sortable-initialized')) {
                    new Sortable(sortableContainer, {
                        animation: 150,
                        ghostClass: 'opacity-50',
                        handle: '.kc-item-handle'
                    });
                    sortableContainer.setAttribute('data-sortable-initialized', 'true');
                }
            } else {
                // Wait for Sortable to load (happens occasionally on AJAX loads)
                setTimeout(initKanbanColumns, 100);
                return;
            }
        }

        const chartPayload = @json($columnChartPayload);

        function palette() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                text: isDark ? '#cbd5e1' : '#475569',
                grid: isDark ? 'rgba(148, 163, 184, 0.18)' : '#e2e8f0',
                border: isDark ? 'rgba(148, 163, 184, 0.22)' : '#cbd5e1',
                success: '#10b981',
                muted: '#94a3b8',
            };
        }

        function destroyColumnCharts() {
            if (!window.kanbanColumnCharts) return;
            Object.values(window.kanbanColumnCharts).forEach((chart) => {
                if (chart && typeof chart.destroy === 'function') chart.destroy();
            });
            window.kanbanColumnCharts = {};
        }

        function buildColumnCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(buildColumnCharts, 100);
                return;
            }
            destroyColumnCharts();
            window.kanbanColumnCharts = {};
            const colors = palette();

            const loadCanvas = document.getElementById('columnLoadChart');
            if (loadCanvas && chartPayload.length > 0) {
                window.kanbanColumnCharts.load = new Chart(loadCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartPayload.map((item) => item.label),
                        datasets: [{
                            label: 'Pedidos',
                            data: chartPayload.map((item) => Number(item.orders || 0)),
                            backgroundColor: chartPayload.map((item) => item.color || '#7c3aed'),
                            borderRadius: 10,
                            maxBarThickness: 34
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: colors.text, font: { size: 11, weight: '600' } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: colors.grid },
                                ticks: { color: colors.text, precision: 0, font: { size: 11, weight: '600' } }
                            }
                        }
                    }
                });
            }


        }

        buildColumnCharts();
        
        // Ensure event listeners are not duplicating on multiple AJAX visits
        if (!window._kanbanColumnsEventsBound) {
            window.addEventListener('dark-mode-toggled', buildColumnCharts);
            document.addEventListener('ajax-content-loaded', buildColumnCharts);
            window._kanbanColumnsEventsBound = true;
        }
    })();

    function saveOrder() {
        const statusIds = Array.from(document.querySelectorAll('[data-status-id]'))
            .map((el) => parseInt(el.dataset.statusId, 10));

        if (statusIds.length === 0) {
            alert('Nenhuma coluna encontrada para reordenar.');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
            return;
        }

        fetch('{{ route("kanban.columns.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ statuses: statusIds })
        })
        .then((response) => {
            if (!response.ok) {
                return response.json().then((err) => {
                    throw new Error(err.message || 'Erro ao salvar ordem');
                }).catch(() => {
                    throw new Error('Erro ao processar resposta do servidor');
                });
            }

            return response.json();
        })
        .then((data) => {
            if (data.success) {
                alert('Ordem salva com sucesso!');
                location.reload();
                return;
            }

            alert('Erro ao salvar ordem: ' + (data.message || 'Erro desconhecido'));
        })
        .catch((error) => {
            console.error('Erro:', error);
            alert('Erro ao salvar ordem das colunas: ' + error.message);
        });
    }
    window.saveOrder = saveOrder;

    let statusToDelete = null;

    function openMoveModal(statusId, statusName, ordersCount) {
        const moveCount = document.getElementById('moveCount');
        const moveFromColumn = document.getElementById('moveFromColumn');
        const moveForm = document.getElementById('moveForm');
        const moveModal = document.getElementById('moveModal');
        if (!moveCount || !moveFromColumn || !moveForm || !moveModal) return;

        moveCount.textContent = ordersCount;
        moveFromColumn.textContent = statusName;
        moveForm.action = `{{ url('kanban/columns') }}/${statusId}/move-orders`;
        moveModal.classList.remove('hidden');
    }
    window.openMoveModal = openMoveModal;

    function closeMoveModal() {
        document.getElementById('moveModal')?.classList.add('hidden');
    }
    window.closeMoveModal = closeMoveModal;

    function submitMove() {
        const form = document.getElementById('moveForm');
        if (!form) return;

        if (!form.target_status_id.value) {
            alert('Selecione uma coluna de destino');
            return;
        }

        form.submit();
    }
    window.submitMove = submitMove;

    function openDeleteModal(statusId, statusName) {
        statusToDelete = statusId;
        const deleteColumnName = document.getElementById('deleteColumnName');
        const deleteModal = document.getElementById('deleteModal');
        if (!deleteColumnName || !deleteModal) return;

        deleteColumnName.textContent = statusName;
        deleteModal.classList.remove('hidden');
    }
    window.openDeleteModal = openDeleteModal;

    function closeDeleteModal() {
        document.getElementById('deleteModal')?.classList.add('hidden');
        statusToDelete = null;
    }
    window.closeDeleteModal = closeDeleteModal;

    function confirmDelete() {
        if (!statusToDelete) return;
        document.getElementById('delete-form-' + statusToDelete)?.submit();
    }
    window.confirmDelete = confirmDelete;

    window.addEventListener('click', function(e) {
        const moveModal = document.getElementById('moveModal');
        const deleteModal = document.getElementById('deleteModal');

        if (e.target === moveModal) closeMoveModal();
        if (e.target === deleteModal) closeDeleteModal();
    });
</script>
@endsection
