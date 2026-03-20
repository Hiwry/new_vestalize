@extends('layouts.admin')

@section('content')
<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-in { animation: fadeInUp 0.4s ease-out forwards; }

.glass-card {
    background: #1e293b;
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
}
.tab-btn {
    padding: 0.45rem 1rem;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.2s;
    white-space: nowrap;
}
.tab-btn:hover { color: #e2e8f0; background: rgba(255,255,255,0.05); }
.tab-btn.active {
    color: #c4b5fd;
    background: rgba(139,92,246,0.15);
    border-color: rgba(139,92,246,0.3);
}

/* Color chip */
.color-chip {
    display: inline-block;
    width: 12px; height: 12px;
    border-radius: 3px;
    border: 1px solid rgba(255,255,255,0.2);
    flex-shrink: 0;
}

/* Stock table */
.stock-table { width: 100%; border-collapse: collapse; }
.stock-table th {
    font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em;
    color: #475569; padding: 0.6rem 0.75rem; text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.stock-table th.center { text-align: center; }
.stock-table td { padding: 0.65rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.03); }
.stock-table tr:last-child td { border-bottom: none; }
.stock-table tr:hover td { background: rgba(255,255,255,0.02); }

/* Store badge */
.store-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 0.2rem 0.55rem; border-radius: 6px;
    font-size: 0.65rem; font-weight: 700;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    color: #94a3b8;
}
.store-badge.has-stock { background: rgba(139,92,246,0.1); border-color: rgba(139,92,246,0.25); color: #c4b5fd; }
.store-badge .qty { font-size: 0.8rem; font-weight: 900; color: #e2e8f0; }



/* Loading skeleton */
.skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 anim-in">
        <div class="flex items-center gap-3">
            <a href="{{ route('fabric-pieces.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-white transition"
               style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Estoque por <span class="text-emerald-400">Cor</span></h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Visualização consolidada de peças ativas por tipo e cor</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fabric-pieces.bulk-create') }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-purple-300 flex items-center gap-2 transition hover:text-white"
               style="background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.25);">
                <i class="fa-solid fa-layer-group"></i>
                Cadastro em Lote
            </a>
            <a href="{{ route('fabric-pieces.create') }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 flex items-center gap-2 transition hover:text-gray-200"
               style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                <i class="fa-solid fa-plus"></i>
                Nova Peça
            </a>
        </div>
    </div>

    {{-- Filtro de Loja --}}
    <div class="glass-card p-4 flex flex-wrap gap-3 items-center anim-in" style="animation-delay:0.05s;opacity:0;">
        <span class="field-label mb-0 mr-1">Loja:</span>
        <button type="button" class="tab-btn {{ !$selectedStoreId ? 'active' : '' }}" onclick="filterStore(null, this)">Todas</button>
        @foreach($stores as $store)
            <button type="button" class="tab-btn {{ $selectedStoreId == $store->id ? 'active' : '' }}"
                    onclick="filterStore({{ $store->id }}, this)">{{ $store->name }}</button>
        @endforeach
    </div>

    {{-- Tabs de Tecido --}}
    <div class="glass-card overflow-hidden anim-in" style="animation-delay:0.1s;opacity:0;">
        <div class="overflow-x-auto border-b" style="border-color:rgba(255,255,255,0.06);scrollbar-width:thin;scrollbar-color:rgba(139,92,246,0.4) transparent;">
            <div class="flex items-center gap-1 px-4 py-3" style="min-width:max-content;">
                @foreach($fabricTypes as $ft)
                    <button type="button"
                            class="tab-btn fabric-tab {{ $selectedFabricTypeId == $ft->id ? 'active' : '' }}"
                            data-fabric-id="{{ $ft->id }}"
                            onclick="selectFabric({{ $ft->id }}, this)">
                        {{ $ft->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Conteúdo da aba selecionada --}}
        <div id="stock-content" class="p-4">
            <div id="loading-state" class="py-10 flex flex-col items-center gap-3">
                <div class="skeleton w-full h-10 mb-2"></div>
                <div class="skeleton w-full h-8"></div>
                <div class="skeleton w-full h-8"></div>
                <div class="skeleton w-3/4 h-8"></div>
            </div>
            <div id="table-wrapper" class="hidden overflow-x-auto"></div>
            <div id="empty-fabric" class="hidden py-12 flex flex-col items-center text-gray-600">
                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-30"></i>
                <p class="text-sm font-semibold">Nenhuma peça ativa encontrada</p>
                <p class="text-xs mt-1">Cadastre peças com este tipo de tecido</p>
            </div>
        </div>

        {{-- Summary Footer --}}
        <div id="summary-footer" class="hidden px-5 py-3 border-t flex flex-wrap items-center gap-4 text-xs text-gray-500"
             style="border-color:rgba(255,255,255,0.06);background:rgba(255,255,255,0.01);">
            <span>Total: <strong id="s-total-pieces" class="text-white">0</strong> peça(s)</span>
            <span id="s-total-kg-wrap">Total Kg: <strong id="s-total-kg" class="text-white">0</strong></span>
            <span id="s-total-m-wrap" class="hidden">Total Metros: <strong id="s-total-m" class="text-white">0</strong></span>
        </div>
    </div>

    {{-- Legend --}}
    <div class="text-xs text-gray-600 flex flex-wrap gap-4 px-1 anim-in" style="animation-delay:0.15s;opacity:0;">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-purple-500/20 border border-purple-500/30 inline-block"></span> Peças disponíveis</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-500/10 border border-gray-500/20 inline-block"></span> Sem estoque nessa loja</span>
        <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle text-blue-400 text-[8px]"></i> = Fechada &nbsp; <i class="fa-solid fa-circle text-emerald-400 text-[8px]"></i> = Aberta</span>
    </div>
</div>



@push('scripts')
<script>
let currentFabricId = {{ $selectedFabricTypeId ?? 'null' }};
let currentStoreId = {{ $selectedStoreId ?? 'null' }};
let storeList = @json($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));

// ─── Carregamento de dados ───────────────────────────────────────────
async function loadStockData(fabricId, storeId) {
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('table-wrapper').classList.add('hidden');
    document.getElementById('empty-fabric').classList.add('hidden');
    document.getElementById('summary-footer').classList.add('hidden');

    try {
        const params = new URLSearchParams();
        if (fabricId) params.set('fabric_type_id', fabricId);
        if (storeId)  params.set('store_id', storeId);

        const res = await fetch(`{{ route('fabric-pieces.stock-summary.pieces') }}?${params}`);
        const data = await res.json();

        document.getElementById('loading-state').classList.add('hidden');

        if (!data.rows || data.rows.length === 0) {
            document.getElementById('empty-fabric').classList.remove('hidden');
            return;
        }

        renderTable(data.rows, storeId);
    } catch (e) {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('empty-fabric').classList.remove('hidden');
    }
}

// ─── Renderização da tabela ──────────────────────────────────────────
function renderTable(rows, filteredStoreId) {
    const wrapper = document.getElementById('table-wrapper');

    // Coletar lojas que aparecem nos dados
    let storesInData = {};
    rows.forEach(row => {
        row.stores.forEach(st => {
            storesInData[st.store_name] = true;
        });
    });
    const storeNames = Object.keys(storesInData);

    // Se filtrando por loja, mostrar só essa loja
    const displayStores = filteredStoreId
        ? storeList.filter(s => s.id == filteredStoreId).map(s => s.name)
        : storeNames;

    let totalPieces = 0, totalKg = 0, totalM = 0;
    let controlUnit = rows[0]?.control_unit ?? 'kg';

    let html = `<table class="stock-table">
        <thead><tr>
            <th style="min-width:160px;">Cor</th>
            ${displayStores.map(n => `<th class="center" style="min-width:130px;">${n}</th>`).join('')}
            <th class="center" style="min-width:100px;">Total</th>
        </tr></thead>
        <tbody>`;

    rows.forEach(row => {
        totalPieces += row.total_pieces;
        totalKg += row.control_unit === 'kg' ? row.total_qty : 0;
        totalM  += row.control_unit === 'metros' ? row.total_qty : 0;

        const storeMap = {};
        row.stores.forEach(st => { storeMap[st.store_name] = st; });

        const storeCells = displayStores.map(storeName => {
            const st = storeMap[storeName];
            if (!st || st.count === 0) {
                return `<td class="center"><span class="store-badge">–</span></td>`;
            }
            const unit = st.pieces[0]?.unit ?? 'kg';
            const qty = unit === 'metros'
                ? st.qty.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})
                : st.qty.toLocaleString('pt-BR', {minimumFractionDigits:3, maximumFractionDigits:3});
            const unitLabel = unit === 'metros' ? 'm' : 'kg';

            const statusDots = st.pieces.map(p =>
                `<i class="fa-solid fa-circle text-[6px] ${p.status === 'aberta' ? 'text-emerald-400' : 'text-blue-400'}" title="${p.status}: ${p.qty}${unitLabel}"></i>`
            ).join(' ');

            return `<td class="center">
                <div class="store-badge has-stock flex-col gap-0.5" style="display:inline-flex;">
                    <span class="qty">${qty} <span style="font-size:0.6rem;font-weight:600;color:#94a3b8;">${unitLabel}</span></span>
                    <span style="font-size:0.6rem;color:#6366f1;">${st.count} peça(s)</span>
                    <span>${statusDots}</span>
                </div>
            </td>`;
        }).join('');

        const totalUnit = row.control_unit === 'metros' ? 'm' : 'kg';
        const totalFmt = row.control_unit === 'metros'
            ? row.total_qty.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})
            : row.total_qty.toLocaleString('pt-BR', {minimumFractionDigits:3, maximumFractionDigits:3});

        html += `<tr>
            <td>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-white">${row.color_name}</span>
                    <span class="text-[10px] text-gray-600">${row.total_pieces} peça(s)</span>
                </div>
            </td>
            ${storeCells}
            <td class="center">
                <span class="text-sm font-black text-white">${totalFmt}</span>
                <span class="text-[10px] text-gray-500"> ${totalUnit}</span>
            </td>
        </tr>`;
    });

    html += `</tbody></table>`;
    wrapper.innerHTML = html;
    wrapper.classList.remove('hidden');

    // Footer
    document.getElementById('s-total-pieces').textContent = totalPieces;
    document.getElementById('s-total-kg').textContent = totalKg.toLocaleString('pt-BR', {minimumFractionDigits:3, maximumFractionDigits:3});
    document.getElementById('s-total-m').textContent = totalM.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});

    if (controlUnit === 'metros') {
        document.getElementById('s-total-kg-wrap').classList.add('hidden');
        document.getElementById('s-total-m-wrap').classList.remove('hidden');
    } else {
        document.getElementById('s-total-kg-wrap').classList.remove('hidden');
        document.getElementById('s-total-m-wrap').classList.add('hidden');
    }
    document.getElementById('summary-footer').classList.remove('hidden');
}

// ─── Navegação ────────────────────────────────────────────────────────
function selectFabric(fabricId, el) {
    currentFabricId = fabricId;
    document.querySelectorAll('.fabric-tab').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
    loadStockData(currentFabricId, currentStoreId);
}

function filterStore(storeId, el) {
    currentStoreId = storeId;
    document.querySelectorAll('.tab-btn[onclick*="filterStore"]').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
    if (currentFabricId) loadStockData(currentFabricId, currentStoreId);
}



// ─── Inicialização ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (currentFabricId) loadStockData(currentFabricId, currentStoreId);
});
</script>
@endpush
@endsection
