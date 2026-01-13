@extends('layouts.admin')

@section('content')
<style>
    /* Estilos para melhor visualização da tabela */
    .stock-requests-table tbody tr:hover {
        background-color: rgb(249 250 251) !important;
    }
    
    .dark .stock-requests-table tbody tr:hover {
        background-color: rgb(31 41 55) !important;
    }
    
    /* Sticky columns */
    .sticky-left {
        position: sticky;
        left: 0;
        z-index: 5;
        background-color: white;
    }
    
    .dark .sticky-left {
        background-color: rgb(31 41 55);
    }
    
    .sticky-right {
        position: sticky;
        right: 0;
        z-index: 5;
        background-color: white;
    }
    
    .dark .sticky-right {
        background-color: rgb(31 41 55);
    }
</style>
<script>
    // Função stub para evitar erros
    (function() {
        window.openAddProductModal = function() {
            return false;
        };
    })();
    
    // Definir funções globais imediatamente
    // Definir funções globais imediatamente
    window.approveRequest = function(id, isBroadcast) {
        const modal = document.getElementById('approve-request-id');
        const approveModal = document.getElementById('approve-modal');
        const storeSelectContainer = document.getElementById('approve-fulfilling-store-container');
        const storeSelect = document.getElementById('approve-fulfilling-store');

        if (modal && approveModal) {
            modal.value = id;
            
            if (isBroadcast && storeSelectContainer) {
                storeSelectContainer.style.display = 'block';
                if(storeSelect) storeSelect.required = true;
            } else if (storeSelectContainer) {
                storeSelectContainer.style.display = 'none';
                if(storeSelect) storeSelect.required = false;
            }

            // Update form action
            const form = document.getElementById('approve-form');
            if (form) {
                form.action = `/stock-requests/${id}/approve`;
            }

            approveModal.classList.remove('hidden');
        }
    };
    
    window.approveAllGroup = function(requests) {
        const requestsInput = document.getElementById('approve-group-requests');
        const modal = document.getElementById('approve-group-modal');
        if (requestsInput && modal) {
            requestsInput.value = JSON.stringify(requests);
            modal.classList.remove('hidden');
            const useRequestedCheckbox = document.getElementById('approve-group-use-requested');
            const customQuantityContainer = document.getElementById('approve-group-custom-quantity-container');
            if (useRequestedCheckbox && customQuantityContainer) {
                useRequestedCheckbox.checked = true;
                customQuantityContainer.style.display = 'none';
            }
        }
    };
    
    window.rejectRequest = function(id) {
        const modal = document.getElementById('reject-request-id');
        const rejectModal = document.getElementById('reject-modal');
        if (modal && rejectModal) {
            modal.value = id;
            rejectModal.classList.remove('hidden');
        }
    };
    
    window.rejectRequestGroup = function(ids) {
        const idsInput = document.getElementById('reject-group-ids');
        const modal = document.getElementById('reject-group-modal');
        if (idsInput && modal) {
            idsInput.value = JSON.stringify(ids);
            modal.classList.remove('hidden');
        }
    };
    
    window.completeRequest = function(id) {
        const modal = document.getElementById('complete-request-id');
        const completeModal = document.getElementById('complete-modal');
        if (modal && completeModal) {
            modal.value = id;
            completeModal.classList.remove('hidden');
        }
    };
</script>

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Solicitações de Estoque</h1>
    </div>
    <div class="flex flex-wrap gap-2">
        <button onclick="openRequestTransferModal()" 
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            Transferência
        </button>
        <button onclick="openRequestOrderModal()" 
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Para Pedido
        </button>
        <button onclick="openRequestDecrementModal()" 
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
            Retirada
        </button>
        <a href="{{ route('stocks.index') }}" 
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Ver Estoque
        </a>
    </div>
</div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-100 dark:border-green-400 rounded-lg shadow" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-100 dark:border-red-400 rounded-lg shadow" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('stock-requests.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
            <select name="store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>
                        {{ $store->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                Filtrar
            </button>
            @if(request('status') || request('store_id'))
            <a href="{{ route('stock-requests.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Limpar
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs stock-requests-table">
            <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Origem <span class="text-gray-400">→</span> Destino</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Tamanhos</th>
                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Solic.</th>
                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Aprov.</th>
                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Aprovado Por</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Data Aprov.</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Data Solic.</th>
                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Observações</th>
                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($requests as $group)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">
                        @if($group['order_id'])
                            <div class="flex items-center gap-2">
                                <span class="font-medium">
                                    @php
                                        $order = $group['order'] ?? null;
                                        $isPdv = $order && $order->is_pdv;
                                    @endphp
                                    @if($isPdv)
                                        Venda #{{ str_pad($group['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    @else
                                        Pedido #{{ str_pad($group['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    @endif
                                </span>
                                @if($isPdv)
                                <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-semibold rounded">
                                    PDV
                                </span>
                                @endif
                            </div>
                        @else
                            <span class="font-medium">#{{ $group['requests'][0]->id }}</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col gap-1">
                            <span class="font-medium">{{ $group['requesting_store']->name }}</span>
                            
                            @if($group['target_store'])
                                <div class="flex items-center gap-1 text-gray-500 dark:text-gray-400 text-[10px]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    {{ $group['target_store']->name }}
                                </div>
                            @else
                                <div class="flex items-center gap-1 text-purple-600 dark:text-purple-400 text-[10px] font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                    TODAS (GERAL)
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col">
                            <span class="font-medium">{{ $group['fabric']->name ?? '-' }}</span>
                            <span class="text-gray-500 dark:text-gray-400">{{ $group['color']->name ?? '-' }} • {{ $group['cut_type']->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-2 py-2 text-xs">
                        <div class="flex flex-wrap gap-1">
                            @php
                                $sizeOrder = ['PP' => 1, 'P' => 2, 'M' => 3, 'G' => 4, 'GG' => 5, 'EXG' => 6, 'G1' => 7, 'G2' => 8, 'G3' => 9];
                                uksort($group['sizes_summary'], function($a, $b) use ($sizeOrder) {
                                    return ($sizeOrder[$a] ?? 99) <=> ($sizeOrder[$b] ?? 99);
                                });
                            @endphp
                            @foreach($group['sizes_summary'] as $size => $quantity)
                                <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded">
                                    {{ $quantity }}{{ $size }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-2 py-2 text-xs text-center text-gray-900 dark:text-gray-100 font-medium">
                        @php
                            $totalRequested = array_sum($group['sizes_summary']);
                        @endphp
                        {{ $totalRequested }}
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        @php
                            $totalApproved = 0;
                            foreach ($group['requests'] as $req) {
                                $totalApproved += $req->approved_quantity ?? 0;
                            }
                        @endphp
                        @if($totalApproved > 0)
                            <span class="text-green-600 dark:text-green-400 font-medium">{{ $totalApproved }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        @php
                            $statusColors = [
                                'pendente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'rejeitado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'em_transferencia' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'concluido' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'cancelado' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            ];
                            $color = $statusColors[$group['status']] ?? 'bg-gray-100 text-gray-800';
                            
                            // Verificar se alguma request do grupo tem estoque reservado
                            $hasReservedStock = false;
                            $isTransfer = false;
                            foreach ($group['requests'] as $req) {
                                if ($req->is_stock_reserved) {
                                    $hasReservedStock = true;
                                }
                                if ($req->is_transfer) {
                                    $isTransfer = true;
                                }
                            }
                        @endphp
                        <div class="flex flex-col gap-1 items-center">
                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $group['status'])) }}
                            </span>
                            @if($hasReservedStock && $group['status'] === 'pendente')
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 flex items-center gap-0.5" title="Estoque já reservado automaticamente">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    RESERVADO
                                </span>
                            @endif
                            @if($isTransfer)
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 flex items-center gap-0.5" title="Transferência entre lojas">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    TRANSF.
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-900 dark:text-gray-100">
                        @if($group['approved_by'] ?? null)
                            <span class="font-medium">{{ $group['approved_by']->name ?? '-' }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-500 dark:text-gray-400">
                        @php
                            $approvedAt = $group['approved_at'] ?? null;
                        @endphp
                        @if($approvedAt && ($approvedAt instanceof \Carbon\Carbon || $approvedAt instanceof \DateTime))
                            <span>{{ \Carbon\Carbon::parse($approvedAt)->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $group['created_at']->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-2 py-2 text-xs text-gray-500 dark:text-gray-400 max-w-xs">
                        @php
                            $notes = $group['request_notes'] ?? null;
                            if (!$notes) {
                                foreach ($group['requests'] as $req) {
                                    if ($req->request_notes) {
                                        $notes = $req->request_notes;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if($notes)
                            <span class="truncate block" title="{{ $notes }}">{{ Str::limit($notes, 30) }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-xs text-center">
                        <div class="flex gap-1 justify-center">
                            @if($group['status'] === 'pendente')
                                <button onclick="approveRequest({{ $req->id }}, {{ $group['target_store'] ? 'false' : 'true' }})" 
                                        class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition"
                                        title="Aprovar">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                <button onclick="rejectRequestGroup({{ json_encode(array_map(fn($r) => $r->id, $group['requests'])) }})" 
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition"
                                        title="Rejeitar">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @elseif(in_array($group['status'], ['aprovado', 'em_transferencia', 'concluido']))
                                {{-- Botão para baixar comprovante de conferência --}}
                                <a href="{{ route('stock-requests.receipt', $group['requests'][0]->id) }}" 
                                   target="_blank"
                                   class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded transition flex items-center gap-1"
                                   title="Baixar Termo de Conferência">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </a>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="px-4 py-12 text-center">
                        <div class="text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm font-medium">Nenhuma solicitação encontrada</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
@if($requests->hasPages())
<div class="mt-4 flex justify-center">
    {{ $requests->links() }}
</div>
@endif

<!-- Modais -->
<!-- Modal de Aprovação -->
<div id="approve-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aprovar Solicitação</h3>
        <form id="approve-form" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" id="approve-request-id" name="id">
            
            <div class="mb-4" id="approve-fulfilling-store-container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja de Origem (Atendimento):</label>
                <select id="approve-fulfilling-store" name="fulfilling_store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja...</option>
                    @foreach($stores as $store)
                        @if(\App\Helpers\StoreHelper::canAccessStore($store->id))
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Selecione de qual loja sairá o estoque.</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade:</label>
                <input type="number" id="approve-quantity" name="approved_quantity" min="1" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="approve-notes" name="approval_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="approve-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Aprovar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Rejeição -->
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rejeitar Solicitação</h3>
        <form id="reject-form">
            <input type="hidden" id="reject-request-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo:</label>
                <textarea id="reject-reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="reject-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Rejeitar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Transferência -->
<div id="request-transfer-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-2xl w-full my-8">
        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white dark:bg-gray-800 pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar Transferência</h3>
            <button onclick="closeRequestTransferModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-transfer-form" onsubmit="submitRequestTransfer(event)" class="space-y-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja Solicitante:</label>
                <select id="transfer-requesting-store" name="requesting_store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja de Destino:</label>
                <select id="transfer-target-store" name="target_store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tecido:</label>
                <select id="transfer-fabric" name="fabric_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tecido</option>
                    @foreach($fabrics as $fabric)
                        <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cor:</label>
                <select id="transfer-color" name="color_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a cor</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Corte:</label>
                <select id="transfer-cut-type" name="cut_type_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tipo de corte</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Quantidades por Tamanho
                </label>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    @foreach($sizes as $size)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 text-center">
                            {{ $size }}
                        </label>
                        <input type="number" 
                               id="transfer-size-{{ $size }}"
                               name="sizes[{{ $size }}]" 
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade para cada tamanho que deseja transferir. Deixe em 0 para não incluir.
                </p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="transfer-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestTransferModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar para Pedido -->
<div id="request-order-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-3xl w-full my-8 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white dark:bg-gray-800 pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar para Pedido</h3>
            <button onclick="closeRequestOrderModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-order-form" onsubmit="submitRequestOrder(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pedido:</label>
                <select id="order-id" name="order_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o pedido</option>
                    @foreach($recentOrders as $order)
                        <option value="{{ $order->id }}">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja Solicitante:</label>
                <select id="order-requesting-store" name="requesting_store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tecido:</label>
                <select id="order-fabric" name="fabric_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tecido</option>
                    @foreach($fabrics as $fabric)
                        <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cor:</label>
                <select id="order-color" name="color_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a cor</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Corte:</label>
                <select id="order-cut-type" name="cut_type_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tipo de corte</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Quantidades por Tamanho
                </label>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    @foreach($sizes as $size)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 text-center">
                            {{ $size }}
                        </label>
                        <input type="number" 
                               id="order-size-{{ $size }}"
                               name="sizes[{{ $size }}]" 
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade para cada tamanho que deseja solicitar. Deixe em 0 para não incluir.
                </p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="order-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestOrderModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Retirada -->
<div id="request-decrement-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-lg w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar Retirada</h3>
            <button onclick="closeRequestDecrementModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-decrement-form" onsubmit="submitRequestDecrement(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja:</label>
                <select id="decrement-store" name="requesting_store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tecido:</label>
                <select id="decrement-fabric" name="fabric_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tecido</option>
                    @foreach($fabrics as $fabric)
                        <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cor:</label>
                <select id="decrement-color" name="color_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a cor</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Corte:</label>
                <select id="decrement-cut-type" name="cut_type_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tipo de corte</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Quantidades por Tamanho
                </label>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    @foreach($sizes as $size)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 text-center">
                            {{ $size }}
                        </label>
                        <input type="number" 
                               id="decrement-size-{{ $size }}"
                               name="sizes[{{ $size }}]" 
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade para cada tamanho que deseja retirar. Deixe em 0 para não incluir.
                </p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="decrement-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestDecrementModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Conclusão -->
<div id="complete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Concluir Transferência</h3>
        <form id="complete-form">
            <input type="hidden" id="complete-request-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade Transferida:</label>
                <input type="number" id="complete-quantity" min="1" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="complete-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Concluir</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Aprovação em Grupo -->
<div id="approve-group-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aprovar Grupo</h3>
        <form id="approve-group-form">
            <input type="hidden" id="approve-group-requests">
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <input type="checkbox" id="approve-group-use-requested" checked class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label class="text-sm text-gray-700 dark:text-gray-300">Usar quantidade solicitada</label>
                </div>
            </div>
            <div class="mb-4" id="approve-group-custom-quantity-container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade Personalizada:</label>
                <input type="number" id="approve-group-quantity" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="approve-group-notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="approve-group-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Aprovar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Rejeição em Grupo -->
<div id="reject-group-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rejeitar Grupo</h3>
        <form id="reject-group-form">
            <input type="hidden" id="reject-group-ids">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo:</label>
                <textarea id="reject-group-reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="reject-group-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Rejeitar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation para botões de ação
        document.addEventListener('click', function(e) {
            const button = e.target.closest('[data-action]');
            if (button) {
                const action = button.getAttribute('data-action');
                const id = button.getAttribute('data-id');
                const ids = button.getAttribute('data-ids');
                
                switch(action) {
                    case 'approve':
                        if (id && window.approveRequest) {
                            window.approveRequest(parseInt(id));
                        }
                        break;
                    case 'approve-group':
                        if (button.getAttribute('data-requests') && window.approveAllGroup) {
                            try {
                                const requests = JSON.parse(button.getAttribute('data-requests'));
                                window.approveAllGroup(requests);
                            } catch (e) {
                                console.error('Erro ao parsear requests:', e);
                            }
                        }
                        break;
                    case 'reject':
                        if (id && window.rejectRequest) {
                            window.rejectRequest(parseInt(id));
                        }
                        break;
                    case 'reject-group':
                        if (ids && window.rejectRequestGroup) {
                            try {
                                const idsArray = JSON.parse(ids);
                                window.rejectRequestGroup(idsArray);
                            } catch (e) {
                                console.error('Erro ao parsear IDs:', e);
                            }
                        }
                        break;
                    case 'complete':
                        if (id && window.completeRequest) {
                            window.completeRequest(parseInt(id));
                        }
                        break;
                }
            }
            
            const closeButton = e.target.closest('[data-modal-close]');
            if (closeButton) {
                const modalId = closeButton.getAttribute('data-modal-close');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            }
        });
        
        // Event listeners para formulários
        const approveGroupForm = document.getElementById('approve-group-form');
        if (approveGroupForm) {
            approveGroupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitApproveGroup(e);
            });
        }
        
        const rejectGroupForm = document.getElementById('reject-group-form');
        if (rejectGroupForm) {
            rejectGroupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitRejectGroup(e);
            });
        }
        
        const approveForm = document.getElementById('approve-form');
        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitApprove(e);
            });
        }
        
        const rejectForm = document.getElementById('reject-form');
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitReject(e);
            });
        }
        
        const completeForm = document.getElementById('complete-form');
        if (completeForm) {
            completeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitComplete(e);
            });
        }
        
        // Checkbox de usar quantidade solicitada
        const useRequestedCheckbox = document.getElementById('approve-group-use-requested');
        const customQuantityContainer = document.getElementById('approve-group-custom-quantity-container');
        
        if (useRequestedCheckbox && customQuantityContainer) {
            useRequestedCheckbox.addEventListener('change', function() {
                customQuantityContainer.style.display = this.checked ? 'none' : 'block';
            });
        }
    });
    
    function submitApproveGroup(event) {
        event.preventDefault();
        const requestIds = JSON.parse(document.getElementById('approve-group-requests').value);
        const useRequested = document.getElementById('approve-group-use-requested').checked;
        const customQuantityInput = document.getElementById('approve-group-quantity').value;
        const customQuantity = customQuantityInput ? parseInt(customQuantityInput) : null;
        const notes = document.getElementById('approve-group-notes').value;
        
        if (!useRequested && (!customQuantity || customQuantity <= 0)) {
            showNotification('Informe uma quantidade válida', 'error');
            return;
        }
        
        let successCount = 0;
        let errorCount = 0;
        const promises = requestIds.map(requestId => {
            // Se useRequested é true, não enviamos quantidade customizada
            // O backend vai usar a quantidade solicitada
            const approvalData = {
                approval_notes: notes || 'Aprovação em grupo'
            };
            
            if (!useRequested && customQuantity) {
                approvalData.approved_quantity = customQuantity;
            }
            
            return fetch(`/stock-requests/${requestId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(approvalData)
            })
            .then(async res => {
                const data = await res.json();
                if (res.ok && data.success) {
                    successCount++;
                    // Guardar URL do PDF para abrir ao final (basta um para o lote)
                    if (data.pdf_url) {
                        window.latestPdfUrl = data.pdf_url;
                    }
                } else {
                    errorCount++;
                    console.error('Erro ao aprovar solicitação:', data.message || data.errors);
                }
            })
            .catch(error => {
                errorCount++;
                console.error('Erro ao aprovar solicitação:', error);
            });
        });
        
        Promise.all(promises).then(() => {
            document.getElementById('approve-group-modal').classList.add('hidden');
            if (successCount > 0) {
                const msg = `${successCount} solicitação(ões) aprovada(s) com sucesso!${errorCount > 0 ? ' ' + errorCount + ' erro(s).' : ''}`;
                showNotification(msg, 'success');
                
                if (window.latestPdfUrl) {
                    window.open(window.latestPdfUrl, '_blank');
                }
                
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Nenhuma solicitação foi aprovada. Verifique os erros no console.', 'error');
            }
        });
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-md ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
    
    function submitApprove(event) {
        event.preventDefault();
        const id = document.getElementById('approve-request-id').value;
        const quantity = parseInt(document.getElementById('approve-quantity').value);
        const notes = document.getElementById('approve-notes').value;
        
        if (!quantity || quantity <= 0) {
            showNotification('Informe uma quantidade válida', 'error');
            return;
        }
        
        fetch(`/stock-requests/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                approved_quantity: quantity,
                approval_notes: notes
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                document.getElementById('approve-modal').classList.add('hidden');
                showNotification('Solicitação aprovada com sucesso!', 'success');
                
                if (data.pdf_url) {
                    window.open(data.pdf_url, '_blank');
                }
                
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorMsg = data.message || data.errors || 'Erro desconhecido';
                showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao aprovar solicitação. Verifique o console para mais detalhes.', 'error');
        });
    }
    
    function submitReject(event) {
        event.preventDefault();
        const id = document.getElementById('reject-request-id').value;
        const reason = document.getElementById('reject-reason').value;
        
        if (!reason || reason.trim() === '') {
            showNotification('Informe o motivo da rejeição', 'error');
            return;
        }
        
        fetch(`/stock-requests/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rejection_reason: reason
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                document.getElementById('reject-modal').classList.add('hidden');
                showNotification('Solicitação rejeitada.', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorMsg = data.message || data.errors || 'Erro desconhecido';
                showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao rejeitar solicitação. Verifique o console para mais detalhes.', 'error');
        });
    }
    
    function submitRejectGroup(event) {
        event.preventDefault();
        const ids = JSON.parse(document.getElementById('reject-group-ids').value);
        const reason = document.getElementById('reject-group-reason').value;
        
        if (!reason || reason.trim() === '') {
            showNotification('Informe o motivo da rejeição', 'error');
            return;
        }
        
        let successCount = 0;
        let errorCount = 0;
        const promises = ids.map(id => 
            fetch(`/stock-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejection_reason: reason
                })
            })
            .then(async res => {
                const data = await res.json();
                if (res.ok && data.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            })
            .catch(() => errorCount++)
        );
        
        Promise.all(promises).then(() => {
            document.getElementById('reject-group-modal').classList.add('hidden');
            if (successCount > 0) {
                showNotification(`${successCount} solicitação(ões) rejeitada(s) com sucesso!${errorCount > 0 ? ' ' + errorCount + ' erro(s).' : ''}`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Nenhuma solicitação foi rejeitada. Verifique os erros.', 'error');
            }
        });
    }
    
    // Funções para modais de criação de solicitações
    function openRequestTransferModal() {
        const modal = document.getElementById('request-transfer-modal');
        modal.classList.remove('hidden');
        
        // Adicionar listener para filtrar loja de destino quando origem mudar
        const requestingStore = document.getElementById('transfer-requesting-store');
        const targetStore = document.getElementById('transfer-target-store');
        
        requestingStore.addEventListener('change', function() {
            const selectedStoreId = this.value;
            Array.from(targetStore.options).forEach(option => {
                if (option.value && option.value === selectedStoreId) {
                    option.style.display = 'none';
                } else if (option.value) {
                    option.style.display = 'block';
                }
            });
            if (targetStore.value === selectedStoreId) {
                targetStore.value = '';
            }
        });
    }
    
    function closeRequestTransferModal() {
        document.getElementById('request-transfer-modal').classList.add('hidden');
        document.getElementById('request-transfer-form').reset();
    }
    
    function submitRequestTransfer(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const sizes = @json($sizes);
        
        // Coletar quantidades de todos os tamanhos
        const sizeQuantities = {};
        let hasQuantities = false;
        
        sizes.forEach(size => {
            const input = document.getElementById(`transfer-size-${size}`);
            const quantity = input ? parseInt(input.value) || 0 : 0;
            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            alert('Por favor, informe pelo menos uma quantidade para algum tamanho.');
            return;
        }
        
        // Criar solicitações para cada tamanho com quantidade > 0
        const requests = [];
        sizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                requests.push({
                    requesting_store_id: parseInt(formData.get('requesting_store_id')),
                    target_store_id: parseInt(formData.get('target_store_id')),
                    fabric_id: formData.get('fabric_id') ? parseInt(formData.get('fabric_id')) : null,
                    color_id: formData.get('color_id') ? parseInt(formData.get('color_id')) : null,
                    cut_type_id: formData.get('cut_type_id') ? parseInt(formData.get('cut_type_id')) : null,
                    size: size,
                    requested_quantity: sizeQuantities[size],
                    request_notes: formData.get('request_notes') || null,
                });
            }
        });
        
        // Enviar todas as solicitações
        Promise.all(requests.map(request => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            }).then(res => res.json())
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            
            if (successCount > 0) {
                if (failCount > 0) {
                    alert(`${successCount} solicitação(ões) criada(s) com sucesso! ${failCount} falharam.`);
                } else {
                    alert(`${successCount} solicitação(ões) de transferência criada(s) com sucesso!`);
                }
                closeRequestTransferModal();
                location.reload();
            } else {
                alert('Erro ao criar solicitações: ' + (results[0]?.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitações:', error);
            alert('Erro ao criar solicitações de transferência');
        });
    }
    
    function openRequestOrderModal() {
        document.getElementById('request-order-modal').classList.remove('hidden');
    }
    
    function closeRequestOrderModal() {
        document.getElementById('request-order-modal').classList.add('hidden');
        document.getElementById('request-order-form').reset();
    }
    
    function submitRequestOrder(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const sizes = @json($sizes);
        
        // Validar campos obrigatórios
        const orderId = formData.get('order_id');
        const requestingStoreId = formData.get('requesting_store_id');
        
        if (!orderId || orderId === '') {
            alert('Por favor, selecione um pedido.');
            return;
        }
        
        if (!requestingStoreId || requestingStoreId === '') {
            alert('Por favor, selecione a loja solicitante.');
            return;
        }
        
        // Coletar quantidades de todos os tamanhos
        const sizeQuantities = {};
        let hasQuantities = false;
        
        sizes.forEach(size => {
            const input = document.getElementById(`order-size-${size}`);
            const quantity = input ? parseInt(input.value) || 0 : 0;
            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            alert('Por favor, informe pelo menos uma quantidade para algum tamanho.');
            return;
        }
        
        // Criar solicitações para cada tamanho com quantidade > 0
        const requests = [];
        sizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                const request = {
                    order_id: parseInt(orderId),
                    requesting_store_id: parseInt(requestingStoreId),
                    target_store_id: null,
                    size: size,
                    requested_quantity: sizeQuantities[size],
                };
                
                // Adicionar campos opcionais apenas se tiverem valor
                const fabricId = formData.get('fabric_id');
                if (fabricId && fabricId !== '') {
                    request.fabric_id = parseInt(fabricId);
                } else {
                    request.fabric_id = null;
                }
                
                const colorId = formData.get('color_id');
                if (colorId && colorId !== '') {
                    request.color_id = parseInt(colorId);
                } else {
                    request.color_id = null;
                }
                
                const cutTypeId = formData.get('cut_type_id');
                if (cutTypeId && cutTypeId !== '') {
                    request.cut_type_id = parseInt(cutTypeId);
                } else {
                    request.cut_type_id = null;
                }
                
                const notes = formData.get('request_notes');
                if (notes && notes.trim() !== '') {
                    request.request_notes = notes.trim();
                } else {
                    request.request_notes = null;
                }
                
                requests.push(request);
            }
        });
        
        // Log para debug
        console.log('Solicitações a serem enviadas:', requests);
        
        // Enviar todas as solicitações
        Promise.all(requests.map((request, index) => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            })
            .then(async res => {
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await res.text();
                    console.error(`Resposta não é JSON para requisição ${index + 1}:`, text.substring(0, 500));
                    return { success: false, message: 'Resposta do servidor não é JSON' };
                }
                const data = await res.json();
                if (!res.ok) {
                    console.error(`Erro na requisição ${index + 1}:`, data);
                    return { success: false, message: data.message || 'Erro desconhecido', errors: data.errors };
                }
                return data;
            })
            .catch(error => {
                console.error(`Erro ao enviar requisição ${index + 1}:`, error);
                return { success: false, message: error.message || 'Erro ao enviar requisição' };
            })
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            const failedRequests = results.filter(r => !r.success);
            
            if (successCount > 0) {
                if (failCount > 0) {
                    const errorMessages = failedRequests.map(r => r.message).join(', ');
                    alert(`${successCount} solicitação(ões) criada(s) com sucesso! ${failCount} falharam.\n\nErros: ${errorMessages}`);
                } else {
                    alert(`${successCount} solicitação(ões) para pedido criada(s) com sucesso!`);
                }
                closeRequestOrderModal();
                location.reload();
            } else {
                const errorMessages = failedRequests.map(r => r.message || 'Erro desconhecido').join('\n');
                alert('Erro ao criar solicitações:\n\n' + errorMessages);
            }
        })
        .catch(error => {
            console.error('Erro ao processar solicitações:', error);
            alert('Erro ao criar solicitações para pedido: ' + error.message);
        });
    }
    
    function openRequestDecrementModal() {
        document.getElementById('request-decrement-modal').classList.remove('hidden');
    }
    
    function closeRequestDecrementModal() {
        document.getElementById('request-decrement-modal').classList.add('hidden');
        document.getElementById('request-decrement-form').reset();
    }
    
    function submitRequestDecrement(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const sizes = @json($sizes);
        
        // Coletar quantidades de todos os tamanhos
        const sizeQuantities = {};
        let hasQuantities = false;
        
        sizes.forEach(size => {
            const input = document.getElementById(`decrement-size-${size}`);
            const quantity = input ? parseInt(input.value) || 0 : 0;
            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            alert('Por favor, informe pelo menos uma quantidade para algum tamanho.');
            return;
        }
        
        // Criar solicitações para cada tamanho com quantidade > 0
        const requests = [];
        sizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                requests.push({
                    requesting_store_id: parseInt(formData.get('requesting_store_id')),
                    target_store_id: null,
                    order_id: null,
                    fabric_id: formData.get('fabric_id') ? parseInt(formData.get('fabric_id')) : null,
                    color_id: formData.get('color_id') ? parseInt(formData.get('color_id')) : null,
                    cut_type_id: formData.get('cut_type_id') ? parseInt(formData.get('cut_type_id')) : null,
                    size: size,
                    requested_quantity: sizeQuantities[size],
                    request_notes: formData.get('request_notes') || null,
                });
            }
        });
        
        // Enviar todas as solicitações
        Promise.all(requests.map(request => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            }).then(res => res.json())
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            
            if (successCount > 0) {
                if (failCount > 0) {
                    alert(`${successCount} solicitação(ões) criada(s) com sucesso! ${failCount} falharam.`);
                } else {
                    alert(`${successCount} solicitação(ões) de retirada criada(s) com sucesso!`);
                }
                closeRequestDecrementModal();
                location.reload();
            } else {
                alert('Erro ao criar solicitações: ' + (results[0]?.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitações:', error);
            alert('Erro ao criar solicitações de retirada');
        });
    }
    
    // Garantir que as funções estão disponíveis globalmente
    window.openRequestTransferModal = openRequestTransferModal;
    window.openRequestOrderModal = openRequestOrderModal;
    window.openRequestDecrementModal = openRequestDecrementModal;
    
    function submitComplete(event) {
        event.preventDefault();
        const id = document.getElementById('complete-request-id').value;
        const quantity = parseInt(document.getElementById('complete-quantity').value);
        
        fetch(`/stock-requests/${id}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                transferred_quantity: quantity
            })
        })
        .then(async res => {
            if (res.ok) {
                // Se for redirecionamento ou sucesso 200, recarregar a página
                // Como o backend agora retorna redirect()->back(), o fetch seguirá o redirect e retornará o HTML da página
                document.getElementById('complete-modal').classList.add('hidden');
                showNotification('Transferência concluída com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                // Tenta ler como JSON se der erro, caso contrário texto
                try {
                    const data = await res.json();
                    const errorMsg = data.message || data.errors || 'Erro desconhecido';
                    showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
                } catch (e) {
                    showNotification('Erro ao concluir transferência.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao concluir transferência. Verifique o console.', 'error');
        });
    }
</script>
@endpush
@endsection
