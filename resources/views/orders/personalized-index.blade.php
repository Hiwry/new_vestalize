@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4 animate-fade-in-blur">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Lista de Personalizados</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gerencie e acompanhe pedidos personalizados</p>
    </div>
    <a href="{{ route('personalized.index') }}" 
       style="color: white !important;"
       class="w-full md:w-auto px-6 py-3 bg-[#7c3aed] text-white font-bold rounded-xl transition-all text-center shadow-lg shadow-purple-500/25 flex items-center justify-center gap-2 group">
        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        Novo Personalizado
    </a>
</div>

<!-- Filtros Premium -->
<div class="w-full landing-card mb-8 p-0 overflow-hidden animate-fade-in-up" x-data="{ filtersOpen: window.innerWidth >= 768 }">
    <div class="p-4 md:p-6 border-b border-gray-100 dark:border-white/5 flex justify-between items-center cursor-pointer md:cursor-default" @click="filtersOpen = window.innerWidth < 768 ? !filtersOpen : true">
        <h3 class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            </div>
            Filtros de Busca
        </h3>
        <button class="md:hidden p-2 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
            <svg class="w-5 h-5 transform transition-transform duration-300" :class="filtersOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>
    
    <div x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-6">
        <form method="GET" action="{{ route('personalized.orders.index') }}" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-8">
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Buscar Personalizado</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Nº do personalizado, cliente, telefone ou nome da arte..."
                               style="padding-left: 3.25rem !important;"
                               class="w-full pr-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium">
                        <option value="">Todos os Status</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}" {{ $status == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end border-t border-gray-100 dark:border-white/5 pt-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Tipo de Data</label>
                    <select name="date_type" class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium">
                        <option value="created" {{ ($dateType ?? 'created') == 'created' ? 'selected' : '' }}>Data de Criação</option>
                        <option value="delivery" {{ ($dateType ?? 'created') == 'delivery' ? 'selected' : '' }}>Data de Entrega</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Início</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Fim</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium">
                </div>

                <div class="flex gap-2">
                    <button type="submit" style="color: white !important;" class="flex-1 px-6 py-3 bg-gradient-to-br from-[#7c3aed] to-[#6d28d9] text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-white" style="color: white !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        <span style="color: white !important;">Filtrar</span>
                    </button>
                    <a href="{{ route('personalized.orders.index') }}" class="p-3 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-gray-200 dark:hover:bg-white/10 transition-colors" title="Limpar Filtros">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Personalizados Mobile -->
<div class="space-y-4 md:hidden animate-fade-in-up">
    @forelse($orders as $order)
    <div class="landing-card p-5 relative overflow-hidden group">
        <!-- Barra Lateral de Status -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $order->status->color ?? '#6b7280' }}"></div>
        
        <div class="flex justify-between items-start mb-4">
            <div>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Personalizado #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                <h3 class="font-bold text-gray-900 dark:text-white leading-tight mt-1">{{ $order->client ? $order->client->name : 'Sem cliente' }}</h3>
            </div>
            <div class="text-right">
                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-md uppercase tracking-wider" 
                      style="background-color: {{ ($order->status->color ?? '#6b7280') }}20; color: {{ $order->status->color ?? '#6b7280' }}">
                    {{ $order->status->name ?? 'Pendente' }}
                </span>
                <div class="text-[10px] text-gray-400 mt-1 font-medium">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-100 dark:border-white/5">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Itens</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $order->items->sum('quantity') }} peças</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Total</p>
                <p class="text-sm font-bold text-purple-600 dark:text-purple-400 mt-0.5">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.show', $order->id) }}" style="color: white !important;" class="flex-1 py-2.5 bg-[#7c3aed] text-white text-xs font-bold rounded-xl text-center shadow-lg shadow-purple-500/20 transition-all">
                Ver Detalhes
            </a>
            <button onclick="openCancellationModal({{ $order->id }})" class="p-2.5 bg-red-50 dark:bg-red-500/10 text-red-500 rounded-xl">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </div>
    </div>
    @empty
    <div class="landing-card p-10 text-center">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum personalizado encontrado.</p>
    </div>
    @endforelse
</div>

<!-- Lista de Personalizados Desktop -->
<div class="hidden md:block landing-card p-0 overflow-hidden animate-fade-in-up delay-100">
    <div class="table-sticky-wrapper overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 dark:divide-white/5 sticky-table">
            <thead class="bg-gray-50/50 dark:bg-white/5">
                <tr>
                    <th data-sticky class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Personalizado</th>
                    <th data-sticky class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Cliente</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Nome da Arte</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Data</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Entrega</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Itens</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Total</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($orders as $order)
                <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-all duration-200 {{ $order->is_cancelled ? 'opacity-60 bg-red-50/30 dark:bg-red-900/10' : '' }}">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $order->status->color ?? '#6b7280' }}"></div>
                            <div class="text-sm font-bold {{ $order->is_cancelled ? 'text-red-500 line-through' : 'text-gray-900 dark:text-white' }}">
                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        @if($order->is_cancelled)
                        <div class="mt-1 pl-5">
                            <span class="text-[10px] font-bold text-red-500 uppercase tracking-tighter">Cancelado</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        @if($order->client)
                        <div class="text-sm font-semibold text-gray-900 dark:text-white max-w-[180px] truncate" title="{{ $order->client->name }}">
                            {{ $order->client->name }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $order->client->phone_primary }}</div>
                        @else
                        <div class="text-sm text-gray-400 italic">Sem cliente</div>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        @php
                            $artName = $order->items->first()->art_name ?? null;
                            $hasSublocal = false;
                            $sublocalInfo = [];
                            if ($order->is_pdv) {
                                foreach ($order->items as $item) {
                                    if ($item->sublimations) {
                                        foreach ($item->sublimations as $sub) {
                                            if ($sub->location_id || $sub->location_name) {
                                                $hasSublocal = true;
                                                $sublocalInfo[] = $sub;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if($artName)
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-300 max-w-[150px] truncate" title="{{ $artName }}">
                                {{ $artName }}
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">Sem nome</span>
                        @endif
                        
                        @if($order->is_pdv && $hasSublocal)
                            <div class="mt-1">
                                <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">Sublimação Local</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <span class="px-3 py-1 inline-flex text-[11px] leading-4 font-bold rounded-lg uppercase tracking-wider" 
                                  style="background-color: {{ ($order->status->color ?? '#6b7280') }}15; color: {{ $order->status->color ?? '#6b7280' }}">
                                {{ $order->status->name ?? 'Indefinido' }}
                            </span>
                            @if($order->has_pending_cancellation)
                            <span class="text-[9px] font-bold text-yellow-600 dark:text-yellow-500 uppercase px-1 tracking-tighter animate-pulse inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2h8m-8 20h8M9 2v6l3 3 3-3V2M9 22v-6l3-3 3 3v6"></path>
                                </svg>
                                Aguardando Cancelamento
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m') }} <span class="text-[10px] font-normal opacity-50">{{ \Carbon\Carbon::parse($order->created_at)->format('/y') }}</span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                        @if($order->delivery_date)
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m') }} <span class="text-[10px] font-normal opacity-50">{{ \Carbon\Carbon::parse($order->delivery_date)->format('/y') }}</span>
                        @else
                            <span class="opacity-30">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-center">
                        <span class="bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs px-2 py-1 rounded-md font-bold">
                            {{ $order->items->sum('quantity') }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">
                        R$ {{ number_format($order->total, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            {{-- Botão Ver --}}
                            <a href="{{ route('orders.show', $order->id) }}" 
                               class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-500/10 rounded-lg transition-colors"
                               title="Ver Detalhes">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            
                            {{-- Botão Cancelar --}}
                            @if(!$order->is_cancelled && !$order->has_pending_cancellation)
                            <button onclick="openCancellationModal({{ $order->id }})" 
                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors"
                                    title="Solicitar Cancelamento">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                            @endif
                            
                            {{-- Botão Editar --}}
                            @if(!$order->is_cancelled)
                                @php
                                    $approvedEditRequest = $order->editRequests->where('status', 'approved')->first();
                                    $pendingEditRequest = $order->editRequests->where('status', 'pending')->first();
                                @endphp
                                
                                    @if(Auth::user()->isAdmin() || $approvedEditRequest)
                                        <a href="{{ route('orders.edit.start', $order->id) }}" 
                                           class="p-2 {{ $approvedEditRequest ? 'bg-gradient-to-br from-green-500 to-green-600 shadow-green-200/50 hover:shadow-green-300/50' : 'bg-gradient-to-br from-[#7c3aed] to-[#6d28d9] shadow-purple-200/50 hover:shadow-purple-300/50' }} text-white rounded-xl transition-all shadow-md hover:-translate-y-0.5 hover:shadow-lg flex items-center justify-center"
                                           title="Editar Personalizado">
                                            <svg class="w-5 h-5 text-white" style="color: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                    @elseif($pendingEditRequest)
                                        <div class="p-2 text-yellow-500 opacity-50 cursor-wait" title="Edição Pendente">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                    @else
                                        <button onclick="openEditRequestModal({{ $order->id }})" 
                                                class="p-2 text-orange-500 hover:bg-orange-50 dark:hover:bg-white/5 rounded-lg transition-colors"
                                                title="Solicitar Edição">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                        </button>
                                    @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-600 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4a2 2 0 012-2m16 0h-16" /></svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium tracking-tight">Céus! Nenhum personalizado foi encontrado.</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Tente ajustar seus filtros ou comece um novo personalizado.</p>
                        </div>
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
<div id="cancellationModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 w-full max-w-md animate-fade-in-up">
        <div class="bg-white dark:bg-[#0f1218] rounded-2xl shadow-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <!-- Header do Modal -->
            <div class="p-6 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-red-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Solicitar Cancelamento</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Personalizado <span id="modalOrderId" class="text-purple-600 dark:text-purple-400 font-bold"></span></p>
                    </div>
                </div>
                <button onclick="closeCancellationModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-6">
                <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/20 rounded-xl p-4 mb-6 flex gap-3">
                    <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-medium leading-relaxed">
                        Sua solicitação será analisada por um administrador. Descreva detalhadamente o motivo abaixo.
                    </p>
                </div>

                <label for="cancellationReason" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                    Motivo do Cancelamento <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="cancellationReason" 
                    rows="4" 
                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium placeholder-gray-400 dark:placeholder-gray-600"
                    placeholder="Ex: O cliente desistiu da compra devido ao prazo..."
                    maxlength="1000"></textarea>
                <div class="flex justify-between mt-2">
                    <p id="reasonError" class="text-[10px] text-red-500 font-bold uppercase tracking-tighter hidden">Motivo é obrigatório</p>
                    <p class="text-[10px] text-gray-400 font-medium ml-auto">máx. 1000 caracteres</p>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="p-6 bg-gray-50/50 dark:bg-white/[0.02] flex items-center justify-end gap-3">
                <button onclick="closeCancellationModal()" class="px-5 py-2.5 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Voltar
                </button>
                <button onclick="submitCancellation()" class="px-6 py-2.5 bg-red-500 text-white text-xs font-bold rounded-xl hover:bg-red-600 shadow-lg shadow-red-500/20 transition-all active:scale-95">
                    Confirmar Solicitação
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Solicitação de Edição -->
<div id="editRequestModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 w-full max-w-md animate-fade-in-up">
        <div class="bg-white dark:bg-[#0f1218] rounded-2xl shadow-2xl border border-gray-100 dark:border-white/5 overflow-hidden">
            <!-- Header do Modal -->
            <div class="p-6 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Solicitar Edição</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Personalizado <span id="modalEditOrderId" class="text-purple-600 dark:text-purple-400 font-bold"></span></p>
                    </div>
                </div>
                <button onclick="closeEditRequestModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-6">
                <div class="bg-blue-50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/20 rounded-xl p-4 mb-6 flex gap-3">
                    <svg class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-xs text-blue-700 dark:text-blue-400 font-medium leading-relaxed">
                        Após a confirmação, o pedido será liberado para alterações assim que o administrador aprovar.
                    </p>
                </div>

                <label for="editRequestReason" class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                    Motivo da Edição <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="editRequestReason" 
                    rows="4" 
                    class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium placeholder-gray-400 dark:placeholder-gray-600"
                    placeholder="Descreva o que precisa ser alterado..."
                    maxlength="1000"></textarea>
                <div class="flex justify-between mt-2">
                    <p id="editReasonError" class="text-[10px] text-red-500 font-bold uppercase tracking-tighter hidden">O motivo é obrigatório</p>
                    <p class="text-[10px] text-gray-400 font-medium ml-auto">máx. 1000 caracteres</p>
                </div>
            </div>

            <!-- Rodapé do Modal -->
            <div class="p-6 bg-gray-50/50 dark:bg-white/[0.02] flex items-center justify-end gap-3">
                <button onclick="closeEditRequestModal()" class="px-5 py-2.5 text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Cancelar
                </button>
                <button onclick="submitEditRequest()" class="px-6 py-2.5 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 shadow-lg shadow-orange-500/20 transition-all active:scale-95">
                    Solicitar Acesso
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sticky-table.js') }}"></script>
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
