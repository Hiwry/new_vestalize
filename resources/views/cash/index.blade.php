@extends('layouts.admin')

@section('content')
<style>
    /* ─── Cash Page Design System ─── */
    .cf-root {
        --cf-from: #f3f4f8; --cf-to: #eceff4; --cf-border: #d8dce6;
        --cf-text: #0f172a; --cf-muted: #64748b;
        --cf-input-bg: #ffffff; --cf-input-border: #d6d9e2; --cf-input-text: #334155;
        --cf-card-bg: #ffffff; --cf-card-border: #dde2ea;
        --cf-card-shadow: 0 8px 20px rgba(15,23,42,.05);
        --cf-row-border: #eef1f6;
        background: linear-gradient(180deg, var(--cf-from), var(--cf-to));
        border: 1px solid var(--cf-border); border-radius: 20px;
        padding: 20px; color: var(--cf-text);
        box-shadow: 0 20px 50px rgba(15,23,42,.08);
    }
    .dark .cf-root {
        --cf-from: #0f172a; --cf-to: #0b1322; --cf-border: rgba(148,163,184,.25);
        --cf-text: #e2e8f0; --cf-muted: #94a3b8;
        --cf-input-bg: #0b1322; --cf-input-border: rgba(148,163,184,.3); --cf-input-text: #e2e8f0;
        --cf-card-bg: #111827; --cf-card-border: rgba(148,163,184,.22);
        --cf-card-shadow: 0 18px 38px rgba(2,6,23,.55);
        --cf-row-border: rgba(148,163,184,.16);
    }
    /* Layout helpers */
    .cf-topbar { display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:18px; }
    .cf-brand  { display:flex; align-items:center; gap:12px; }
    .cf-logo   { width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#059669,#10b981); color:#fff !important; display:inline-flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
    .cf-logo i { color:#fff !important; }
    .cf-title  { font-size:18px; font-weight:700; letter-spacing:-.015em; color:var(--cf-text); }
    .cf-sub    { color:var(--cf-muted); font-size:12px; font-weight:600; margin-top:2px; }
    .cf-actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    /* Buttons */
    .cf-btn { height:36px; border-radius:10px; padding:0 14px; display:inline-flex; align-items:center; gap:7px; font-size:13px; font-weight:700; cursor:pointer; border:0; text-decoration:none; transition:transform .18s,filter .18s; color:#fff !important; }
    .cf-btn i,.cf-btn span { color:#fff !important; }
    .cf-btn:hover { transform:translateY(-1px); filter:brightness(1.06); }
    .cf-btn-green  { background:linear-gradient(135deg,#059669,#10b981); box-shadow:0 8px 18px rgba(5,150,105,.22); }
    .cf-btn-blue   { background:linear-gradient(135deg,#1d4ed8,#3b82f6); box-shadow:0 8px 18px rgba(59,130,246,.22); }
    .cf-btn-purple { background:linear-gradient(135deg,#6d28d9,#7c3aed); box-shadow:0 8px 18px rgba(109,40,217,.22); }
    .cf-btn-amber  { background:linear-gradient(135deg,#d97706,#f59e0b); box-shadow:0 8px 18px rgba(217,119,6,.22); }
    .cf-btn-muted  { background:var(--cf-input-bg); color:var(--cf-input-text) !important; border:1px solid var(--cf-input-border); box-shadow:none; }
    .cf-btn-muted i,.cf-btn-muted span { color:var(--cf-input-text) !important; }
    /* KPIs */
    .cf-kpis { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:18px; }
    .cf-kpi  { flex:1 1 180px; min-height:110px; background:var(--cf-card-bg); border:1px solid var(--cf-card-border); border-radius:14px; padding:16px; box-shadow:var(--cf-card-shadow); }
    .cf-kpi-icon  { width:42px; height:42px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; color:#fff !important; font-size:16px; margin-bottom:10px; }
    .cf-kpi-icon i { color:#fff !important; }
    .cf-kpi-value { font-size:22px; font-weight:800; letter-spacing:-.03em; color:var(--cf-text); line-height:1.1; }
    .cf-kpi-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--cf-muted); font-weight:700; margin-top:5px; }
    /* Filter */
    .cf-filter { background:var(--cf-card-bg); border:1px solid var(--cf-card-border); border-radius:14px; padding:16px; margin-bottom:18px; box-shadow:var(--cf-card-shadow); }
    .cf-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; align-items:end; }
    .cf-label { display:block; font-size:10px; font-weight:800; color:var(--cf-text); text-transform:uppercase; letter-spacing:.06em; margin-bottom:5px; }
    .cf-input { width:100%; height:40px; border-radius:10px; border:1px solid var(--cf-input-border); background:var(--cf-input-bg) !important; color:var(--cf-input-text) !important; padding:0 12px; font-size:13px; font-weight:600; appearance:auto; -webkit-appearance:auto; }
    .cf-input:focus { outline:none; border-color:#6d28d9; box-shadow:0 0 0 2px rgba(109,40,217,.15); }
    select.cf-input { padding-right:28px; }
    /* View toggle */
    .cf-view-bar  { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px; }
    .cf-view-toggle { display:flex; background:var(--cf-input-bg); border:1px solid var(--cf-input-border); border-radius:10px; padding:3px; gap:3px; }
    .cf-view-btn { height:30px; padding:0 12px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:0; background:transparent; color:var(--cf-muted); transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
    .cf-view-btn.is-active { background:linear-gradient(135deg,#6d28d9,#7c3aed); color:#fff !important; box-shadow:0 4px 10px rgba(109,40,217,.25); }
    .cf-view-btn.is-active i { color:#fff !important; }
    .cf-section-title { font-size:14px; font-weight:700; color:var(--cf-text); }
    /* Grid */
    .cf-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(255px,1fr)); gap:14px; }
    /* Tx card (grid view) */
    .cf-tx-card { background:var(--cf-card-bg); border:1px solid var(--cf-card-border); border-radius:12px; padding:14px; display:flex; flex-direction:column; gap:8px; box-shadow:var(--cf-card-shadow); transition:border-color .15s,box-shadow .15s; }
    .cf-tx-card:hover { border-color:rgba(109,40,217,.3); box-shadow:0 6px 20px rgba(109,40,217,.1); }
    .cf-tx-type { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; }
    .cf-tx-in  { background:rgba(16,185,129,.14); color:#047857; } .dark .cf-tx-in  { color:#6ee7b7; }
    .cf-tx-out { background:rgba(239,68,68,.14);  color:#b91c1c; } .dark .cf-tx-out { color:#fca5a5; }
    .cf-tx-amt-in  { font-size:20px; font-weight:800; color:#059669; } .dark .cf-tx-amt-in  { color:#34d399; }
    .cf-tx-amt-out { font-size:20px; font-weight:800; color:#dc2626; } .dark .cf-tx-amt-out { color:#f87171; }
    .cf-tx-meta { font-size:11px; color:var(--cf-muted); font-weight:600; }
    /* Kanban */
    .cf-kanban { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
    @media (max-width:1024px) { .cf-kanban { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:640px)  { .cf-kanban { grid-template-columns:1fr; } }
    .cf-col { background:var(--cf-card-bg); border:1px solid var(--cf-card-border); border-radius:14px; overflow:hidden; }
    .cf-col-head { padding:12px 14px; border-bottom:1px solid var(--cf-row-border); }
    .cf-col-title { font-size:13px; font-weight:800; display:flex; align-items:center; gap:6px; }
    .cf-col-subtitle { font-size:11px; color:var(--cf-muted); font-weight:600; margin-top:2px; }
    .cf-col-count { display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:20px; border-radius:5px; font-size:11px; font-weight:800; background:rgba(15,23,42,.08); color:var(--cf-text); }
    .dark .cf-col-count { background:rgba(255,255,255,.09); }
    .cf-col-body { padding:10px; min-height:20rem; max-height:36rem; overflow-y:auto; display:grid; gap:8px; transition:background .18s,box-shadow .18s; align-content:start; }
    .cf-col-body.drop-active { background:rgba(109,40,217,.08); box-shadow:inset 0 0 0 2px rgba(109,40,217,.3); }
    .dark .cf-col-body.drop-active { background:rgba(109,40,217,.15); box-shadow:inset 0 0 0 2px rgba(139,92,246,.4); }
    /* column accent heads */
    .cf-col--pendente  .cf-col-head { background:linear-gradient(90deg,rgba(245,158,11,.14),rgba(251,191,36,.07)); }
    .cf-col--confirmado .cf-col-head { background:linear-gradient(90deg,rgba(16,185,129,.14),rgba(52,211,153,.07)); }
    .cf-col--cancelado .cf-col-head { background:linear-gradient(90deg,rgba(239,68,68,.14),rgba(248,113,113,.07)); }
    .cf-col--sangria   .cf-col-head { background:linear-gradient(90deg,rgba(139,92,246,.14),rgba(167,139,250,.07)); }
    .dark .cf-col--pendente  .cf-col-head { background:linear-gradient(90deg,rgba(180,83,9,.3),rgba(146,64,14,.18)); }
    .dark .cf-col--confirmado .cf-col-head { background:linear-gradient(90deg,rgba(5,150,105,.28),rgba(16,185,129,.14)); }
    .dark .cf-col--cancelado .cf-col-head { background:linear-gradient(90deg,rgba(185,28,28,.3),rgba(239,68,68,.14)); }
    .dark .cf-col--sangria   .cf-col-head { background:linear-gradient(90deg,rgba(109,40,217,.3),rgba(139,92,246,.14)); }
    /* Empty state */
    .cf-empty { text-align:center; padding:32px 12px; }
    .cf-empty-icon { width:46px; height:46px; border-radius:13px; margin:0 auto 10px; display:inline-flex; align-items:center; justify-content:center; background:rgba(109,40,217,.1); color:#6d28d9; font-size:18px; }
    .cf-empty-text { font-size:12px; color:var(--cf-muted); font-weight:600; }
    /* Alert */
    .cf-alert { border-radius:10px; padding:12px 14px; font-size:12px; font-weight:700; margin-bottom:14px; }
    .cf-alert-success { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.25); color:#065f46; }
    .dark .cf-alert-success { color:#a7f3d0; }
    /* Modal */
    .cf-modal { position:fixed; inset:0; z-index:60; display:none; align-items:center; justify-content:center; padding:16px; background:rgba(15,23,42,.62); backdrop-filter:blur(6px); }
    .cf-modal.is-open { display:flex; }
    .cf-modal-card { width:min(100%,700px); max-height:90vh; overflow-y:auto; background:var(--cf-card-bg); border:1px solid var(--cf-card-border); border-radius:18px; padding:22px; box-shadow:0 24px 60px rgba(0,0,0,.4); }
    .cf-modal-card.wide { width:min(100%,940px); }
    .cf-modal-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; }
    .cf-modal-title { font-size:17px; font-weight:800; color:var(--cf-text); }
    .cf-modal-sub { font-size:12px; color:var(--cf-muted); font-weight:600; margin-top:3px; }
    .cf-modal-close { width:34px; height:34px; border-radius:10px; border:1px solid var(--cf-input-border); background:var(--cf-input-bg); color:var(--cf-muted); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; }
    .cf-modal-close:hover { color:var(--cf-text); }
    /* Report KPI */
    .cf-rpt-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
    @media (max-width:520px) { .cf-rpt-grid { grid-template-columns:1fr; } }
    .cf-rpt-kpi { background:var(--cf-input-bg); border:1px solid var(--cf-input-border); border-radius:12px; padding:14px 16px; display:flex; flex-direction:column; gap:4px; }
    .cf-rpt-kpi-icon { width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; font-size:13px; margin-bottom:6px; flex-shrink:0; }
    .cf-rpt-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--cf-muted) !important; font-weight:700; }
    .cf-rpt-value { font-size:20px; font-weight:800; letter-spacing:-.02em; line-height:1.1; }
    .cf-rpt-section { margin-bottom:12px; background:var(--cf-input-bg); border:1px solid var(--cf-input-border); border-radius:12px; overflow:hidden; }
    .cf-rpt-section-hd { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; color:var(--cf-text) !important; padding:10px 12px; border-bottom:1px solid var(--cf-card-border); display:flex; align-items:center; gap:7px; background:rgba(148,163,184,.04); }
    .cf-rpt-section-hd i { opacity:.6; }
    .cf-rpt-table { width:100%; border-collapse:collapse; font-size:12px; }
    .cf-rpt-table th { text-align:left; padding:7px 12px; color:var(--cf-muted); font-weight:800; text-transform:uppercase; letter-spacing:.06em; font-size:10px; }
    .cf-rpt-table td { padding:9px 12px; border-bottom:1px solid var(--cf-row-border); color:var(--cf-text) !important; font-weight:600; }
    .cf-rpt-table tr:last-child td { border-bottom:0; }
    .cf-rpt-table tbody tr:hover td { background:rgba(148,163,184,.05); }
    .cf-rpt-pill { display:inline-flex; align-items:center; gap:6px; background:var(--cf-input-bg); border:1px solid var(--cf-input-border); border-radius:20px; padding:5px 12px; font-size:11px; font-weight:700; color:var(--cf-text) !important; margin-bottom:14px; }
    .cf-rpt-pill i { color:var(--cf-muted); font-size:10px; }
    @media (max-width:640px) {
        .cf-root { padding:14px; border-radius:14px; }
        .cf-topbar { flex-direction:column; align-items:flex-start; }
        .cf-actions { width:100%; }
        .cf-btn { flex:1; justify-content:center; }
    }
    /* Legacy cards keep dragging visual */
    .cash-kanban-card.cash-card-dragging { opacity:.45; transform:scale(.98); }
</style>

<div class="max-w-[1800px] mx-auto">
<div class="cf-root">

    {{-- ─── Topbar ─── --}}
    <div class="cf-topbar">
        <div class="cf-brand">
            <span class="cf-logo"><i class="fa-solid fa-cash-register"></i></span>
            <div>
                <div class="cf-title">Controle de Caixa</div>
                <div class="cf-sub">Gerencie transações, sangrias e relatórios em tempo real.</div>
            </div>
        </div>
        <div class="cf-actions">
            <button onclick="window.openReportSimplified()" class="cf-btn cf-btn-green">
                <i class="fa-solid fa-chart-bar"></i><span>Resumo</span>
            </button>
            <button onclick="window.openReportDetailed()" class="cf-btn cf-btn-blue">
                <i class="fa-solid fa-file-lines"></i><span>Detalhado</span>
            </button>
            {{-- Fechamento de Caixa PDF --}}
            <div x-data="{ openFech: false, fechPeriod: 'day', fechDate: '{{ now()->format('Y-m-d') }}' }" class="relative">
                <button @click="openFech = !openFech" class="cf-btn cf-btn-amber">
                    <i class="fa-solid fa-file-pdf"></i><span>Fechamento PDF</span>
                </button>
                <div x-show="openFech" x-cloak @click.away="openFech = false"
                     class="absolute right-0 top-10 z-50 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-4 w-64">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">FECHAMENTO DE CAIXA</p>
                    <div class="mb-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Período</label>
                        <select x-model="fechPeriod" class="w-full mt-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="day">Diário</option>
                            <option value="week">Semanal</option>
                            <option value="month">Mensal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Data de referência</label>
                        <input type="date" x-model="fechDate" class="w-full mt-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <a :href="`{{ route('cash.fechamento.pdf') }}?period=${fechPeriod}&date=${fechDate}`" target="_blank"
                       class="cf-btn cf-btn-purple w-full justify-center">
                        <i class="fa-solid fa-file-pdf"></i><span>Gerar PDF</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('cash.create') }}" class="cf-btn cf-btn-purple">
                <i class="fa-solid fa-plus"></i><span>Nova Transação</span>
            </a>
            @if(isset($pendentesVendaTotal) && $pendentesVendaTotal > 0)
            <a href="{{ route('cash.approvals.index') }}" class="cf-btn cf-btn-amber">
                <i class="fa-solid fa-clock"></i><span>Aprovações ({{ $pendentesVendaTotal }})</span>
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="cf-alert cf-alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ─── KPIs ─── --}}
    <div class="cf-kpis">
        <div class="cf-kpi">
            <span class="cf-kpi-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);"><i class="fa-solid fa-wallet"></i></span>
            <div class="cf-kpi-value {{ $saldoAtual < 0 ? 'text-red-500' : '' }}">R$ {{ number_format($saldoAtual, 2, ',', '.') }}</div>
            <div class="cf-kpi-label">Saldo Atual em Caixa</div>
        </div>
        <div class="cf-kpi">
            <span class="cf-kpi-icon" style="background:linear-gradient(135deg,#059669,#10b981);"><i class="fa-solid fa-arrow-trend-up"></i></span>
            <div class="cf-kpi-value" style="color:#059669;">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
            <div class="cf-kpi-label">Entradas no Período</div>
        </div>
        <div class="cf-kpi">
            <span class="cf-kpi-icon" style="background:linear-gradient(135deg,#dc2626,#ef4444);"><i class="fa-solid fa-arrow-trend-down"></i></span>
            <div class="cf-kpi-value" style="color:#dc2626;">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</div>
            <div class="cf-kpi-label">Saídas no Período</div>
        </div>
        <div class="cf-kpi">
            <span class="cf-kpi-icon" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);"><i class="fa-solid fa-hourglass-half"></i></span>
            <div class="cf-kpi-value">{{ $pendentes->count() }}</div>
            <div class="cf-kpi-label">Transações Pendentes</div>
        </div>
    </div>

    {{-- ─── Filtros ─── --}}
    <div class="cf-filter">
        <form method="GET" action="{{ route('cash.index') }}" class="cf-filter-grid">
            <div>
                <label class="cf-label">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="cf-input">
            </div>
            <div>
                <label class="cf-label">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="cf-input">
            </div>
            <div>
                <label class="cf-label">Tipo</label>
                <select name="type" class="cf-input">
                    <option value="all"    {{ $type === 'all'    ? 'selected' : '' }}>Todos</option>
                    <option value="entrada"{{ $type === 'entrada'? 'selected' : '' }}>Entradas</option>
                    <option value="saida"  {{ $type === 'saida'  ? 'selected' : '' }}>Saídas</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end;">
                <button type="submit" class="cf-btn cf-btn-purple" style="height:36px;flex:1;">
                    <i class="fa-solid fa-magnifying-glass"></i><span>Filtrar</span>
                </button>
                <a href="{{ route('cash.index') }}" class="cf-btn cf-btn-muted" style="height:36px;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- ─── View toggle ─── --}}
    <div class="cf-view-bar">
        <div class="cf-section-title">Transações</div>
        <div class="cf-view-toggle">
            <button id="btn-grid"   class="cf-view-btn is-active" onclick="window.toggleView('grid')">
                <i class="fa-solid fa-grip"></i> Grid
            </button>
            <button id="btn-kanban" class="cf-view-btn"           onclick="window.toggleView('kanban')">
                <i class="fa-solid fa-table-columns"></i> Kanban
            </button>
        </div>
    </div>

    {{-- ─── Grid View ─── --}}
    <div id="view-grid" class="cf-grid">
        @forelse($confirmadas->take(12) as $transaction)
        @php
            $pm = $transaction->payment_methods ?? [];
            if (!is_array($pm) && !empty($pm)) {
                $dec = json_decode($pm, true);
                $pm  = (json_last_error() === JSON_ERROR_NONE && is_array($dec)) ? $dec : [];
            }
            if (empty($pm) && $transaction->payment_method) {
                $pm = [['method' => $transaction->payment_method, 'amount' => $transaction->amount]];
            }
        @endphp
        <div class="cf-tx-card">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span class="cf-tx-type {{ $transaction->type === 'entrada' ? 'cf-tx-in' : 'cf-tx-out' }}">
                    <i class="fa-solid {{ $transaction->type === 'entrada' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ $transaction->type === 'entrada' ? 'Entrada' : 'Saída' }}
                </span>
                <span class="cf-tx-meta">{{ $transaction->transaction_date->format('d/m H:i') }}</span>
            </div>
            <div class="{{ $transaction->type === 'entrada' ? 'cf-tx-amt-in' : 'cf-tx-amt-out' }}">
                {{ $transaction->type === 'entrada' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
            </div>
            <div style="font-size:13px;font-weight:700;color:var(--cf-text);">{{ $transaction->category }}</div>
            @if($transaction->description)
            <div class="cf-tx-meta">{{ Str::limit($transaction->description, 65) }}</div>
            @endif
            <div style="display:flex;flex-direction:column;gap:3px;">
                @foreach($pm as $m)
                <div style="display:flex;justify-content:space-between;font-size:11px;">
                    <span class="cf-tx-meta" style="text-transform:capitalize;">{{ str_replace('_',' ', $m['method'] ?? '-') }}</span>
                    <span style="font-weight:700;color:var(--cf-text);">R$ {{ number_format($m['amount'] ?? 0, 2, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:8px;border-top:1px solid var(--cf-row-border);">
                <span class="cf-tx-meta">{{ $transaction->user_name ?? 'Sistema' }}</span>
                <div style="display:flex;gap:8px;">
                    @if($transaction->order_id)
                    <a href="{{ route('orders.show', $transaction->order_id) }}" style="font-size:11px;color:#6d28d9;font-weight:700;">#{{ str_pad($transaction->order_id, 5, '0', STR_PAD_LEFT) }}</a>
                    @endif
                    <a href="{{ route('cash.edit', $transaction) }}" style="font-size:11px;color:#6d28d9;font-weight:700;">Editar</a>
                </div>
            </div>
        </div>
        @empty
        <div class="cf-empty" style="grid-column:1/-1;">
            <div class="cf-empty-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="cf-empty-text">Nenhuma transação confirmada no período</div>
            @if(isset($pendentesVenda) && $pendentesVenda->count() > 0)
            <a href="{{ route('cash.approvals.index') }}" class="cf-btn cf-btn-amber" style="margin-top:12px;display:inline-flex;">
                <i class="fa-solid fa-clock"></i><span>Aprovações PDV ({{ $pendentesVenda->count() }})</span>
            </a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- ─── Kanban View ─── --}}
    <div id="view-kanban" class="cf-kanban" style="display:none">

        {{-- Pendentes --}}
        <div class="cf-col cf-col--pendente">
            <div class="cf-col-head">
                <div class="cf-col-title" style="color:#b45309;">
                    <i class="fa-solid fa-hourglass-half"></i> Pendente
                    <span class="cf-col-count js-col-count" data-col-count="pendente">{{ $pendentes->count() }}</span>
                </div>
                <div class="cf-col-subtitle">R$ {{ number_format($totalPendentes, 2, ',', '.') }}</div>
            </div>
            <div class="cf-col-body js-kanban-dropzone" data-drop-status="pendente">
                @forelse($pendentes as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                @empty
                    <div class="js-empty-message cf-empty"><div class="cf-empty-text">Nenhuma pendente</div></div>
                @endforelse
            </div>
        </div>

        {{-- Confirmadas --}}
        <div class="cf-col cf-col--confirmado">
            <div class="cf-col-head">
                <div class="cf-col-title" style="color:#047857;">
                    <i class="fa-solid fa-circle-check"></i> Confirmado
                    <span class="cf-col-count js-col-count" data-col-count="confirmado">{{ $confirmadas->count() }}</span>
                </div>
                <div class="cf-col-subtitle">Saldo: R$ {{ number_format($totalConfirmadas, 2, ',', '.') }}</div>
            </div>
            <div class="cf-col-body js-kanban-dropzone" data-drop-status="confirmado">
                @forelse($confirmadas as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                @empty
                    <div class="js-empty-message cf-empty"><div class="cf-empty-text">Nenhuma confirmada</div></div>
                @endforelse
            </div>
        </div>

        {{-- Canceladas --}}
        <div class="cf-col cf-col--cancelado">
            <div class="cf-col-head">
                <div class="cf-col-title" style="color:#b91c1c;">
                    <i class="fa-solid fa-ban"></i> Cancelado
                    <span class="cf-col-count js-col-count" data-col-count="cancelado">{{ $canceladas->count() }}</span>
                </div>
                <div class="cf-col-subtitle">R$ {{ number_format($totalCanceladas, 2, ',', '.') }}</div>
            </div>
            <div class="cf-col-body js-kanban-dropzone" data-drop-status="cancelado">
                @forelse($canceladas as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                @empty
                    <div class="js-empty-message cf-empty"><div class="cf-empty-text">Nenhuma cancelada</div></div>
                @endforelse
            </div>
        </div>

        {{-- Sangrias --}}
        <div class="cf-col cf-col--sangria">
            <div class="cf-col-head">
                <div class="cf-col-title" style="color:#6d28d9;">
                    <i class="fa-solid fa-right-from-bracket"></i> Sangria
                    <span class="cf-col-count">{{ $sangrias->count() }}</span>
                </div>
                <div class="cf-col-subtitle">R$ {{ number_format($totalSangrias, 2, ',', '.') }}</div>
            </div>
            <div class="cf-col-body">
                @forelse($sangrias as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                @empty
                    <div class="js-empty-message cf-empty"><div class="cf-empty-text">Nenhuma sangria</div></div>
                @endforelse
            </div>
        </div>

    </div>{{-- /kanban --}}

</div>{{-- /cf-root --}}
</div>{{-- /max-w --}}

{{-- ─── Modal Resumo ─── --}}
<div id="modal-report-simplified" class="cf-modal" onclick="if(event.target===this)window.closeReportSimplified()">
    <div class="cf-modal-card">
        <div class="cf-modal-head">
            <div>
                <div class="cf-modal-title">Relatório Resumo</div>
                <div class="cf-modal-sub">Visão consolidada por período</div>
            </div>
            <button class="cf-modal-close" onclick="window.closeReportSimplified()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label class="cf-label">Período</label>
                <select id="report-simplified-period" class="cf-input" onchange="loadSimplifiedReport()">
                    <option value="day">Dia</option>
                    <option value="week">Semana</option>
                    <option value="month" selected>Mês</option>
                    <option value="year">Ano</option>
                </select>
            </div>
            <div>
                <label class="cf-label">Data de Referência</label>
                <input type="date" id="report-simplified-date" value="{{ $latestCashReferenceDate ?? date('Y-m-d') }}" class="cf-input" onchange="loadSimplifiedReport()">
            </div>
        </div>
        <div id="report-simplified-content">
            <div style="text-align:center;padding:24px;color:var(--cf-muted);font-size:13px;">Selecione o período para carregar.</div>
        </div>
    </div>
</div>

{{-- ─── Modal Detalhado ─── --}}
<div id="modal-report-detailed" class="cf-modal" onclick="if(event.target===this)window.closeReportDetailed()">
    <div class="cf-modal-card wide">
        <div class="cf-modal-head">
            <div>
                <div class="cf-modal-title">Relatório Detalhado</div>
                <div class="cf-modal-sub">Todas as transações com informações completas</div>
            </div>
            <button class="cf-modal-close" onclick="window.closeReportDetailed()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:flex-end;margin-bottom:14px;">
            <div>
                <label class="cf-label">Data Inicial</label>
                <input type="date" id="report-detailed-start" value="{{ $startDate }}" class="cf-input">
            </div>
            <div>
                <label class="cf-label">Data Final</label>
                <input type="date" id="report-detailed-end" value="{{ $endDate }}" class="cf-input">
            </div>
            <button onclick="loadDetailedReport()" class="cf-btn cf-btn-purple" style="height:36px;">
                <i class="fa-solid fa-magnifying-glass"></i><span>Carregar</span>
            </button>
        </div>
        <div id="report-detailed-content" style="max-height:62vh;overflow-y:auto;">
            <div style="text-align:center;padding:24px;color:var(--cf-muted);font-size:13px;">Defina o período e clique em Carregar.</div>
        </div>
    </div>
</div>

<script>
    // ══════════════════════════════════════════════
    // GLOBALS — defined FIRST so inline onclick attrs and AJAX-navigated
    // pages both have access before any DOMContentLoaded fires.
    // ══════════════════════════════════════════════
    window.toggleView = function(view) {
        const grid   = document.getElementById('view-grid');
        const kanban = document.getElementById('view-kanban');
        if (!grid || !kanban) return;
        const v = view === 'kanban' ? 'kanban' : 'grid';
        grid.style.display   = v === 'grid'   ? '' : 'none';
        kanban.style.display = v === 'kanban' ? '' : 'none';
        document.getElementById('btn-grid')  ?.classList.toggle('is-active', v === 'grid');
        document.getElementById('btn-kanban')?.classList.toggle('is-active', v === 'kanban');
        localStorage.setItem('cash-view', v);
    };

    window.openReportSimplified = function() {
        document.getElementById('modal-report-simplified').classList.add('is-open');
        loadSimplifiedReport();
    };
    window.closeReportSimplified = function() {
        document.getElementById('modal-report-simplified').classList.remove('is-open');
    };
    window.openReportDetailed = function() {
        document.getElementById('modal-report-detailed').classList.add('is-open');
        loadDetailedReport();
    };
    window.closeReportDetailed = function() {
        document.getElementById('modal-report-detailed').classList.remove('is-open');
    };

    // ─── Utilities ───────────────────────────────
    function fmtBRL(v) {
        const n = Number(v);
        return isNaN(n) ? 'R$ 0,00' : 'R$ ' + n.toFixed(2).replace('.', ',');
    }
    function fmtDate(s) {
        if (!s) return '';
        const [y, m, d] = String(s).split('-');
        return (y && m && d) ? d + '/' + m + '/' + y : s;
    }
    function periodLabel(p) {
        if (!p || !p.inicio || !p.fim) return '';
        const map = { day:'Dia', week:'Semana', month:'Mês', year:'Ano', custom:'Período' };
        return (map[p.tipo] || 'Período') + ': ' + fmtDate(p.inicio) + ' até ' + fmtDate(p.fim);
    }

    // ─── Reports ─────────────────────────────────
    function rptKpi(icon, iconColor, iconBg, label, value, valueColor) {
        return '<div class="cf-rpt-kpi">'
             + '<div class="cf-rpt-kpi-icon" style="background:' + iconBg + ';color:' + iconColor + ' !important;"><i class="fa-solid ' + icon + '"></i></div>'
             + '<div class="cf-rpt-label">' + label + '</div>'
             + '<div class="cf-rpt-value" style="color:' + valueColor + ' !important;">' + value + '</div>'
             + '</div>';
    }

    function loadSimplifiedReport() {
        const period = document.getElementById('report-simplified-period')?.value || 'month';
        const date   = document.getElementById('report-simplified-date')?.value  || '{{ $latestCashReferenceDate ?? date("Y-m-d") }}';
        const box    = document.getElementById('report-simplified-content');
        if (!box) return;
        box.innerHTML = '<div style="text-align:center;padding:32px 0;"><i class="fa-solid fa-spinner fa-spin" style="color:var(--cf-muted);font-size:20px;"></i></div>';
        fetch('{{ route("cash.report.simplified") }}?period=' + period + '&date=' + date)
            .then(function(r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function(data) {
                if (!data.success) { box.innerHTML = '<div style="text-align:center;padding:24px;color:#dc2626;"><i class="fa-solid fa-circle-exclamation"></i> Erro ao carregar dados.</div>'; return; }
                const pm  = Object.entries(data.resumo?.por_meio_pagamento || {});
                const vs  = Array.isArray(data.resumo?.comissoes_por_vendedor) ? data.resumo.comissoes_por_vendedor : [];
                const pl  = periodLabel(data.periodo);
                var html  = pl ? '<div class="cf-rpt-pill"><i class="fa-regular fa-calendar"></i>' + pl + '</div>' : '';
                html += '<div class="cf-rpt-grid">'
                     + rptKpi('fa-arrow-trend-up', '#059669', 'rgba(5,150,105,.15)', 'Entradas', fmtBRL(data.resumo.total_entradas), '#059669')
                     + rptKpi('fa-arrow-trend-down', '#dc2626', 'rgba(220,38,38,.15)', 'Saídas', fmtBRL(data.resumo.total_saidas), '#dc2626')
                     + rptKpi('fa-scale-balanced', '#3b82f6', 'rgba(59,130,246,.15)', 'Saldo', fmtBRL(data.resumo.saldo), '#3b82f6')
                     + rptKpi('fa-tag', '#7c3aed', 'rgba(124,58,237,.15)', 'Descontos', fmtBRL(data.resumo.total_descontos), '#7c3aed')
                     + '</div>';
                html += '<div class="cf-rpt-section">'
                     + '<div class="cf-rpt-section-hd"><i class="fa-solid fa-credit-card"></i>Por Meio de Pagamento</div>'
                     + '<table class="cf-rpt-table"><thead><tr><th>Meio</th><th style="text-align:right">Entradas</th><th style="text-align:right">Saídas</th></tr></thead><tbody>';
                if (!pm.length) {
                    html += '<tr><td colspan="3" style="text-align:center;padding:18px;color:var(--cf-muted) !important;">Sem movimentação</td></tr>';
                } else {
                    pm.forEach(function(entry) {
                        html += '<tr>'
                              + '<td style="text-transform:capitalize;color:var(--cf-text) !important;">' + entry[0].replace(/_/g, ' ') + '</td>'
                              + '<td style="text-align:right;color:#059669 !important;">' + fmtBRL(entry[1].entradas) + '</td>'
                              + '<td style="text-align:right;color:#dc2626 !important;">' + fmtBRL(entry[1].saidas) + '</td>'
                              + '</tr>';
                    });
                }
                html += '</tbody></table></div>';
                if (vs.length) {
                    html += '<div class="cf-rpt-section">'
                         + '<div class="cf-rpt-section-hd"><i class="fa-solid fa-users"></i>Comissões por Vendedor</div>'
                         + '<table class="cf-rpt-table"><thead><tr><th>Vendedor</th><th style="text-align:right">Total</th><th style="text-align:right">Transações</th></tr></thead><tbody>';
                    vs.forEach(function(vend) {
                        html += '<tr>'
                              + '<td style="color:var(--cf-text) !important;">' + vend.nome + '</td>'
                              + '<td style="text-align:right;color:#059669 !important;">' + fmtBRL(vend.total) + '</td>'
                              + '<td style="text-align:right;color:var(--cf-muted) !important;font-weight:700;">' + vend.transacoes + '</td>'
                              + '</tr>';
                    });
                    html += '</tbody></table></div>';
                }
                box.innerHTML = html;
            })
            .catch(function(e) { box.innerHTML = '<div style="text-align:center;padding:24px;color:#dc2626;"><i class="fa-solid fa-circle-exclamation"></i> Erro: ' + e + '</div>'; });
    }

    function loadDetailedReport() {
        const s   = document.getElementById('report-detailed-start')?.value;
        const e   = document.getElementById('report-detailed-end')?.value;
        const box = document.getElementById('report-detailed-content');
        if (!box || !s || !e) return;
        box.innerHTML = '<div style="text-align:center;padding:24px;color:var(--cf-muted);">Carregando…</div>';
        fetch('{{ route("cash.report.detailed") }}?start_date=' + s + '&end_date=' + e)
            .then(function(r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function(data) {
                if (!data.success) { box.innerHTML = '<p style="color:red;text-align:center;">Erro ao carregar</p>'; return; }
                var html = '<div style="font-size:12px;color:var(--cf-muted);font-weight:600;margin-bottom:12px;">Total: ' + data.total_transacoes + ' transação(ões)</div>';
                if (!data.detalhes.length) {
                    html += '<div style="text-align:center;padding:32px;color:var(--cf-muted);">Nenhuma transação no período.</div>';
                }
                data.detalhes.forEach(function(tx) {
                    var isIn  = tx.tipo === 'entrada';
                    var color = isIn ? '#059669' : '#dc2626';
                    var pm    = Array.isArray(tx.meios_pagamento) ? tx.meios_pagamento : [];
                    html += '<div style="background:var(--cf-input-bg);border:1px solid var(--cf-input-border);border-radius:10px;padding:12px;margin-bottom:10px;">'
                          + '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">'
                          +   '<div><div style="font-size:13px;font-weight:800;color:var(--cf-text);">' + tx.categoria + '</div>'
                          +   '<div style="font-size:11px;color:var(--cf-muted);font-weight:600;">' + tx.descricao + '</div></div>'
                          +   '<div style="text-align:right;"><div style="font-size:17px;font-weight:800;color:' + color + ';">' + (isIn?'+':'-') + ' R$ ' + parseFloat(tx.valor).toFixed(2).replace('.',',') + '</div>'
                          +   '<div style="font-size:10px;color:var(--cf-muted);font-weight:600;">' + tx.data + ' ' + tx.hora + '</div></div>'
                          + '</div>';
                    if (pm.length) {
                        html += '<div style="display:flex;flex-direction:column;gap:3px;margin-bottom:8px;">';
                        pm.forEach(function(m) {
                            html += '<div style="display:flex;justify-content:space-between;font-size:11px;">'
                                  + '<span style="color:var(--cf-muted);text-transform:capitalize;">' + (m.method||'N/A').replace(/_/g,' ') + '</span>'
                                  + '<span style="font-weight:700;color:var(--cf-text);">R$ ' + parseFloat(m.amount||0).toFixed(2).replace('.',',') + '</span></div>';
                        });
                        html += '</div>';
                    }
                    html += '<div style="font-size:11px;color:var(--cf-muted);font-weight:600;">Vendedor: ' + tx.vendedor + '</div>';
                    if (tx.pedido) {
                        html += '<div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--cf-row-border);font-size:11px;">'
                              + '<span style="font-weight:800;color:var(--cf-text);">Pedido #' + tx.pedido.numero + '</span>'
                              + ' — ' + tx.pedido.cliente
                              + ' — Total: R$ ' + parseFloat(tx.pedido.total).toFixed(2).replace('.',',') + '</div>';
                    }
                    html += '</div>';
                });
                box.innerHTML = html;
            })
            .catch(function(e) { box.innerHTML = '<p style="color:red;text-align:center;">Erro: ' + e + '</p>'; });
    }

    // ─── Kanban drag-and-drop ─────────────────────
    const _cashStatusUrl = @json(route('cash.update-status', ['cash' => '__ID__']));
    const _cashCsrf      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function getCashStatusUrl(id) {
        return _cashStatusUrl.replace('__ID__', encodeURIComponent(String(id)));
    }
    function normStatus(s) {
        const allowed = ['pendente', 'confirmado', 'cancelado'];
        const n = String(s || '').toLowerCase();
        return allowed.includes(n) ? n : null;
    }
    function updateColCount(status, delta) {
        const n  = normStatus(status);
        const el = n && document.querySelector('.js-col-count[data-col-count="' + n + '"]');
        if (el) el.textContent = String(Math.max(0, (parseInt(el.textContent || '0', 10) || 0) + delta));
    }
    function ensureEmpty(zone) {
        if (!zone) return;
        const status = normStatus(zone.dataset.dropStatus);
        const cards  = Array.from(zone.children).filter(function(c) { return c.classList?.contains('cash-kanban-card'); });
        const cur    = zone.querySelector('.js-empty-message');
        if (cards.length > 0) { cur?.remove(); return; }
        if (cur) return;
        const labels = { pendente:'Nenhuma pendente', confirmado:'Nenhuma confirmada', cancelado:'Nenhuma cancelada' };
        const msg = document.createElement('div');
        msg.className = 'js-empty-message cf-empty';
        msg.innerHTML = '<div class="cf-empty-text">' + (status ? (labels[status] || 'Nenhum item') : 'Nenhum item') + '</div>';
        zone.appendChild(msg);
    }

    function bindKanban() {
        const board = document.getElementById('view-kanban');
        if (!board) return;
        const zones = board.querySelectorAll('.js-kanban-dropzone[data-drop-status]');
        if (!zones.length) return;

        var drag = null, srcZone = null, srcStatus = null;

        function resetDrag() {
            if (drag) drag.classList.remove('cash-card-dragging');
            drag = null; srcZone = null; srcStatus = null;
            zones.forEach(function(z) { z.classList.remove('drop-active', 'cash-drop-active'); });
        }

        board.querySelectorAll('.cash-kanban-card[draggable="true"]').forEach(function(card) {
            if (card.dataset.dragBound === '1') return;
            card.dataset.dragBound = '1';
            card.addEventListener('dragstart', function(ev) {
                var s = normStatus(card.dataset.status);
                if (!s || String(card.dataset.category || '').toLowerCase() === 'sangria' || card.dataset.updating === '1') {
                    ev.preventDefault(); return;
                }
                drag = card; srcZone = card.closest('.js-kanban-dropzone'); srcStatus = s;
                if (ev.dataTransfer) ev.dataTransfer.setData('text/plain', card.dataset.transactionId || '');
                requestAnimationFrame(function() { card.classList.add('cash-card-dragging'); });
            });
            card.addEventListener('dragend', resetDrag);
        });

        zones.forEach(function(zone) {
            if (zone.dataset.dropBound === '1') return;
            zone.dataset.dropBound = '1';
            zone.addEventListener('dragover', function(ev) {
                if (!drag) return;
                ev.preventDefault();
                zone.classList.add('drop-active', 'cash-drop-active');
                if (ev.dataTransfer) ev.dataTransfer.dropEffect = 'move';
            });
            zone.addEventListener('dragleave', function(ev) {
                if (!zone.contains(ev.relatedTarget)) zone.classList.remove('drop-active', 'cash-drop-active');
            });
            zone.addEventListener('drop', function(ev) {
                ev.preventDefault();
                zone.classList.remove('drop-active', 'cash-drop-active');
                if (!drag || !srcZone || !srcStatus) { resetDrag(); return; }
                var tgt = normStatus(zone.dataset.dropStatus);
                var tid = drag.dataset.transactionId;
                if (!tgt || !tid) { resetDrag(); return; }
                if (tgt === srcStatus) {
                    if (zone !== srcZone) { zone.appendChild(drag); ensureEmpty(srcZone); ensureEmpty(zone); }
                    resetDrag(); return;
                }
                var prev = drag.previousElementSibling, origZone = srcZone, origStatus = srcStatus;
                zone.appendChild(drag);
                drag.dataset.status = tgt;
                drag.dataset.updating = '1';
                drag.classList.add('pointer-events-none', 'opacity-70');
                updateColCount(origStatus, -1); updateColCount(tgt, 1);
                ensureEmpty(origZone); ensureEmpty(zone);
                resetDrag();
                fetch(getCashStatusUrl(tid), {
                    method: 'PATCH',
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': _cashCsrf },
                    body: JSON.stringify({ status: tgt })
                }).then(function(res) {
                    return res.json().then(function(payload) {
                        if (!res.ok || !payload.success) throw new Error(payload.message || 'Falha');
                    });
                }).catch(function(err) {
                    console.error('Kanban move failed:', err);
                    if (prev && prev.parentElement === origZone) {
                        prev.insertAdjacentElement('afterend', drag);
                    } else {
                        origZone.prepend(drag);
                    }
                    drag.dataset.status = origStatus;
                    updateColCount(tgt, -1); updateColCount(origStatus, 1);
                    ensureEmpty(origZone); ensureEmpty(zone);
                    alert('Não foi possível mover. Tente novamente.');
                }).finally(function() {
                    drag && (drag.dataset.updating = '0');
                    drag?.classList.remove('pointer-events-none', 'opacity-70');
                });
            });
        });

        zones.forEach(ensureEmpty);
    }

    // ─── Page init — works for normal load AND AJAX navigation ───
    function initCashPage() {
        bindKanban();
        window.toggleView(localStorage.getItem('cash-view') || 'grid');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCashPage);
    } else {
        initCashPage();
    }
</script>
@endsection
