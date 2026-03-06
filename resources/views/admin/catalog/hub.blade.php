@extends('layouts.admin')

@section('content')
@php
    $storeCode = auth()->user()->tenant->store_code ?? null;
    $catalogUrl = $storeCode ? route('catalog.show', ['storeCode' => strtolower($storeCode)]) : null;

    $cards = [
        [
            'title' => 'Pedidos do Catalogo',
            'desc' => 'Visualize, acompanhe e converta os pedidos captados pelo catalogo publico.',
            'route' => route('admin.catalog-orders.index'),
            'accent' => '#7c3aed',
            'icon' => 'fa-cart-shopping',
        ],
        [
            'title' => 'Produtos',
            'desc' => 'Defina o que entra na vitrine digital e mantenha o mix publicado sempre atualizado.',
            'route' => route('admin.products.index'),
            'accent' => '#3b82f6',
            'icon' => 'fa-box',
        ],
        [
            'title' => 'Gateway de Pagamento',
            'desc' => 'Ajuste pagamento, aprovacao e conversao automatica dos pedidos do catalogo.',
            'route' => route('admin.catalog-gateway.edit'),
            'accent' => '#059669',
            'icon' => 'fa-credit-card',
        ],
    ];

    $quickActions = array_filter([
        $catalogUrl ? [
            'label' => 'Abrir Catalogo',
            'href' => $catalogUrl,
            'icon' => 'fa-arrow-up-right-from-square',
            'variant' => 'success',
            'target' => '_blank',
        ] : null,
        [
            'label' => 'Pedidos do Catalogo',
            'href' => route('admin.catalog-orders.index'),
            'icon' => 'fa-list-check',
            'variant' => 'primary',
        ],
    ]);
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

    .sh-intro,
    .sh-panel {
        background: var(--sh-card-bg);
        border: 1px solid var(--sh-card-border);
        border-radius: 14px;
        box-shadow: var(--sh-card-shadow);
    }

    .sh-intro {
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .sh-intro p {
        font-size: 14px;
        color: var(--sh-text-secondary);
        font-weight: 600;
    }

    .sh-meta-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(260px, 0.9fr);
        gap: 14px;
        margin-bottom: 14px;
    }

    .sh-panel {
        padding: 16px;
    }

    .sh-panel-title {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--sh-text-secondary);
        margin-bottom: 8px;
    }

    .sh-link-shell {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sh-link-box {
        min-height: 42px;
        flex: 1 1 320px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 12px;
        border: 1px solid var(--sh-card-border);
        background: color-mix(in srgb, var(--sh-card-bg) 88%, #f8fafc);
        padding: 0 12px;
    }

    .sh-link-box i {
        color: var(--sh-text-secondary);
        font-size: 12px;
    }

    .sh-link-input {
        width: 100%;
        border: 0;
        background: transparent;
        outline: 0;
        color: var(--sh-text-primary);
        font-size: 12px;
        font-weight: 600;
    }

    .sh-copy {
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--sh-card-border);
        background: var(--sh-card-bg);
        color: var(--sh-text-primary);
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        transition: transform .18s ease, border-color .2s ease, color .2s ease;
    }

    .sh-copy:hover {
        transform: translateY(-1px);
        color: #6d28d9;
        border-color: rgba(109, 40, 217, 0.25);
    }

    .sh-copy.is-copied {
        color: #059669;
        border-color: rgba(5, 150, 105, 0.26);
    }

    .sh-status {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .sh-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 12px;
        background: color-mix(in srgb, #10b981 12%, transparent);
        color: #047857;
        font-size: 12px;
        font-weight: 800;
    }

    .dark .sh-status-badge {
        color: #a7f3d0;
        background: rgba(16, 185, 129, 0.14);
    }

    .sh-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.14);
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

        .sh-meta-grid {
            grid-template-columns: 1fr;
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

        .sh-link-shell {
            flex-direction: column;
            align-items: stretch;
        }

        .sh-copy {
            justify-content: center;
        }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="sales-hub">
        <div class="sh-topbar">
            <div class="sh-brand">
                <span class="sh-logo"><i class="fa-solid fa-store"></i></span>
                <div>
                    <h1 class="sh-title">Central do Catalogo</h1>
                    <p class="sh-subtitle">Gerencie vitrine, pedidos online e operacao comercial no mesmo fluxo visual de vendas</p>
                </div>
            </div>

            <div class="sh-actions">
                @foreach($quickActions as $action)
                    @php
                        $variantClass = ($action['variant'] ?? 'primary') === 'success' ? 'sh-action-success' : 'sh-action-primary';
                    @endphp
                    <a
                        href="{{ $action['href'] }}"
                        class="sh-action {{ $variantClass }}"
                        @if(($action['target'] ?? null) === '_blank') target="_blank" rel="noopener noreferrer" @endif
                    >
                        <i class="fa-solid {{ $action['icon'] }}"></i>
                        <span>{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="sh-intro">
            <p>O hub do catalogo agora segue a mesma UX/UI de vendas para manter leitura, hierarquia visual e acesso rapido consistentes em toda a operacao comercial.</p>
        </div>

        <div class="sh-meta-grid">
            <section class="sh-panel">
                <p class="sh-panel-title">Link Publico do Catalogo</p>
                @if($catalogUrl)
                    <div class="sh-link-shell">
                        <div class="sh-link-box">
                            <i class="fa-solid fa-link"></i>
                            <input type="text" id="catalog-url-input" class="sh-link-input" value="{{ $catalogUrl }}" readonly>
                        </div>
                        <button type="button" class="sh-copy" id="copy-catalog-btn" onclick="copyCatalogUrl()">
                            <i class="fa-solid fa-copy" id="copy-icon"></i>
                            <span id="copy-text">Copiar link</span>
                        </button>
                    </div>
                @else
                    <p class="sh-card-desc">Defina um `store_code` para liberar o link publico do catalogo.</p>
                @endif
            </section>

            <section class="sh-panel">
                <p class="sh-panel-title">Status do Canal</p>
                <div class="sh-status">
                    <div>
                        <p class="sh-card-title" style="font-size: 18px;">{{ auth()->user()->tenant->name ?? 'Minha Loja' }}</p>
                        <p class="sh-card-desc">Catalogo online pronto para divulgar produtos e receber pedidos.</p>
                    </div>
                    <span class="sh-status-badge">
                        <span class="sh-status-dot"></span>
                        Canal ativo
                    </span>
                </div>
            </section>
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
                        <span class="sh-card-link">{{ $disabled ? 'Indisponivel' : 'Ir agora' }}</span>
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

@push('scripts')
<script>
    async function copyCatalogUrl() {
        const input = document.getElementById('catalog-url-input');
        const button = document.getElementById('copy-catalog-btn');
        const text = document.getElementById('copy-text');
        const icon = document.getElementById('copy-icon');

        if (!input || !button || !text || !icon) {
            return;
        }

        try {
            await navigator.clipboard.writeText(input.value);
            button.classList.add('is-copied');
            text.textContent = 'Copiado';
            icon.className = 'fa-solid fa-check';

            setTimeout(function() {
                button.classList.remove('is-copied');
                text.textContent = 'Copiar link';
                icon.className = 'fa-solid fa-copy';
            }, 1800);
        } catch (error) {
            input.select();
            document.execCommand('copy');
            button.classList.add('is-copied');
            text.textContent = 'Copiado';
            icon.className = 'fa-solid fa-check';

            setTimeout(function() {
                button.classList.remove('is-copied');
                text.textContent = 'Copiar link';
                icon.className = 'fa-solid fa-copy';
            }, 1800);
        }
    }
</script>
@endpush
