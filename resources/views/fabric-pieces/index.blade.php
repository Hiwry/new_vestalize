@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Peças de Tecido</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerenciamento de estoque de peças/rolos</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fabric-pieces.report') }}" 
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Relatório
            </a>
            @if(!Auth::user()->isVendedor())
            <a href="{{ route('fabric-pieces.create') }}" 
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Peça
            </a>
            <button onclick="document.getElementById('import-modal').classList.remove('hidden')" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Importar Excel
            </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Ativas</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Fechadas</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['fechadas'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Abertas</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['abertas'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Em Transferência</div>
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['em_transferencia'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Vendidas</div>
            <div class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $stats['vendidas'] }}</div>
        </div>
    </div>

    {{-- Stats por Loja --}}
    @if(count($storeStats) > 1)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quantidade por Loja</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($storeStats as $storeId => $storeStat)
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $storeStat['name'] }}:</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $storeStat['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="NF, Código..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fornecedor</label>
                <input type="text" name="supplier" value="{{ request('supplier') }}" placeholder="Nome..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo Tecido</label>
                <select name="fabric_type_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todos</option>
                    @foreach($fabricTypes as $fabricType)
                        <option value="{{ $fabricType->id }}" {{ request('fabric_type_id') == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor</label>
                <select name="color_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todas</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ request('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="fechada" {{ request('status') == 'fechada' ? 'selected' : '' }}>Fechada</option>
                    <option value="aberta" {{ request('status') == 'aberta' ? 'selected' : '' }}>Aberta</option>
                    <option value="em_transferencia" {{ request('status') == 'em_transferencia' ? 'selected' : '' }}>Em Transferência</option>
                    <option value="vendida" {{ request('status') == 'vendida' ? 'selected' : '' }}>Vendida</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
                <select name="store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2 md:col-span-4 lg:col-span-6 flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">Filtrar</button>
                <a href="{{ route('fabric-pieces.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Limpar</a>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Loja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tecido/Cor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fornecedor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NF</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Peso</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prateleira</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pieces as $piece)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $piece->store->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $piece->fabricType->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $piece->color->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $piece->supplier ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $piece->invoice_number ?? '-' }}</div>
                                @if($piece->invoice_key)
                                    <div class="text-[10px] text-gray-400 truncate max-w-[100px]" title="{{ $piece->invoice_key }}">
                                        {{ Str::limit($piece->invoice_key, 15) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $piece->weight ? number_format($piece->weight, 2, ',', '.') . ' kg' : '-' }}</div>
                                @if($piece->weight_current && $piece->weight_current != $piece->weight)
                                    <div class="text-xs text-green-600 dark:text-green-400">Atual: {{ number_format($piece->weight_current, 2, ',', '.') }} kg</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                {{ $piece->shelf ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $piece->status_color }}">
                                    {{ $piece->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-1">
                                    @if(!Auth::user()->isVendedor())
                                        @if($piece->status === 'fechada')
                                            <button onclick="openPiece({{ $piece->id }})" title="Abrir Peça"
                                                    class="p-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($piece->status === 'aberta')
                                            <button onclick="sellPartial({{ $piece->id }}, {{ $piece->weight_current ?? $piece->weight ?? 0 }})" title="Vender Metros/Kg"
                                                    class="p-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded transition">
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
                                                        class="p-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="cancelTransfer({{ $pendingTransfer->id }})" title="Cancelar Transferência"
                                                        class="p-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                        @if(in_array($piece->status, ['fechada', 'aberta']))
                                            <button onclick="transferPiece({{ $piece->id }})" title="Transferir"
                                                    class="p-1.5 bg-orange-100 text-orange-700 hover:bg-orange-200 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </button>
                                            <button onclick="sellPiece({{ $piece->id }})" title="Vender Peça Inteira"
                                                    class="p-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <a href="{{ route('fabric-pieces.edit', $piece->id) }}" title="Editar"
                                           class="p-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('fabric-pieces.destroy', $piece->id) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta peça?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir"
                                                    class="p-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p>Nenhuma peça encontrada</p>
                                <a href="{{ route('fabric-pieces.create') }}" class="text-indigo-600 hover:underline mt-2 inline-block">Cadastrar primeira peça</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pieces->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $pieces->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Abrir Peça --}}
<div id="open-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Abrir Peça</h3>
        <form id="open-form">
            <input type="hidden" id="open-piece-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso Atual (kg)</label>
                <input type="number" step="0.001" id="open-weight" placeholder="Ex: 25.500"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter o peso original</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeOpenModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Abrir Peça</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Transferir --}}
<div id="transfer-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Transferir Peça</h3>
        <form id="transfer-form">
            <input type="hidden" id="transfer-piece-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja Destino</label>
                <select id="transfer-store" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                <textarea id="transfer-notes" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeTransferModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">Transferir</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Venda Parcial --}}
<div id="sell-partial-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Vender Quantidade</h3>
        <form id="sell-partial-form">
            <input type="hidden" id="sell-partial-piece-id">
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Disponível: <span id="sell-partial-available" class="font-bold text-gray-900 dark:text-gray-100">0</span> kg</p>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade *</label>
                    <input type="number" step="0.001" id="sell-partial-quantity" required placeholder="Ex: 5.500"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unidade</label>
                    <select id="sell-partial-unit" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        <option value="kg">Quilos (kg)</option>
                        <option value="metros">Metros (m)</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço por Kg (Venda)</label>
                <input type="number" step="0.01" id="sell-partial-price" placeholder="Ex: 35.00"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vincular a Venda/Pedido</label>
                <select id="sell-partial-order" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    <option value="">Nenhum (venda avulsa)</option>
                    @foreach($recentOrders as $order)
                        <option value="{{ $order['id'] }}">{{ $order['label'] }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Vincule a uma venda do PDV ou pedido para rastreabilidade</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                <input type="text" id="sell-partial-notes" placeholder="Cliente, pedido..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeSellPartialModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">Confirmar Venda</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Confirmar Ação --}}
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div id="confirm-icon" class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 id="confirm-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar</h3>
        </div>
        <p id="confirm-message" class="text-gray-600 dark:text-gray-400 mb-6">Tem certeza que deseja continuar?</p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
            <button type="button" id="confirm-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Confirmar</button>
        </div>
    </div>
</div>

{{-- Modal Cancelar Transferência --}}
<div id="cancel-transfer-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Cancelar Transferência</h3>
        <form id="cancel-transfer-form">
            <input type="hidden" id="cancel-transfer-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo (opcional)</label>
                <textarea id="cancel-transfer-reason" rows="2" placeholder="Informe o motivo..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCancelTransferModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Voltar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Cancelar Transferência</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Sucesso Transferência (com opção de imprimir) --}}
<div id="transfer-success-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="text-center mb-6">
            <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Peça Transferida!</h3>
            <p class="text-gray-600 dark:text-gray-400 mt-2">A transferência foi realizada com sucesso.</p>
        </div>
        <input type="hidden" id="transfer-success-id">
        <div class="flex gap-2">
            <button type="button" onclick="closeTransferSuccessModal(false)" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Fechar</button>
            <button type="button" onclick="closeTransferSuccessModal(true)" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir Nota
            </button>
        </div>
    </div>
</div>

    </div>
</div>

{{-- Modal Importar Excel --}}
<div id="import-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Importar Peças (Excel)</h3>
        <form action="{{ route('fabric-pieces.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Arquivo Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Importar</button>
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
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        };
        
        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in min-w-[300px]`;
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="hover:opacity-80">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
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
    function openPiece(id) {
        document.getElementById('open-piece-id').value = id;
        document.getElementById('open-weight').value = '';
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
            body: JSON.stringify({ weight_current: weight || null })
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
    function sellPiece(id) {
        showConfirm(
            'Vender Peça Inteira',
            'Tem certeza que deseja marcar esta peça INTEIRA como vendida?',
            () => {
                fetch(`/fabric-pieces/${id}/sell`, {
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
    function sellPartial(id, available) {
        document.getElementById('sell-partial-piece-id').value = id;
        document.getElementById('sell-partial-available').textContent = available.toFixed(3);
        document.getElementById('sell-partial-quantity').value = '';
        document.getElementById('sell-partial-price').value = '';
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
                showToast(`${data.message} Restante: ${data.remaining.toFixed(3)} ${unit}`, 'success');
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

<style>
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endpush
