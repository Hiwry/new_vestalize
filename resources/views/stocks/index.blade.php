@extends('layouts.admin')

@section('content')

@if(request('view') === 'hub')
@php
    $cards = [
        [
            'title' => 'Inventario Geral',
            'desc' => 'Consulta completa de tecidos, cortes, cores e quantidades disponiveis.',
            'route' => route('stocks.index'),
            'accent' => '#10b981',
            'icon' => 'fa-clipboard-list',
        ],
        [
            'title' => 'Dashboard',
            'desc' => 'Visao consolidada com indicadores e graficos do estoque.',
            'route' => route('stocks.dashboard'),
            'accent' => '#3b82f6',
            'icon' => 'fa-chart-pie',
        ],
        [
            'title' => 'Solicitacoes',
            'desc' => 'Acompanhe pedidos internos e fluxo de aprovacao de materiais.',
            'route' => route('stock-requests.index'),
            'accent' => '#f59e0b',
            'icon' => 'fa-file-invoice',
        ],
        [
            'title' => 'Historico',
            'desc' => 'Consulte entradas, saidas e movimentacoes registradas no modulo.',
            'route' => route('stocks.history'),
            'accent' => '#64748b',
            'icon' => 'fa-clock-rotate-left',
        ],
        [
            'title' => 'Maquinas',
            'desc' => 'Gerencie maquinas de costura e equipamentos vinculados ao estoque.',
            'route' => route('sewing-machines.index'),
            'accent' => '#8b5cf6',
            'icon' => 'fa-scissors',
        ],
        [
            'title' => 'Suprimentos',
            'desc' => 'Controle linhas, agulhas, insumos e materiais auxiliares de producao.',
            'route' => route('production-supplies.index'),
            'accent' => '#ec4899',
            'icon' => 'fa-spool-of-thread',
        ],
        [
            'title' => 'Uniformes e EPI',
            'desc' => 'Acesse o controle de entrega e disponibilidade de uniformes e EPI.',
            'route' => route('uniforms.index'),
            'accent' => '#f43f5e',
            'icon' => 'fa-shirt',
        ],
    ];
@endphp

<style>
    .stocks-hub {
        --sh-surface-from: #f3f4f8;
        --sh-surface-to: #eceff4;
        --sh-surface-border: #d8dce6;
        --sh-text-primary: #0f172a;
        --sh-text-secondary: #64748b;
        --sh-card-bg: #ffffff;
        --sh-card-border: #dde2ea;
        --sh-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --sh-action-primary: #6d28d9;
        --sh-action-primary-hover: #7c3aed;
        --sh-action-success: #059669;
        --sh-action-success-hover: #10b981;
        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%);
        border: 1px solid var(--sh-surface-border);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        color: var(--sh-text-primary);
    }

    .dark .stocks-hub {
        --sh-surface-from: #0f172a;
        --sh-surface-to: #0b1322;
        --sh-surface-border: rgba(148, 163, 184, 0.25);
        --sh-text-primary: #e2e8f0;
        --sh-text-secondary: #94a3b8;
        --sh-card-bg: #111827;
        --sh-card-border: rgba(148, 163, 184, 0.22);
        --sh-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        box-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
    }

    .sh-topbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 16px; }
    .sh-brand { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1 1 320px; }
    .sh-logo { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #6d28d9, #8b5cf6); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .sh-title { font-size: 24px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; }
    .sh-subtitle { margin-top: 3px; font-size: 13px; color: var(--sh-text-secondary); font-weight: 600; }
    .sh-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .sh-action { height: 38px; border-radius: 12px; padding: 0 14px; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; text-decoration: none; color: #fff !important; transition: transform .18s ease, box-shadow .2s ease, filter .2s ease; white-space: nowrap; }
    .sh-action:hover { transform: translateY(-1px); filter: brightness(1.03); }
    .sh-action-primary { background: linear-gradient(135deg, var(--sh-action-primary), var(--sh-action-primary-hover)); box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25); }
    .sh-action-success { background: linear-gradient(135deg, var(--sh-action-success), var(--sh-action-success-hover)); box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25); }
    .sh-intro { background: var(--sh-card-bg); border: 1px solid var(--sh-card-border); border-radius: 14px; box-shadow: var(--sh-card-shadow); padding: 14px 16px; margin-bottom: 14px; }
    .sh-intro p { font-size: 14px; color: var(--sh-text-secondary); font-weight: 600; }
    .sh-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .sh-card { display: flex; flex-direction: column; gap: 12px; min-height: 190px; background: var(--sh-card-bg); border: 1px solid var(--sh-card-border); border-radius: 14px; box-shadow: var(--sh-card-shadow); padding: 16px; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .sh-card:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--sh-accent) 40%, var(--sh-card-border)); box-shadow: 0 16px 28px rgba(15, 23, 42, 0.1); }
    .sh-card-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .sh-icon { width: 42px; height: 42px; border-radius: 12px; border: 1px solid color-mix(in srgb, var(--sh-accent) 35%, transparent); background: color-mix(in srgb, var(--sh-accent) 14%, transparent); color: var(--sh-accent); display: inline-flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
    .sh-card-title { font-size: 20px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: var(--sh-text-primary); }
    .sh-card-desc { font-size: 13px; line-height: 1.45; color: var(--sh-text-secondary); font-weight: 600; }
    .sh-card-foot { margin-top: auto; padding-top: 10px; border-top: 1px solid var(--sh-card-border); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .sh-card-link { font-size: 12px; font-weight: 700; color: var(--sh-text-secondary); }
    .sh-card-arrow { width: 30px; height: 30px; border-radius: 999px; border: 1px solid var(--sh-card-border); display: inline-flex; align-items: center; justify-content: center; color: var(--sh-text-secondary); }
    .sh-card:hover .sh-card-link, .sh-card:hover .sh-card-arrow { color: var(--sh-accent); border-color: color-mix(in srgb, var(--sh-accent) 40%, var(--sh-card-border)); }

    @media (max-width: 1200px) {
        .sh-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 760px) {
        .stocks-hub { padding: 14px; border-radius: 16px; }
        .sh-title { font-size: 20px; }
        .sh-actions { width: 100%; }
        .sh-action { width: 100%; justify-content: center; }
        .sh-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="stocks-hub">
        <div class="sh-topbar">
            <div class="sh-brand">
                <span class="sh-logo"><i class="fa-solid fa-boxes-stacked"></i></span>
                <div>
                    <h1 class="sh-title">Central de Estoque</h1>
                    <p class="sh-subtitle">Escolha a operacao e acesse rapidamente os fluxos mais usados do modulo</p>
                </div>
            </div>

            <div class="sh-actions">
                <a href="{{ route('stocks.index') }}" class="sh-action sh-action-success">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Abrir Estoque</span>
                </a>
                <a href="{{ route('stocks.create') }}" class="sh-action sh-action-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Novo Item</span>
                </a>
            </div>
        </div>

        <div class="sh-intro">
            <p>Todos os fluxos de estoque foram reunidos em uma navegacao unica para facilitar consulta, movimentacao e gestao operacional no dia a dia.</p>
        </div>

        <div class="sh-grid">
            @foreach($cards as $card)
                <a href="{{ $card['route'] }}" class="sh-card" style="--sh-accent: {{ $card['accent'] }}">
                    <div class="sh-card-head">
                        <span class="sh-icon">
                            <i class="fa-solid {{ $card['icon'] }}"></i>
                        </span>
                    </div>

                    <div>
                        <h2 class="sh-card-title">{{ $card['title'] }}</h2>
                        <p class="sh-card-desc">{{ $card['desc'] }}</p>
                    </div>

                    <div class="sh-card-foot">
                        <span class="sh-card-link">Ir agora</span>
                        <span class="sh-card-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
@else
    @include('stocks.table')
@endif

@endsection
