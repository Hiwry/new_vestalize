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
.field-input {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #e2e8f0;
    padding: 0.5rem 0.75rem;
    width: 100%;
    font-size: 0.875rem;
    transition: border-color 0.15s;
}
.field-input:focus {
    outline: none;
    border-color: rgba(139,92,246,0.6);
    box-shadow: 0 0 0 2px rgba(139,92,246,0.15);
}
.field-input option { background: #1e293b; color: #e2e8f0; }
.field-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #64748b;
    margin-bottom: 0.35rem;
}
.row-line {
    display: grid;
    grid-template-columns: 2fr 1.2fr 1fr 1fr 2fr auto;
    gap: 0.5rem;
    align-items: end;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: background 0.15s;
}
.row-line:hover { background: rgba(255,255,255,0.02); }
.row-num {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: rgba(139,92,246,0.15);
    color: #a78bfa;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.35rem;
}
@media (max-width: 768px) {
    .row-line {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto auto;
    }
}
</style>

<div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 anim-in">
        <div class="flex items-center gap-3">
            <a href="{{ route('fabric-pieces.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-white transition"
               style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Cadastro em <span class="text-purple-400">Lote</span></h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Registre múltiplas peças de uma nota fiscal</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fabric-pieces.stock-summary') }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-emerald-400 flex items-center gap-2 transition hover:text-emerald-300"
               style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);">
                <i class="fa-solid fa-table-cells"></i>
                Ver Estoque por Cor
            </a>
            <a href="{{ route('fabric-pieces.create') }}"
               class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 flex items-center gap-2 transition hover:text-gray-200"
               style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                <i class="fa-solid fa-plus"></i>
                Cadastro Individual
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('error'))
    <div class="p-4 rounded-xl text-red-400 text-sm font-semibold" style="background:rgba(239,68,68,0.08);border-left:4px solid #ef4444;">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ session('error') }}
    </div>
    @endif

    <form action="{{ route('fabric-pieces.bulk-store') }}" method="POST" id="bulk-form">
        @csrf

        {{-- === CABEÇALHO DA NOTA === --}}
        <div class="glass-card p-6 space-y-5 anim-in" style="animation-delay:0.05s;opacity:0;">
            <h2 class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-purple-500"></i>
                Dados da Nota Fiscal / Entrada
            </h2>

            {{-- Linha 1: Loja, Tipo Tecido, Fornecedor, NF, Chave --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="field-label">Loja *</label>
                    <select name="store_id" required class="field-input">
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                    @error('store_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label">Tipo de Tecido *</label>
                    <select name="fabric_type_id" id="fabric_type_id" required class="field-input">
                        <option value="">Selecione...</option>
                        @foreach($fabricTypes as $ft)
                            <option value="{{ $ft->id }}" {{ old('fabric_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Fornecedor</label>
                    <select name="supplier" class="field-input">
                        <option value="">Selecione...</option>
                        @foreach(['EVETEX','BRASIL','ATUAL','MENEGOTTI','4K','KANXA','AVIL','EF','MANTOS','CEDRO','KIRIN'] as $s)
                            <option value="{{ $s }}" {{ old('supplier') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Nº Nota Fiscal</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="000000" class="field-input">
                </div>
                <div>
                    <label class="field-label">Data Recebimento</label>
                    <input type="date" name="received_at" value="{{ old('received_at', date('Y-m-d')) }}" class="field-input">
                </div>
            </div>

            {{-- Linha 2: Chave NF-e, Controle, Preço, Alerta --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="field-label">Chave NF-e (44 dígitos)</label>
                    <input type="text" name="invoice_key" value="{{ old('invoice_key') }}" maxlength="44" placeholder="00000000000000000000000000000000000000000000"
                           class="field-input font-mono text-xs">
                </div>
                <div>
                    <label class="field-label">Unidade de Controle *</label>
                    <select name="control_unit" id="control_unit" class="field-input" onchange="updateQtyHeaders()">
                        <option value="kg" {{ old('control_unit','kg') === 'kg' ? 'selected' : '' }}>Kg (peso)</option>
                        <option value="metros" {{ old('control_unit') === 'metros' ? 'selected' : '' }}>Metros</option>
                    </select>
                </div>
                <div>
                    <label class="field-label" id="sale-price-label">Preço de Venda (por Kg)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" placeholder="0.00" class="field-input">
                </div>
            </div>

            {{-- Canais --}}
            <div class="flex flex-wrap gap-5">
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="available_in_pdv" value="1" checked class="w-4 h-4 rounded accent-purple-500">
                    Liberar no PDV
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="available_in_catalog" value="1" checked class="w-4 h-4 rounded accent-purple-500">
                    Liberar no Catálogo
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="available_in_orders" value="1" checked class="w-4 h-4 rounded accent-purple-500">
                    Permitir em Pedidos
                </label>
            </div>
        </div>

        {{-- === GRADE DE PEÇAS === --}}
        <div class="glass-card overflow-hidden anim-in" style="animation-delay:0.1s;opacity:0;">
            {{-- Toolbar da grade --}}
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.06);">
                <div>
                    <h2 class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-purple-500"></i>
                        Peças da Entrada
                        <span id="piece-count-badge" class="text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-full text-[10px]">0 peças</span>
                    </h2>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="addColorRows()"
                            class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest text-purple-300 flex items-center gap-1.5 transition hover:text-white"
                            style="background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.25);">
                        <i class="fa-solid fa-palette text-sm"></i>
                        Adicionar todas as cores
                    </button>
                    <button type="button" onclick="addRow()"
                            class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest text-emerald-300 flex items-center gap-1.5 transition hover:text-white"
                            style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);">
                        <i class="fa-solid fa-plus text-sm"></i>
                        Adicionar Linha
                    </button>
                </div>
            </div>

            {{-- Cabeçalho da Grade --}}
            <div class="row-line px-5 py-2" style="background:rgba(255,255,255,0.02);">
                <div class="field-label mb-0">#  Cor</div>
                <div class="field-label mb-0" id="qty-col-label">Quantidade (kg)</div>
                <div class="field-label mb-0">Cód. Barras</div>
                <div class="field-label mb-0">Prateleira</div>
                <div class="field-label mb-0">Observações</div>
                <div></div>
            </div>

            {{-- Corpo - linhas dinâmicas --}}
            <div id="rows-container"></div>

            {{-- Adicionar linha vazia quando não há linhas --}}
            <div id="empty-state" class="flex flex-col items-center justify-center py-12 text-gray-600">
                <i class="fa-solid fa-table-list text-4xl mb-3 opacity-30"></i>
                <p class="text-sm font-semibold">Nenhuma peça adicionada</p>
                <p class="text-xs mt-1">Clique em "Adicionar Linha" ou "Adicionar todas as cores"</p>
            </div>

            {{-- Footer da grade --}}
            <div id="grade-footer" class="hidden px-5 py-4 border-t flex items-center justify-between" style="border-color:rgba(255,255,255,0.06);background:rgba(139,92,246,0.04);">
                <div class="text-sm text-gray-400">
                    Total: <span id="total-qty-display" class="font-black text-white">0,000</span>
                    <span id="total-unit-label" class="text-gray-500 text-xs">kg</span>
                    em <span id="total-pieces-count" class="font-black text-white">0</span> peça(s)
                </div>
            </div>
        </div>

        {{-- === BOTÕES DE AÇÃO === --}}
        <div class="flex justify-end gap-3 anim-in" style="animation-delay:0.15s;opacity:0;">
            <a href="{{ route('fabric-pieces.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-gray-400 hover:text-gray-200 transition"
               style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                Cancelar
            </a>
            <button type="submit"
                    class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:shadow-lg hover:shadow-purple-600/30 transition active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-layer-group"></i>
                Cadastrar Lote
            </button>
        </div>
    </form>
</div>

{{-- Template de linha (hidden) --}}
<template id="row-template">
    <div class="row-line" data-row-index="__IDX__">
        <div class="flex items-end gap-2">
            <div class="row-num">__NUM__</div>
            <div class="flex-1">
                <select name="pieces[__IDX__][color_id]" class="field-input color-select">
                    <option value="">Sem cor / Genérica</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <input type="number" step="0.001" min="0" name="pieces[__IDX__][weight]"
                   class="field-input qty-input" placeholder="0,000">
        </div>
        <div>
            <input type="text" name="pieces[__IDX__][barcode]" class="field-input" placeholder="SKU / Cód.">
        </div>
        <div>
            <input type="text" name="pieces[__IDX__][shelf]" class="field-input" placeholder="A1-01">
        </div>
        <div>
            <input type="text" name="pieces[__IDX__][notes]" class="field-input" placeholder="Observações...">
        </div>
        <div class="flex items-end pb-0.5">
            <button type="button" onclick="removeRow(this)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
const colors = @json($colors->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
let rowCount = 0;

function getTemplate(idx) {
    const tpl = document.getElementById('row-template').innerHTML;
    return tpl.replaceAll('__IDX__', idx).replaceAll('__NUM__', idx + 1);
}

function addRow(colorId = '', colorName = '') {
    const container = document.getElementById('rows-container');
    const idx = rowCount++;
    const div = document.createElement('div');
    div.innerHTML = getTemplate(idx);
    const rowEl = div.firstElementChild;

    if (colorId) {
        const sel = rowEl.querySelector('.color-select');
        if (sel) {
            const opt = sel.querySelector(`option[value="${colorId}"]`);
            if (opt) opt.selected = true;
        }
    }

    // Set correct qty field name based on control unit
    const unit = document.getElementById('control_unit').value;
    const qtyInput = rowEl.querySelector('.qty-input');
    if (qtyInput) {
        qtyInput.name = unit === 'metros' ? `pieces[${idx}][meters]` : `pieces[${idx}][weight]`;
        qtyInput.step = unit === 'metros' ? '0.01' : '0.001';
        qtyInput.placeholder = unit === 'metros' ? '0,00' : '0,000';
    }

    qtyInput?.addEventListener('input', updateTotals);
    container.appendChild(rowEl);
    updateState();
    rowEl.querySelector('.color-select')?.focus();
}

function addColorRows() {
    colors.forEach(c => addRow(c.id, c.name));
}

function removeRow(btn) {
    btn.closest('[data-row-index]').remove();
    updateState();
}

function updateState() {
    const rows = document.querySelectorAll('#rows-container [data-row-index]');
    const empty = document.getElementById('empty-state');
    const footer = document.getElementById('grade-footer');
    const badge = document.getElementById('piece-count-badge');

    empty.classList.toggle('hidden', rows.length > 0);
    footer.classList.toggle('hidden', rows.length === 0);
    badge.textContent = rows.length + ' peça(s)';

    // Renumerar visualmente
    rows.forEach((row, i) => {
        const num = row.querySelector('.row-num');
        if (num) num.textContent = i + 1;
    });

    updateTotals();
}

function updateTotals() {
    const inputs = document.querySelectorAll('.qty-input');
    let total = 0;
    let count = 0;
    inputs.forEach(inp => {
        const v = parseFloat(inp.value) || 0;
        if (v > 0) { total += v; count++; }
    });
    const unit = document.getElementById('control_unit').value;
    document.getElementById('total-qty-display').textContent = total.toLocaleString('pt-BR', {minimumFractionDigits: unit === 'metros' ? 2 : 3, maximumFractionDigits: unit === 'metros' ? 2 : 3});
    document.getElementById('total-unit-label').textContent = unit === 'metros' ? 'm' : 'kg';
    document.getElementById('total-pieces-count').textContent = count;
}

function updateQtyHeaders() {
    const unit = document.getElementById('control_unit').value;
    document.getElementById('qty-col-label').textContent = 'Quantidade (' + (unit === 'metros' ? 'm' : 'kg') + ')';
    document.getElementById('sale-price-label').textContent = 'Preço de Venda (por ' + (unit === 'metros' ? 'Metro' : 'Kg') + ')';

    // Update all qty inputs
    document.querySelectorAll('.qty-input').forEach((inp, i) => {
        const idx = inp.closest('[data-row-index]').dataset.rowIndex;
        inp.name = unit === 'metros' ? `pieces[${idx}][meters]` : `pieces[${idx}][weight]`;
        inp.step = unit === 'metros' ? '0.01' : '0.001';
        inp.placeholder = unit === 'metros' ? '0,00' : '0,000';
    });

    updateTotals();
}

// Validação antes de enviar
document.getElementById('bulk-form').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#rows-container [data-row-index]');
    if (rows.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos uma peça antes de salvar.');
        return;
    }

    const fabricType = document.getElementById('fabric_type_id').value;
    if (!fabricType) {
        e.preventDefault();
        alert('Selecione o tipo de tecido.');
        document.getElementById('fabric_type_id').focus();
    }
});

// Init
updateState();
</script>
@endpush
@endsection
