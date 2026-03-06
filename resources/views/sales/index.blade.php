@extends('layouts.admin')

@section('content')
@php
    $cards = [
        [
            'title' => 'Pedidos',
            'desc' => 'Criação, edição e acompanhamento completo de pedidos.',
            'route' => route('orders.index'),
            'accent' => '#7c3aed',
            'icon' => 'fa-shopping-bag',
        ],
        [
            'title' => 'Orçamentos',
            'desc' => 'Monte propostas e converta em pedido com poucos cliques.',
            'route' => route('budget.index'),
            'accent' => '#3b82f6',
            'icon' => 'fa-file-invoice-dollar',
        ],
        [
            'title' => 'PDV',
            'desc' => 'Venda rápida em balcão com checkout otimizado.',
            'route' => route('pdv.index'),
            'accent' => '#059669',
            'icon' => 'fa-cash-register',
        ],
        [
            'title' => 'Personalizados',
            'desc' => 'Gerencie pedidos sob medida com fluxo dedicado.',
            'route' => route('personalized.orders.index'),
            'accent' => '#ec4899',
            'icon' => 'fa-pen-ruler',
        ],
        [
            'title' => 'Clientes',
            'desc' => 'Acesse rapidamente sua base de clientes e histórico.',
            'route' => route('clients.index'),
            'accent' => '#10b981',
            'icon' => 'fa-users',
        ],
        [
            'title' => 'Kanban',
            'desc' => 'Visualize etapas da produção em tempo real.',
            'route' => route('kanban.index'),
            'accent' => '#d946ef',
            'icon' => 'fa-layer-group',
        ],
        [
            'title' => 'Link de Orçamento',
            'desc' => 'Canal online de captação de pedidos em lançamento.',
            'route' => '#',
            'accent' => '#64748b',
            'icon' => 'fa-paper-plane',
            'disabled' => true,
            'badge' => 'Em breve',
        ],
    ];
@endphp

<style>
    .sales-hub {
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

    .dark .sales-hub {
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

    .sh-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .sh-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1 1 320px;
    }

    .sh-logo {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .sh-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .sh-subtitle {
        margin-top: 3px;
        font-size: 13px;
        color: var(--sh-text-secondary);
        font-weight: 600;
    }

    .sh-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sh-action {
        height: 38px;
        border-radius: 12px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: #fff !important;
        transition: transform .18s ease, box-shadow .2s ease, filter .2s ease;
        white-space: nowrap;
    }

    .sh-action,
    .sh-action span,
    .sh-action i,
    .sh-action svg,
    .sh-action svg * {
        color: #ffffff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    .sh-action:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .sh-action-primary {
        background: linear-gradient(135deg, var(--sh-action-primary), var(--sh-action-primary-hover));
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25);
    }

    .sh-action-success {
        background: linear-gradient(135deg, var(--sh-action-success), var(--sh-action-success-hover));
        box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25);
    }

    .sh-intro {
        background: var(--sh-card-bg);
        border: 1px solid var(--sh-card-border);
        border-radius: 14px;
        box-shadow: var(--sh-card-shadow);
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .sh-intro p {
        font-size: 14px;
        color: var(--sh-text-secondary);
        font-weight: 600;
    }

    .sh-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sh-card {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 190px;
        background: var(--sh-card-bg);
        border: 1px solid var(--sh-card-border);
        border-radius: 14px;
        box-shadow: var(--sh-card-shadow);
        padding: 16px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .sh-card:not(.is-disabled):hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--sh-accent) 40%, var(--sh-card-border));
        box-shadow: 0 16px 28px rgba(15, 23, 42, 0.1);
    }

    .sh-card.is-disabled {
        cursor: not-allowed;
        opacity: .75;
    }

    .sh-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .sh-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid color-mix(in srgb, var(--sh-accent) 35%, transparent);
        background: color-mix(in srgb, var(--sh-accent) 14%, transparent);
        color: var(--sh-accent);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .sh-badge {
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        background: #f3f4f6;
        color: #475569;
    }

    .dark .sh-badge {
        background: rgba(148, 163, 184, .15);
        color: #cbd5e1;
    }

    .sh-card-title {
        font-size: 20px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--sh-text-primary);
    }

    .sh-card-desc {
        font-size: 13px;
        line-height: 1.45;
        color: var(--sh-text-secondary);
        font-weight: 600;
    }

    .sh-card-foot {
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px solid var(--sh-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .sh-card-link {
        font-size: 12px;
        font-weight: 700;
        color: var(--sh-text-secondary);
    }

    .sh-card-arrow {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        border: 1px solid var(--sh-card-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--sh-text-secondary);
    }

    .sh-card:not(.is-disabled):hover .sh-card-link,
    .sh-card:not(.is-disabled):hover .sh-card-arrow {
        color: var(--sh-accent);
        border-color: color-mix(in srgb, var(--sh-accent) 40%, var(--sh-card-border));
    }

    @media (max-width: 1200px) {
        .sh-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .sales-hub {
            padding: 14px;
            border-radius: 16px;
        }

        .sh-title {
            font-size: 20px;
        }

        .sh-actions {
            width: 100%;
        }

        .sh-action {
            width: 100%;
            justify-content: center;
        }

        .sh-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="sales-hub">
        <div class="sh-topbar">
            <div class="sh-brand">
                <span class="sh-logo"><i class="fa-solid fa-bag-shopping"></i></span>
                <div>
                    <h1 class="sh-title">Central de Vendas</h1>
                    <p class="sh-subtitle">Escolha o fluxo de vendas e acesse as operações mais usadas</p>
                </div>
            </div>

            <div class="sh-actions">
                <a href="{{ route('pdv.index') }}" class="sh-action sh-action-success">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Abrir PDV</span>
                </a>
                <a href="{{ route('orders.wizard.start') }}" class="sh-action sh-action-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Novo Pedido</span>
                </a>
            </div>
        </div>

        <div class="sh-intro">
            <p>Todos os módulos de vendas foram padronizados no mesmo estilo visual do dashboard para facilitar navegação, leitura e operação diária.</p>
        </div>

        <div class="sh-grid">
            @foreach($cards as $card)
                @php
                    $disabled = (bool) ($card['disabled'] ?? false);
                @endphp
                <a
                    href="{{ $disabled ? '#' : $card['route'] }}"
                    @if($disabled) aria-disabled="true" @endif
                    class="sh-card {{ $disabled ? 'is-disabled' : '' }}"
                    style="--sh-accent: {{ $card['accent'] }}"
                >
                    <div class="sh-card-head">
                        <span class="sh-icon">
                            <i class="fa-solid {{ $card['icon'] }}"></i>
                        </span>
                        @if(!empty($card['badge']))
                            <span class="sh-badge">{{ $card['badge'] }}</span>
                        @endif
                    </div>

                    <div>
                        <h2 class="sh-card-title">{{ $card['title'] }}</h2>
                        <p class="sh-card-desc">{{ $card['desc'] }}</p>
                    </div>

                    <div class="sh-card-foot">
                        <span class="sh-card-link">{{ $disabled ? 'Indisponível' : 'Ir agora' }}</span>
                        @if(!$disabled)
                            <span class="sh-card-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
