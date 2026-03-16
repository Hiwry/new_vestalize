@extends('layouts.admin')

@section('content')
@php
    $periodLabels = ['day' => 'Hoje', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre', 'year' => 'Ano', 'custom' => 'Personalizado'];
    $periodLabel = $periodLabels[$period] ?? 'Mes';
    $selectedCount = is_array($selectedColumns ?? null) ? count($selectedColumns) : 0;
    $totalStatuses = isset($allStatuses) ? $allStatuses->count() : 0;

    $formattedAvg = 'Sem dados';
    if ($avgProductionTime) {
        $days = floor($avgProductionTime / 86400);
        $hours = floor(($avgProductionTime % 86400) / 3600);
        $minutes = floor(($avgProductionTime % 3600) / 60);
        $formattedAvg = '';
        if ($days > 0) $formattedAvg .= $days . 'd ';
        if ($hours > 0) $formattedAvg .= $hours . 'h ';
        $formattedAvg .= $minutes . 'm';
    }

    $slowestLabel = data_get($slowestStatus, 'status_name', 'Sem dados');
    $slowestTime = data_get($slowestStatus, 'avg_formatted', 'Sem dados');
    $timeStatusLead = collect($timeInStatusSeries ?? [])->first();
@endphp

<style>
    .prod-ft {
        --pf-surface-from: #f3f4f8;
        --pf-surface-to: #eceff4;
        --pf-surface-border: #d8dce6;
        --pf-text-primary: #0f172a;
        --pf-text-secondary: #64748b;
        --pf-input-bg: #ffffff;
        --pf-input-border: #d6d9e2;
        --pf-input-text: #334155;
        --pf-card-bg: #ffffff;
        --pf-card-border: #dde2ea;
        --pf-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --pf-grid-color: #edf0f6;
        --pf-chart-label: #475569;
        --pf-table-head-border: #e5e9f1;
        --pf-table-row-border: #eef1f6;
        background: linear-gradient(180deg, var(--pf-surface-from) 0%, var(--pf-surface-to) 100%);
        border: 1px solid var(--pf-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--pf-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .prod-ft {
        --pf-surface-from: #0f172a;
        --pf-surface-to: #0b1322;
        --pf-surface-border: rgba(148, 163, 184, 0.25);
        --pf-text-primary: #e2e8f0;
        --pf-text-secondary: #94a3b8;
        --pf-input-bg: #0b1322;
        --pf-input-border: rgba(148, 163, 184, 0.3);
        --pf-input-text: #e2e8f0;
        --pf-card-bg: #111827;
        --pf-card-border: rgba(148, 163, 184, 0.22);
        --pf-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        --pf-grid-color: rgba(148, 163, 184, 0.2);
        --pf-chart-label: #cbd5e1;
        --pf-table-head-border: rgba(148, 163, 184, 0.25);
        --pf-table-row-border: rgba(148, 163, 184, 0.16);
    }

    .pf-topbar, .pf-tools, .pf-actions, .pf-brand, .pf-kpis { display: flex; gap: 14px; flex-wrap: wrap; }
    .pf-topbar { justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .pf-brand { align-items: center; flex: 1 1 320px; min-width: 0; }
    .pf-logo { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #6d28d9, #8b5cf6); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .pf-title { font-size: 18px; font-weight: 700; letter-spacing: -0.015em; }
    .pf-subtitle { color: var(--pf-text-secondary); font-size: 12px; font-weight: 600; margin-top: 2px; }
    .pf-actions { align-items: center; }
    .pf-action { height: 38px; border-radius: 12px; padding: 0 14px; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; text-decoration: none; color: #fff !important; }
    .pf-action:hover { transform: translateY(-1px); filter: brightness(1.03); }
    .pf-action-primary { background: linear-gradient(135deg, #6d28d9, #7c3aed); box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25); color: #fff !important; }
    .pf-action-success { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25); color: #fff !important; }
    .pf-card { background: var(--pf-card-bg); border: 1px solid var(--pf-card-border); border-radius: 14px; padding: 16px; box-shadow: var(--pf-card-shadow); color: #fff; }
    .pf-tools { align-items: center; margin-bottom: 14px; }
    .pf-select, .pf-date, .pf-button { height: 38px; border-radius: 10px; border: 1px solid var(--pf-input-border); background: var(--pf-input-bg); color: var(--pf-input-text); padding: 0 12px; font-size: 12px; font-weight: 700; }
    .pf-button { background: linear-gradient(135deg, #6d28d9, #7c3aed); color: #fff; border: 0; padding: 0 14px; }
    .pf-kpis { margin-bottom: 14px; }
    .pf-kpi { flex: 1 1 220px; min-height: 132px; position: relative; overflow: hidden; }
    .pf-kpi-value { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; color: #fff !important; margin-top: 10px; }
    .pf-kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #fff !important; font-weight: 700; opacity: 0.9; }
    .pf-kpi-note { font-size: 12px; color: #fff !important; margin-top: 8px; font-weight: 600; opacity: 0.7; }
    .pf-kpi-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
    .pf-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 14px; }
    .pf-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .pf-card-title { font-size: 17px; font-weight: 700; color: var(--pf-text-primary); }
    .pf-card-subtitle { font-size: 12px; color: var(--pf-text-secondary); font-weight: 600; margin-top: 2px; }
    .pf-chart-box { position: relative; height: 260px; }
    .pf-chart-box-sm { position: relative; height: 220px; }
    .pf-chip-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .pf-chip { position: relative; display: flex; align-items: center; gap: 8px; border-radius: 12px; border: 1px solid var(--pf-card-border); background: color-mix(in srgb, var(--pf-card-bg) 75%, #f8fafc); padding: 10px 12px; font-size: 12px; font-weight: 700; color: var(--pf-text-primary); cursor: pointer; }
    .pf-chip.is-selected { background: rgba(124, 58, 237, 0.12); border-color: rgba(124, 58, 237, 0.26); }
    .pf-chip input { display: none; }
    .pf-dot { width: 10px; height: 10px; border-radius: 999px; flex-shrink: 0; }
    .pf-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .pf-summary { border-radius: 12px; padding: 12px; border: 1px solid var(--pf-card-border); background: color-mix(in srgb, var(--pf-card-bg) 80%, #f8fafc); }
    .pf-summary strong { display: block; font-size: 22px; margin-top: 6px; color: var(--pf-text-primary); }
    .pf-summary span { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--pf-text-secondary); font-weight: 700; }
    .pf-table-wrap { border: 1px solid var(--pf-table-head-border); border-radius: 12px; overflow-x: auto; }
    .pf-table { width: 100%; border-collapse: collapse; }
    .pf-table th, .pf-table td { padding: 10px 12px; border-bottom: 1px solid var(--pf-table-row-border); text-align: left; }
    .pf-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--pf-text-secondary); }
    .pf-table td { font-size: 12px; color: var(--pf-text-primary); font-weight: 600; }
    .pf-delivery-list { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .pf-delivery-item { border-radius: 12px; border: 1px solid var(--pf-card-border); background: color-mix(in srgb, var(--pf-card-bg) 85%, #f8fafc); padding: 12px; text-decoration: none; }
    .pf-delivery-item h4 { font-size: 13px; font-weight: 700; color: var(--pf-text-primary); }
    .pf-delivery-item p, .pf-delivery-item span { font-size: 12px; color: var(--pf-text-secondary); font-weight: 600; display: block; }

    @media (max-width: 1140px) { .pf-grid-2, .pf-summary-grid, .pf-delivery-list { grid-template-columns: 1fr; } }
    @media (max-width: 760px) {
        .prod-ft { padding: 14px; border-radius: 16px; }
        .pf-actions, .pf-tools { width: 100%; }
        .pf-action, .pf-select, .pf-date, .pf-button { width: 100%; justify-content: center; }
        .pf-chip-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="prod-ft">
        <div class="pf-topbar">
            <div class="pf-brand">
                <span class="pf-logo"><i class="fa-solid fa-industry"></i></span>
                <div>
                    <div class="pf-title">Painel de Producao</div>
                    <div class="pf-subtitle">Mesmo shell visual do dashboard de vendedor, com foco em throughput, gargalos e prazo.</div>
                </div>
            </div>

            <div class="pf-actions">
                <a href="{{ route('production.index') }}" class="pf-action pf-action-success"><i class="fa-solid fa-list-check"></i><span>Lista de Ordens</span></a>
                <a href="{{ route('kanban.index') }}" class="pf-action pf-action-primary"><i class="fa-solid fa-table-columns"></i><span>Abrir Kanban</span></a>
            </div>
        </div>

        <form method="GET" action="{{ route('production.dashboard') }}" id="dashboard-filter-form">
            <input type="hidden" name="filter_submitted" value="1">
            <input type="hidden" name="delivery_filter" value="{{ $deliveryFilter ?? 'today' }}">

            <div class="pf-tools">
                <select name="period" id="period-select" class="pf-select">
                    <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hoje</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Semana</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Mes</option>
                    <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Trimestre</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Ano</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
                <input type="date" name="start_date" id="start-date-field" class="pf-date {{ $period === 'custom' ? '' : 'hidden' }}" value="{{ $startDate }}">
                <input type="date" name="end_date" id="end-date-field" class="pf-date {{ $period === 'custom' ? '' : 'hidden' }}" value="{{ $endDate }}">
                <button type="submit" class="pf-button"><i class="fa-solid fa-sliders"></i>Aplicar</button>
            </div>

            <div class="pf-card" style="margin-bottom: 14px;">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Etapas monitoradas</div>
                        <div class="pf-card-subtitle">{{ $selectedCount }}/{{ $totalStatuses }} colunas ativas para o dashboard.</div>
                    </div>
                    <div class="pf-actions" style="gap: 8px;">
                        <button type="button" class="pf-button" style="background: #e2e8f0; color: #334155;" onclick="selectAllColumns()">Selecionar todas</button>
                        <button type="button" class="pf-button" style="background: #e2e8f0; color: #334155;" onclick="deselectAllColumns()">Limpar</button>
                    </div>
                </div>
                <div class="pf-chip-grid">
                    @foreach($allStatuses as $status)
                        <label class="pf-chip {{ in_array($status->id, $selectedColumns) ? 'is-selected' : '' }}">
                            <input type="checkbox" class="column-checkbox" name="columns[]" value="{{ $status->id }}" {{ in_array($status->id, $selectedColumns) ? 'checked' : '' }}>
                            <span class="pf-dot" style="background: {{ $status->color ?? '#7c3aed' }}"></span>
                            <span>{{ $status->dashboard_label ?? $status->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </form>

        <div class="pf-kpis">
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #6d28d9, #7c3aed);"><i class="fa-solid fa-boxes-stacked"></i></span>
                <div class="pf-kpi-value">{{ $totalOrders }}</div>
                <div class="pf-kpi-label">Pedidos no periodo</div>
                <div class="pf-kpi-note">{{ $periodLabel }} monitorado de {{ \Carbon\Carbon::parse($startDate)->format('d/m') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m') }}</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #2563eb, #3b82f6);"><i class="fa-solid fa-gears"></i></span>
                <div class="pf-kpi-value">{{ $ordersInProduction }}</div>
                <div class="pf-kpi-label">Ordens em producao</div>
                <div class="pf-kpi-note">Fila ativa que ainda nao saiu do fluxo operacional.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #059669, #10b981);"><i class="fa-solid fa-stopwatch"></i></span>
                <div class="pf-kpi-value">{{ $formattedAvg }}</div>
                <div class="pf-kpi-label">Tempo medio total</div>
                <div class="pf-kpi-note">Tempo medio acumulado dos pedidos em acompanhamento.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <div class="pf-kpi-value">{{ $slowestLabel }}</div>
                <div class="pf-kpi-label">Maior gargalo</div>
                <div class="pf-kpi-note">{{ $slowestTime }}</div>
            </div>
        </div>

        <div class="pf-grid-2">
            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Throughput do periodo</div>
                        <div class="pf-card-subtitle">Entrada de ordens por dia para medir ritmo operacional.</div>
                    </div>
                </div>
                <div class="pf-chart-box"><canvas id="productionThroughputChart"></canvas></div>
            </section>

            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Distribuicao por etapa</div>
                        <div class="pf-card-subtitle">Onde a fila esta concentrada agora.</div>
                    </div>
                </div>
                <div class="pf-chart-box"><canvas id="productionFlowChart"></canvas></div>
            </section>
        </div>

        <div class="pf-grid-2">
            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Tempo medio por setor</div>
                        <div class="pf-card-subtitle">Etapas que mais seguram o lead time.</div>
                    </div>
                    <div class="pf-card-subtitle">{{ data_get($timeStatusLead, 'label', 'Sem lider') }}</div>
                </div>
                <div class="pf-chart-box-sm"><canvas id="productionTimeChart"></canvas></div>
            </section>

            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Saude dos prazos</div>
                        <div class="pf-card-subtitle">Visao rapida do que precisa prioridade hoje.</div>
                    </div>
                </div>
                <div class="pf-summary-grid">
                    <div class="pf-summary"><span>Atrasados</span><strong>{{ data_get($deliveryHealth, 'late', 0) }}</strong></div>
                    <div class="pf-summary"><span>Hoje</span><strong>{{ data_get($deliveryHealth, 'today', 0) }}</strong></div>
                    <div class="pf-summary"><span>Na semana</span><strong>{{ data_get($deliveryHealth, 'week', 0) }}</strong></div>
                    <div class="pf-summary"><span>Concluidos</span><strong>{{ data_get($deliveryHealth, 'completed_ready', 0) }}</strong></div>
                </div>
                <div class="pf-chart-box-sm" style="margin-top: 14px;"><canvas id="productionDeadlineChart"></canvas></div>
            </section>
        </div>

        <div class="pf-grid-2">
            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Setores monitorados</div>
                        <div class="pf-card-subtitle">Resumo tabular de tempo e volume por etapa.</div>
                    </div>
                </div>
                <div class="pf-table-wrap">
                    <table class="pf-table">
                        <thead>
                            <tr><th>Setor</th><th>Pedidos</th><th>Tempo medio</th><th>Minimo</th><th>Maximo</th></tr>
                        </thead>
                        <tbody>
                            @foreach($statuses as $status)
                                @php $stat = collect($statusStats)->firstWhere('status_id', $status->id); @endphp
                                <tr>
                                    <td>{{ $status->dashboard_label ?? $status->name }}</td>
                                    <td>{{ $ordersByStatus[$status->id] ?? 0 }}</td>
                                    <td>{{ data_get($stat, 'avg_formatted', 'Sem dados') }}</td>
                                    <td>{{ data_get($stat, 'min_formatted', 'Sem dados') }}</td>
                                    <td>{{ data_get($stat, 'max_formatted', 'Sem dados') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="pf-card">
                <div class="pf-card-head">
                    <div>
                        <div class="pf-card-title">Proximas entregas</div>
                        <div class="pf-card-subtitle">Pedidos priorizados pelo filtro de entrega atual.</div>
                    </div>
                </div>
                <div class="pf-delivery-list">
                    @forelse($deliveryOrders->take(6) as $order)
                        <a href="{{ route('orders.show', $order->id) }}" class="pf-delivery-item">
                            <h4>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} {{ $order->client->name ?? 'Sem cliente' }}</h4>
                            <p>{{ $order->status->name ?? 'Sem status' }}</p>
                            <span>Entrega: {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Sem data' }}</span>
                        </a>
                    @empty
                        <div class="pf-delivery-item"><h4>Sem entregas</h4><p>Nenhum pedido encontrado para o filtro atual.</p></div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</div>

@php
    $deadlineSeries = [
        ['label' => 'Atrasados', 'total' => data_get($deliveryHealth, 'late', 0)],
        ['label' => 'Hoje', 'total' => data_get($deliveryHealth, 'today', 0)],
        ['label' => 'Semana', 'total' => data_get($deliveryHealth, 'week', 0)],
        ['label' => 'Concluidos', 'total' => data_get($deliveryHealth, 'completed_ready', 0)],
    ];
@endphp

@push('scripts')
<script>
    (function() {
        const payload = {
            throughput: @json($throughputSeries ?? []),
            flow: @json($statusFlowSeries ?? []),
            time: @json($timeInStatusSeries ?? []),
            deadline: @json($deadlineSeries),
        };

        function syncCustomRange() {
            const periodSelect = document.getElementById('period-select');
            const startField = document.getElementById('start-date-field');
            const endField = document.getElementById('end-date-field');
            if (!periodSelect || !startField || !endField) return;
            const isCustom = periodSelect.value === 'custom';
            startField.classList.toggle('hidden', !isCustom);
            endField.classList.toggle('hidden', !isCustom);
        }

        function syncColumnChips() {
            document.querySelectorAll('.pf-chip').forEach((chip) => {
                const checkbox = chip.querySelector('.column-checkbox');
                chip.classList.toggle('is-selected', !!checkbox?.checked);
            });
        }

        window.selectAllColumns = function() {
            document.querySelectorAll('.column-checkbox').forEach((checkbox) => { checkbox.checked = true; });
            syncColumnChips();
            document.getElementById('dashboard-filter-form')?.submit();
        };

        window.deselectAllColumns = function() {
            document.querySelectorAll('.column-checkbox').forEach((checkbox) => { checkbox.checked = false; });
            syncColumnChips();
            document.getElementById('dashboard-filter-form')?.submit();
        };

        function destroyCharts() {
            if (!window.productionCharts) return;
            Object.values(window.productionCharts).forEach((chart) => {
                if (chart && typeof chart.destroy === 'function') chart.destroy();
            });
            window.productionCharts = {};
        }

        function palette() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                text: isDark ? '#cbd5e1' : '#475569',
                grid: isDark ? 'rgba(148, 163, 184, 0.18)' : '#edf0f6',
                purple: '#7c3aed',
                emerald: '#10b981',
                blue: '#3b82f6',
                amber: '#f59e0b',
                red: '#ef4444',
            };
        }

        function buildCharts() {
            if (typeof Chart === 'undefined') return;
            destroyCharts();
            window.productionCharts = {};
            const colors = palette();

            const throughputCanvas = document.getElementById('productionThroughputChart');
            if (throughputCanvas && payload.throughput.length > 0) {
                const ctx = throughputCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(124, 58, 237, 0.35)');
                gradient.addColorStop(1, 'rgba(124, 58, 237, 0.04)');
                window.productionCharts.throughput = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: payload.throughput.map((item) => item.label),
                        datasets: [{ data: payload.throughput.map((item) => Number(item.total || 0)), borderColor: colors.purple, backgroundColor: gradient, fill: true, borderWidth: 3, tension: 0.35, pointRadius: 3, pointBackgroundColor: colors.purple }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11, weight: '600' } } }, y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.text, precision: 0, font: { size: 11, weight: '600' } } } } }
                });
            }

            const flowCanvas = document.getElementById('productionFlowChart');
            if (flowCanvas && payload.flow.length > 0) {
                window.productionCharts.flow = new Chart(flowCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: { labels: payload.flow.map((item) => item.label), datasets: [{ data: payload.flow.map((item) => Number(item.total || 0)), backgroundColor: payload.flow.map((item) => item.color || colors.purple), borderWidth: 0, cutout: '62%' }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: colors.text, boxWidth: 10, usePointStyle: true, font: { size: 11, weight: '600' } } } } }
                });
            }

            const timeCanvas = document.getElementById('productionTimeChart');
            if (timeCanvas && payload.time.length > 0) {
                const timeData = payload.time.slice(0, 8);
                window.productionCharts.time = new Chart(timeCanvas.getContext('2d'), {
                    type: 'bar',
                    data: { labels: timeData.map((item) => item.label), datasets: [{ label: 'Horas medias', data: timeData.map((item) => Number(item.avg_hours || 0)), backgroundColor: colors.blue, borderRadius: 8, maxBarThickness: 28 }] },
                    options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.text, font: { size: 11, weight: '600' } } }, y: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11, weight: '600' } } } } }
                });
            }

            const deadlineCanvas = document.getElementById('productionDeadlineChart');
            if (deadlineCanvas && payload.deadline.length > 0) {
                window.productionCharts.deadline = new Chart(deadlineCanvas.getContext('2d'), {
                    type: 'bar',
                    data: { labels: payload.deadline.map((item) => item.label), datasets: [{ data: payload.deadline.map((item) => Number(item.total || 0)), backgroundColor: [colors.red, colors.amber, colors.blue, colors.emerald], borderRadius: 8, maxBarThickness: 42 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11, weight: '600' } } }, y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.text, precision: 0, font: { size: 11, weight: '600' } } } } }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            syncCustomRange();
            syncColumnChips();
            document.getElementById('period-select')?.addEventListener('change', syncCustomRange);
            document.querySelectorAll('.column-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', function() {
                    syncColumnChips();
                    setTimeout(function() { document.getElementById('dashboard-filter-form')?.submit(); }, 120);
                });
            });
            buildCharts();
        });

        window.addEventListener('dark-mode-toggled', buildCharts);
        document.addEventListener('ajax-content-loaded', buildCharts);
    })();
</script>
@endpush
@endsection
