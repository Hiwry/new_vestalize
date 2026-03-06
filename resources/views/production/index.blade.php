@extends('layouts.admin')

@section('content')
@php
    $today = \Carbon\Carbon::today();
    $ordersCollection = $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->getCollection() : collect($orders);
    $dueTodayCount = $ordersCollection->filter(function ($order) use ($today) {
        return $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isSameDay($today);
    })->count();
    $lateOrdersCount = $ordersCollection->filter(function ($order) use ($today) {
        return $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isBefore($today);
    })->count();
    $ordersWithoutDate = $ordersCollection->filter(function ($order) {
        return empty($order->delivery_date);
    })->count();
    $upcomingOrders = $ordersCollection->filter(function ($order) {
        return !empty($order->delivery_date);
    })->sortBy(function ($order) {
        return \Carbon\Carbon::parse($order->delivery_date)->timestamp;
    })->take(5);
    $periodLabels = [
        'all' => 'Todo o periodo',
        'day' => 'Hoje',
        'week' => 'Semana util',
        'month' => 'Este mes',
        'custom' => 'Personalizado'
    ];
    $periodLabel = $periodLabels[$period] ?? 'Periodo';
    $periodRange = null;
    if (!empty($startDate) && !empty($endDate)) {
        $periodRange = \Carbon\Carbon::parse($startDate)->format('d/m') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m');
    } elseif (!empty($startDate)) {
        $periodRange = \Carbon\Carbon::parse($startDate)->format('d/m/Y');
    }

    $personalizationSummary = $ordersByPersonalization->map(function ($group) {
        return $group->count();
    })->sortDesc();
    $maxPersonalizationCount = (int) ($personalizationSummary->first() ?? 0);
    $activeFiltersCount = collect([$search, $status, $personalizationType, $storeId])->filter(fn ($value) => filled($value))->count();

    if ($period !== 'all') {
        $activeFiltersCount++;
    }

    $activeFilters = collect([
        filled($search) ? 'Busca: ' . $search : null,
        filled($status) ? 'Status: ' . optional($statuses->firstWhere('id', (int) $status))->name : null,
        filled($personalizationType) ? 'Personalizacao: ' . ($personalizationTypes[$personalizationType] ?? $personalizationType) : null,
        filled($storeId) ? 'Loja: ' . optional($stores->firstWhere('id', (int) $storeId))->name : null,
        $period !== 'all' ? 'Periodo: ' . $periodLabel . ($periodRange ? ' (' . $periodRange . ')' : '') : null,
    ])->filter()->values();

    $quickPeriods = [
        'all' => 'Tudo',
        'day' => 'Hoje',
        'week' => 'Semana',
        'month' => 'Mes',
        'custom' => 'Personalizado',
    ];
@endphp

<style>
    .pl-summary::-webkit-details-marker { display: none; }
    .pl-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(99, 102, 241, .18);
        background: rgba(99, 102, 241, .08);
        font-size: 12px;
        font-weight: 700;
    }
    .pl-chip-muted {
        border-color: rgba(148, 163, 184, .22);
        background: rgba(148, 163, 184, .10);
    }
    .pl-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, .22);
        background: rgba(255, 255, 255, .72);
        font-size: 13px;
        font-weight: 800;
        color: rgb(71 85 105);
        transition: .18s ease;
    }
    .dark .pl-pill {
        background: rgba(15, 23, 42, .68);
        color: rgb(148 163 184);
    }
    .pl-pill:hover { transform: translateY(-1px); }
    .pl-pill.is-active {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 12px 24px rgba(79, 70, 229, .24);
    }
    .pl-progress-track {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(148, 163, 184, .18);
    }
    .pl-progress-bar {
        height: 100%;
        border-radius: inherit;
    }
    .pl-table-wrap { overflow-x: auto; }
    .pl-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pl-table th {
        padding: 14px 18px;
        background: rgba(148, 163, 184, .08);
        border-bottom: 1px solid rgba(148, 163, 184, .18);
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgb(100 116 139);
    }
    .pl-table td {
        padding: 16px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, .14);
        vertical-align: top;
    }
    .pl-table tbody tr:hover td { background: rgba(99, 102, 241, .03); }
</style>

<div class="max-w-[1800px] mx-auto pb-24 space-y-8">
    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <section class="stay-white relative overflow-hidden rounded-[32px] border border-slate-200/70 bg-gradient-to-br from-slate-950 via-indigo-950 to-cyan-900 px-6 py-6 text-white shadow-2xl shadow-slate-900/20 md:px-8 md:py-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,.16),transparent_36%),radial-gradient(circle_at_bottom_right,rgba(45,212,191,.18),transparent_32%)]"></div>
        <div class="relative z-10 flex flex-col gap-8 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl space-y-5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="stay-white inline-flex items-center gap-2 rounded-full bg-white/12 px-3 py-1 text-[11px] font-black uppercase tracking-[0.24em] text-cyan-100">
                        Operacao de producao
                    </span>
                    <span class="stay-white inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-100">
                        {{ $periodLabel }}
                        @if($periodRange)
                            <span class="stay-white rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.18em] text-cyan-100">{{ $periodRange }}</span>
                        @endif
                    </span>
                </div>

                <div>
                    <h1 class="stay-white max-w-2xl text-3xl font-black tracking-[-0.04em] text-white md:text-5xl">Lista operacional com foco em prioridade, prazo e proximo passo.</h1>
                    <p class="stay-white mt-3 max-w-2xl text-sm text-slate-200/85 md:text-base">
                        A pagina concentra urgencias, distribuicao da fila, filtros de acesso rapido e uma leitura mais clara da lista para uso continuo pela producao.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('orders.wizard.start') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-900 transition hover:-translate-y-0.5 hover:bg-cyan-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Novo pedido
                    </a>
                    <a href="{{ route('kanban.index') }}" class="stay-white inline-flex items-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-white/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        Abrir kanban
                    </a>
                    <a href="{{ route('production.pdf') }}?{{ http_build_query(request()->except('page')) }}" target="_blank" class="stay-white inline-flex items-center gap-2 rounded-2xl border border-emerald-300/20 bg-emerald-400/15 px-5 py-3 text-sm font-black text-emerald-50 transition hover:-translate-y-0.5 hover:bg-emerald-400/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Exportar PDF
                    </a>
                </div>
            </div>

            <div class="grid w-full max-w-xl grid-cols-1 gap-3 sm:grid-cols-3 xl:max-w-md">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <p class="stay-white text-[11px] font-black uppercase tracking-[0.24em] text-slate-200">Entrega hoje</p>
                    <p class="stay-white mt-3 text-3xl font-black text-white">{{ $dueTodayCount }}</p>
                    <p class="stay-white mt-2 text-xs font-semibold text-slate-200/80">Pedidos com vencimento no dia.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-rose-400/12 p-4 backdrop-blur">
                    <p class="stay-white text-[11px] font-black uppercase tracking-[0.24em] text-rose-100">Atrasados</p>
                    <p class="stay-white mt-3 text-3xl font-black text-rose-100">{{ $lateOrdersCount }}</p>
                    <p class="stay-white mt-2 text-xs font-semibold text-rose-50/80">Fila que pede repriorizacao.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-amber-400/12 p-4 backdrop-blur">
                    <p class="stay-white text-[11px] font-black uppercase tracking-[0.24em] text-amber-100">Sem data</p>
                    <p class="stay-white mt-3 text-3xl font-black text-amber-50">{{ $ordersWithoutDate }}</p>
                    <p class="stay-white mt-2 text-xs font-semibold text-amber-50/80">Pedidos sem prazo definido.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.8fr)_minmax(320px,1fr)]">
        <div class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Leitura rapida</p>
                    <h2 class="mt-2 text-2xl font-black tracking-[-0.03em] text-slate-900 dark:text-slate-100">Atalhos de periodo e filtros ativos.</h2>
                    <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400">Troque a janela de acompanhamento sem abrir o painel completo.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($quickPeriods as $periodKey => $periodText)
                        <a href="{{ route('production.index', array_merge(request()->except('page', 'period', 'start_date', 'end_date'), ['period' => $periodKey])) }}"
                           class="pl-pill {{ $period === $periodKey ? 'is-active' : '' }}">
                            {{ $periodText }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @forelse($activeFilters as $filterLabel)
                    <span class="pl-chip">{{ $filterLabel }}</span>
                @empty
                    <span class="pl-chip pl-chip-muted">Sem filtros especificos. A lista mostra toda a operacao.</span>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/70 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Monitoramento</p>
                    <h2 class="mt-2 text-xl font-black tracking-[-0.03em] text-slate-900 dark:text-slate-100">Estado da pagina</h2>
                </div>
                <span class="stay-white rounded-full bg-slate-900 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white dark:bg-slate-100 dark:text-slate-900" style="color: #ffffff;">
                    {{ $activeFiltersCount }} filtro(s)
                </span>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/50">
                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Pedidos</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ $totalOrders }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/50">
                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Valor</p>
                    <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-100">R$ {{ number_format($totalValue, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/50">
                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Etapas</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ $ordersByStatus->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/50">
                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Tipos</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ $ordersByPersonalization->count() }}</p>
                </div>
            </div>
        </div>
    </section>
    <section class="grid grid-cols-1 gap-6 2xl:grid-cols-[minmax(0,1.45fr)_minmax(0,1fr)_minmax(320px,.95fr)]">
        <div class="rounded-3xl border border-slate-200/70 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">Pipeline por status</h2>
                    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Concentracao atual da fila por etapa.</p>
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Leitura direta</span>
            </div>

            <div class="mt-5 grid gap-3">
                @foreach($statuses as $statusItem)
                    @php
                        $count = ($ordersByStatus[$statusItem->id] ?? collect())->count();
                        $share = $totalOrders > 0 ? round(($count / $totalOrders) * 100) : 0;
                    @endphp
                    <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/40">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-3 w-3 rounded-full" style="background-color: {{ $statusItem->color ?? '#9ca3af' }}"></span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-slate-900 dark:text-slate-100">{{ $statusItem->name }}</p>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $share }}% da fila</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $count }}</p>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">pedidos</p>
                            </div>
                        </div>
                        <div class="pl-progress-track mt-4">
                            <div class="pl-progress-bar" style="width: {{ $share }}%; background-color: {{ $statusItem->color ?? '#9ca3af' }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/70 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">Proximas entregas</h2>
                    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Pedidos com prazo definido e leitura priorizada.</p>
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $upcomingOrders->count() }} itens</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($upcomingOrders as $order)
                    @php
                        $deliveryDate = \Carbon\Carbon::parse($order->delivery_date);
                        $isLate = $deliveryDate->isBefore($today);
                        $isToday = $deliveryDate->isSameDay($today);
                    @endphp
                    <a href="{{ route('orders.show', $order->id) }}" class="block rounded-2xl border border-slate-200/70 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white dark:border-slate-700 dark:bg-slate-950/40 dark:hover:border-indigo-500/40 dark:hover:bg-slate-950">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-black text-indigo-600 dark:text-indigo-400">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $order->client->name ?? '-' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $order->status->name ?? 'Indefinido' }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] {{ $isLate ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200' : ($isToday ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200') }}">
                                {{ $isLate ? 'Atrasado' : ($isToday ? 'Hoje' : 'Planejado') }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>{{ $deliveryDate->format('d/m/Y') }}</span>
                            <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-400">
                        Nenhuma entrega com data definida neste recorte.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/70 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">Tipos de personalizacao</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Mix do periodo e alertas basicos.</p>
            </div>

            <div class="mt-5 space-y-4">
                @forelse($personalizationSummary as $type => $count)
                    @php
                        $p = $maxPersonalizationCount > 0 ? ($count / $maxPersonalizationCount) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $type }}</p>
                            <span class="text-sm font-black text-slate-700 dark:text-slate-200">{{ $count }}</span>
                        </div>
                        <div class="pl-progress-track mt-2">
                            <div class="pl-progress-bar bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-500" style="width: {{ $p }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500 dark:text-slate-400">Nenhuma personalizacao no periodo.</div>
                @endforelse
            </div>

            <div class="mt-6 space-y-3">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-600/30 dark:bg-amber-900/20 dark:text-amber-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Sem data</p>
                    <p class="mt-1 text-sm">{{ $ordersWithoutDate }} pedido(s) sem data de entrega.</p>
                </div>
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 dark:border-rose-600/30 dark:bg-rose-900/20 dark:text-rose-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Atrasados</p>
                    <p class="mt-1 text-sm">{{ $lateOrdersCount }} pedido(s) com entrega atrasada.</p>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-indigo-800 dark:border-indigo-600/30 dark:bg-indigo-900/20 dark:text-indigo-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Entregas hoje</p>
                    <p class="mt-1 text-sm">{{ $dueTodayCount }} pedido(s) para entrega hoje.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
        <details class="group" {{ ($search || $status || $personalizationType || $storeId || $period === 'custom') ? 'open' : '' }}>
            <summary class="pl-summary flex cursor-pointer items-center justify-between gap-4 px-6 py-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-slate-100">Filtros avancados</h2>
                    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Refine a lista por periodo, status, loja ou texto livre.</p>
                </div>
                <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
                    <span class="text-xs font-bold">{{ $activeFiltersCount }} ativo(s)</span>
                    <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </summary>
            <div class="border-t border-slate-200/80 px-6 py-5 dark:border-slate-800">
                <form method="GET" action="{{ route('production.index') }}" class="space-y-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Periodo</label>
                            <select name="period" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo o periodo</option>
                                <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hoje</option>
                                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta semana</option>
                                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este mes</option>
                                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                            </select>
                        </div>

                        <div id="start-date-field" class="{{ $period === 'custom' ? '' : 'hidden' }}">
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Data inicio</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        </div>

                        <div id="end-date-field" class="{{ $period === 'custom' ? '' : 'hidden' }}">
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Data fim</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Status</label>
                            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="">Todos os status</option>
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption->id }}" {{ $status == $statusOption->id ? 'selected' : '' }}>{{ $statusOption->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Personalizacao</label>
                            <select name="personalization_type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="">Todos os tipos</option>
                                @foreach($personalizationTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $personalizationType == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Loja</label>
                            <select name="store_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="">Todas as lojas</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2 xl:col-span-6">
                            <label class="mb-2 block text-xs font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Busca</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Numero do pedido, nome do cliente, telefone ou nome da arte..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 placeholder:text-slate-400 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500">
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200/80 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Os filtros afetam a lista exibida e a exportacao em PDF.</p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5">
                                Aplicar filtros
                            </button>
                            <a href="{{ route('production.index') }}" class="rounded-2xl border border-slate-200 px-6 py-3 text-center text-sm font-black text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </details>
    </section>
    <section class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
        <div class="border-b border-slate-200/80 px-6 py-5 dark:border-slate-800">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h3 class="text-2xl font-black tracking-[-0.03em] text-slate-900 dark:text-slate-100">Pedidos encontrados</h3>
                    <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                Mostrando {{ $orders->count() }} de {{ $totalOrders }} pedidos
                @if($period === 'day')
                    ativos em producao.
                @elseif($period === 'week')
                    com entrega esta semana ({{ \Carbon\Carbon::parse($startDate)->format('d/m') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m') }}).
                @elseif($period === 'month')
                    com entrega este mes.
                @elseif($period === 'all')
                    em qualquer periodo.
                @else
                    com entrega no periodo selecionado.
                @endif
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="pl-chip pl-chip-muted">Desktop: tabela completa</span>
                    <span class="pl-chip pl-chip-muted">Mobile: cards resumidos</span>
                </div>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="hidden lg:block pl-table-wrap">
                <table class="pl-table">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Loja</th>
                            <th>Vendedor</th>
                            <th>Status</th>
                            <th>Personalizacao</th>
                            <th>Datas</th>
                            <th>Total</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $firstItem = $order->items->first();
                                $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date) : null;
                                $deliveryTone = $deliveryDate
                                    ? ($deliveryDate->isBefore($today) ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200'
                                    : ($deliveryDate->isSameDay($today) ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200'
                                    : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200'))
                                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                            @endphp
                            <tr>
                                <td>
                                    <div class="space-y-1">
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-sm font-black text-indigo-600 transition hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-1">
                                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $order->client->name ?? 'Cliente nao encontrado' }}</div>
                                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $order->client->phone_primary ?? '-' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $order->store?->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $order->seller ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-black text-white" style="background-color: {{ $order->status->color ?? '#6B7280' }}">
                                        {{ $order->status->name ?? 'Indefinido' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="space-y-1">
                                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $firstItem->print_type ?? '-' }}</div>
                                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $firstItem->art_name ?? 'Sem arte informada' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-2">
                                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Entrega</p>
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-black {{ $deliveryTone }}">
                                            {{ $deliveryDate ? $deliveryDate->format('d/m/Y') : 'Sem data' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm font-black text-slate-900 dark:text-slate-100">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                        Ver detalhes
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 px-6 py-5 lg:hidden">
                @foreach($orders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date) : null;
                        $deliveryTone = $deliveryDate
                            ? ($deliveryDate->isBefore($today) ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-200'
                            : ($deliveryDate->isSameDay($today) ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200'
                            : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200'))
                            : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                    @endphp
                    <article class="rounded-3xl border border-slate-200/70 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-950/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('orders.show', $order->id) }}" class="text-base font-black text-indigo-600 dark:text-indigo-400">
                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                                <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $order->client->name ?? 'Cliente nao encontrado' }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $order->client->phone_primary ?? '-' }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-black text-white" style="background-color: {{ $order->status->color ?? '#6B7280' }}">
                                {{ $order->status->name ?? 'Indefinido' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Entrega</p>
                                <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-black {{ $deliveryTone }}">{{ $deliveryDate ? $deliveryDate->format('d/m/Y') : 'Sem data' }}</span>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Total</p>
                                <p class="mt-2 text-sm font-black text-slate-900 dark:text-slate-100">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Loja</p>
                                <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $order->store?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Vendedor</p>
                                <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $order->seller ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-400">Personalizacao</p>
                            <p class="mt-2 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $firstItem->print_type ?? '-' }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $firstItem->art_name ?? 'Sem arte informada' }}</p>
                        </div>

                        <a href="{{ route('orders.show', $order->id) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                            Abrir pedido
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="border-t border-slate-200/80 px-6 py-4 dark:border-slate-800">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100">Nenhum pedido encontrado</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    @if($search || $status || $personalizationType || $storeId || $period === 'custom')
                        Tente ajustar os filtros para encontrar pedidos.
                    @else
                        Nao ha pedidos para o periodo selecionado.
                    @endif
                </p>
            </div>
        @endif
    </section>
</div>

<script>
    const periodSelect = document.querySelector('select[name="period"]');

    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const startDateField = document.getElementById('start-date-field');
            const endDateField = document.getElementById('end-date-field');
            const startDateInput = startDateField ? startDateField.querySelector('input[name="start_date"]') : null;
            const endDateInput = endDateField ? endDateField.querySelector('input[name="end_date"]') : null;

            if (this.value === 'custom') {
                if (startDateField) startDateField.classList.remove('hidden');
                if (endDateField) endDateField.classList.remove('hidden');
            } else {
                if (startDateField) startDateField.classList.add('hidden');
                if (endDateField) endDateField.classList.add('hidden');
                if (startDateInput) startDateInput.value = '';
                if (endDateInput) endDateInput.value = '';
            }
        });
    }
</script>
@endsection
