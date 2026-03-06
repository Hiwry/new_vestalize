@php
    $dashboardTitle = $dashboardTitle ?? 'Painel Financeiro';
    $periodValue = $period ?? 'month';
    $selectedStoreId = $selectedStoreId ?? request('store_id');
    $selectedVendorId = request('vendor_id');
    $isVendorDashboard = auth()->check() && auth()->user()->isVendedor();
    $isSellerInsights = (bool) ($sellerInsightsEnabled ?? $isVendorDashboard);
    $showQuickActions = (bool) ($showQuickActions ?? false);
    $quickActions = collect($quickActions ?? [])->filter(function ($action) {
        return !empty($action['label']) && !empty($action['href']);
    })->values();
    $paymentFilterValue = strtolower((string) ($effectivePaymentStatusFilter ?? request('payment_status', 'all')));
    if (!in_array($paymentFilterValue, ['all', 'paid', 'pending'], true)) {
        $paymentFilterValue = 'all';
    }

    $storesCollection = collect($stores ?? []);
    $vendorsCollection = collect($vendedores ?? []);
    $recentTransactions = collect($pedidosRecentes ?? collect())->take(7);
    $orderLocationSeries = collect($sellerOrderLocation ?? []);

    $paidOrders = (int) data_get($sellerPaymentSummary ?? [], 'paid', 0);
    $pendingOrders = (int) data_get($sellerPaymentSummary ?? [], 'pending', 0);
    $paymentOrdersTotal = max(0, $paidOrders + $pendingOrders);
    $paidPercent = $paymentOrdersTotal > 0 ? (int) round(($paidOrders / $paymentOrdersTotal) * 100) : 0;
    $pendingPercent = $paymentOrdersTotal > 0 ? (100 - $paidPercent) : 0;

    $totalRevenue = (float) ($totalFaturamento ?? 0);
    $totalPending = (float) ($totalPendente ?? 0);
    $marginPercent = $totalRevenue > 0 ? round((($totalRevenue - $totalPending) / $totalRevenue) * 100) : 74;
    $marginPercent = max(0, min(100, $marginPercent));
    $costPercent = 100 - $marginPercent;
    $formatMonthLabel = function ($month, $format = 'M/y') {
        try {
            return \Carbon\Carbon::createFromFormat('Y-m', (string) $month)
                ->locale('pt_BR')
                ->translatedFormat($format);
        } catch (\Throwable $e) {
            return (string) $month;
        }
    };

    $monthlyRevenueSeries = collect($receitaPorMes ?? $pedidosPorMes ?? [])->sortBy('mes')->map(function ($item) use ($formatMonthLabel) {
        return [
            'label' => $formatMonthLabel($item->mes ?? ''),
            'value' => (float) ($item->faturamento ?? 0),
        ];
    })->values();

    $fallbackMonthlyFromDaily = collect($faturamentoDiario ?? [])
        ->filter(function ($item) {
            return !empty($item->dia);
        })
        ->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->dia)->format('Y-m');
        })
        ->map(function ($items, $month) use ($formatMonthLabel) {
            return [
                'label' => $formatMonthLabel($month),
                'value' => (float) collect($items)->sum('total'),
            ];
        })
        ->values();

    // A visão geral de receita deve ser mensal (pedido do usuário).
    $revenueSeries = $monthlyRevenueSeries->take(-12)->values();
    if ($revenueSeries->isEmpty()) {
        $revenueSeries = $fallbackMonthlyFromDaily->take(-12)->values();
    }

    $monthlyBase = collect($pedidosPorMes ?? [])->sortBy('mes')->values();
    $monthlyByMonth = $monthlyBase->keyBy(function ($item) {
        return $item->mes ?? null;
    });
    $referenceMonth = (string) (($monthlyBase->last()->mes ?? null) ?: now()->format('Y-m'));
    $referenceDate = \Carbon\Carbon::createFromFormat('Y-m', $referenceMonth)->startOfMonth();

    $monthlyComparison = collect(range(2, 0))->map(function ($offset) use ($monthlyByMonth, $referenceDate) {
        $monthKey = $referenceDate->copy()->subMonths($offset)->format('Y-m');
        $source = $monthlyByMonth->get($monthKey);

        return (object) [
            'mes' => $monthKey,
            'faturamento' => (float) ($source->faturamento ?? 0),
        ];
    });

    $monthlyComparison = $monthlyComparison->map(function ($item) use ($marginPercent, $formatMonthLabel) {
        $revenue = (float) ($item->faturamento ?? 0);
        return [
            'month' => $formatMonthLabel($item->mes ?? '', 'M'),
            'revenue' => $revenue,
            'profit' => round($revenue * ($marginPercent / 100), 2),
        ];
    })->values();
@endphp

<style>
    .ft-dashboard {
        --ft-surface-from: #f3f4f8;
        --ft-surface-to: #eceff4;
        --ft-surface-border: #d8dce6;
        --ft-text-primary: #0f172a;
        --ft-text-secondary: #64748b;
        --ft-tab-text: #4b5563;
        --ft-tab-hover-bg: #e7e5ef;
        --ft-tab-hover-text: #111827;
        --ft-tab-active-bg: #ede9fe;
        --ft-tab-active-text: #5b21b6;
        --ft-input-bg: #ffffff;
        --ft-input-border: #d6d9e2;
        --ft-input-text: #334155;
        --ft-card-bg: #ffffff;
        --ft-card-border: #dde2ea;
        --ft-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --ft-kpi-text: #111827;
        --ft-grid-color: #edf0f6;
        --ft-chart-label: #475569;
        --ft-table-head-border: #e5e9f1;
        --ft-table-row-border: #eef1f6;
        --ft-muted-icon: #94a3b8;
        background: linear-gradient(180deg, var(--ft-surface-from) 0%, var(--ft-surface-to) 100%);
        border: 1px solid var(--ft-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--ft-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .ft-dashboard {
        --ft-surface-from: #0f172a;
        --ft-surface-to: #0b1322;
        --ft-surface-border: rgba(148, 163, 184, 0.25);
        --ft-text-primary: #e2e8f0;
        --ft-text-secondary: #94a3b8;
        --ft-tab-text: #94a3b8;
        --ft-tab-hover-bg: rgba(124, 58, 237, 0.2);
        --ft-tab-hover-text: #ddd6fe;
        --ft-tab-active-bg: rgba(124, 58, 237, 0.24);
        --ft-tab-active-text: #e9d5ff;
        --ft-input-bg: #0b1322;
        --ft-input-border: rgba(148, 163, 184, 0.3);
        --ft-input-text: #e2e8f0;
        --ft-card-bg: #111827;
        --ft-card-border: rgba(148, 163, 184, 0.22);
        --ft-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        --ft-kpi-text: #f8fafc;
        --ft-grid-color: rgba(148, 163, 184, 0.2);
        --ft-chart-label: #cbd5e1;
        --ft-table-head-border: rgba(148, 163, 184, 0.25);
        --ft-table-row-border: rgba(148, 163, 184, 0.16);
        --ft-muted-icon: #94a3b8;
        background: linear-gradient(180deg, var(--ft-surface-from) 0%, var(--ft-surface-to) 100%);
        color: var(--ft-text-primary);
        border-color: var(--ft-surface-border);
    }

    .ft-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .ft-topbar > * {
        min-width: 0;
    }

    .ft-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1 1 260px;
    }

    .ft-logo {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .ft-title {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    .ft-subtitle {
        color: var(--ft-text-secondary);
        font-size: 12px;
        font-weight: 600;
    }
    .ft-tools {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
        flex: 1 1 420px;
        max-width: 100%;
        min-width: 0;
    }

    .ft-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        flex: 0 1 auto;
    }

    .ft-action {
        height: 38px;
        border-radius: 12px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: #ffffff !important;
        border: 0;
        transition: transform .18s ease, box-shadow .2s ease, filter .2s ease;
        white-space: nowrap;
    }

    .ft-action:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .ft-action-primary {
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25);
    }

    .ft-action-success {
        background: linear-gradient(135deg, #059669, #10b981);
        box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25);
    }

    .avento-theme .ft-dashboard .ft-search,
    .ft-dashboard .ft-search {
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 10px;
        border: 1px solid var(--ft-input-border);
        background: var(--ft-input-bg);
        color: var(--ft-input-text);
        padding: 0 12px !important;
        width: auto;
        min-width: 150px;
        max-width: 230px;
        flex: 1 1 170px;
        font-size: 13px !important;
        line-height: 38px !important;
    }

    .avento-theme .ft-dashboard .ft-search::placeholder,
    .ft-dashboard .ft-search::placeholder {
        color: var(--ft-text-secondary);
    }

    .avento-theme .ft-dashboard .ft-select,
    .avento-theme .ft-dashboard .ft-date,
    .ft-dashboard .ft-select,
    .ft-dashboard .ft-date {
        box-sizing: border-box;
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 10px !important;
        border: 1px solid var(--ft-input-border) !important;
        background: var(--ft-input-bg) !important;
        font-size: 12px !important;
        color: var(--ft-input-text) !important;
        width: auto;
        min-width: 122px;
        max-width: 170px;
        flex: 0 1 142px;
    }

    .avento-theme .ft-dashboard .ft-select,
    .ft-dashboard .ft-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding: 0 34px 0 12px !important;
        line-height: 38px !important;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .avento-theme .ft-dashboard .ft-date,
    .ft-dashboard .ft-date {
        padding: 0 12px !important;
        line-height: 1.2 !important;
    }

    .avento-theme .ft-dashboard .ft-select[name="period"],
    .ft-dashboard .ft-select[name="period"] {
        min-width: 128px;
    }

    .ft-button {
        height: 38px;
        padding: 0 14px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        color: #fff !important;
        border: 0;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ft-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 14px;
    }

    .ft-card {
        background: var(--ft-card-bg);
        border: 1px solid var(--ft-card-border);
        border-radius: 14px;
        padding: 16px;
        box-shadow: var(--ft-card-shadow);
    }

    .ft-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--ft-text-primary);
    }

    .ft-card-subtitle {
        font-size: 13px;
        color: var(--ft-text-secondary);
        font-weight: 600;
    }

    .ft-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 10px;
    }

    .ft-kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--ft-kpi-text);
    }

    .ft-chart-box {
        position: relative;
        height: 250px;
    }

    .ft-chart-box-sm {
        position: relative;
        height: 220px;
    }

    .ft-donut-wrap {
        position: relative;
        width: 190px;
        height: 190px;
        margin: 8px auto 2px;
    }

    .ft-donut-center {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        font-size: 42px;
        font-weight: 800;
        color: var(--ft-kpi-text);
    }

    .ft-legend {
        margin-top: 8px;
        display: flex;
        justify-content: center;
        gap: 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ft-chart-label);
    }

    .ft-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 99px;
        display: inline-block;
        margin-right: 6px;
    }

    .ft-table-wrap {
        border: 1px solid var(--ft-table-head-border);
        border-radius: 12px;
        background: var(--ft-card-bg);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        overflow-x: auto;
        overflow-y: hidden;
    }

    .ft-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ft-table th {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--ft-text-secondary);
        text-align: left;
        font-weight: 700;
        padding: 10px 8px;
        border-bottom: 1px solid var(--ft-table-head-border);
    }

    .ft-table td {
        font-size: 12px;
        color: var(--ft-text-primary);
        padding: 10px 8px;
        border-bottom: 1px solid var(--ft-table-row-border);
        white-space: nowrap;
    }

    .ft-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ft-badge {
        font-size: 10px;
        font-weight: 700;
        border-radius: 999px;
        padding: 4px 8px;
        display: inline-block;
    }

    .ft-badge-ok {
        color: #166534;
        background: #dcfce7;
    }

    .ft-badge-pending {
        color: #6b21a8;
        background: #f3e8ff;
    }

    .dark .ft-dashboard .ft-badge-ok {
        color: #86efac;
        background: rgba(34, 197, 94, 0.18);
    }

    .dark .ft-dashboard .ft-badge-pending {
        color: #e9d5ff;
        background: rgba(147, 51, 234, 0.24);
    }

    .dark .ft-dashboard .text-slate-500,
    .dark .ft-dashboard .text-slate-400 {
        color: var(--ft-text-secondary) !important;
    }

    @media (max-width: 1140px) {
        .ft-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 1480px) {
        .ft-actions {
            order: 2;
            width: 100%;
            justify-content: flex-start;
        }

        .ft-tools {
            order: 3;
            flex: 1 1 100%;
            justify-content: flex-start;
        }

        .ft-search {
            max-width: none;
        }
    }

    @media (max-width: 640px) {
        .ft-dashboard {
            padding: 14px;
            border-radius: 16px;
        }

        .ft-search {
            width: 100%;
            max-width: none;
        }

        .ft-tools {
            width: 100%;
            justify-content: stretch;
        }

        .ft-actions {
            width: 100%;
        }

        .ft-action {
            width: 100%;
            justify-content: center;
        }

        .ft-search,
        .ft-select,
        .ft-date,
        .ft-button {
            width: 100%;
            max-width: none;
            flex: 1 1 100%;
        }
    }
</style>

<div class="ft-dashboard">
    <div class="ft-topbar">
        <div class="ft-brand">
            <div class="ft-logo">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="ft-title">{{ $dashboardTitle }}</p>
                <p class="ft-subtitle">Painel financeiro com visão consolidada</p>
            </div>
        </div>

        @if($showQuickActions && $quickActions->isNotEmpty())
            <div class="ft-actions">
                @foreach($quickActions as $action)
                    @php
                        $variant = strtolower((string) ($action['variant'] ?? 'primary'));
                        $variantClass = $variant === 'success' ? 'ft-action-success' : 'ft-action-primary';
                    @endphp
                    <a href="{{ $action['href'] }}" class="ft-action {{ $variantClass }}">
                        @if(!empty($action['icon']))
                            <i class="fa-solid {{ $action['icon'] }}"></i>
                        @endif
                        <span>{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ url()->current() }}" class="ft-tools">
            <input type="text" class="ft-search" placeholder="Buscar..." aria-label="Buscar">

            <select name="period" class="ft-select" onchange="this.form.submit()">
                <option value="today" {{ $periodValue === 'today' ? 'selected' : '' }}>Hoje</option>
                <option value="week" {{ $periodValue === 'week' ? 'selected' : '' }}>Semana</option>
                <option value="month" {{ $periodValue === 'month' ? 'selected' : '' }}>Mês</option>
                <option value="year" {{ $periodValue === 'year' ? 'selected' : '' }}>Ano</option>
                <option value="custom" {{ $periodValue === 'custom' ? 'selected' : '' }}>Personalizado</option>
            </select>

            @if($periodValue === 'custom')
                <input type="date" name="start_date" class="ft-date" value="{{ isset($startDate) ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : '' }}">
                <input type="date" name="end_date" class="ft-date" value="{{ isset($endDate) ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : '' }}">
            @endif

            @if($storesCollection->count() > 1)
                <select name="store_id" class="ft-select" onchange="this.form.submit()">
                    <option value="">Todas as unidades</option>
                    @foreach($storesCollection as $store)
                        <option value="{{ $store->id }}" {{ (string)$selectedStoreId === (string)$store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            @elseif(!empty($selectedStoreId))
                <input type="hidden" name="store_id" value="{{ $selectedStoreId }}">
            @endif

            @if($vendorsCollection->count() > 0 && !auth()->user()->isVendedor())
                <select name="vendor_id" class="ft-select" onchange="this.form.submit()">
                    <option value="">Todos os vendedores</option>
                    @foreach($vendorsCollection as $vendor)
                        <option value="{{ $vendor->id }}" {{ (string)$selectedVendorId === (string)$vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            @elseif(!empty($selectedVendorId))
                <input type="hidden" name="vendor_id" value="{{ $selectedVendorId }}">
            @endif

            @if($isSellerInsights)
                <select name="payment_status" class="ft-select" onchange="this.form.submit()">
                    <option value="all" {{ $paymentFilterValue === 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="paid" {{ $paymentFilterValue === 'paid' ? 'selected' : '' }}>Pagos</option>
                    <option value="pending" {{ $paymentFilterValue === 'pending' ? 'selected' : '' }}>Pendentes</option>
                </select>
            @endif

            <button type="submit" class="ft-button">
                <i class="fa-solid fa-sliders"></i>
                Aplicar
            </button>
        </form>
    </div>

    <div class="ft-grid">
        <section id="ft-overview" class="ft-card">
            <div class="ft-card-head">
                <div>
                    <p class="ft-card-title">Visão Geral da Receita</p>
                    <p class="ft-card-subtitle">Evolução recente do faturamento</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">Receita Total</p>
                    <p class="ft-kpi-value">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="ft-chart-box">
                <canvas id="ftRevenueChart"></canvas>
            </div>
        </section>

        <section id="ft-transactions" class="ft-card">
            <div class="ft-card-head">
                <div>
                    @if($isSellerInsights)
                        <p class="ft-card-title">Situação de Pagamento</p>
                        <p class="ft-card-subtitle">Resumo de pagos x pendentes</p>
                    @else
                        <p class="ft-card-title">Margem Financeira</p>
                        <p class="ft-card-subtitle">Receita líquida estimada</p>
                    @endif
                </div>
                <i class="fa-solid fa-ellipsis text-slate-400"></i>
            </div>
            <div class="ft-donut-wrap">
                <canvas id="ftMarginChart"></canvas>
                <div class="ft-donut-center">{{ $isSellerInsights ? $paidPercent : $marginPercent }}%</div>
            </div>
            <div class="ft-legend">
                @if($isSellerInsights)
                    <span><span class="ft-legend-dot" style="background:#10b981;"></span>Pagos: {{ $paidOrders }}</span>
                    <span><span class="ft-legend-dot" style="background:#f59e0b;"></span>Pendentes: {{ $pendingOrders }}</span>
                @else
                    <span><span class="ft-legend-dot" style="background:#6d28d9;"></span>Margem: {{ $marginPercent }}%</span>
                    <span><span class="ft-legend-dot" style="background:#c4b5fd;"></span>Custo: {{ $costPercent }}%</span>
                @endif
            </div>
        </section>

        <section id="ft-analytics" class="ft-card">
            <div class="ft-card-head">
                <div>
                    <p class="ft-card-title">Transações Recentes</p>
                    <p class="ft-card-subtitle">Pedidos e movimentações recentes</p>
                </div>
                <i class="fa-solid fa-ellipsis text-slate-400"></i>
            </div>
            <div class="ft-table-wrap">
                <table class="ft-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Canal</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            @php
                                $statusText = $tx->status->name ?? 'Em aberto';
                                $isSuccess = \Illuminate\Support\Str::contains(strtolower($statusText), ['entreg', 'pag', 'finaliz', 'concl']);
                            @endphp
                            <tr>
                                <td>{{ $tx->created_at ? $tx->created_at->format('d/m/Y') : '-' }}</td>
                                <td>#{{ $tx->id }} {{ $tx->client->name ?? 'Sem cliente' }}</td>
                                <td>{{ $tx->is_pdv ? 'PDV' : 'Online' }}</td>
                                <td>R$ {{ number_format((float)($tx->total ?? 0), 2, ',', '.') }}</td>
                                <td>
                                    <span class="ft-badge {{ $isSuccess ? 'ft-badge-ok' : 'ft-badge-pending' }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Nenhuma transação encontrada para o período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ft-card">
            <div class="ft-card-head">
                <div>
                    @if($isSellerInsights)
                        <p class="ft-card-title">Onde Estão os Pedidos</p>
                        <p class="ft-card-subtitle">Etapas com maior volume</p>
                    @else
                        <p class="ft-card-title">Comparação Mensal</p>
                        <p class="ft-card-subtitle">Receita x Margem (3 últimos meses)</p>
                    @endif
                </div>
                <i class="fa-solid fa-ellipsis text-slate-400"></i>
            </div>
            <div class="ft-chart-box-sm">
                @if($isSellerInsights)
                    <canvas id="ftLocationChart"></canvas>
                    @if($orderLocationSeries->isEmpty())
                        <p class="text-xs text-slate-500 mt-2">Sem dados de pedidos para o filtro atual.</p>
                    @endif
                @else
                    <canvas id="ftMonthlyChart"></canvas>
                @endif
            </div>
        </section>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const chartPayload = {
                revenue: @json($revenueSeries),
                monthly: @json($monthlyComparison),
                isVendor: @json($isSellerInsights),
                margin: {
                    value: @json($marginPercent),
                    cost: @json($costPercent),
                },
                payment: {
                    paid: @json($paidOrders),
                    pending: @json($pendingOrders),
                    paidPercent: @json($paidPercent),
                    pendingPercent: @json($pendingPercent),
                },
                location: @json($orderLocationSeries),
            };

            window.ftDashboardPayload = chartPayload;

            function ensureChartsRegistry() {
                if (!window.ftDashboardCharts) {
                    window.ftDashboardCharts = {};
                }
                return window.ftDashboardCharts;
            }

            function safeDestroy(chart) {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            }

            function chartPalette() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    return {
                        lineFillStart: 'rgba(139, 92, 246, 0.42)',
                        lineFillEnd: 'rgba(139, 92, 246, 0.06)',
                        lineColor: '#8b5cf6',
                        pointBorder: '#0f172a',
                        gridColor: 'rgba(148, 163, 184, 0.22)',
                        tickColor: '#cbd5e1',
                        legendColor: '#cbd5e1',
                        marginSecondary: '#a78bfa',
                        paidColor: '#10b981',
                        pendingColor: '#f59e0b',
                    };
                }

                return {
                    lineFillStart: 'rgba(109, 40, 217, 0.32)',
                    lineFillEnd: 'rgba(109, 40, 217, 0.03)',
                    lineColor: '#6d28d9',
                    pointBorder: '#ffffff',
                    gridColor: '#edf0f6',
                    tickColor: '#475569',
                    legendColor: '#334155',
                    marginSecondary: '#c4b5fd',
                    paidColor: '#10b981',
                    pendingColor: '#f59e0b',
                };
            }

            function buildCharts() {
                if (typeof Chart === 'undefined') {
                    return;
                }

                const payload = window.ftDashboardPayload || chartPayload;
                const palette = chartPalette();
                const registry = ensureChartsRegistry();
                safeDestroy(registry.revenue);
                safeDestroy(registry.margin);
                safeDestroy(registry.monthly);
                safeDestroy(registry.location);
                registry.revenue = null;
                registry.margin = null;
                registry.monthly = null;
                registry.location = null;

                const revenueCanvas = document.getElementById('ftRevenueChart');
                if (revenueCanvas && payload.revenue.length > 0) {
                    const ctx = revenueCanvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                    gradient.addColorStop(0, palette.lineFillStart);
                    gradient.addColorStop(1, palette.lineFillEnd);
                    const revenueLabels = payload.revenue.map(item => item.label);
                    const revenueValues = payload.revenue.map(item => Number(item.value || 0));
                    const compactTicks = revenueLabels.length > 16;
                    const pointRadius = revenueLabels.length > 24 ? 1.6 : 3;
                    const pointHoverRadius = revenueLabels.length > 24 ? 3.2 : 5;

                    registry.revenue = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: revenueLabels,
                            datasets: [{
                                data: revenueValues,
                                borderColor: palette.lineColor,
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.38,
                                spanGaps: true,
                                fill: true,
                                pointRadius,
                                pointHoverRadius,
                                pointBackgroundColor: palette.lineColor,
                                pointBorderColor: palette.pointBorder,
                                pointBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: {
                                    grid: { color: palette.gridColor },
                                    ticks: {
                                        color: palette.tickColor,
                                        font: { size: 11, weight: '600' },
                                        autoSkip: true,
                                        maxTicksLimit: compactTicks ? 8 : 12,
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: palette.gridColor },
                                    ticks: {
                                        color: palette.tickColor,
                                        font: { size: 11, weight: '600' },
                                        maxTicksLimit: 6,
                                        callback: function(value) {
                                            return 'R$ ' + Number(value).toLocaleString('pt-BR');
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                const marginCanvas = document.getElementById('ftMarginChart');
                if (marginCanvas) {
                    const marginLabels = payload.isVendor ? ['Pagos', 'Pendentes'] : ['Margem', 'Custo'];
                    const marginData = payload.isVendor
                        ? [Number(payload.payment.paid || 0), Number(payload.payment.pending || 0)]
                        : [Number(payload.margin.value || 0), Number(payload.margin.cost || 0)];
                    const marginColors = payload.isVendor
                        ? [palette.paidColor, palette.pendingColor]
                        : [palette.lineColor, palette.marginSecondary];

                    registry.margin = new Chart(marginCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: marginLabels,
                            datasets: [{
                                data: marginData,
                                backgroundColor: marginColors,
                                borderWidth: 0,
                                hoverOffset: 4,
                                cutout: '64%',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                }

                if (payload.isVendor) {
                    const locationCanvas = document.getElementById('ftLocationChart');
                    if (locationCanvas && payload.location.length > 0) {
                        registry.location = new Chart(locationCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: payload.location.map(item => item.label),
                                datasets: [{
                                    label: 'Pedidos',
                                    data: payload.location.map(item => Number(item.total || 0)),
                                    backgroundColor: palette.lineColor,
                                    borderRadius: 8,
                                    maxBarThickness: 32,
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
                                        ticks: { color: palette.tickColor, font: { size: 11, weight: '600' } }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: palette.gridColor },
                                        ticks: {
                                            precision: 0,
                                            color: palette.tickColor,
                                            font: { size: 11, weight: '600' }
                                        }
                                    }
                                }
                            }
                        });
                    }
                } else {
                    const monthlyCanvas = document.getElementById('ftMonthlyChart');
                    if (monthlyCanvas && payload.monthly.length > 0) {
                        registry.monthly = new Chart(monthlyCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: payload.monthly.map(item => item.month),
                                datasets: [
                                    {
                                        label: 'Receita',
                                        data: payload.monthly.map(item => Number(item.revenue || 0)),
                                        backgroundColor: palette.lineColor,
                                        borderRadius: 8,
                                        maxBarThickness: 24,
                                    },
                                    {
                                        label: 'Lucro',
                                        data: payload.monthly.map(item => Number(item.profit || 0)),
                                        backgroundColor: palette.marginSecondary,
                                        borderRadius: 8,
                                        maxBarThickness: 24,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: {
                                            boxWidth: 10,
                                            usePointStyle: true,
                                            color: palette.legendColor,
                                            font: { size: 11, weight: '600' }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: palette.tickColor, font: { size: 11, weight: '600' } }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: palette.gridColor },
                                        ticks: {
                                            color: palette.tickColor,
                                            font: { size: 11, weight: '600' },
                                            callback: function(value) {
                                                return 'R$ ' + Number(value).toLocaleString('pt-BR');
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }

            function ensureChartReady(callback) {
                if (typeof window.ensureChartJsLoaded === 'function') {
                    window.ensureChartJsLoaded(callback);
                    return;
                }
                if (typeof Chart !== 'undefined') {
                    callback();
                    return;
                }
                setTimeout(() => ensureChartReady(callback), 120);
            }

            function scheduleRender() {
                ensureChartReady(buildCharts);
            }
            window.scheduleFtDashboardRender = scheduleRender;

            if (!window.ftDashboardThemeBindingsRegistered) {
                window.ftDashboardThemeBindingsRegistered = true;

                const rerender = function() {
                    if (typeof window.scheduleFtDashboardRender === 'function') {
                        window.scheduleFtDashboardRender();
                    }
                };

                document.addEventListener('ajax-content-loaded', rerender);
                document.addEventListener('content-loaded', rerender);
                window.addEventListener('theme-changed', rerender);
                window.addEventListener('dark-mode-toggled', rerender);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    scheduleRender();
                });
            } else {
                scheduleRender();
            }
        })();
    </script>
@endpush

