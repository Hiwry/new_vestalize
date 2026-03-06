@extends('layouts.admin')

@section('content')
@php
    $stats = $stats ?? [];
    $recentOrders = collect($recent_orders ?? []);
    $recentUsers = collect($recent_users ?? []);
    $pendingDeliveries = collect($pending_delivery_requests ?? []);
    $recentCashTransactions = collect($recent_cash_transactions ?? []);
    $storesCollection = collect($stores ?? []);
    $isTenantScopeMissing = (bool) ($isSuperAdmin ?? false);

    $managementLinks = collect([
        [
            'label' => 'Lista de Pedidos',
            'description' => 'Acompanhe o fluxo principal de pedidos.',
            'href' => route('orders.index'),
            'icon' => 'fa-list-check',
            'tone' => 'primary',
            'badge' => number_format((int) ($stats['total_orders'] ?? 0), 0, ',', '.'),
        ],
        [
            'label' => 'Cancelamentos',
            'description' => 'Revise solicitações pendentes de cancelamento.',
            'href' => route('admin.cancellations.index'),
            'icon' => 'fa-ban',
            'tone' => 'danger',
            'badge' => number_format((int) ($stats['pending_cancellations'] ?? 0), 0, ',', '.'),
        ],
        [
            'label' => 'Solicitações de Entrega',
            'description' => 'Monitore entregas aguardando aprovação.',
            'href' => route('delivery-requests.index'),
            'icon' => 'fa-truck-fast',
            'tone' => 'warning',
            'badge' => number_format((int) ($stats['pending_delivery_requests'] ?? 0), 0, ',', '.'),
        ],
    ]);

    if (auth()->user()->tenant_id === null || auth()->user()->tenant?->canAccess('kanban')) {
        $managementLinks->push([
            'label' => 'Kanban de Produção',
            'description' => 'Visualize gargalos e etapas produtivas.',
            'href' => route('kanban.index'),
            'icon' => 'fa-clapperboard',
            'tone' => 'primary',
            'badge' => 'Fluxo',
        ]);
    }

    $financeLinks = collect();
    if (auth()->user()->tenant_id === null || auth()->user()->tenant?->canAccess('financial')) {
        $financeLinks->push([
            'label' => 'Controle de Caixa',
            'description' => 'Consulte movimentações e saldo operacional.',
            'href' => route('cash.index'),
            'icon' => 'fa-cash-register',
            'tone' => 'success',
            'badge' => number_format((int) ($stats['total_cash_transactions'] ?? 0), 0, ',', '.'),
        ]);
    }

    $catalogLinks = collect([
        ['label' => 'Opções de Produtos', 'href' => route('admin.product-options.index'), 'tone' => 'primary'],
        ['label' => 'Preços de Personalização', 'href' => route('admin.personalization-prices.index'), 'tone' => 'success'],
        ['label' => 'Gerenciar Catálogo', 'href' => route('admin.catalog-items.index'), 'tone' => 'primary'],
    ]);

    if (auth()->user()->tenant_id === null || auth()->user()->tenant?->canAccess('pdv')) {
        $catalogLinks->prepend(['label' => 'Produtos PDV', 'href' => route('admin.quick-products.index'), 'tone' => 'warning']);
    }

    $systemLinks = auth()->user()->tenant_id === null
        ? collect([
            ['label' => 'Gerenciar Usuários', 'href' => route('admin.users.index'), 'tone' => 'danger'],
            ['label' => 'Configurações da Empresa', 'href' => route('admin.company.settings'), 'tone' => 'primary'],
            ['label' => 'Gerenciar Lojas', 'href' => route('admin.stores.index'), 'tone' => 'success'],
        ])
        : collect();

    $kpis = collect([
        ['label' => 'Pedidos totais', 'value' => number_format((int) ($stats['total_orders'] ?? 0), 0, ',', '.'), 'icon' => 'fa-box-open', 'tone' => 'primary'],
        ['label' => 'Entregas pendentes', 'value' => number_format((int) ($stats['pending_delivery_requests'] ?? 0), 0, ',', '.'), 'icon' => 'fa-truck-ramp-box', 'tone' => 'warning'],
        ['label' => 'Cancelamentos', 'value' => number_format((int) ($stats['pending_cancellations'] ?? 0), 0, ',', '.'), 'icon' => 'fa-ban', 'tone' => 'danger'],
    ]);

    if (auth()->user()->tenant_id === null) {
        $kpis->splice(1, 0, [[
            'label' => 'Usuários ativos',
            'value' => number_format((int) ($stats['total_users'] ?? 0), 0, ',', '.'),
            'icon' => 'fa-users',
            'tone' => 'success',
        ]]);
    }

    $toneClasses = [
        'primary' => 'ft-tone-primary',
        'success' => 'ft-tone-success',
        'warning' => 'ft-tone-warning',
        'danger' => 'ft-tone-danger',
    ];
@endphp

<style>
    .ft-dashboard-admin {
        --ft-surface-from: #f3f4f8;
        --ft-surface-to: #eceff4;
        --ft-surface-border: #d8dce6;
        --ft-text-primary: #0f172a;
        --ft-text-secondary: #64748b;
        --ft-card-bg: #ffffff;
        --ft-card-border: #dde2ea;
        --ft-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --ft-kpi-text: #111827;
        --ft-table-head-border: #e5e9f1;
        --ft-table-row-border: #eef1f6;
        --ft-tag-bg: #f8fafc;
        --ft-tag-border: #e2e8f0;
        --ft-tag-text: #475569;
        background: linear-gradient(180deg, var(--ft-surface-from) 0%, var(--ft-surface-to) 100%);
        border: 1px solid var(--ft-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--ft-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .ft-dashboard-admin {
        --ft-surface-from: #0f172a;
        --ft-surface-to: #0b1322;
        --ft-surface-border: rgba(148, 163, 184, 0.25);
        --ft-text-primary: #e2e8f0;
        --ft-text-secondary: #94a3b8;
        --ft-card-bg: #111827;
        --ft-card-border: rgba(148, 163, 184, 0.22);
        --ft-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        --ft-kpi-text: #f8fafc;
        --ft-table-head-border: rgba(148, 163, 184, 0.25);
        --ft-table-row-border: rgba(148, 163, 184, 0.16);
        --ft-tag-bg: rgba(15, 23, 42, 0.55);
        --ft-tag-border: rgba(148, 163, 184, 0.2);
        --ft-tag-text: #cbd5e1;
    }

    .ft-topbar, .ft-card-head, .ft-link-row, .ft-list-item, .ft-store-item, .ft-tag, .ft-badge, .ft-kpi-head {
        display: flex;
        align-items: center;
    }

    .ft-topbar, .ft-card-head, .ft-link-row, .ft-list-item, .ft-store-item {
        justify-content: space-between;
        gap: 14px;
    }

    .ft-topbar { margin-bottom: 20px; flex-wrap: wrap; }
    .ft-brand { display: flex; align-items: center; gap: 12px; flex: 1 1 320px; }
    .ft-logo { width: 36px; height: 36px; border-radius: 11px; background: linear-gradient(135deg, #6d28d9, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .ft-title { font-size: 18px; font-weight: 700; letter-spacing: -0.015em; }
    .ft-subtitle { color: var(--ft-text-secondary); font-size: 12px; font-weight: 600; }
    .ft-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; flex: 1 1 420px; }
    .ft-action { height: 38px; border-radius: 12px; padding: 0 14px; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; text-decoration: none; color: #fff !important; transition: transform .18s ease, box-shadow .2s ease, filter .2s ease; white-space: nowrap; }
    .ft-action:hover { transform: translateY(-1px); filter: brightness(1.03); }
    .ft-action-primary { background: linear-gradient(135deg, #6d28d9, #7c3aed); box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25); }
    .ft-action-success { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25); }
    .ft-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(320px, 1fr); gap: 14px; }
    .ft-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .ft-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .ft-card { background: var(--ft-card-bg); border: 1px solid var(--ft-card-border); border-radius: 14px; padding: 16px; box-shadow: var(--ft-card-shadow); }
    .ft-card-title { font-size: 18px; font-weight: 700; color: var(--ft-text-primary); }
    .ft-card-subtitle { font-size: 13px; color: var(--ft-text-secondary); font-weight: 600; }
    .ft-kpi-stack { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
    .ft-kpi-card { padding: 16px; border-radius: 14px; background: linear-gradient(180deg, rgba(255,255,255,.85) 0%, rgba(248,250,252,.9) 100%); border: 1px solid var(--ft-card-border); min-height: 136px; }
    .dark .ft-kpi-card { background: linear-gradient(180deg, rgba(15,23,42,.6) 0%, rgba(17,24,39,.95) 100%); }
    .ft-kpi-head { justify-content: space-between; margin-bottom: 24px; }
    .ft-kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--ft-text-secondary); font-weight: 700; }
    .ft-kpi-value { font-size: 28px; font-weight: 800; color: var(--ft-kpi-text); line-height: 1; }
    .ft-kpi-note { font-size: 12px; color: var(--ft-text-secondary); font-weight: 600; margin-top: 8px; }
    .ft-icon { width: 42px; height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; }
    .ft-tone-primary { color: #6d28d9; background: rgba(109, 40, 217, 0.12); }
    .ft-tone-success { color: #059669; background: rgba(5, 150, 105, 0.12); }
    .ft-tone-warning { color: #d97706; background: rgba(217, 119, 6, 0.12); }
    .ft-tone-danger { color: #dc2626; background: rgba(220, 38, 38, 0.12); }
    .ft-link-list, .ft-list { display: flex; flex-direction: column; gap: 10px; }
    .ft-link-row, .ft-list-item, .ft-store-item { padding: 14px; border-radius: 12px; border: 1px solid var(--ft-table-row-border); background: rgba(255, 255, 255, 0.55); text-decoration: none; color: inherit; transition: transform .18s ease, border-color .18s ease, background .18s ease; }
    .dark .ft-link-row, .dark .ft-list-item, .dark .ft-store-item { background: rgba(15, 23, 42, 0.45); }
    .ft-link-row:hover, .ft-list-item:hover, .ft-store-item:hover { transform: translateY(-1px); border-color: var(--ft-card-border); background: rgba(255, 255, 255, 0.9); }
    .dark .ft-link-row:hover, .dark .ft-list-item:hover, .dark .ft-store-item:hover { background: rgba(15, 23, 42, 0.72); }
    .ft-link-main, .ft-list-main { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ft-link-copy, .ft-list-copy { min-width: 0; }
    .ft-link-copy strong, .ft-list-copy strong { display: block; font-size: 14px; color: var(--ft-text-primary); }
    .ft-link-copy span, .ft-list-copy span { display: block; font-size: 12px; color: var(--ft-text-secondary); font-weight: 600; }
    .ft-tag, .ft-badge { justify-content: center; padding: 6px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .ft-tag { background: var(--ft-tag-bg); border: 1px solid var(--ft-tag-border); color: var(--ft-tag-text); }
    .ft-badge-ok { color: #166534; background: #dcfce7; }
    .ft-badge-pending { color: #92400e; background: #fef3c7; }
    .dark .ft-badge-ok { color: #86efac; background: rgba(34, 197, 94, 0.18); }
    .dark .ft-badge-pending { color: #fcd34d; background: rgba(217, 119, 6, 0.18); }
    .ft-table-wrap { border: 1px solid var(--ft-table-head-border); border-radius: 12px; background: var(--ft-card-bg); overflow-x: auto; }
    .ft-table { width: 100%; border-collapse: collapse; }
    .ft-table th { font-size: 11px; text-transform: uppercase; color: var(--ft-text-secondary); text-align: left; font-weight: 700; padding: 10px 8px; border-bottom: 1px solid var(--ft-table-head-border); }
    .ft-table td { font-size: 12px; color: var(--ft-text-primary); padding: 12px 8px; border-bottom: 1px solid var(--ft-table-row-border); vertical-align: middle; }
    .ft-table tbody tr:last-child td { border-bottom: 0; }
    .ft-empty { border: 1px dashed var(--ft-table-head-border); border-radius: 14px; padding: 22px; text-align: center; color: var(--ft-text-secondary); font-size: 13px; font-weight: 600; }
    .ft-card + .ft-card, .ft-section + .ft-section { margin-top: 14px; }

    @media (max-width: 1200px) { .ft-grid, .ft-grid-3 { grid-template-columns: 1fr; } }
    @media (max-width: 860px) { .ft-grid-2 { grid-template-columns: 1fr; } }
    @media (max-width: 640px) {
        .ft-dashboard-admin { padding: 14px; border-radius: 16px; }
        .ft-actions, .ft-action { width: 100%; }
        .ft-action { justify-content: center; }
        .ft-link-row, .ft-list-item, .ft-store-item, .ft-topbar, .ft-card-head { align-items: flex-start; flex-direction: column; }
        .ft-tag, .ft-badge { align-self: flex-start; }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <div class="ft-dashboard-admin">
        <div class="ft-topbar">
            <div class="ft-brand">
                <div class="ft-logo"><i class="fa-solid fa-chart-line"></i></div>
                <div>
                    <p class="ft-title">Painel Administrativo</p>
                    <p class="ft-subtitle">Mesma linguagem visual do painel do vendedor, com foco em operação global.</p>
                </div>
            </div>
            <div class="ft-actions">
                <a href="{{ route('orders.index') }}" class="ft-action ft-action-primary"><i class="fa-solid fa-list-check"></i><span>Ver pedidos</span></a>
                @if(auth()->user()->tenant_id === null || auth()->user()->tenant?->canAccess('pdv'))
                    <a href="{{ route('admin.quick-products.index') }}" class="ft-action ft-action-success"><i class="fa-solid fa-basket-shopping"></i><span>Produtos PDV</span></a>
                @endif
            </div>
        </div>

        @if($isTenantScopeMissing)
            <section class="ft-card">
                <div class="ft-card-head">
                    <div>
                        <p class="ft-card-title">Selecione uma empresa para iniciar</p>
                        <p class="ft-card-subtitle">O dashboard administrativo precisa de um tenant ativo para consolidar métricas e listas.</p>
                    </div>
                    <span class="ft-tag">Escopo vazio</span>
                </div>
                <div class="ft-empty">Escolha uma empresa no seletor global do sistema. Assim que um tenant estiver ativo, este painel exibirá pedidos, usuários, entregas, caixa e lojas no padrão visual do vendedor.</div>
            </section>
        @else
            <section class="ft-section">
                <div class="ft-kpi-stack">
                    @foreach($kpis as $kpi)
                        <article class="ft-kpi-card">
                            <div class="ft-kpi-head">
                                <span class="ft-kpi-label">{{ $kpi['label'] }}</span>
                                <span class="ft-icon {{ $toneClasses[$kpi['tone']] ?? 'ft-tone-primary' }}"><i class="fa-solid {{ $kpi['icon'] }}"></i></span>
                            </div>
                            <div class="ft-kpi-value">{{ $kpi['value'] }}</div>
                            <p class="ft-kpi-note">
                                @if($kpi['label'] === 'Pedidos totais')
                                    Volume consolidado de pedidos do tenant selecionado.
                                @elseif($kpi['label'] === 'Usuários ativos')
                                    Usuários vinculados às lojas da operação atual.
                                @elseif($kpi['label'] === 'Entregas pendentes')
                                    Solicitações aguardando ação da equipe administrativa.
                                @else
                                    Pedidos em análise de cancelamento.
                                @endif
                            </p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="ft-grid ft-section">
                <div>
                    <article class="ft-card">
                        <div class="ft-card-head">
                            <div>
                                <p class="ft-card-title">Operação central</p>
                                <p class="ft-card-subtitle">Atalhos principais para pedidos, logística e produção.</p>
                            </div>
                            <span class="ft-tag">{{ $managementLinks->count() }} módulos</span>
                        </div>
                        <div class="ft-link-list">
                            @foreach($managementLinks as $link)
                                <a href="{{ $link['href'] }}" class="ft-link-row">
                                    <div class="ft-link-main">
                                        <span class="ft-icon {{ $toneClasses[$link['tone']] ?? 'ft-tone-primary' }}"><i class="fa-solid {{ $link['icon'] }}"></i></span>
                                        <div class="ft-link-copy">
                                            <strong>{{ $link['label'] }}</strong>
                                            <span>{{ $link['description'] }}</span>
                                        </div>
                                    </div>
                                    <span class="ft-tag">{{ $link['badge'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </article>

                    @if($financeLinks->isNotEmpty())
                        <article class="ft-card">
                            <div class="ft-card-head">
                                <div>
                                    <p class="ft-card-title">Financeiro</p>
                                    <p class="ft-card-subtitle">Acesso rápido ao caixa e à movimentação operacional.</p>
                                </div>
                                <span class="ft-tag">Finance</span>
                            </div>
                            <div class="ft-link-list">
                                @foreach($financeLinks as $link)
                                    <a href="{{ $link['href'] }}" class="ft-link-row">
                                        <div class="ft-link-main">
                                            <span class="ft-icon {{ $toneClasses[$link['tone']] ?? 'ft-tone-success' }}"><i class="fa-solid {{ $link['icon'] }}"></i></span>
                                            <div class="ft-link-copy">
                                                <strong>{{ $link['label'] }}</strong>
                                                <span>{{ $link['description'] }}</span>
                                            </div>
                                        </div>
                                        <span class="ft-tag">{{ $link['badge'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </article>
                    @endif
                </div>

                <article class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Entregas pendentes</p>
                            <p class="ft-card-subtitle">Fila recente para priorização logística.</p>
                        </div>
                        <span class="ft-tag">{{ (int) ($stats['pending_delivery_requests'] ?? 0) }} pendentes</span>
                    </div>
                    @if($pendingDeliveries->isEmpty())
                        <div class="ft-empty">Nenhuma solicitação de entrega pendente no momento.</div>
                    @else
                        <div class="ft-list">
                            @foreach($pendingDeliveries as $delivery)
                                <a href="{{ route('delivery-requests.index') }}" class="ft-list-item">
                                    <div class="ft-list-main">
                                        <span class="ft-icon ft-tone-warning"><i class="fa-solid fa-truck-fast"></i></span>
                                        <div class="ft-list-copy">
                                            <strong>#{{ str_pad($delivery->order_id ?? 0, 6, '0', STR_PAD_LEFT) }} · {{ $delivery->order?->client?->name ?? 'Cliente avulso' }}</strong>
                                            <span>{{ $delivery->created_at?->format('d/m/Y H:i') }} · {{ ucfirst($delivery->status ?? 'pendente') }}</span>
                                        </div>
                                    </div>
                                    <span class="ft-badge ft-badge-pending">Aguardando</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>

            <section class="ft-grid-3 ft-section">
                <article class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Produtos e catálogo</p>
                            <p class="ft-card-subtitle">Estruture preços, catálogo e PDV no mesmo fluxo visual.</p>
                        </div>
                    </div>
                    <div class="ft-link-list">
                        @foreach($catalogLinks as $link)
                            <a href="{{ $link['href'] }}" class="ft-link-row">
                                <div class="ft-link-copy"><strong>{{ $link['label'] }}</strong></div>
                                <span class="ft-tag {{ $toneClasses[$link['tone']] ?? 'ft-tone-primary' }}">{{ strtoupper(substr($link['label'], 0, 3)) }}</span>
                            </a>
                        @endforeach
                    </div>
                </article>

                @if($systemLinks->isNotEmpty())
                    <article class="ft-card">
                        <div class="ft-card-head">
                            <div>
                                <p class="ft-card-title">Sistema e usuários</p>
                                <p class="ft-card-subtitle">Configuração global e governança do ambiente.</p>
                            </div>
                        </div>
                        <div class="ft-link-list">
                            @foreach($systemLinks as $link)
                                <a href="{{ $link['href'] }}" class="ft-link-row">
                                    <div class="ft-link-copy"><strong>{{ $link['label'] }}</strong></div>
                                    <span class="ft-tag {{ $toneClasses[$link['tone']] ?? 'ft-tone-primary' }}">{{ strtoupper(substr($link['label'], 0, 3)) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endif

                <article class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Lojas da operação</p>
                            <p class="ft-card-subtitle">Resumo rápido das unidades ativas no tenant atual.</p>
                        </div>
                        <span class="ft-tag">{{ number_format((int) ($stats['total_stores'] ?? 0), 0, ',', '.') }} lojas</span>
                    </div>
                    @if($storesCollection->isEmpty())
                        <div class="ft-empty">Nenhuma loja cadastrada para o tenant selecionado.</div>
                    @else
                        <div class="ft-list">
                            @foreach($storesCollection->take(5) as $store)
                                <a href="{{ route('admin.stores.index') }}" class="ft-store-item">
                                    <div class="ft-list-main">
                                        <span class="ft-icon ft-tone-primary"><i class="fa-solid fa-store"></i></span>
                                        <div class="ft-list-copy">
                                            <strong>{{ $store->name }}</strong>
                                            <span>{{ $store->isMain() ? 'Loja principal' : 'Sub-loja' }}@if($store->parent) · Vinculada a {{ $store->parent->name }} @endif</span>
                                        </div>
                                    </div>
                                    <span class="ft-badge {{ $store->active ? 'ft-badge-ok' : 'ft-badge-pending' }}">{{ $store->active ? 'Ativa' : 'Inativa' }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>

            <section class="ft-grid-2 ft-section">
                <article class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Pedidos recentes</p>
                            <p class="ft-card-subtitle">Últimos pedidos registrados na operação.</p>
                        </div>
                        <span class="ft-tag">Pedidos</span>
                    </div>
                    @if($recentOrders->isEmpty())
                        <div class="ft-empty">Nenhum pedido recente para exibir.</div>
                    @else
                        <div class="ft-table-wrap">
                            <table class="ft-table">
                                <thead><tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $order->client?->name ?? 'Cliente avulso' }}</td>
                                            <td>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                                            <td>{{ $order->status?->name ?? 'Sem status' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </article>

                <article class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">{{ auth()->user()->tenant_id === null ? 'Usuários recentes' : 'Caixa recente' }}</p>
                            <p class="ft-card-subtitle">{{ auth()->user()->tenant_id === null ? 'Últimos acessos cadastrados nas lojas.' : 'Últimas movimentações financeiras registradas.' }}</p>
                        </div>
                        <span class="ft-tag">{{ auth()->user()->tenant_id === null ? 'Usuários' : 'Caixa' }}</span>
                    </div>
                    @if(auth()->user()->tenant_id === null)
                        @if($recentUsers->isEmpty())
                            <div class="ft-empty">Nenhum usuário recente para exibir.</div>
                        @else
                            <div class="ft-list">
                                @foreach($recentUsers as $user)
                                    <div class="ft-list-item">
                                        <div class="ft-list-main">
                                            <span class="ft-icon ft-tone-success"><i class="fa-solid fa-user-check"></i></span>
                                            <div class="ft-list-copy">
                                                <strong>{{ $user->name }}</strong>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                        </div>
                                        <span class="ft-tag">{{ strtoupper($user->role ?? 'user') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        @if($recentCashTransactions->isEmpty())
                            <div class="ft-empty">Nenhuma movimentação de caixa recente.</div>
                        @else
                            <div class="ft-list">
                                @foreach($recentCashTransactions as $transaction)
                                    <div class="ft-list-item">
                                        <div class="ft-list-main">
                                            <span class="ft-icon ft-tone-success"><i class="fa-solid fa-sack-dollar"></i></span>
                                            <div class="ft-list-copy">
                                                <strong>{{ $transaction->description ?? 'Movimentação de caixa' }}</strong>
                                                <span>{{ $transaction->created_at?->format('d/m/Y H:i') }} · {{ $transaction->user?->name ?? 'Sistema' }}</span>
                                            </div>
                                        </div>
                                        <span class="ft-tag">R$ {{ number_format((float) ($transaction->amount ?? 0), 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </article>
            </section>
        @endif
    </div>
</div>
@endsection
