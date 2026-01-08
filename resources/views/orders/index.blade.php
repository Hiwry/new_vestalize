@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 self-start md:self-auto">Lista de Pedidos</h1>
    <a href="{{ route('orders.wizard.start') }}" 
       class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-center shadow-sm">
        + Novo Pedido
    </a>
</div>

<!-- Filtros -->
<div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 mb-6 md:p-6" x-data="{ filtersOpen: window.innerWidth >= 768 }">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:hidden" @click="filtersOpen = !filtersOpen">
        <h3 class="font-medium text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            Filtros
        </h3>
        <button class="text-gray-500 dark:text-gray-400 focus:outline-none">
            <svg class="w-5 h-5 transform transition-transform duration-200" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>
    
    <div x-show="filtersOpen" x-transition x-cloak class="p-4 md:p-0">
        <form method="GET" action="{{ route('orders.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Nº do pedido, nome do cliente, telefone ou nome da arte..."
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        <option value="">Todos</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" {{ $status == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filtro de Data -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Data</label>
                        <select name="date_type" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            <option value="created" {{ ($dateType ?? 'created') == 'created' ? 'selected' : '' }}>Data de Criação</option>
                            <option value="delivery" {{ ($dateType ?? 'created') == 'delivery' ? 'selected' : '' }}>Data de Entrega</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicial</label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ $startDate }}"
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Final</label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ $endDate }}"
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    </div>

                    <div class="flex items-end">
                        <div class="w-full space-y-2">
                            <button type="submit" class="w-full px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Filtrar
                            </button>
                            <a href="{{ route('orders.index') }}" class="block w-full px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-center transition">
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Pedidos -->
<div class="space-y-4 md:hidden">
    @forelse($orders as $order)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 relative overflow-hidden">
        <!-- Status Indicator Strip -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $order->status->color ?? '#6b7280' }}"></div>
        
        <div class="pl-2">
            <!-- Header: ID + Status + Date -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->client ? $order->client->name : 'Sem cliente' }}</h3>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md whitespace-nowrap" 
                          style="background-color: {{ ($order->status->color ?? '#6b7280') }}20; color: {{ $order->status->color ?? '#6b7280' }}">
                        {{ $order->status->name ?? 'Indefinido' }}
                    </span>
                    <div class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="mb-3 space-y-1">
                @php
                    $artName = $order->items->first()->art_name ?? null;
                @endphp
                @if($artName)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="truncate">{{ $artName }}</span>
                </div>
                @endif
                
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    <span>{{ $order->items->sum('quantity') }} peças</span>
                </div>
                
                <div class="flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    R$ {{ number_format($order->total, 2, ',', '.') }}
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end items-center gap-3 pt-3 border-t border-gray-100 dark:border-gray-700 mt-3">
                 <a href="{{ route('orders.show', $order->id) }}" class="flex-1 text-center py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-lg">
                    Ver Detalhes
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl">
        <p class="text-gray-500 dark:text-gray-400">Nenhum pedido encontrado.</p>
    </div>
    @endforelse
</div>

<!-- Tabela Desktop -->
<div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome da Arte</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entrega</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Itens</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $order->is_cancelled ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium {{ $order->is_cancelled ? 'text-red-600 dark:text-red-400 line-through' : 'text-gray-900 dark:text-gray-100' }}">
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        @if($order->is_cancelled)
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                CANCELADO
                            </span>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($order->client)
                        <div class="text-sm text-gray-900 dark:text-gray-100 font-medium max-w-[200px] truncate {{ $order->is_cancelled ? 'opacity-50' : '' }}" title="{{ $order->client->name }}">
                            {{ $order->client->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->client->phone_primary }}</div>
                        @else
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic {{ $order->is_cancelled ? 'opacity-50' : '' }}">
                            Sem cliente cadastrado
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @php
                            $artName = $order->items->first()->art_name ?? null;
                            
                            // Verificar se tem sublimação local (para vendas PDV)
                            $hasSublocal = false;
                            $sublocalInfo = [];
                            if ($order->is_pdv) {
                                foreach ($order->items as $item) {
                                    if ($item->sublimations) {
                                        foreach ($item->sublimations as $sub) {
                                            if ($sub->location_id || $sub->location_name) {
                                                $hasSublocal = true;
                                                $locationName = $sub->location ? $sub->location->name : ($sub->location_name ?? 'Local não informado');
                                                $sizeName = $sub->size ? $sub->size->name : ($sub->size_name ?? '');
                                                $sublocalInfo[] = [
                                                    'location' => $locationName,
                                                    'size' => $sizeName,
                                                    'quantity' => $sub->quantity,
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if($artName)
                            <div class="text-sm text-gray-900 dark:text-gray-100 {{ $order->is_cancelled ? 'opacity-50' : '' }}">
                                <span class="block max-w-[200px] truncate" title="{{ $artName }}">{{ $artName }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sem nome</span>
                        @endif
                        
                        <!-- Sublimação Local (apenas para vendas PDV) -->
                        @if($order->is_pdv && $hasSublocal && !empty($sublocalInfo))
                        <div class="mt-2 pt-2 border-t border-indigo-200 dark:border-indigo-700">
                            <div class="text-xs font-semibold text-indigo-700 dark:text-indigo-400 mb-1">
                                Sublimação Local:
                            </div>
                            @foreach($sublocalInfo as $sublocal)
                            <div class="text-xs text-indigo-600 dark:text-indigo-400">
                                • {{ $sublocal['location'] }}
                                @if($sublocal['size'])
                                - {{ $sublocal['size'] }}
                                @endif
                                ({{ $sublocal['quantity'] }}x)
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap" 
                                  style="background-color: {{ ($order->status->color ?? '#6b7280') }}20; color: {{ $order->status->color ?? '#6b7280' }}">
                                {{ $order->status->name ?? 'Indefinido' }}
                            </span>
                            @if($order->has_pending_cancellation)
                            <div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⏳ Cancelamento Pendente
                                </span>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if($order->delivery_date)
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                        {{ $order->items->sum('quantity') }} peças
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-right">
                        R$ {{ number_format($order->total, 2, ',', '.') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('orders.show', $order->id) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center transition"
                               title="Ver Detalhes">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="text-xs">Ver</span>
                            </a>
                            
                            @if(!$order->is_cancelled && !$order->has_pending_cancellation)
                            <button onclick="openCancellationModal({{ $order->id }})" 
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition"
                                    title="Solicitar Cancelamento">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                            
                            @if(!$order->is_cancelled)
                                @php
                                    $approvedEditRequest = $order->editRequests->where('status', 'approved')->first();
                                    $pendingEditRequest = $order->editRequests->where('status', 'pending')->first();
                                @endphp
                                
                                @if(Auth::user()->isAdmin())
                                    {{-- Admin pode editar direto --}}
                                    <a href="{{ route('orders.edit.start', $order->id) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition"
                                       title="Editar Pedido">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                @elseif($approvedEditRequest)
                                    {{-- Vendedor com edição aprovada pode editar --}}
                                    <a href="{{ route('orders.edit.start', $order->id) }}" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition"
                                        title="Editar Pedido (Aprovado pelo Admin)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                @elseif($pendingEditRequest)
                                    {{-- Solicitação pendente --}}
                                    <span class="text-yellow-600 dark:text-yellow-400" title="Solicitação de edição pendente">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </span>
                                @else
                                    {{-- Vendedor sem aprovação - solicitar edição --}}
                                    <button onclick="openEditRequestModal({{ $order->id }})" 
                                            class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300 transition"
                                            title="Solicitar Edição">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        </svg>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Nenhum pedido encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
@if($orders->hasPages())
<div class="mt-6">
    {{ $orders->onEachSide(1)->links() }}
</div>
@endif

<!-- Modal de Cancelamento -->
<div id="cancellationModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-700 w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar Cancelamento</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pedido <span id="modalOrderId" class="font-semibold"></span></p>
                    </div>
                </div>
                <button onclick="closeCancellationModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mt-4">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                Esta solicitação será enviada para aprovação do administrador.
                            </p>
                        </div>
                    </div>
                </div>

                <label for="cancellationReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Motivo do Cancelamento <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <textarea 
                    id="cancellationReason" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Descreva o motivo pelo qual este pedido deve ser cancelado..."
                    maxlength="1000"></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de 1000 caracteres</p>
                <p id="reasonError" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden">O motivo é obrigatório</p>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    onclick="closeCancellationModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                    Cancelar
                </button>
                <button 
                    onclick="submitCancellation()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    Solicitar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Solicitação de Edição -->
<div id="editRequestModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-700 w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900/30">
                        <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar Edição</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pedido <span id="modalEditOrderId" class="font-semibold"></span></p>
                    </div>
                </div>
                <button onclick="closeEditRequestModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="mt-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Esta solicitação será enviada para aprovação do administrador. Após aprovação, você poderá editar o pedido.
                            </p>
                        </div>
                    </div>
                </div>

                <label for="editRequestReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Motivo da Edição <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <textarea 
                    id="editRequestReason" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Descreva o motivo pelo qual você precisa editar este pedido..."
                    maxlength="1000"></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de 1000 caracteres</p>
                <p id="editReasonError" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden">O motivo é obrigatório</p>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button 
                    onclick="closeEditRequestModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                    Cancelar
                </button>
                <button 
                    onclick="submitEditRequest()" 
                    class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                    Solicitar Edição
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentOrderId = null;
    let currentEditOrderId = null;

    // Expor funções globalmente para garantir que estejam disponíveis
    window.openCancellationModal = function(orderId) {
        currentOrderId = orderId;
        const modalOrderIdEl = document.getElementById('modalOrderId');
        const cancellationReasonEl = document.getElementById('cancellationReason');
        const reasonErrorEl = document.getElementById('reasonError');
        const cancellationModalEl = document.getElementById('cancellationModal');
        
        if (modalOrderIdEl) {
            modalOrderIdEl.textContent = '#' + String(orderId).padStart(6, '0');
        }
        if (cancellationReasonEl) {
            cancellationReasonEl.value = '';
        }
        if (reasonErrorEl) {
            reasonErrorEl.classList.add('hidden');
        }
        if (cancellationModalEl) {
            cancellationModalEl.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeCancellationModal = function() {
        const cancellationModalEl = document.getElementById('cancellationModal');
        if (cancellationModalEl) {
            cancellationModalEl.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        currentOrderId = null;
    };
    
    // Manter compatibilidade com chamadas diretas
    function openCancellationModal(orderId) {
        window.openCancellationModal(orderId);
    }

    function closeCancellationModal() {
        window.closeCancellationModal();
    }

    window.submitCancellation = function() {
        const reason = document.getElementById('cancellationReason')?.value.trim();
        const errorElement = document.getElementById('reasonError');

        if (!reason) {
            if (errorElement) {
                errorElement.classList.remove('hidden');
            }
            const reasonInput = document.getElementById('cancellationReason');
            if (reasonInput) {
                reasonInput.focus();
            }
            return;
        }

        if (errorElement) {
            errorElement.classList.add('hidden');
        }

        // Desabilitar botão para evitar duplo clique
        const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
        }

        fetch(`/pedidos/${currentOrderId}/cancelar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
            
            if (data.success) {
                window.closeCancellationModal();
                
                // Mostrar mensagem de sucesso
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Solicitação de cancelamento enviada com sucesso!</span>
                    </div>
                `;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert(data.message || 'Erro ao enviar solicitação de cancelamento');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Solicitar Cancelamento';
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao enviar solicitação de cancelamento');
            const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Solicitar Cancelamento';
            }
        });
    };
    
    // Manter compatibilidade
    function submitCancellation() {
        window.submitCancellation();
    }

    // Funções do Modal de Solicitação de Edição
    window.openEditRequestModal = function(orderId) {
        currentEditOrderId = orderId;
        const modalEditOrderIdEl = document.getElementById('modalEditOrderId');
        const editRequestReasonEl = document.getElementById('editRequestReason');
        const editReasonErrorEl = document.getElementById('editReasonError');
        const editRequestModalEl = document.getElementById('editRequestModal');
        
        if (modalEditOrderIdEl) {
            modalEditOrderIdEl.textContent = '#' + String(orderId).padStart(6, '0');
        }
        if (editRequestReasonEl) {
            editRequestReasonEl.value = '';
        }
        if (editReasonErrorEl) {
            editReasonErrorEl.classList.add('hidden');
        }
        if (editRequestModalEl) {
            editRequestModalEl.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeEditRequestModal = function() {
        const editRequestModalEl = document.getElementById('editRequestModal');
        if (editRequestModalEl) {
            editRequestModalEl.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        currentEditOrderId = null;
    };
    
    // Manter compatibilidade com chamadas diretas
    function openEditRequestModal(orderId) {
        window.openEditRequestModal(orderId);
    }

    function closeEditRequestModal() {
        window.closeEditRequestModal();
    }

    window.submitEditRequest = function() {
        const reason = document.getElementById('editRequestReason')?.value.trim();
        const errorElement = document.getElementById('editReasonError');

        if (!reason) {
            if (errorElement) {
                errorElement.classList.remove('hidden');
            }
            const reasonInput = document.getElementById('editRequestReason');
            if (reasonInput) {
                reasonInput.focus();
            }
            return;
        }

        if (errorElement) {
            errorElement.classList.add('hidden');
        }

        // Desabilitar botão para evitar duplo clique
        const submitBtn = document.querySelector('#editRequestModal button[onclick*="submitEditRequest"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
        }

        fetch(`/pedidos/${currentEditOrderId}/edit-request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                reason: reason
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            const submitBtn = document.querySelector('#editRequestModal button[onclick*="submitEditRequest"]');
            
            if (data.success) {
                window.closeEditRequestModal();
                
                // Mostrar mensagem de sucesso
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Solicitação de edição enviada com sucesso!</span>
                    </div>
                `;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert(data.message || 'Erro ao enviar solicitação de edição');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Solicitar Edição';
                }
            }
        })
        .catch(error => {
            console.error('Erro detalhado:', error);
            let errorMessage = 'Erro ao enviar solicitação de edição';
            
            if (error.message) {
                errorMessage = error.message;
            } else if (error.errors) {
                errorMessage = Object.values(error.errors).flat().join(', ');
            }
            
            alert(errorMessage);
            const submitBtn = document.querySelector('#editRequestModal button[onclick*="submitEditRequest"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Solicitar Edição';
            }
        });
    };
    
    // Manter compatibilidade
    function submitEditRequest() {
        window.submitEditRequest();
    }

    // Inicializar event listeners quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Fechar modais ao clicar fora deles
        const cancellationModalEl = document.getElementById('cancellationModal');
        if (cancellationModalEl) {
            cancellationModalEl.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeCancellationModal();
                }
            });
        }

        const editRequestModalEl = document.getElementById('editRequestModal');
        if (editRequestModalEl) {
            editRequestModalEl.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeEditRequestModal();
                }
            });
        }

        // Fechar modais com tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const cancellationModal = document.getElementById('cancellationModal');
                const editRequestModal = document.getElementById('editRequestModal');
                
                if (cancellationModal && !cancellationModal.classList.contains('hidden')) {
                    window.closeCancellationModal();
                }
                if (editRequestModal && !editRequestModal.classList.contains('hidden')) {
                    window.closeEditRequestModal();
                }
            }
        });
    });
</script>
@endpush
