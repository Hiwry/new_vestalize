@php
$groupCollection = collect($groupedStocks ?? []);
$totalGroups = $groupCollection->count();
$totalAvailable = (int) $groupCollection->sum('total_available');
$reservedTotal = (int) $groupCollection->sum('total_reserved');
$statusStats = ['in_stock' => 0, 'low_stock' => 0, 'out_stock' => 0];

foreach ($groupCollection as $group) {
    $minStockTotal = collect($group['sizes'] ?? [])->sum(function ($sizeData) {
        return max(1, (int) ($sizeData['min_stock'] ?? 5));
    });

    if ($minStockTotal <= 0) {
        $minStockTotal = 5;
    }

    $available = (int) ($group['total_available'] ?? 0);

    if ($available <= 0) {
        $statusStats['out_stock']++;
    } elseif ($available < $minStockTotal) {
        $statusStats['low_stock']++;
    } else {
        $statusStats['in_stock']++;
    }
}

$criticalGroups = $statusStats['low_stock'] + $statusStats['out_stock'];
@endphp

<style>
.fsb-shell{position:relative;overflow:hidden;border-radius:26px;padding:22px;border:1px solid #d6dde8;background:radial-gradient(950px 360px at 110% 120%,rgba(105,63,222,.16),transparent 52%),radial-gradient(500px 240px at -12% -24%,rgba(59,130,246,.14),transparent 66%),linear-gradient(180deg,#f6f8fc 0%,#eef2f8 100%);box-shadow:0 28px 60px rgba(15,23,42,.12)}
.dark .fsb-shell{border-color:rgba(148,163,184,.24);background:radial-gradient(950px 360px at 110% 120%,rgba(124,58,237,.22),transparent 52%),radial-gradient(500px 240px at -12% -24%,rgba(37,99,235,.2),transparent 66%),linear-gradient(180deg,#0d1424 0%,#0b1220 100%);box-shadow:0 28px 60px rgba(2,6,23,.6)}
.fsb-top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
.fsb-kicker{margin:0;font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#6366f1}
.fsb-title{margin-top:8px;font-size:clamp(1.65rem,1.3rem + 1vw,2.2rem);line-height:1.05;letter-spacing:-.03em;font-weight:800;color:#0f172a}
.dark .fsb-title{color:#e2e8f0}
.fsb-subtitle{margin-top:8px;font-size:13px;font-weight:600;color:#64748b}
.dark .fsb-subtitle{color:#93a4bd}
.fsb-actions,.fsb-filter-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fsb-btn{height:40px;border-radius:12px;padding:0 14px;display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:800;text-decoration:none;transition:transform .18s ease,filter .2s ease;white-space:nowrap}
.fsb-btn:hover{transform:translateY(-1px);filter:brightness(1.03)}
.fsb-btn-soft{color:#334155;border:1px solid #cfd7e6;background:#fff}
.dark .fsb-btn-soft{color:#cbd5e1;border-color:rgba(148,163,184,.3);background:rgba(15,23,42,.75)}
.fsb-btn-primary{color:#fff !important;border:1px solid transparent;background:linear-gradient(135deg,#6d28d9,#7c3aed);box-shadow:0 14px 24px rgba(109,40,217,.28)}
.fsb-metrics{margin-top:18px;display:grid;gap:12px;grid-template-columns:repeat(4,minmax(0,1fr))}
.fsb-metric{border-radius:16px;padding:14px;border:1px solid #d4dceb;background:rgba(255,255,255,.85);backdrop-filter:blur(8px)}
.dark .fsb-metric{border-color:rgba(148,163,184,.24);background:rgba(15,23,42,.7)}
.fsb-metric-label{margin:0;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#64748b}
.dark .fsb-metric-label{color:#94a3b8}
.fsb-metric-value{margin-top:8px;font-size:28px;line-height:1;letter-spacing:-.02em;font-weight:800;color:#111827}
.dark .fsb-metric-value{color:#f1f5f9}
.fsb-toolbar{margin-top:14px;border-radius:18px;border:1px solid #d6deea;padding:14px;background:rgba(255,255,255,.9);box-shadow:0 16px 34px rgba(15,23,42,.06)}
.dark .fsb-toolbar{border-color:rgba(148,163,184,.22);background:rgba(15,23,42,.74);box-shadow:none}
.fsb-fields{display:grid;grid-template-columns:minmax(220px,1.2fr) minmax(150px,.95fr) minmax(210px,1.15fr) repeat(3,minmax(150px,.9fr));gap:10px}
.fsb-input,.fsb-select{height:44px;width:100%;border-radius:12px;border:1px solid #cfd8e6;background:#fff;padding:0 12px;font-size:13px;font-weight:600;line-height:1.2;color:#0f172a}
.dark .fsb-input,.dark .fsb-select{border-color:rgba(148,163,184,.28);background:rgba(15,23,42,.86);color:#e2e8f0}
.fsb-search{position:relative}
.fsb-search-btn{position:absolute;left:8px;top:50%;transform:translateY(-50%);width:30px;height:30px;border:0;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;cursor:pointer;transition:background-color .18s ease,color .18s ease}
.fsb-search-btn:hover{background:rgba(148,163,184,.12);color:#475569}
.dark .fsb-search-btn:hover{background:rgba(148,163,184,.16);color:#e2e8f0}
.fsb-search .fsb-input{padding-left:44px}
.fsb-select{appearance:none;-webkit-appearance:none;-moz-appearance:none;padding-right:38px;background-image:linear-gradient(45deg,transparent 50%,#64748b 50%),linear-gradient(135deg,#64748b 50%,transparent 50%);background-position:calc(100% - 18px) calc(50% - 3px),calc(100% - 12px) calc(50% - 3px);background-size:6px 6px,6px 6px;background-repeat:no-repeat;text-overflow:ellipsis}
.dark .fsb-select{background-image:linear-gradient(45deg,transparent 50%,#94a3b8 50%),linear-gradient(135deg,#94a3b8 50%,transparent 50%)}
.fsb-toolbar-foot{margin-top:12px;padding-top:12px;border-top:1px dashed #d7dfeb;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
.dark .fsb-toolbar-foot{border-color:rgba(148,163,184,.25)}
.fsb-checkbox{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#334155}
.dark .fsb-checkbox{color:#cbd5e1}
.fsb-filter-btn{height:38px;border-radius:10px;padding:0 12px;font-size:12px;font-weight:800;border:1px solid transparent;display:inline-flex;align-items:center;justify-content:center}
.fsb-filter-btn-light{border-color:#cfd8e6;background:#fff;color:#334155}
.dark .fsb-filter-btn-light{border-color:rgba(148,163,184,.28);background:rgba(15,23,42,.7);color:#cbd5e1}
.fsb-filter-btn-primary{background:#4f46e5;color:#fff}
.fsb-table-card{margin-top:14px;border-radius:20px;border:1px solid #d4dceb;overflow:hidden;background:#fff;box-shadow:0 22px 42px rgba(15,23,42,.09)}
.dark .fsb-table-card{border-color:rgba(148,163,184,.22);background:rgba(15,23,42,.78);box-shadow:none}
.fsb-table-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:16px 18px;border-bottom:1px solid #dbe3ef;background:linear-gradient(180deg,#f8fafe 0%,#f0f4fb 100%)}
.dark .fsb-table-head{border-bottom-color:rgba(148,163,184,.2);background:rgba(30,41,59,.6)}
.fsb-table-head h2{margin:0;font-size:22px;line-height:1;font-weight:800;letter-spacing:-.02em;color:#111827}
.dark .fsb-table-head h2{color:#f1f5f9}
.fsb-table-head p{margin:0;font-size:12px;font-weight:700;color:#64748b}
.dark .fsb-table-head p{color:#94a3b8}
.fsb-table-wrap{overflow-x:auto}
.fsb-table{width:100%;min-width:1120px;border-collapse:separate;border-spacing:0;font-size:12px}
.fsb-table thead th{padding:10px 12px;border-bottom:1px solid #dde5f1;background:#f8fafd;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:#475569;white-space:nowrap;text-align:left}
.dark .fsb-table thead th{border-bottom-color:rgba(148,163,184,.24);background:#172131;color:#a8bacf}
.fsb-table tbody td{padding:12px;border-bottom:1px solid #edf2f8;color:#1f2937;vertical-align:middle}
.dark .fsb-table tbody td{border-bottom-color:rgba(148,163,184,.12);color:#dbe5f2}
.fsb-table tbody tr:hover{background:#f7faff}
.dark .fsb-table tbody tr:hover{background:rgba(59,130,246,.08)}
.fsb-sku{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:11px;font-weight:700;letter-spacing:.05em;color:#4b5563}
.dark .fsb-sku{color:#b8c4d5}
.fsb-product{display:flex;align-items:center;gap:10px;min-width:220px}
.fsb-swatch{width:18px;height:18px;border-radius:999px;border:2px solid rgba(255,255,255,.8);box-shadow:0 0 0 1px rgba(15,23,42,.16);flex-shrink:0}
.dark .fsb-swatch{border-color:rgba(15,23,42,.8);box-shadow:0 0 0 1px rgba(148,163,184,.3)}
.fsb-product-name{font-size:13px;font-weight:800;color:#0f172a;line-height:1.25}
.dark .fsb-product-name{color:#e2e8f0}
.fsb-product-meta,.fsb-level-meta,.fsb-qty-sub{margin-top:2px;font-size:11px;font-weight:600;color:#64748b}
.dark .fsb-product-meta,.dark .fsb-level-meta,.dark .fsb-qty-sub{color:#9bb0ca}
.fsb-chip{display:inline-flex;align-items:center;height:24px;padding:0 10px;border-radius:999px;border:1px solid;font-size:11px;font-weight:800;white-space:nowrap}
.fsb-chip-ok{color:#166534;background:#dcfce7;border-color:#86efac}
.fsb-chip-low{color:#92400e;background:#fef3c7;border-color:#fcd34d}
.fsb-chip-out{color:#991b1b;background:#fee2e2;border-color:#fca5a5}
.dark .fsb-chip-ok{color:#6ee7b7;background:rgba(6,78,59,.55);border-color:rgba(16,185,129,.6)}
.dark .fsb-chip-low{color:#fcd34d;background:rgba(120,53,15,.55);border-color:rgba(245,158,11,.6)}
.dark .fsb-chip-out{color:#fca5a5;background:rgba(127,29,29,.55);border-color:rgba(239,68,68,.6)}
.fsb-qty{font-size:14px;font-weight:800;color:#0f172a;line-height:1.1}
.dark .fsb-qty{color:#e2e8f0}
.fsb-level-wrap{min-width:124px}
.fsb-level-track{width:100%;height:7px;border-radius:999px;background:#e2e8f0;overflow:hidden}
.dark .fsb-level-track{background:#1e293b}
.fsb-level-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#34d399,#16a34a)}
.fsb-level-fill.low{background:linear-gradient(90deg,#f59e0b,#f97316)}
.fsb-level-fill.out{background:linear-gradient(90deg,#f43f5e,#dc2626)}
.fsb-location{font-size:12px;font-weight:700;color:#334155;line-height:1.35;min-width:160px}
.dark .fsb-location{color:#c8d6e8}
.fsb-row-actions{display:flex;align-items:center;justify-content:flex-end;gap:6px;white-space:nowrap}
.fsb-row-btn{width:30px;height:30px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;color:#fff !important;font-size:11px;border:0;cursor:pointer}
.fsb-row-btn.edit{background:#6366f1}.fsb-row-btn.transfer{background:#10b981}.fsb-row-btn.delete{background:#ef4444}
.fsb-empty{text-align:center;padding:32px 16px;color:#64748b;font-size:13px;font-weight:600}
.dark .fsb-empty{color:#94a3b8}
.fsb-legend{margin-top:14px;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fsb-legend-text{font-size:12px;font-weight:700;color:#64748b}
.dark .fsb-legend-text{color:#94a3b8}
.fsb-modal-mask{z-index:70}
@media (max-width:1320px){.fsb-fields{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:860px){.fsb-shell{border-radius:20px;padding:14px}.fsb-actions,.fsb-filter-actions{width:100%}.fsb-btn,.fsb-filter-btn{width:100%;justify-content:center}.fsb-fields{grid-template-columns:1fr}.fsb-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>

<div class="max-w-[1520px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <section class="fsb-shell">
        <div class="fsb-top">
            <div>
                <p class="fsb-kicker">Estoque / Tecidos</p>
                <h1 class="fsb-title">Gestao de Estoque</h1>
                <p class="fsb-subtitle">Total de itens: {{ number_format($totalGroups, 0, ',', '.') }} <span class="mx-1">|</span> Alertas: {{ number_format($criticalGroups, 0, ',', '.') }}</p>
            </div>

            <div class="fsb-actions">
                <a href="{{ route('stocks.dashboard') }}" class="fsb-btn fsb-btn-soft"><i class="fa-solid fa-chart-pie"></i>Painel</a>
                <a href="{{ route('stocks.history') }}" class="fsb-btn fsb-btn-soft"><i class="fa-solid fa-clock-rotate-left"></i>Historico</a>
                <a href="{{ route('stock-requests.index') }}" class="fsb-btn fsb-btn-soft"><i class="fa-solid fa-file-invoice"></i>Solicitacoes</a>
                <a href="{{ route('stocks.create') }}" class="fsb-btn fsb-btn-primary"><i class="fa-solid fa-plus"></i>Adicionar Item</a>
            </div>
        </div>

        <div class="fsb-metrics">
            <article class="fsb-metric"><p class="fsb-metric-label">Disponivel</p><div class="fsb-metric-value">{{ number_format($totalAvailable, 0, ',', '.') }}</div></article>
            <article class="fsb-metric"><p class="fsb-metric-label">Reservado</p><div class="fsb-metric-value">{{ number_format($reservedTotal, 0, ',', '.') }}</div></article>
            <article class="fsb-metric"><p class="fsb-metric-label">Em Estoque</p><div class="fsb-metric-value">{{ number_format($statusStats['in_stock'], 0, ',', '.') }}</div></article>
            <article class="fsb-metric"><p class="fsb-metric-label">Baixo / Zerado</p><div class="fsb-metric-value">{{ number_format($criticalGroups, 0, ',', '.') }}</div></article>
        </div>

        <div class="fsb-toolbar">
            <form method="GET" action="{{ route('stocks.index') }}">
                <div class="fsb-fields">
                    <div class="fsb-search">
                        <button type="submit" class="fsb-search-btn" aria-label="Buscar no estoque">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <input type="text" name="search_id" value="{{ request('search_id') }}" placeholder="Buscar por ID do estoque..." class="fsb-input">
                    </div>

                    <select name="store_id" class="fsb-select">
                        <option value="">Todas Lojas</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>

                    <select name="fabric_type_id" class="fsb-select">
                        <option value="">Todos Tipos de Tecido</option>
                        @foreach($fabricTypes as $fabricType)
                            <option value="{{ $fabricType->id }}" {{ request('fabric_type_id') == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                        @endforeach
                    </select>

                    <select name="color_id" class="fsb-select">
                        <option value="">Todas Cores</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>

                    <select name="cut_type_id" class="fsb-select">
                        <option value="">Todos Tipos</option>
                        @foreach($cutTypes as $cutType)
                            <option value="{{ $cutType->id }}" {{ $cutTypeId == $cutType->id ? 'selected' : '' }}>{{ $cutType->name }}</option>
                        @endforeach
                    </select>

                    <select name="size" class="fsb-select">
                        <option value="">Todos Tamanhos</option>
                        @foreach($sizes as $sizeOption)
                            <option value="{{ $sizeOption }}" {{ $sizeOption == request('size') ? 'selected' : '' }}>{{ $sizeOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="fsb-toolbar-foot">
                    <label class="fsb-checkbox">
                        <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600">
                        Apenas estoque critico
                    </label>

                    <div class="fsb-filter-actions">
                        <a href="{{ route('stocks.index') }}" class="fsb-filter-btn fsb-filter-btn-light">Limpar</a>
                        <button type="submit" class="fsb-filter-btn fsb-filter-btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="fsb-table-card">
            <div class="fsb-table-head">
                <div>
                    <h2>Estoque de Rolos de Tecido</h2>
                    <p>Visualizacao dos tecidos por loja, cor e categoria.</p>
                </div>
                <p>Registros: {{ number_format($totalGroups, 0, ',', '.') }}</p>
            </div>

            <div class="fsb-table-wrap">
                <table class="fsb-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Nivel</th>
                            <th>Status</th>
                            <th>Localizacao</th>
                            <th>Atualizado</th>
                            <th class="text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedStocks as $key => $group)
                            @php
                                $fabricName = $group['fabric']['name'] ?? null;
                                $fabricTypeName = $group['fabric_type']['name'] ?? null;
                                $productName = $fabricName ?? $fabricTypeName ?? 'Sem tecido';
                                $categoryName = $group['cut_type']['name'] ?? 'Tecido';
                                $colorName = $group['color']['name'] ?? 'Sem cor';
                                $productMeta = $fabricTypeName ? ('Tipo: ' . $fabricTypeName . ' - ' . $colorName) : $colorName;
                                $displayFabricName = $fabricName && $fabricTypeName
                                    ? ($fabricName . ' - ' . $fabricTypeName)
                                    : ($fabricName ?? $fabricTypeName ?? '');
                                $colorHex = $group['color']['hex'] ?? null;
                                $colorSwatch = (is_string($colorHex) && preg_match('/^#?[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', trim($colorHex))) ? ('#' . ltrim(trim($colorHex), '#')) : '#6b7280';
                                $totalAvailableRow = (int) ($group['total_available'] ?? 0);
                                $totalReservedRow = (int) ($group['total_reserved'] ?? 0);

                                $minStockTotal = collect($group['sizes'] ?? [])->sum(function ($sizeData) {
                                    return max(1, (int) ($sizeData['min_stock'] ?? 5));
                                });

                                if ($minStockTotal <= 0) {
                                    $minStockTotal = 5;
                                }

                                $levelTarget = max(1, $minStockTotal * 2);
                                $levelPercent = (int) min(100, round(($totalAvailableRow / $levelTarget) * 100));

                                if ($totalAvailableRow <= 0) {
                                    $statusText = 'Sem estoque';
                                    $statusClass = 'fsb-chip-out';
                                    $levelClass = 'out';
                                } elseif ($totalAvailableRow < $minStockTotal) {
                                    $statusText = 'Baixo estoque';
                                    $statusClass = 'fsb-chip-low';
                                    $levelClass = 'low';
                                } else {
                                    $statusText = 'Em estoque';
                                    $statusClass = 'fsb-chip-ok';
                                    $levelClass = '';
                                }

                                $shelf = null;
                                if (isset($group['sizes']) && is_array($group['sizes'])) {
                                    foreach ($sizes as $sizeOption) {
                                        if (!empty($group['sizes'][$sizeOption]['shelf'])) {
                                            $shelf = $group['sizes'][$sizeOption]['shelf'];
                                            break;
                                        }
                                    }
                                }

                                $firstStockId = null;
                                if (isset($group['sizes']) && is_array($group['sizes'])) {
                                    foreach ($sizes as $sizeOption) {
                                        if (!empty($group['sizes'][$sizeOption]['id'])) {
                                            $firstStockId = (int) $group['sizes'][$sizeOption]['id'];
                                            break;
                                        }
                                    }
                                }

                                $skuNumber = $firstStockId ?: ($loop->iteration);
                                $skuCode = 'TX-' . str_pad((string) $skuNumber, 4, '0', STR_PAD_LEFT);
                            @endphp

                            <tr>
                                <td><span class="fsb-sku">{{ $skuCode }}</span></td>

                                <td>
                                    <div class="fsb-product">
                                        <span class="fsb-swatch" style="background-color: {{ $colorSwatch }};"></span>
                                        <div>
                                            <div class="fsb-product-name">{{ $productName }}</div>
                                            <div class="fsb-product-meta">{{ $productMeta }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td><span class="fsb-chip fsb-chip-low" style="border-color:#dbeafe;background:#eff6ff;color:#1d4ed8;">{{ $categoryName }}</span></td>

                                <td>
                                    <div class="fsb-qty">{{ number_format($totalAvailableRow, 0, ',', '.') }}</div>
                                    <div class="fsb-qty-sub">{{ $totalReservedRow > 0 ? 'Reservado: '.number_format($totalReservedRow, 0, ',', '.') : 'Sem reserva' }}</div>
                                </td>

                                <td>
                                    <div class="fsb-level-wrap">
                                        <div class="fsb-level-track">
                                            <div class="fsb-level-fill {{ $levelClass }}" style="width: {{ max(0, min(100, $levelPercent)) }}%;"></div>
                                        </div>
                                        <div class="fsb-level-meta">{{ $levelPercent }}%</div>
                                    </div>
                                </td>

                                <td><span class="fsb-chip {{ $statusClass }}">{{ $statusText }}</span></td>

                                <td>
                                    <div class="fsb-location">{{ $group['store']['name'] ?? '-' }}<br><span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $shelf ?: 'Sem prateleira' }}</span></div>
                                </td>

                                <td class="text-[11px] font-semibold text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($group['last_updated'])->format('d/m/Y H:i') }}</td>

                                <td>
                                    <div class="fsb-row-actions">
                                        @if($firstStockId)
                                            <a href="{{ route('stocks.edit', ['store_id' => $group['store']['id'] ?? null, 'fabric_id' => $group['fabric']['id'] ?? null, 'fabric_type_id' => $group['fabric_type']['id'] ?? null, 'color_id' => $group['color']['id'] ?? null, 'cut_type_id' => $group['cut_type']['id'] ?? null]) }}" class="fsb-row-btn edit" title="Editar"><i class="fa-solid fa-pen"></i></a>

                                            <button type="button" onclick="openTransferModal({{ $firstStockId }}, '{{ addslashes($group['store']['name'] ?? '') }}', '{{ addslashes($displayFabricName) }}', '{{ addslashes($group['color']['name'] ?? '') }}', {{ json_encode($group['sizes'] ?? []) }})" class="fsb-row-btn transfer" title="Transferir"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>

                                            <button type="button" onclick="openDeleteModal({{ $firstStockId }}, '{{ addslashes($displayFabricName) }}', '{{ addslashes($group['color']['name'] ?? '') }}')" class="fsb-row-btn delete" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                                        @else
                                            <span class="text-[11px] text-gray-500 italic">Indisponivel</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="fsb-empty">Nenhum estoque encontrado para os filtros aplicados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="fsb-legend">
            <span class="fsb-legend-text">Status:</span>
            <span class="fsb-chip fsb-chip-ok">Em estoque</span>
            <span class="fsb-chip fsb-chip-low">Baixo estoque</span>
            <span class="fsb-chip fsb-chip-out">Sem estoque</span>
            <span class="fsb-legend-text">Niveis calculados com base no estoque minimo.</span>
        </div>
    </section>
</div>

<!-- Modal de Transferencia -->
<div id="transfer-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm fsb-modal-mask flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-100">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center">
                    <i class="fa-solid fa-arrow-right-arrow-left text-sm" style="color: #ffffff !important;"></i>
                </div>
                Transferir Estoque
            </h3>
            <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form id="transfer-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Origem</label>
                <input type="text" id="transfer-from" readonly class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white font-bold text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Destino</label>
                <select name="target_store_id" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 dark:text-white text-sm font-bold appearance-none cursor-pointer">
                    <option value="">Selecione a loja de destino...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantidades</label>
                <div id="transfer-sizes-container" class="space-y-2 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 max-h-48 overflow-y-auto custom-scrollbar">
                    <!-- Inputs gerados via JS -->
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeTransferModal()" class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 shadow-lg shadow-green-200 dark:shadow-none transition-colors" style="color: #ffffff !important;">
                    Confirmar Transferencia
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Exclusao -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm fsb-modal-mask flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 text-red-500">
            <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
        </div>

        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Excluir item?</h3>
        <p id="delete-message" class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">Esta acao removera permanentemente o estoque selecionado.</p>

        <form id="delete-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
            <button type="submit" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition-colors" style="color: #ffffff !important;">Sim, excluir</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openTransferModal(stockId, storeName, fabric, color, sizes) {
    document.getElementById('transfer-from').value = `${storeName} - ${fabric} - ${color}`;
    document.getElementById('transfer-form').action = `/stocks/${stockId}/transfer`;

    const container = document.getElementById('transfer-sizes-container');
    container.innerHTML = '';

    let hasAvailable = false;
    const orderedSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];

    orderedSizes.forEach(size => {
        if (sizes && sizes[size]) {
            const data = sizes[size];
            const maxQty = data.available_quantity || 0;

            if (maxQty > 0) {
                hasAvailable = true;
                const div = document.createElement('div');
                div.className = 'flex items-center gap-4 p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors';
                div.innerHTML = `
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 text-xs">
                        ${size}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-gray-500 uppercase">Quantidade</span>
                            <span class="text-indigo-600 font-bold">Max: ${maxQty}</span>
                        </div>
                        <input type="number" name="quantities[${size}]" min="0" max="${maxQty}" placeholder="0"
                               class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-bold focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                `;
                container.appendChild(div);
            }
        }
    });

    if (!hasAvailable) {
        container.innerHTML = `<div class="text-center py-4"><p class="text-sm font-bold text-gray-400">Nenhum item disponivel para transferencia.</p></div>`;
    }

    document.getElementById('transfer-modal').classList.remove('hidden');
}

function closeTransferModal() { document.getElementById('transfer-modal').classList.add('hidden'); }
function closeDeleteModal() { document.getElementById('delete-modal').classList.add('hidden'); }

document.getElementById('transfer-modal')?.addEventListener('click', function(e) { if (e.target === this) closeTransferModal(); });

document.getElementById('delete-modal')?.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

function openDeleteModal(stockId, fabric, color) {
    document.getElementById('delete-message').innerHTML = `Tem certeza que deseja excluir o estoque de <br><strong class="text-gray-900 dark:text-white">${fabric} - ${color}</strong>?`;
    document.getElementById('delete-form').action = `/stocks/${stockId}`;
    document.getElementById('delete-modal').classList.remove('hidden');
}
</script>
@endpush
