@extends('layouts.admin')

@section('content')
@php
    $dashboardTitle = Auth::user()->tenant
        ? 'Painel Financeiro - ' . Auth::user()->tenant->name
        : 'Painel Financeiro - Administrativo';
@endphp
<div class="max-w-[1520px] mx-auto py-4 md:py-6">
    @include('dashboard.partials.fintrack-style', [
        'dashboardTitle' => $dashboardTitle,
        'showQuickActions' => true,
        'quickActions' => [
            [
                'label' => 'Abrir PDV',
                'href' => route('pdv.index'),
                'icon' => 'fa-cash-register',
                'variant' => 'success',
            ],
            [
                'label' => 'Novo Pedido',
                'href' => route('orders.wizard.start'),
                'icon' => 'fa-plus',
                'variant' => 'primary',
            ],
        ],
    ])

    @if(!empty($showNetworkInsights))
        @php
            $topStore = data_get($networkInsights, 'top_store');
        @endphp
        <div class="ft-dashboard mt-6">
            <div class="ft-card-head">
                <div>
                    <p class="ft-card-title">Visao de Rede</p>
                    <p class="ft-card-subtitle">Comparativo consolidado entre todas as lojas do tenant.</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--ft-text-secondary);">Escopo</p>
                    <p class="text-sm font-bold" style="color: var(--ft-text-primary);">Admin geral</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
                <section class="ft-card">
                    <p class="text-[11px] font-semibold uppercase tracking-wide mb-2" style="color: var(--ft-text-secondary);">Lojas ativas</p>
                    <p class="ft-kpi-value">{{ number_format((int) data_get($networkInsights, 'total_active_stores', 0), 0, ',', '.') }}</p>
                    <p class="text-xs mt-2" style="color: var(--ft-text-secondary);">
                        {{ number_format((int) data_get($networkInsights, 'stores_with_sales', 0), 0, ',', '.') }} com vendas no periodo
                    </p>
                </section>

                <section class="ft-card">
                    <p class="text-[11px] font-semibold uppercase tracking-wide mb-2" style="color: var(--ft-text-secondary);">Vendedores ativos</p>
                    <p class="ft-kpi-value">{{ number_format((int) data_get($networkInsights, 'active_vendors', 0), 0, ',', '.') }}</p>
                    <p class="text-xs mt-2" style="color: var(--ft-text-secondary);">
                        Usuarios com pedidos capturados no intervalo
                    </p>
                </section>

                <section class="ft-card">
                    <p class="text-[11px] font-semibold uppercase tracking-wide mb-2" style="color: var(--ft-text-secondary);">Taxa de quitacao</p>
                    <p class="ft-kpi-value">{{ number_format((float) data_get($networkInsights, 'approval_rate', 0), 1, ',', '.') }}%</p>
                    <p class="text-xs mt-2" style="color: var(--ft-text-secondary);">
                        Pedidos pagos e aprovados pelo caixa
                    </p>
                </section>

                <section class="ft-card">
                    <p class="text-[11px] font-semibold uppercase tracking-wide mb-2" style="color: var(--ft-text-secondary);">Lojas com atencao</p>
                    <p class="ft-kpi-value">{{ number_format((int) data_get($networkInsights, 'stores_needing_attention', 0), 0, ',', '.') }}</p>
                    <p class="text-xs mt-2" style="color: var(--ft-text-secondary);">
                        Sem vendas ou com logistica pendente
                    </p>
                </section>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <section class="ft-card">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Destaques da rede</p>
                            <p class="ft-card-subtitle">Resumo do que exige decisao rapida.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--ft-text-secondary);">Melhor loja do periodo</p>
                            <p class="text-lg font-bold mt-1" style="color: var(--ft-text-primary);">
                                {{ data_get($topStore, 'name', 'Sem dados') }}
                            </p>
                            <p class="text-xs mt-1" style="color: var(--ft-text-secondary);">
                                R$ {{ number_format((float) data_get($topStore, 'total_faturamento', 0), 2, ',', '.') }}
                                em {{ number_format((int) data_get($topStore, 'total_pedidos', 0), 0, ',', '.') }} pedidos
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                            <div class="rounded-xl px-4 py-3" style="background: rgba(16, 185, 129, 0.10); border: 1px solid rgba(16, 185, 129, 0.18);">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Receita media por loja</p>
                                <p class="text-base font-bold mt-1" style="color: var(--ft-text-primary);">
                                    R$ {{ number_format((float) data_get($networkInsights, 'avg_revenue_per_store', 0), 2, ',', '.') }}
                                </p>
                            </div>

                            <div class="rounded-xl px-4 py-3" style="background: rgba(245, 158, 11, 0.10); border: 1px solid rgba(245, 158, 11, 0.18);">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300">Logistica pendente</p>
                                <p class="text-base font-bold mt-1" style="color: var(--ft-text-primary);">
                                    {{ number_format((int) data_get($networkInsights, 'pending_logistics_total', 0), 0, ',', '.') }} solicitacoes
                                </p>
                            </div>

                            <div class="rounded-xl px-4 py-3" style="background: rgba(239, 68, 68, 0.10); border: 1px solid rgba(239, 68, 68, 0.18);">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-red-600 dark:text-red-300">Recebimentos pendentes</p>
                                <p class="text-base font-bold mt-1" style="color: var(--ft-text-primary);">
                                    R$ {{ number_format((float) data_get($networkInsights, 'pending_receivables', 0), 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ft-card xl:col-span-2">
                    <div class="ft-card-head">
                        <div>
                            <p class="ft-card-title">Ranking por loja</p>
                            <p class="ft-card-subtitle">Pedidos, faturamento, ticket medio e gargalos por unidade.</p>
                        </div>
                    </div>

                    <div class="ft-table-wrap">
                        <table class="ft-table">
                            <thead>
                                <tr>
                                    <th>Loja</th>
                                    <th>Pedidos</th>
                                    <th>Faturamento</th>
                                    <th>Ticket medio</th>
                                    <th>Vendedores</th>
                                    <th>Logistica</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storePerformanceRanking as $storeData)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">{{ $storeData['name'] }}</span>
                                                @if(!empty($storeData['is_main']))
                                                    <span class="ft-badge ft-badge-ok">Principal</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ number_format((int) ($storeData['total_pedidos'] ?? 0), 0, ',', '.') }}</td>
                                        <td>R$ {{ number_format((float) ($storeData['total_faturamento'] ?? 0), 2, ',', '.') }}</td>
                                        <td>R$ {{ number_format((float) ($storeData['ticket_medio'] ?? 0), 2, ',', '.') }}</td>
                                        <td>{{ number_format((int) ($storeData['vendedores_ativos'] ?? 0), 0, ',', '.') }}</td>
                                        <td>
                                            @if(($storeData['pendencias_logisticas'] ?? 0) > 0)
                                                <span class="ft-badge ft-badge-pending">
                                                    {{ number_format((int) $storeData['pendencias_logisticas'], 0, ',', '.') }} pend.
                                                </span>
                                            @else
                                                <span class="ft-badge ft-badge-ok">Sem fila</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Sem dados consolidados para o periodo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    @endif
</div>
@endsection
