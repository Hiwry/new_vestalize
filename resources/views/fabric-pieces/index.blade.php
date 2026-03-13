@extends('layouts.admin')

@section('content')
<style>
/* Animações Premium */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(32px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes pulse-soft {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.65; }
}
@keyframes slide-in {
    from { opacity: 0; transform: translateX(100%); }
    to   { opacity: 1; transform: translateX(0); }
}

.animate-fade-in-up   { animation: fadeInUp 0.55s ease-out forwards; }
.animate-slide-in-right { animation: slideInRight 0.45s ease-out forwards; }
.animate-pulse-soft   { animation: pulse-soft 2s ease-in-out infinite; }
.animate-slide-in     { animation: slide-in 0.3s ease-out; }

.delay-100 { animation-delay: 0.1s; opacity: 0; }
.delay-200 { animation-delay: 0.2s; opacity: 0; }
.delay-300 { animation-delay: 0.3s; opacity: 0; }
.delay-400 { animation-delay: 0.4s; opacity: 0; }
.delay-500 { animation-delay: 0.5s; opacity: 0; }
.delay-600 { animation-delay: 0.6s; opacity: 0; }

/* Glass Card */
.glass-card {
    background: #1e293b;
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 16px;
}

/* Hover Lift */
.hover-lift {
    transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.hover-lift:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 20px 40px -12px rgba(0,0,0,0.5);
}

/* Stat Card Gradient Border on Hover */
.stat-grd {
    position: relative;
    overflow: hidden;
}
.stat-grd::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1px;
    background: linear-gradient(135deg, var(--g-from), var(--g-to));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
}
.stat-grd:hover::before { opacity: 1; }

/* Table row hover */
.fp-row { transition: background 0.15s; }
.fp-row:hover { background: rgba(255,255,255,0.025); }

/* Modal glass */
.modal-glass {
    background: rgba(8,8,8,0.97);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* Scrollbar hide */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-1 animate-fade-in-up">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center text-purple-500">
                <i class="fa-solid fa-layer-group text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Peças de <span class="text-purple-500">Tecido</span>
                </h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Gerenciamento de estoque de peças/rolos</p>
            </div>
        </div>
        <div class="flex gap-2 animate-slide-in-right">
            <a href="{{ route('fabric-pieces.report') }}"
               class="glass-card px-4 py-2.5 text-gray-400 rounded-xl hover:text-white hover:border-white/10 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest">
                <i class="fa-solid fa-chart-bar text-sm"></i>
                Relatório
            </a>
            @if(!Auth::user()->isVendedor())
            <a href="{{ route('fabric-pieces.create') }}"
               class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:shadow-purple-600/30 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-plus text-sm"></i>
                Nova Peça
            </a>
            <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
               class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-600/30 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-file-excel text-sm"></i>
                Importar Excel
            </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-xl border-l-4 border-emerald-500 text-emerald-400 text-sm font-semibold animate-fade-in-up"
             style="background: rgba(16,185,129,0.08);" role="alert">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    @php
        $statCards = [
            ['label'=>'Total Ativas',      'value'=>$stats['total'],            'icon'=>'fa-layer-group',         'color'=>'text-white',     'gFrom'=>'#a855f7','gTo'=>'#6366f1'],
            ['label'=>'Fechadas',          'value'=>$stats['fechadas'],         'icon'=>'fa-lock',                'color'=>'text-blue-400',  'gFrom'=>'#3b82f6','gTo'=>'#06b6d4'],
            ['label'=>'Abertas',           'value'=>$stats['abertas'],          'icon'=>'fa-lock-open',           'color'=>'text-emerald-400','gFrom'=>'#10b981','gTo'=>'#059669'],
            ['label'=>'Em Transferência',  'value'=>$stats['em_transferencia'], 'icon'=>'fa-right-left',          'color'=>'text-orange-400', 'gFrom'=>'#f59e0b','gTo'=>'#f97316'],
            ['label'=>'Vendidas',          'value'=>$stats['vendidas'],         'icon'=>'fa-tag',                 'color'=>'text-gray-400',  'gFrom'=>'#6b7280','gTo'=>'#4b5563'],
            ['label'=>'Abaixo do Mínimo',  'value'=>$stats['estoque_baixo'],    'icon'=>'fa-triangle-exclamation','color'=>'text-amber-400', 'gFrom'=>'#f59e0b','gTo'=>'#d97706'],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 sm:gap-4">
        @foreach($statCards as $i => $sc)
        <div class="stat-grd glass-card rounded-2xl p-4 hover-lift animate-fade-in-up delay-{{ ($i+1)*100 }}"
             style="--g-from:{{ $sc['gFrom'] }};--g-to:{{ $sc['gTo'] }};">
            <i class="fa-solid {{ $sc['icon'] }} {{ $sc['color'] }} text-lg mb-2 block"></i>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">{{ $sc['label'] }}</p>
            <p class="text-2xl font-black {{ $sc['color'] }} tabular-nums">{{ $sc['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Stats por Loja --}}
    @if(count($storeStats) > 1)
    <div class="glass-card rounded-2xl p-5 animate-fade-in-up">
        <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Quantidade por Loja</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($storeStats as $storeId => $storeStat)
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl"
                     style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);">
                    <i class="fa-solid fa-store text-purple-500 text-xs"></i>
                    <span class="text-xs text-gray-400 font-semibold">{{ $storeStat['name'] }}:</span>
                    <span class="text-xs font-black text-white">{{ $storeStat['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="glass-card rounded-2xl p-5 animate-fade-in-up">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="NF, Código..."
                       class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Fornecedor</label>
                <input type="text" name="supplier" value="{{ request('supplier') }}" placeholder="Nome..."
                       class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Tipo Tecido</label>
                <select name="fabric_type_id"
                        class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Todos</option>
                    @foreach($fabricTypes as $fabricType)
                        <option value="{{ $fabricType->id }}" {{ request('fabric_type_id') == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Cor</label>
                <select name="color_id"
                        class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Todas</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ request('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Status</label>
                <select name="status"
                        class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Todos</option>
                    <option value="fechada"         {{ request('status') == 'fechada'         ? 'selected' : '' }}>Fechada</option>
                    <option value="aberta"          {{ request('status') == 'aberta'          ? 'selected' : '' }}>Aberta</option>
                    <option value="em_transferencia"{{ request('status') == 'em_transferencia'? 'selected' : '' }}>Em Transferência</option>
                    <option value="vendida"         {{ request('status') == 'vendida'         ? 'selected' : '' }}>Vendida</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Loja</label>
                <select name="store_id"
                        class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2 md:col-span-4 lg:col-span-3 flex items-center">
                <label class="inline-flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="alert_only" value="1" {{ request()->boolean('alert_only') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-purple-600 focus:ring-purple-500">
                    <span class="text-xs font-bold text-gray-400 group-hover:text-gray-300 transition-colors">Mostrar apenas peças abaixo do mínimo informado</span>
                </label>
            </div>
            <div class="col-span-2 md:col-span-4 lg:col-span-3 flex gap-2 justify-end">
                <a href="{{ route('fabric-pieces.index') }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-200 transition"
                   style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    Limpar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-purple-600/30 transition active:scale-95">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="glass-card rounded-2xl overflow-hidden animate-fade-in-up delay-300">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                        <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Loja</th>
                        <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Tecido/Cor</th>
                        <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Fornecedor</th>
                        <th class="px-5 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">NF</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Saldo</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Canais</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Prateleira</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pieces as $piece)
                        <tr class="fp-row" style="border-bottom:1px solid rgba(255,255,255,0.03);">
                            <td class="px-5 py-4 text-sm text-gray-300">
                                {{ $piece->store->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-bold text-white">{{ $piece->fabricType->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $piece->color->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-300">
                                {{ $piece->supplier ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm text-gray-300">{{ $piece->invoice_number ?? '-' }}</div>
                                @if($piece->invoice_key)
                                    <div class="text-[10px] text-gray-600 truncate max-w-[100px] mt-0.5" title="{{ $piece->invoice_key }}">
                                        {{ Str::limit($piece->invoice_key, 15) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="text-sm font-black text-white">
                                    {{ number_format($piece->available_quantity, $piece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $piece->control_unit === 'metros' ? 'm' : 'kg' }}
                                </div>
                                <div class="text-[11px] text-gray-600 mt-0.5">
                                    Inicial: {{ number_format($piece->initial_quantity, $piece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $piece->control_unit === 'metros' ? 'm' : 'kg' }}
                                </div>
                                @if($piece->min_quantity_alert > 0)
                                    <div class="text-[11px] {{ $piece->is_below_alert ? 'text-amber-400 font-black' : 'text-gray-600' }} mt-0.5">
                                        Mín.: {{ number_format($piece->min_quantity_alert, $piece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $piece->control_unit === 'metros' ? 'm' : 'kg' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-1">
                                    @if($piece->available_in_pdv)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black" style="background:rgba(99,102,241,0.2);color:#a5b4fc;">PDV</span>
                                    @endif
                                    @if($piece->available_in_catalog)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black" style="background:rgba(16,185,129,0.2);color:#6ee7b7;">CAT</span>
                                    @endif
                                    @if($piece->available_in_orders)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black" style="background:rgba(255,255,255,0.08);color:#9ca3af;">PED</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center text-sm text-gray-400">
                                {{ $piece->shelf ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-wider {{ $piece->status_color }}">
                                    {{ $piece->status_label }}
                                </span>
                                @if($piece->is_below_alert)
                                    <div class="mt-1 text-[10px] font-black text-amber-400 animate-pulse-soft">⚠ Abaixo do mínimo</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $openPiecePayload = [
                                        'id' => $piece->id,
                                        'unit' => $piece->control_unit,
                                        'available' => (float) $piece->initial_quantity,
                                    ];

                                    $partialSalePayload = [
                                        'id' => $piece->id,
                                        'available' => (float) $piece->available_quantity,
                                        'unit' => $piece->control_unit,
                                        'salePrice' => (float) $piece->sale_price,
                                        'label' => $piece->display_name,
                                    ];

                                    $sellPiecePayload = [
                                        'id' => $piece->id,
                                        'available' => (float) $piece->available_quantity,
                                        'unit' => $piece->control_unit,
                                    ];
                                @endphp
                                <div class="flex justify-center gap-1">
                                    @if(!Auth::user()->isVendedor())
                                        @if($piece->status === 'fechada')
                                            <button onclick='openPiece(@json($openPiecePayload))' title="Abrir Peça"
                                                    class="p-1.5 rounded-lg transition-colors"
                                                    style="background:rgba(16,185,129,0.15);color:#6ee7b7;"
                                                    onmouseover="this.style.background='rgba(16,185,129,0.28)'"
                                                    onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($piece->status === 'aberta')
                                            <button onclick='sellPartial(@json($partialSalePayload))' title="Vender Quantidade"
                                                    class="p-1.5 rounded-lg transition-colors"
                                                    style="background:rgba(16,185,129,0.15);color:#6ee7b7;"
                                                    onmouseover="this.style.background='rgba(16,185,129,0.28)'"
                                                    onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($piece->status === 'em_transferencia')
                                            @php
                                                $pendingTransfer = $piece->transfers()->whereIn('status', ['pendente', 'aprovada', 'em_transito'])->first();
                                            @endphp
                                            @if($pendingTransfer)
                                                <button onclick="receiveTransfer({{ $pendingTransfer->id }})" title="Receber Peça"
                                                        class="p-1.5 rounded-lg transition-colors"
                                                        style="background:rgba(16,185,129,0.15);color:#6ee7b7;"
                                                        onmouseover="this.style.background='rgba(16,185,129,0.28)'"
                                                        onmouseout="this.style.background='rgba(16,185,129,0.15)'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="cancelTransfer({{ $pendingTransfer->id }})" title="Cancelar Transferência"
                                                        class="p-1.5 rounded-lg transition-colors"
                                                        style="background:rgba(239,68,68,0.15);color:#fca5a5;"
                                                        onmouseover="this.style.background='rgba(239,68,68,0.28)'"
                                                        onmouseout="this.style.background='rgba(239,68,68,0.15)'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                        @if(in_array($piece->status, ['fechada', 'aberta']))
                                            <button onclick="transferPiece({{ $piece->id }})" title="Transferir"
                                                    class="p-1.5 rounded-lg transition-colors"
                                                    style="background:rgba(245,158,11,0.15);color:#fcd34d;"
                                                    onmouseover="this.style.background='rgba(245,158,11,0.28)'"
                                                    onmouseout="this.style.background='rgba(245,158,11,0.15)'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </button>
                                            <button onclick='sellPiece(@json($sellPiecePayload))' title="Vender Peça Inteira"
                                                    class="p-1.5 rounded-lg transition-colors"
                                                    style="background:rgba(59,130,246,0.15);color:#93c5fd;"
                                                    onmouseover="this.style.background='rgba(59,130,246,0.28)'"
                                                    onmouseout="this.style.background='rgba(59,130,246,0.15)'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <a href="{{ route('fabric-pieces.edit', $piece->id) }}" title="Editar"
                                           class="p-1.5 rounded-lg transition-colors"
                                           style="background:rgba(255,255,255,0.06);color:#9ca3af;"
                                           onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                                           onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('fabric-pieces.destroy', $piece->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta peça?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir"
                                                    class="p-1.5 rounded-lg transition-colors"
                                                    style="background:rgba(239,68,68,0.15);color:#fca5a5;"
                                                    onmouseover="this.style.background='rgba(239,68,68,0.28)'"
                                                    onmouseout="this.style.background='rgba(239,68,68,0.15)'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                                         style="background:rgba(255,255,255,0.04);">
                                        <i class="fa-solid fa-layer-group text-2xl text-gray-600"></i>
                                    </div>
                                    <p class="text-gray-500 font-bold">Nenhuma peça encontrada</p>
                                    <a href="{{ route('fabric-pieces.create') }}"
                                       class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-purple-600/30 transition">
                                        Cadastrar primeira peça
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pieces->hasPages())
            <div class="px-5 py-4" style="border-top:1px solid rgba(255,255,255,0.06);">
                {{ $pieces->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Abrir Peça --}}
<div id="open-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);">
                <i class="fa-solid fa-lock-open text-emerald-400"></i>
            </div>
            <h3 class="text-base font-black text-white">Abrir Peça</h3>
        </div>
        <form id="open-form">
            <input type="hidden" id="open-piece-id">
            <div class="mb-4">
                <label id="open-current-label" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Quantidade Atual (kg)</label>
                <input type="number" step="0.001" id="open-weight" placeholder="Ex: 25.500"
                       class="w-full px-4 py-3 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                <p class="text-xs text-gray-600 mt-2">Deixe em branco para manter a quantidade original da peça</p>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="closeOpenModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-emerald-600/30 transition active:scale-95">
                    Abrir Peça
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Transferir --}}
<div id="transfer-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(245,158,11,0.15);">
                <i class="fa-solid fa-right-left text-amber-400"></i>
            </div>
            <h3 class="text-base font-black text-white">Transferir Peça</h3>
        </div>
        <form id="transfer-form">
            <input type="hidden" id="transfer-piece-id">
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Loja Destino</label>
                <select id="transfer-store" required
                        class="w-full px-4 py-3 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Selecione...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Observações</label>
                <textarea id="transfer-notes" rows="2"
                          class="w-full px-4 py-3 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 resize-none"
                          style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="closeTransferModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-amber-600/30 transition active:scale-95">
                    Transferir
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Venda Parcial --}}
<div id="sell-partial-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(59,130,246,0.15);">
                <i class="fa-solid fa-coins text-blue-400"></i>
            </div>
            <h3 class="text-base font-black text-white">Vender Quantidade</h3>
        </div>
        <form id="sell-partial-form">
            <input type="hidden" id="sell-partial-piece-id">
            <input type="hidden" id="sell-partial-unit">
            <div class="mb-4 px-4 py-3 rounded-xl" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);">
                <p class="text-xs text-gray-500">Disponível: <span id="sell-partial-available" class="font-black text-white">0</span></p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label id="sell-partial-quantity-label" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Quantidade *</label>
                    <input type="number" step="0.001" id="sell-partial-quantity" required placeholder="Ex: 5.500"
                           class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                           style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Unidade</label>
                    <div id="sell-partial-unit-label"
                         class="w-full px-3 py-2.5 rounded-xl text-sm text-gray-400 font-bold"
                         style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);">Kg</div>
                </div>
            </div>
            <div class="mb-4">
                <label id="sell-partial-price-label" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Preço por Kg (Venda)</label>
                <input type="number" step="0.01" id="sell-partial-price" placeholder="Ex: 35.00"
                       class="w-full px-4 py-2.5 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Vincular a Venda/Pedido</label>
                <select id="sell-partial-order"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-1 focus:ring-purple-500/50 appearance-none"
                        style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <option value="">Nenhum (venda avulsa)</option>
                    @foreach($recentOrders as $order)
                        <option value="{{ $order['id'] }}">{{ $order['label'] }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-600 mt-1">Vincule a uma venda do PDV ou pedido para rastreabilidade</p>
            </div>
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Observações</label>
                <input type="text" id="sell-partial-notes" placeholder="Cliente, pedido..."
                       class="w-full px-4 py-2.5 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="closeSellPartialModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-emerald-600/30 transition active:scale-95">
                    Confirmar Venda
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Confirmar Ação --}}
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(245,158,11,0.15);">
                <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
            </div>
            <h3 id="confirm-title" class="text-base font-black text-white">Confirmar</h3>
        </div>
        <p id="confirm-message" class="text-sm text-gray-400 mb-6">Tem certeza que deseja continuar?</p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeConfirmModal()"
                    class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                    style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                Cancelar
            </button>
            <button type="button" id="confirm-btn"
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg transition active:scale-95">
                Confirmar
            </button>
        </div>
    </div>
</div>

{{-- Modal Cancelar Transferência --}}
<div id="cancel-transfer-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(239,68,68,0.15);">
                <i class="fa-solid fa-xmark text-red-400"></i>
            </div>
            <h3 class="text-base font-black text-white">Cancelar Transferência</h3>
        </div>
        <form id="cancel-transfer-form">
            <input type="hidden" id="cancel-transfer-id">
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Motivo (opcional)</label>
                <textarea id="cancel-transfer-reason" rows="2" placeholder="Informe o motivo..."
                          class="w-full px-4 py-3 rounded-xl text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-red-500/50 resize-none"
                          style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="closeCancelTransferModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                    Voltar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-red-600/30 transition active:scale-95">
                    Cancelar Transferência
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Sucesso Transferência (com opção de imprimir) --}}
<div id="transfer-success-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(16,185,129,0.15);">
                <i class="fa-solid fa-circle-check text-3xl text-emerald-400"></i>
            </div>
            <h3 class="text-base font-black text-white mb-2">Peça Transferida!</h3>
            <p class="text-sm text-gray-500">A transferência foi realizada com sucesso.</p>
        </div>
        <input type="hidden" id="transfer-success-id">
        <div class="flex gap-2">
            <button type="button" onclick="closeTransferSuccessModal(false)"
                    class="flex-1 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                    style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                Fechar
            </button>
            <button type="button" onclick="closeTransferSuccessModal(true)"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-purple-600/30 transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i>
                Imprimir Nota
            </button>
        </div>
    </div>
</div>

{{-- Modal Importar Excel --}}
<div id="import-modal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="modal-glass rounded-2xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
            </div>
            <h3 class="text-base font-black text-white">Importar Peças (Excel)</h3>
        </div>
        <form action="{{ route('fabric-pieces.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Arquivo Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv" required
                       class="w-full px-4 py-3 rounded-xl text-sm text-gray-400 focus:outline-none focus:ring-1 focus:ring-purple-500/50 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600/20 file:text-purple-400 cursor-pointer"
                       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-gray-400 transition"
                        style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-emerald-600/30 transition active:scale-95">
                    Importar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-[60] space-y-2"></div>

@endsection

@push('scripts')
<script>
    // === Toast Notification System ===
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const colors = {
            success: 'from-emerald-700 to-green-700',
            error: 'from-red-700 to-rose-700',
            warning: 'from-amber-700 to-orange-700',
            info: 'from-blue-700 to-indigo-700'
        };
        
        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        };
        
        toast.className = `bg-gradient-to-r ${colors[type]} text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-slide-in min-w-[300px]`;
        toast.style.cssText = 'border: 1px solid rgba(255,255,255,0.12); backdrop-filter: blur(8px);';
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg>
            <span class="flex-1 text-sm font-semibold">${message}</span>
            <button onclick="this.parentElement.remove()" class="hover:opacity-80 ml-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // === Confirm Modal System ===
    let confirmCallback = null;
    
    function showConfirm(title, message, callback, btnText = 'Confirmar', btnClass = 'bg-blue-600 hover:bg-blue-700') {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        const btn = document.getElementById('confirm-btn');
        btn.textContent = btnText;
        btn.className = `px-4 py-2 text-white rounded-lg ${btnClass}`;
        confirmCallback = callback;
        document.getElementById('confirm-modal').classList.remove('hidden');
    }
    
    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
        confirmCallback = null;
    }
    
    document.getElementById('confirm-btn').addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback();
        }
        closeConfirmModal();
    });

    // === Abrir Peça ===
    function formatControlUnit(unit) {
        return unit === 'metros' ? 'm' : 'kg';
    }

    function openPiece(piece) {
        document.getElementById('open-piece-id').value = piece.id;
        document.getElementById('open-weight').value = '';
        document.getElementById('open-current-label').textContent = `Quantidade Atual (${formatControlUnit(piece.unit)})`;
        document.getElementById('open-modal').classList.remove('hidden');
    }

    function closeOpenModal() {
        document.getElementById('open-modal').classList.add('hidden');
    }

    document.getElementById('open-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('open-piece-id').value;
        const weight = document.getElementById('open-weight').value;

        fetch(`/fabric-pieces/${id}/open`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ current_quantity: weight || null })
        })
        .then(r => r.json())
        .then(data => {
            closeOpenModal();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Erro ao abrir peça', 'error');
            }
        })
        .catch(e => showToast('Erro ao abrir peça', 'error'));
    });

    // === Transferir Peça ===
    function transferPiece(id) {
        document.getElementById('transfer-piece-id').value = id;
        document.getElementById('transfer-store').value = '';
        document.getElementById('transfer-notes').value = '';
        document.getElementById('transfer-modal').classList.remove('hidden');
    }

    function closeTransferModal() {
        document.getElementById('transfer-modal').classList.add('hidden');
    }

    document.getElementById('transfer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('transfer-piece-id').value;
        const toStoreId = document.getElementById('transfer-store').value;
        const notes = document.getElementById('transfer-notes').value;

        if (!toStoreId) {
            showToast('Selecione a loja destino', 'warning');
            return;
        }

        fetch(`/fabric-pieces/${id}/transfer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ to_store_id: toStoreId, notes: notes })
        })
        .then(r => r.json())
        .then(data => {
            closeTransferModal();
            if (data.success) {
                // Mostrar modal de sucesso com opção de imprimir
                document.getElementById('transfer-success-id').value = data.transfer_id;
                document.getElementById('transfer-success-modal').classList.remove('hidden');
            } else {
                showToast(data.message || 'Erro ao transferir peça', 'error');
            }
        })
        .catch(e => showToast('Erro ao transferir peça', 'error'));
    });

    function closeTransferSuccessModal(print = false) {
        const transferId = document.getElementById('transfer-success-id').value;
        document.getElementById('transfer-success-modal').classList.add('hidden');
        
        if (print && transferId) {
            window.open(`/fabric-pieces/transfers/${transferId}/print`, '_blank');
        }
        location.reload();
    }

    // === Vender Peça Inteira ===
    function sellPiece(piece) {
        showConfirm(
            'Vender Peça Inteira',
            `Tem certeza que deseja vender toda a peça (${parseFloat(piece.available || 0).toLocaleString('pt-BR', { minimumFractionDigits: piece.unit === 'metros' ? 2 : 3 })} ${formatControlUnit(piece.unit)})?`,
            () => {
                fetch(`/fabric-pieces/${piece.id}/sell`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erro ao vender peça', 'error');
                    }
                })
                .catch(e => showToast('Erro ao vender peça', 'error'));
            },
            'Vender',
            'bg-blue-600 hover:bg-blue-700'
        );
    }

    // === Venda Parcial ===
    function sellPartial(piece) {
        const unitLabel = formatControlUnit(piece.unit);
        document.getElementById('sell-partial-piece-id').value = piece.id;
        document.getElementById('sell-partial-unit').value = piece.unit;
        document.getElementById('sell-partial-available').textContent = `${parseFloat(piece.available || 0).toLocaleString('pt-BR', { minimumFractionDigits: piece.unit === 'metros' ? 2 : 3 })} ${unitLabel}`;
        document.getElementById('sell-partial-quantity').value = '';
        document.getElementById('sell-partial-price').value = piece.salePrice || '';
        document.getElementById('sell-partial-unit-label').textContent = piece.unit === 'metros' ? 'Metros (m)' : 'Quilos (kg)';
        document.getElementById('sell-partial-quantity-label').textContent = `Quantidade (${unitLabel}) *`;
        document.getElementById('sell-partial-price-label').textContent = piece.unit === 'metros' ? 'Preço por Metro (Venda)' : 'Preço por Kg (Venda)';
        document.getElementById('sell-partial-order').value = '';
        document.getElementById('sell-partial-notes').value = '';
        document.getElementById('sell-partial-modal').classList.remove('hidden');
    }

    function closeSellPartialModal() {
        document.getElementById('sell-partial-modal').classList.add('hidden');
    }

    document.getElementById('sell-partial-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('sell-partial-piece-id').value;
        const quantity = parseFloat(document.getElementById('sell-partial-quantity').value);
        const unit = document.getElementById('sell-partial-unit').value;
        const unitPrice = document.getElementById('sell-partial-price').value;
        const orderId = document.getElementById('sell-partial-order').value;
        const notes = document.getElementById('sell-partial-notes').value;

        if (!quantity || quantity <= 0) {
            showToast('Informe uma quantidade válida', 'warning');
            return;
        }

        fetch(`/fabric-pieces/${id}/sell-partial`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                quantity: quantity, 
                unit: unit,
                unit_price: unitPrice || null,
                order_id: orderId || null,
                notes: notes || null
            })
        })
        .then(r => r.json())
        .then(data => {
            closeSellPartialModal();
            if (data.success) {
                const decimals = unit === 'metros' ? 2 : 3;
                showToast(`${data.message} Restante: ${parseFloat(data.remaining || 0).toLocaleString('pt-BR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })} ${formatControlUnit(unit)}`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Erro ao vender', 'error');
            }
        })
        .catch(e => showToast('Erro ao processar venda', 'error'));
    });

    // === Receber Transferência ===
    function receiveTransfer(transferId) {
        showConfirm(
            'Receber Peça',
            'Confirma o recebimento desta peça transferida?',
            () => {
                fetch(`/fabric-pieces/transfers/${transferId}/receive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'Erro ao receber peça', 'error');
                    }
                })
                .catch(e => showToast('Erro ao receber peça', 'error'));
            },
            'Receber',
            'bg-green-600 hover:bg-green-700'
        );
    }

    // === Cancelar Transferência ===
    function cancelTransfer(transferId) {
        document.getElementById('cancel-transfer-id').value = transferId;
        document.getElementById('cancel-transfer-reason').value = '';
        document.getElementById('cancel-transfer-modal').classList.remove('hidden');
    }

    function closeCancelTransferModal() {
        document.getElementById('cancel-transfer-modal').classList.add('hidden');
    }

    document.getElementById('cancel-transfer-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const transferId = document.getElementById('cancel-transfer-id').value;
        const reason = document.getElementById('cancel-transfer-reason').value;

        fetch(`/fabric-pieces/transfers/${transferId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(r => r.json())
        .then(data => {
            closeCancelTransferModal();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Erro ao cancelar transferência', 'error');
            }
        })
        .catch(e => showToast('Erro ao cancelar transferência', 'error'));
    });
</script>
@endpush
