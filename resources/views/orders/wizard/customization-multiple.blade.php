@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    @php
        $customizationAction = (Str::contains(request()->url(), '/pedidos/editar/') || request()->routeIs('orders.edit.*') || session()->has('edit_order_id'))
            ? route('orders.edit.customization')
            : route('orders.wizard.customization');
    @endphp
    
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#7c3aed] text-white stay-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">3</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Personalização</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 3 de 5</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">60%</div>
                </div>
            </div>
            <div class="w-full bg-white dark:bg-slate-800/80 rounded-full h-2.5 shadow-inner border border-gray-200 dark:border-slate-700">
                <div class="bg-gradient-to-r from-[#7c3aed] to-[#7c3aed] dark:from-[#7c3aed] dark:to-[#7c3aed] h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-[#7c3aed]/30 dark:shadow-[#7c3aed]/30" style="width: 60%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-white dark:bg-gradient-to-r dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                             <div class="w-8 h-8 bg-[#7c3aed] rounded-lg flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">
                                <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            Personalizações
                        </h1>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Configure as personalizações de cada item</p>
            </div>

            <!-- Content - Formulário de Arte -->
            <form id="orderArtForm" action="{{ $customizationAction }}" method="POST" enctype="multipart/form-data" class="px-6 pt-6 pb-4">
                @csrf
                <input type="hidden" name="action" value="save_order_art">
                
                <!-- Card do Formulário -->
                <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                        
                        <!-- Nome da Arte -->
                        <div class="lg:col-span-4">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-[#7c3aed] dark:text-[#a78bfa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </div>
                                Nome da Arte
                            </label>
                            <input id="order_art_name" type="text" name="order_art_name" value="{{ $order->items->first()?->art_name }}" 
                                   placeholder="Ex: Logo Cliente, Frente PV"
                                   class="w-full h-11 px-4 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Arquivos -->
                        <div class="lg:col-span-4">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                <div class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/30 rounded flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                Arquivos
                                <span class="text-xs font-normal text-gray-400 dark:text-slate-500">(Corel, PDF)</span>
                            </label>
                            <input id="order_art_files" type="file" name="order_art_files[]" multiple accept=".cdr,.pdf,.ai,.eps"
                                   class="w-full h-11 flex items-center text-sm text-gray-600 dark:text-slate-300 file:mr-3 file:h-full file:py-0 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-900/30 dark:file:text-emerald-400 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-900/50 cursor-pointer border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 focus:ring-2 focus:ring-[#7c3aed]">
                        </div>
                        
                        <!-- Aplicar no Item -->
                        <div class="lg:col-span-3">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                <div class="w-6 h-6 bg-amber-100 dark:bg-amber-900/30 rounded flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                Aplicar no Item
                            </label>
                            <select id="order_art_item" name="item_id" 
                                    class="w-full h-11 px-4 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] focus:border-transparent transition-all">
                                @foreach($order->items as $item)
                                    <option value="{{ $item->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        Item {{ $item->item_number }} • {{ $item->quantity }} peças • {{ $item->fabric }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Botão -->
                        <div class="lg:col-span-1">
                            <button type="submit" class="w-full h-11 px-4 bg-[#7c3aed] text-white stay-white rounded-lg shadow-lg shadow-[#7c3aed]/25 hover:bg-[#6d28d9] hover:shadow-[#7c3aed]/40 transition-all font-semibold text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Preview (hidden by default) -->
                    <div id="order_art_preview" class="mt-4 text-sm text-gray-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-3 hidden"></div>
                </div>
            </form>
            <div class="p-6">
                
                @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Resumo -->
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 rounded-xl p-4 border border-blue-100 dark:border-blue-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <i class="fa-solid fa-boxes-stacked text-white stay-white"></i>
                                </div>
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Total de Itens</p>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->items->count() }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-violet-50 to-violet-100/50 dark:from-violet-900/20 dark:to-violet-800/10 rounded-xl p-4 border border-violet-100 dark:border-violet-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center shadow-lg shadow-violet-500/30">
                                    <i class="fa-solid fa-tag text-white stay-white"></i>
                                </div>
                                <p class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wide">Total de Peças</p>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-pecas" data-total-pecas="{{ $order->items->sum('quantity') }}">{{ $order->items->sum('quantity') }}</p>
                        </div>
        @php
            $totalApplications = 0;
            $totalApplicationsCount = 0;
            foreach($order->items as $item) {
                $persIds = session('item_personalizations.' . $item->id, [[]])[0] ?? [];
                $allowedTypes = !empty($persIds)
                    ? \App\Models\ProductOption::whereIn('id', $persIds)->pluck('name')->map(fn($n)=>strtoupper($n))->toArray()
                    : [];
                $itemApplicationsQuery = \App\Models\OrderSublimation::where('order_item_id', $item->id);
                if (!empty($allowedTypes)) {
                    $itemApplicationsQuery = $itemApplicationsQuery->whereIn('application_type', $allowedTypes);
                } else {
                    $itemApplicationsQuery = $itemApplicationsQuery->whereRaw('1=0');
                }
                $totalApplications += $itemApplicationsQuery->sum('final_price');
                $totalApplicationsCount += $itemApplicationsQuery->count();
            }
            $avgPerPiece = $order->items->sum('quantity') > 0 ? $totalApplications / $order->items->sum('quantity') : 0;
        @endphp
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-emerald-500/20 dark:bg-emerald-400/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-circle-dollar-to-slot text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Total Aplicações</p>
                            </div>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">R$ {{ number_format($totalApplications, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">{{ $totalApplicationsCount }} {{ $totalApplicationsCount == 1 ? 'aplicação' : 'aplicações' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/10 rounded-xl p-4 border border-amber-100 dark:border-amber-800/50">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-amber-500/20 dark:bg-amber-400/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-calculator text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Custo por Peça</p>
                            </div>
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">R$ {{ number_format($avgPerPiece, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Média das aplicações</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Itens -->
                <div class="space-y-6">
                    @foreach($itemPersonalizations as $itemData)
                        @php
                            $item = $itemData['item'];
                            $persIds = $itemData['personalization_ids'];
                            $persNames = $itemData['personalization_names'];
                        @endphp
                        
                        <div class="border border-gray-200 dark:border-slate-700 rounded-xl overflow-hidden bg-white dark:bg-slate-800">
                            
                            <!-- Item Header -->
                            <div class="bg-gray-50 dark:bg-slate-800/50 px-4 sm:px-5 py-4 border-b border-gray-200 dark:border-slate-700">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Item {{ $item->item_number }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-0.5">{{ $item->quantity }} peças • {{ $item->fabric }} • {{ $item->color }}</p>
                                    </div>
                                    <span class="text-xs px-2.5 py-1 bg-purple-100 dark:bg-purple-900/30 text-[#7c3aed] dark:text-[#a78bfa] rounded-md font-medium">
                                        {{ count($persIds) }} {{ count($persIds) == 1 ? 'personalização' : 'personalizações' }}
                                    </span>
                                </div>
                                @php
                                    $allowedTypes = collect($persNames)->map(fn($name) => strtoupper($name))->toArray();
                                    $itemPersonalizationsQuery = \App\Models\OrderSublimation::where('order_item_id', $item->id);
                                    if (!empty($allowedTypes)) {
                                        $itemPersonalizationsQuery = $itemPersonalizationsQuery->whereIn('application_type', $allowedTypes);
                                    } else {
                                        $itemPersonalizationsQuery = $itemPersonalizationsQuery->whereRaw('1=0'); // nenhum tipo permitido
                                    }
                                    $itemTotalApplications = $itemPersonalizationsQuery->sum('final_price');
                                    $itemApplicationsCount = $itemPersonalizationsQuery->count();
                                    $itemAvgPerPiece = $item->quantity > 0 ? $itemTotalApplications / $item->quantity : 0;
                                @endphp
                                @if($itemApplicationsCount > 0)
                                    <div class="grid grid-cols-3 gap-3 text-xs bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-gray-200 dark:border-slate-700">
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Aplicações:</span>
                                            <span class="font-semibold text-gray-900 dark:text-white ml-1">{{ $itemApplicationsCount }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Total:</span>
                                            <span class="font-semibold text-[#7c3aed] dark:text-[#a78bfa] ml-1">R$ {{ number_format($itemTotalApplications, 2, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Por peça:</span>
                                            <span class="font-semibold text-[#7c3aed] dark:text-[#a78bfa] ml-1">R$ {{ number_format($itemAvgPerPiece, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($item->art_name || ($item->files && $item->files->count()))
                                <div class="mt-3 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                                    @if($item->art_name)
                                        <p class="text-sm text-gray-800 dark:text-slate-200 mb-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">Nome da Arte:</span>
                                            {{ $item->art_name }}
                                        </p>
                                    @endif
                                    @if($item->files && $item->files->count())
                                        <div>
                                            <p class="text-xs font-semibold text-gray-600 dark:text-slate-400 mb-2">Arquivos enviados</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($item->files as $file)
                                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 text-xs bg-purple-50 dark:bg-purple-900/30 text-[#7c3aed] dark:text-[#a78bfa] rounded-md border border-purple-100 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-800/70 transition">
                                                        {{ $file->file_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Personalizações do Item -->
                            <div class="p-5 space-y-4">
                                @php
                                    // Buscar TODAS as personalizações do item (independente do tipo)
                                    $allItemPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)
                                        ->with('files')
                                        ->get();
                                    
                                    // Agrupar por tipo
                                    $groupedPersonalizations = $allItemPersonalizations->groupBy(function($pers) {
                                        return strtoupper($pers->application_type);
                                    });
                                @endphp
                                
                                @foreach($persIds as $persId)
                                    @php
                                        $persName = $persNames[$persId] ?? 'Personalização';
                                        $persNameUpper = strtoupper($persName);
                                        
                                        // Buscar personalizações deste tipo específico
                                        $existingPersonalizations = $groupedPersonalizations->get($persNameUpper, collect());
                                    @endphp
                                    
                                    <div class="bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-4">
                                        
                                        <!-- Tipo de Personalização -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-[#7c3aed] dark:text-[#a78bfa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $persName }}</h4>
                                            </div>
                                            <button 
                                                type="button"
                                                onclick="openPersonalizationModal({{ $item->id }}, '{{ $persName }}', {{ $persId }})"
                                                class="text-sm px-3 py-1.5 bg-[#7c3aed] text-white stay-white rounded-lg hover:bg-[#7c3aed] transition-colors font-medium">
                                                + Adicionar
                                            </button>
                                        </div>
                                        
                                        <!-- Lista de Personalizações Adicionadas -->
                                        <div id="personalizations-list-{{ $item->id }}-{{ $persId }}" class="space-y-2 personalizations-list">
                                            @if($existingPersonalizations->count() > 0)
                                                @foreach($existingPersonalizations as $pers)
                                                    <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-4 text-sm">
                                                                    @if($pers->location_name)
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Local:</strong> {{ $pers->location_name }}</span>
                                                                    @endif
                                                                    @if($pers->size_name)
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Tamanho:</strong> {{ $pers->size_name }}</span>
                                                                    @endif
                                                                    <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Qtd:</strong> {{ $pers->quantity }}</span>
                                                                    @if($pers->color_count)
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Cores:</strong> {{ $pers->color_count }}</span>
                                                                    @endif
                                                                    @if($pers->final_price > 0)
                                                                        <span class="text-[#7c3aed] dark:text-[#a78bfa] font-semibold">R$ {{ number_format($pers->final_price, 2, ',', '.') }}</span>
                                                                    @else
                                                                        <span class="text-red-600 dark:text-red-400 font-semibold">R$ 0,00</span>
                                                                    @endif
                                                                </div>
                                                        </div>
                                                        <div class="flex space-x-2 ml-4">
                                                            <button 
                                                                type="button"
                                                                onclick="editPersonalization({{ $pers->id }})"
                                                                class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300"
                                                                title="Editar personalização">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                            <button 
                                                                type="button"
                                                                onclick="removePersonalization({{ $pers->id }})"
                                                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                                                title="Remover personalização">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                        
                                                        @if($pers->files && $pers->files->count() > 0)
                                                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-slate-700">
                                                                <div class="text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">📁 Arquivos da Arte:</div>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach($pers->files as $file)
                                                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                                                           download="{{ $file->file_name }}"
                                                                           class="inline-flex items-center px-2 py-1 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded text-xs hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-gray-700 dark:text-slate-300"
                                                                           title="{{ $file->file_name }} ({{ $file->formatted_size }})">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                                                            </svg>
                                                                            <span class="truncate max-w-xs">{{ $file->file_name }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-500 dark:text-slate-400 text-center py-4">Nenhuma personalização adicionada</p>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                                
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Navegação -->
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" 
                       class="px-4 py-2 text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white font-medium">
                        ← Voltar
                    </a>
                    <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment') }}" 
                       class="px-6 py-2.5 text-sm bg-[#7c3aed] text-white stay-white rounded-lg hover:opacity-90 font-semibold transition-all">
                        Continuar →
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de Adicionar Personalização -->
    <div id="personalizationModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 z-50 flex items-center justify-center p-4 backdrop-blur-md">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700/50 animate-slideUp">
            
            <!-- Modal Header com gradiente e ícone -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-700/50 flex items-center justify-between sticky top-0 bg-white dark:bg-gradient-to-r dark:from-purple-600/20 dark:via-slate-900 dark:to-slate-900 z-10 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-purple-500/20 rounded-xl flex items-center justify-center ring-1 ring-gray-200 dark:ring-purple-500/30">
                        <svg class="w-6 h-6 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Adicionar Aplicação</h3>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5" id="modalSubtitle">Configure os detalhes da personalização</p>
                    </div>
                </div>
                <button type="button" onclick="closePersonalizationModal()" class="p-2 text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="personalizationForm" action="{{ $customizationAction }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="modal_item_id" name="item_id">
                <input type="hidden" id="modal_personalization_type" name="personalization_type">
                <input type="hidden" id="modal_personalization_id" name="personalization_id">
                <input type="hidden" id="editing_personalization_id" name="editing_personalization_id">

                <!-- Vincular a outros itens -->
                @if($order->items->count() > 1)
                <div id="linkItemsSection" class="bg-gradient-to-r from-blue-600/20 to-blue-600/10 border border-blue-500/30 rounded-xl p-4 ring-1 ring-blue-500/20">
                    <label class="flex items-center gap-2 text-sm font-semibold text-blue-300 mb-3">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Aplicar em outros itens também
                    </label>
                    <p class="text-xs text-slate-400 mb-3">Marque os itens que receberão a mesma personalização (mesma arte, local e tamanho)</p>
                    
                    <div class="space-y-2" id="linkItemsCheckboxes">
                        @foreach($order->items as $item)
                        <label class="flex items-center p-2 bg-slate-800/50 border border-slate-700 rounded-lg hover:border-blue-500/50 cursor-pointer transition-colors link-item-label" data-item-id="{{ $item->id }}">
                            <input type="checkbox" name="linked_item_ids[]" value="{{ $item->id }}" 
                                   class="link-item-checkbox w-4 h-4 text-blue-500 border-slate-600 rounded focus:ring-blue-500 bg-slate-700">
                            <div class="ml-3 flex-1">
                                <span class="text-sm font-medium text-white">Item {{ $item->item_number }}</span>
                                <span class="text-xs text-slate-400 ml-2">{{ $item->model }} • {{ $item->quantity }} pç</span>
                            </div>
                            <span class="text-xs text-slate-500">{{ $item->print_type }}</span>
                        </label>
                        @endforeach
                    </div>
                    
                    <div id="linkedItemsSummary" class="mt-3 p-2 bg-green-900/30 border border-green-700/50 rounded-lg hidden">
                        <div class="flex items-center text-sm text-green-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Esta personalização será aplicada em <strong id="linkedItemsCount">0</strong> itens</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Grid de Localização e Tamanho -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Localização (oculto para SUB. TOTAL) -->
                    <div id="locationField">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-200 mb-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Localização
                        </label>
                        <select id="location" name="location" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all cursor-pointer" data-required-for="!SUB. TOTAL">
                            <option value="">Selecione...</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tamanho (oculto para SUB. TOTAL) -->
                    <div id="sizeField">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-200 mb-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                            Tamanho
                        </label>
                        <select id="size" name="size" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all cursor-pointer" data-required-for="!SUB. TOTAL">
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                </div>

                <!-- Campos ocultos para SUB. TOTAL (para evitar validação) -->
                <input type="hidden" id="location_hidden" name="location" value="" disabled>
                <input type="hidden" id="size_hidden" name="size" value="" disabled>
                <input type="hidden" id="quantity_hidden" name="quantity" value="1" disabled>



                <!-- Adicionais (Opções Especiais) -->
                <div id="addonsField" class="hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/10 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800/50">
                        <label class="flex items-center gap-2 text-sm font-bold text-emerald-800 dark:text-emerald-300 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionais (Opções Especiais)
                        </label>
                        
                        <!-- Lista de adicionais adicionados -->
                        <div id="addonsList" class="space-y-2 mb-3">
                            <!-- Adicionais serão adicionados aqui dinamicamente -->
                        </div>
                        
                        <!-- Botão para adicionar adicionais -->
                        <div>
                            <button type="button" id="addAddonBtn" onclick="openAddonModal()"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border-2 border-dashed border-emerald-300 dark:border-emerald-700 text-sm font-semibold rounded-xl text-emerald-700 dark:text-emerald-300 bg-white/50 dark:bg-slate-800/30 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-400 dark:hover:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar Opção Especial
                            </button>
                        </div>
                        
                        <!-- Select oculto para adicionais (para formulário) -->
                        <select id="addons" name="addons[]" multiple class="hidden">
                            <!-- Será preenchido dinamicamente -->
                        </select>
                        
                        <div id="addons-prices" class="mt-3 space-y-1">
                            <!-- Preços dos adicionais selecionados serão exibidos aqui -->
                        </div>
                    </div>
                </div>

                <!-- Quantidade (oculto para SUB. TOTAL) -->
                <div id="quantityField">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-slate-200 mb-2">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        Quantidade
                    </label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" readonly
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-0 cursor-not-allowed transition-all" data-required-for="!SUB. TOTAL">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">Quantidade de peças para esta aplicação</p>
                </div>

                <!-- Cores (para Serigrafia e Emborrachado) -->
                <div id="colorCountField" class="hidden">
                    <div class="bg-gradient-to-r from-purple-600/20 to-pink-600/10 border border-purple-500/30 rounded-xl p-4 ring-1 ring-purple-500/20">
                        <label class="flex items-center gap-2 text-sm font-semibold text-purple-300 mb-3">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                            Número de Cores
                        </label>
                        <input type="number" id="color_count" name="color_count" min="1" value="1"
                               class="w-full px-4 py-3 border border-purple-500/30 rounded-xl bg-slate-800 text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <p class="mt-2 text-xs text-slate-400">Informe quantas cores serão utilizadas na aplicação</p>
                    </div>
                </div>


                <!-- Preço Calculado -->
                <div id="priceDisplay" class="hidden">
                    <div class="bg-gradient-to-r from-purple-600/20 to-purple-600/10 border border-purple-500/30 rounded-xl p-5 ring-1 ring-purple-500/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Preço por Aplicação:</span>
                            </div>
                            <span class="text-2xl font-bold text-indigo-400" id="unitPrice">R$ 0,00</span>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-700/50">
                            <span class="text-sm text-slate-400">Total desta Aplicação:</span>
                            <span class="text-lg font-bold text-white" id="totalPrice">R$ 0,00</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-3 text-center" id="priceFormula">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
                </div>
                <input type="hidden" id="unit_price" name="unit_price" value="0">
                <input type="hidden" id="final_price" name="final_price" value="0">
                <input type="hidden" id="base_size_price" value="0">
                <input type="hidden" id="effects_applied" name="effects_applied" value="">

                <!-- Upload de Imagem -->
                <!-- Removed checks for is_draft to allow image upload anytime -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        Imagem da Arte (opcional) 
                        <span class="text-xs font-normal text-gray-500 dark:text-slate-400 ml-1">(Cole com Ctrl+V)</span>
                    </label>
                    <div id="application_image_dropzone" class="relative border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-colors bg-white dark:bg-slate-800">
                        <input type="file" id="application_image" name="application_image" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="handleApplicationImageChange(this)">
                        <div id="application_image_placeholder" class="px-6 py-4 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-2">
                                <label for="application_image" class="cursor-pointer">
                                    <span class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg font-medium text-sm text-gray-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Escolher imagem
                                    </span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">ou arraste e solte aqui</p>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">PNG, JPG até 10MB</p>
                        </div>
                        <div id="application_image_preview" class="hidden px-6 py-4">
                            <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-green-900 dark:text-green-100" id="application_image_name">imagem.jpg</p>
                                        <p class="text-xs text-green-700 dark:text-green-400" id="application_image_size">0 KB</p>
                                    </div>
                                </div>
                                <button type="button" onclick="removeApplicationImage()" 
                                        class="flex-shrink-0 ml-3 text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">Apenas para visualização rápida</p>
                </div>

                <!-- Detalhes das Cores (oculto para SUB. TOTAL e SUB. LOCAL) -->
                <div id="colorDetailsField" class="@if($order->is_draft) hidden @endif">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Detalhes das Cores (opcional)</label>
                    <textarea id="color_details" name="color_details" rows="2" 
                              placeholder="Ex: Verde limão, Azul marinho, Branco, etc."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Especifique as cores exatas que serão utilizadas na aplicação</p>
                </div>

                <!-- Observações do Vendedor -->
                <div class="@if($order->is_draft) hidden @endif">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Observações do Vendedor (opcional)</label>
                    <textarea id="seller_notes" name="seller_notes" rows="3" 
                              placeholder="Ex: Aplicar com cuidado, cliente pediu urgência, etc."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Informações importantes para a produção</p>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-700/50">
                    <p class="text-xs text-slate-500">* Campos obrigatórios</p>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closePersonalizationModal()"
                                class="px-5 py-2.5 text-sm text-slate-300 hover:text-white hover:bg-slate-700 font-medium rounded-lg transition-all">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 text-sm bg-gradient-to-r from-[#7c3aed] to-[#7c3aed] hover:from-[#7c3aed] hover:to-[#7c3aed] text-white stay-white rounded-xl font-semibold transition-all shadow-lg shadow-purple-900/30 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Adicionar
                        </button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteConfirmationModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full shadow-xl border border-gray-200 dark:border-slate-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Remoção</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-3">Deseja realmente remover esta personalização?</p>
                <div id="delete-item-info" class="p-3 bg-gray-50 dark:bg-slate-800/50 rounded-md text-sm border border-gray-200 dark:border-slate-700">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteConfirmationModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDeletePersonalization()" 
                        class="px-4 py-2 bg-red-600 dark:bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition-colors">
                    Remover
                </button>
            </div>
        </div>
    </div>
</div>

@php
    $orderArtData = $order->items->mapWithKeys(function($item) {
        return [$item->id => [
            'art_name' => $item->art_name,
            'files' => $item->files->map(function($file) {
                return [
                    'name' => $file->file_name,
                    'url' => asset('storage/' . $file->file_path),
                ];
            })->values(),
        ]];
    });
@endphp

<script>
    (function(window) {
        // Global variables/state
        let currentItemId = '';
        let currentPersonalizationType = '';
        let currentPersonalizationId = '';
        let isSubmitting = false; // Flag para prevenir múltiplos envios
        let lastSubmitTime = 0; // Timestamp do último envio
        let pendingDeleteId = null; // ID da personalização pendente de exclusão

        // Dados de tamanhos por tipo
        const personalizationSizes = @json($personalizationData);
        const normalizeTypeKey = (type) => type && type.trim().toUpperCase();
        window.normalizeTypeKey = normalizeTypeKey;
        const orderArtData = @json($orderArtData);
        const personalizationLookup = @json($personalizationLookup->map->id); // Mapeamento de Nome -> ID
        
        // Configurações de personalização (charge_by_color, etc.)
        const personalizationSettings = @json($personalizationSettings ?? []);
        
        // Mapa de quantidades por item (para auto-preencher quantidade)
        const itemQuantitiesMap = {
            @foreach($order->items as $item)
            '{{ $item->id }}': {{ $item->quantity }},
            @endforeach
        };

        // --- DOM Elements ---
        const artItemSelect = document.getElementById('order_art_item');
        const artNameInput = document.getElementById('order_art_name');
        const artPreview = document.getElementById('order_art_preview');
        const personalizationForm = document.getElementById('personalizationForm');

        // --- Functions ---

        function renderOrderArtPreview(itemId) {
            if (!artPreview) return;
            const data = orderArtData[itemId] || {};
            const files = data.files || [];
            let html = '';

            if (data.art_name) {
                html += `<p class="mb-2 text-sm text-gray-700 dark:text-slate-200"><strong class="text-gray-900 dark:text-white">Nome da Arte:</strong> <span class="text-gray-800 dark:text-slate-200">${data.art_name}</span></p>`;
            }

            if (files.length) {
                const fileLinks = files.map(file => `<a href="${file.url}" target="_blank" class="inline-flex items-center px-2.5 py-1 text-xs bg-purple-50 dark:bg-purple-900/30 text-[#7c3aed] dark:text-purple-300 rounded-md border border-purple-100 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-800/70 transition">${file.name}</a>`).join(' ');
                html += `<div class="space-y-2"><p class="text-xs font-semibold text-gray-600 dark:text-slate-400">Arquivos enviados</p><div class="flex flex-wrap gap-2">${fileLinks}</div></div>`;
            }

            if (!html) {
                artPreview.classList.add('hidden');
                artPreview.innerHTML = 'Nenhum arquivo enviado para este item.';
                return;
            }

            artPreview.innerHTML = html;
            artPreview.classList.remove('hidden');
        }
        window.renderOrderArtPreview = renderOrderArtPreview;

        if (artItemSelect && artNameInput) {
            renderOrderArtPreview(artItemSelect.value);
            artItemSelect.addEventListener('change', (e) => {
                const data = orderArtData[e.target.value] || {};
                artNameInput.value = data.art_name || '';
                renderOrderArtPreview(e.target.value);
            });
        }

        let listenerRegistered = false;
        
        if (personalizationForm && !listenerRegistered) {
            personalizationForm.addEventListener('submit', handleFormSubmit);
            listenerRegistered = true;
        }
        
        function loadSizes(type) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            // Normalizar a chave do tipo (remover espaços, pontos, etc se necessário para bater com as chaves do array)
            // Mas aqui as chaves parecem ser strings diretas como 'DTF', 'SERIGRAFIA', etc.
            const typeKey = normalizeTypeKey(type);
            const typeData = personalizationSizes[typeKey] || personalizationSizes[type];
            
            if (typeData && typeData.sizes) {
                // Verificar se \?\? um array antes de iterar
                const sizes = typeData.sizes;
                
                const processSize = (size) => {
                    // Filtrar "COR" para EMBORRACHADO e SERIGRAFIA - COR não é um tamanho válido para seleção
                    if ((typeKey === 'EMBORRACHADO' || typeKey === 'SERIGRAFIA') && size.size_name === 'COR') {
                        return; // Pular esta opção
                    }
                    
                    const option = document.createElement('option');
                    option.value = size.size_name;
                    option.textContent = size.size_name + (size.size_dimensions ? ` (${size.size_dimensions})` : '');
                    option.dataset.dimensions = size.size_dimensions || '';
                    sizeSelect.appendChild(option);
                };

                if (Array.isArray(sizes)) {
                    sizes.forEach(processSize);
                } else if (typeof sizes === 'object') {
                    // Se for um objeto (collection convertida para json as vezes vira objeto com índices numéricos)
                    Object.values(sizes).forEach(processSize);
                }
            }
        }
        window.loadSizes = loadSizes;
        function loadAddons() {
            const addBtn = document.getElementById('addAddonBtn');
            const regataCheck = document.getElementById('regataCheckbox');
            const quantityInput = document.getElementById('quantity');
            const colorCountInput = document.getElementById('color_count');

            // Remover listeners antigos para evitar duplicação
            if (addBtn) {
                addBtn.removeEventListener('click', openAddonModal);
                addBtn.addEventListener('click', openAddonModal);
            }

            if (regataCheck) {
                regataCheck.removeEventListener('change', updateAddonsPrices);
                regataCheck.addEventListener('change', updateAddonsPrices);
            }
            
            if (quantityInput) {
                quantityInput.removeEventListener('input', calculatePrice);
                quantityInput.removeEventListener('change', calculatePrice);
                quantityInput.addEventListener('input', calculatePrice);
                quantityInput.addEventListener('change', calculatePrice);
            }

            if (colorCountInput) {
                colorCountInput.removeEventListener('input', calculatePrice);
                colorCountInput.removeEventListener('change', calculatePrice);
                colorCountInput.addEventListener('input', calculatePrice);
                colorCountInput.addEventListener('change', calculatePrice);
            }
        }
        window.loadAddons = loadAddons;


        function openPersonalizationModal(itemId, persType, persId) {
            
            currentItemId = itemId;
            currentPersonalizationType = persType;
            currentPersonalizationId = persId;
            
            document.getElementById('modal_item_id').value = itemId;
            document.getElementById('modal_personalization_type').value = persType;
            document.getElementById('modal_personalization_id').value = persId;
            document.getElementById('editing_personalization_id').value = '';
            document.getElementById('modalTitle').textContent = `Adicionar ${persType}`;
            const normalizedType = normalizeTypeKey(persType);

            // Limpar lista de adicionais e select oculto
            const addonsList = document.getElementById('addonsList');
            if(addonsList) addonsList.innerHTML = '';
            
            const addonsSelect = document.getElementById('addons');
            if(addonsSelect) addonsSelect.innerHTML = '';
            
            const addonsPrices = document.getElementById('addons-prices');
            if(addonsPrices) addonsPrices.innerHTML = '';
            
            const regataCheck = document.getElementById('regataCheckbox');
            if(regataCheck) regataCheck.checked = false;

            // Verificar se o tipo de personalização usa cobrança por cor (dinâmico baseado nas configurações)
            const typeSetting = personalizationSettings[normalizedType] || personalizationSettings[persType];
            const useColorCharge = typeSetting && typeSetting.charge_by_color;
            
            if (useColorCharge || normalizedType === "SERIGRAFIA" || normalizedType === "EMBORRACHADO") {
                document.getElementById('colorCountField').classList.remove('hidden');
                // Resetar campo de cores para valor padrão
                document.getElementById('color_count').value = '1';
                const effectsField = document.getElementById('effectsField');
                if (effectsField) effectsField.classList.remove('hidden');
                ['effect_dourado','effect_prata','effect_neon'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.checked = false;
                });
            } else {
                document.getElementById('colorCountField').classList.add('hidden');
                const effectsField = document.getElementById('effectsField');
                if (effectsField) effectsField.classList.add('hidden');
            }
            
            // Mostrar/ocultar campos baseado no tipo de personalização
            // NOTA: Adicionais agora aparecem para TODOS os tipos de personalização
            if (normalizedType === 'SUB. TOTAL') {
                // Para SUB. TOTAL: ocultar localização, tamanho único e detalhes das cores, mostrar quantidade e adicionais
                document.getElementById('locationField').classList.add('hidden');
                document.getElementById('sizeField').classList.add('hidden');
                document.getElementById('quantityField').classList.remove('hidden');
                document.getElementById('colorDetailsField').classList.add('hidden');
                document.getElementById('addonsField').classList.remove('hidden');
                
                // Preencher quantidade automaticamente com a quantidade do item clicado
                const itemQty = itemQuantitiesMap[itemId] || 1;
                document.getElementById('quantity').value = itemQty;
                
                loadAddons();
            } else if (normalizedType === 'DTF') {
                // Para DTF: mostrar localização e tamanho único, ocultar detalhes das cores
                document.getElementById('locationField').classList.remove('hidden');
                document.getElementById('sizeField').classList.remove('hidden');
                document.getElementById('quantityField').classList.remove('hidden');
                document.getElementById('colorDetailsField').classList.add('hidden');
                document.getElementById('addonsField').classList.remove('hidden');

                // Preencher quantidade automaticamente com a quantidade do item clicado
                const itemQty = itemQuantitiesMap[itemId] || 1;
                document.getElementById('quantity').value = itemQty;

                loadAddons();
            } else if (normalizedType === 'SUB. LOCAL') {
                // Para SUB. LOCAL: mostrar localização e tamanho único, ocultar detalhes das cores
                document.getElementById('locationField').classList.remove('hidden');
                document.getElementById('sizeField').classList.remove('hidden');
                document.getElementById('quantityField').classList.remove('hidden');
                document.getElementById('colorDetailsField').classList.add('hidden');
                document.getElementById('addonsField').classList.remove('hidden');

                // Preencher quantidade automaticamente com a quantidade do item clicado
                const itemQty = itemQuantitiesMap[itemId] || 1;
                document.getElementById('quantity').value = itemQty;

                loadAddons();
            } else {
                // Para outros tipos: mostrar localização, tamanho único, detalhes das cores e adicionais
                document.getElementById('locationField').classList.remove('hidden');
                document.getElementById('sizeField').classList.remove('hidden');
                document.getElementById('quantityField').classList.remove('hidden');
                document.getElementById('colorDetailsField').classList.remove('hidden');
                document.getElementById('addonsField').classList.remove('hidden');

                // Preencher quantidade automaticamente com a quantidade do item clicado
                const itemQty = itemQuantitiesMap[itemId] || 1;
                document.getElementById('quantity').value = itemQty;

                loadAddons();
            }
            
            setTimeout(() => {
                calculatePrice();
            }, 500);
            
            // Carregar tamanhos
            loadSizes(normalizedType);
            
            // Limpar formulário (mas preservar os campos hidden que acabamos de setar)
            // document.getElementById('personalizationForm').reset(); // Isso limparia os hiddens também
            // Resetar apenas campos visíveis
            document.getElementById('location').value = '';
            document.getElementById('size').value = '';
            document.getElementById('color_details').value = '';
            document.getElementById('seller_notes').value = '';
            const artFilesEl = document.getElementById('art_files');
            if (artFilesEl) artFilesEl.value = '';
            const selectedFilesListEl = document.getElementById('selected_files_list');
            if (selectedFilesListEl) selectedFilesListEl.innerHTML = '';
            const applicationImageEl = document.getElementById('application_image');
            if (applicationImageEl) applicationImageEl.value = '';
            removeApplicationImage();

            // Resetar flag de submissão ao abrir modal
            isSubmitting = false;
            
            // Resetar botão de submit
            const submitBtn = document.getElementById('personalizationForm').querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Adicionar
                `;
            }
            
            // Gerenciar checkboxes de itens vinculados
            setupLinkedItemsCheckboxes(itemId);
            
            // Mostrar modal
            document.getElementById('personalizationModal').classList.remove('hidden');
        }
        window.openPersonalizationModal = openPersonalizationModal;

        // Função para configurar checkboxes de itens vinculados
        let mainLinkedItemId = null;
        
        function setupLinkedItemsCheckboxes(currentItemId) {
            mainLinkedItemId = currentItemId;
            const checkboxes = document.querySelectorAll('.link-item-checkbox');
            const labels = document.querySelectorAll('.link-item-label');
            
            // Desmarcar e remover destaque de todos primeiro
            checkboxes.forEach(cb => {
                cb.checked = false;
                // Remover listeners antigos
                cb.replaceWith(cb.cloneNode(true));
            });
            
            // Re-selecionar após clonar
            const newCheckboxes = document.querySelectorAll('.link-item-checkbox');
            const newLabels = document.querySelectorAll('.link-item-label');
            
            newLabels.forEach(label => {
                label.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-900/30');
            });
            
            // Marcar e destacar o item atual (obrigatório)
            newCheckboxes.forEach(cb => {
                if (cb.value == currentItemId) {
                    cb.checked = true;
                    const label = cb.closest('.link-item-label');
                    if (label) {
                        label.classList.add('ring-2', 'ring-purple-500', 'bg-purple-900/30');
                    }
                }
                
                // Adicionar listener para prevenir desmarcação do item principal
                cb.addEventListener('click', function(e) {
                    if (this.value == mainLinkedItemId && !this.checked) {
                        e.preventDefault();
                        this.checked = true;
                    }
                });
                
                // Adicionar listener para resumo
                cb.addEventListener('change', updateLinkedItemsSummary);
            });
            
            updateLinkedItemsSummary();
        }
        
        // Atualizar resumo de itens vinculados
        function updateLinkedItemsSummary() {
            const checkboxes = document.querySelectorAll('.link-item-checkbox:checked');
            const summary = document.getElementById('linkedItemsSummary');
            const countEl = document.getElementById('linkedItemsCount');
            const quantityInput = document.getElementById('quantity');
            
            // Quando há itens vinculados, NÃO atualizamos a quantidade no formulário
            // porque cada item receberá sua própria quantidade automaticamente
            // Apenas mostramos o resumo informativo
            
            if (!summary || !countEl) return;
            
            if (checkboxes.length > 1) {
                // Construir lista de itens com suas quantidades
                let itemsList = [];
                let totalQty = 0;
                checkboxes.forEach(cb => {
                    const qty = itemQuantitiesMap[cb.value] || 0;
                    totalQty += qty;
                    const label = cb.closest('.link-item-label');
                    const itemName = label ? label.querySelector('.text-sm.font-medium')?.textContent : `Item ${cb.value}`;
                    itemsList.push(`${itemName}: ${qty} pç`);
                });
                
                // Atualizar resumo com detalhes
                summary.innerHTML = `
                    <div class="text-sm text-green-400">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <strong>${checkboxes.length} itens vinculados</strong> (${totalQty} peças no total)
                        </div>
                        <p class="text-xs text-slate-400 ml-6">Cada item receberá a personalização com sua própria quantidade de peças</p>
                    </div>
                `;
                summary.classList.remove('hidden');
                
                // Desabilitar campo de quantidade quando itens estão vinculados
                if (quantityInput) {
                    quantityInput.disabled = true;
                    quantityInput.classList.add('opacity-50', 'cursor-not-allowed');
                    quantityInput.value = totalQty;
                    quantityInput.title = 'Quantidade será calculada automaticamente para cada item';
                }
            } else {
                summary.classList.add('hidden');
                
                // Habilitar campo de quantidade quando apenas 1 item
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.classList.remove('opacity-50', 'cursor-not-allowed');
                    quantityInput.title = '';
                    // Preencher com quantidade do item selecionado
                    const singleItemId = checkboxes[0]?.value;
                    if (singleItemId && itemQuantitiesMap[singleItemId]) {
                        quantityInput.value = itemQuantitiesMap[singleItemId];
                        quantityInput.dispatchEvent(new Event('change'));
                    }
                }
            }
        }

        // FUNÇÃO REMOVIDA: setupFormValidation() - Não é mais necessária
        // O event listener já está registrado no início do script

        function handleFormSubmit(event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            
            const currentTime = Date.now();
            if (currentTime - lastSubmitTime < 1000) {
                return false;
            }
            
            if (isSubmitting) {
                return false;
            }
            
            const persType = document.getElementById('modal_personalization_type').value;
            const normalizedType = normalizeTypeKey(persType);
            let isValid = true;
            let errorMessage = '';
            
            if (normalizedType === 'SUB. TOTAL') {
                const artFilesElement = document.getElementById('art_files');
                const artFiles = artFilesElement ? artFilesElement.files.length : 0;
                
                const isDraft = {{ $order->is_draft ? 'true' : 'false' }};

                // Arquivo não é mais obrigatório - foi movido para outra seção
                if (false && !isDraft && artFiles === 0) {
                    isValid = false;
                    errorMessage += 'Pelo menos um arquivo da arte é obrigatório.\n';
                }
                
                if (isValid) {
                    document.getElementById('location').disabled = true;
                    document.getElementById('size').disabled = true;
                    document.getElementById('location_hidden').disabled = false;
                    document.getElementById('size_hidden').disabled = false;
                    
                    const form = document.getElementById('personalizationForm');
                    const isEditing = {{ request()->routeIs('orders.edit.*') ? 'true' : 'false' }};
                    
                    if (isEditing) {
                        form.action = '{{ route("orders.edit.customization") }}';
                    } else {
                        form.action = '{{ route("orders.wizard.customization") }}';
                    }
                    
                    form.removeEventListener('submit', handleFormSubmit);
                    
                    const formData = new FormData(form);
                    const targetUrl = isEditing ? '{{ route("orders.edit.customization") }}' : '{{ route("orders.wizard.customization") }}';
                    
                    isSubmitting = true;
                    lastSubmitTime = Date.now();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Enviando...';
                    }
                    
                    fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Erro na resposta do servidor');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            isSubmitting = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = normalizedType === 'SUB. TOTAL' ? 'Salvar Alterações' : 'Adicionar Personalização';
                            }
                            alert('Erro: ' + (data.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Adicionar Personalização';
                        }
                        console.error('Erro ao enviar formulário:', error);
                        alert('Erro ao processar a solicitação: ' + error.message);
                    });
                }
            } else {
                const location = document.getElementById('location').value;
                const size = document.getElementById('size').value;
                const artFilesInput = document.getElementById('art_files');
                const artFiles = artFilesInput ? artFilesInput.files.length : 0;
                
                if (!location) {
                    isValid = false;
                    errorMessage += 'Localização é obrigatória.\n';
                }
                
                if (!size) {
                    isValid = false;
                    errorMessage += 'Tamanho é obrigatório.\n';
                }
                
                const isDraft = {{ $order->is_draft ? 'true' : 'false' }};
                
                // Arquivo não é mais obrigatório - foi movido para outra seção
                if (false && !isDraft && artFiles === 0) {
                    isValid = false;
                    errorMessage += 'Pelo menos um arquivo da arte é obrigatório.\n';
                }
                
                if (isValid) {
                    document.getElementById('location').name = 'location';
                    document.getElementById('size').name = 'size';
                    
                    const form = document.getElementById('personalizationForm');
                    const isEditing = {{ request()->routeIs('orders.edit.*') ? 'true' : 'false' }};
                    
                    if (isEditing) {
                        form.action = '{{ route("orders.edit.customization") }}';
                    } else {
                        form.action = '{{ route("orders.wizard.customization") }}';
                    }
                    
                    form.removeEventListener('submit', handleFormSubmit);
                    
                    const formData = new FormData(form);
                    const targetUrl = isEditing ? '{{ route("orders.edit.customization") }}' : '{{ route("orders.wizard.customization") }}';
                    
                    isSubmitting = true;
                    lastSubmitTime = Date.now();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Enviando...';
                    }
                    
                    fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Erro na resposta do servidor');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            isSubmitting = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                const isEditing = document.getElementById('editing_personalization_id').value;
                                submitBtn.innerHTML = isEditing ? 'Salvar Alterações' : 'Adicionar Personalização';
                            }
                            alert('Erro: ' + (data.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            const isEditing = document.getElementById('editing_personalization_id').value;
                            submitBtn.innerHTML = isEditing ? 'Salvar Alterações' : 'Adicionar Personalização';
                        }
                        console.error('Erro ao enviar formulário:', error);
                        alert('Erro ao processar a solicitação: ' + error.message);
                    });
                }
            }
            
            if (!isValid) {
                alert('Por favor, corrija os seguintes erros:\n\n' + errorMessage);
                return false;
            }
        }
        window.handleFormSubmit = handleFormSubmit;

        function closePersonalizationModal() {
            const modal = document.getElementById('personalizationModal');
            if (modal) modal.classList.add('hidden');
            currentItemId = '';
            currentPersonalizationType = '';
            currentPersonalizationId = '';
            
            // Limpar feedback de uploads ao fechar (with null checks)
            if (typeof removeApplicationImage === 'function') {
                removeApplicationImage();
            }
            const artFilesEl = document.getElementById('art_files');
            if (artFilesEl) artFilesEl.value = '';
            const selectedFilesListEl = document.getElementById('selected_files_list');
            if (selectedFilesListEl) selectedFilesListEl.innerHTML = '';
            const artFilesPlaceholderEl = document.getElementById('art_files_placeholder');
            if (artFilesPlaceholderEl) artFilesPlaceholderEl.style.opacity = '1';
            const artFilesDropzoneEl = document.getElementById('art_files_dropzone');
            if (artFilesDropzoneEl) {
                artFilesDropzoneEl.classList.remove('border-[#7c3aed]', 'dark:border-[#7c3aed]', 'bg-purple-100/30', 'dark:bg-purple-900/20');
                artFilesDropzoneEl.classList.add('border-purple-200', 'dark:border-purple-800');
            }
        }
        window.closePersonalizationModal = closePersonalizationModal;

        // Função para lidar com mudança na imagem da aplicação
        function handleApplicationImageChange(input) {
            const file = input.files[0];
            if (file) {
                // Validar tipo de arquivo
                if (!file.type.match('image.*')) {
                    alert('Por favor, selecione apenas arquivos de imagem (PNG, JPG, etc.)');
                    input.value = '';
                    return;
                }
                
                // Validar tamanho (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('A imagem deve ter no máximo 10MB');
                    input.value = '';
                    return;
                }
                
                // Mostrar preview
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2);
                const sizeUnit = fileSize > 1024 ? ((fileSize / 1024).toFixed(2) + ' MB') : (fileSize + ' KB');
                
                document.getElementById('application_image_name').textContent = fileName;
                document.getElementById('application_image_size').textContent = sizeUnit;
                document.getElementById('application_image_placeholder').classList.add('hidden');
                document.getElementById('application_image_preview').classList.remove('hidden');
                document.getElementById('application_image_dropzone').classList.remove('border-gray-300', 'dark:border-slate-600');
                document.getElementById('application_image_dropzone').classList.add('border-green-400', 'dark:border-green-500', 'bg-green-50/30', 'dark:bg-green-900/20');
                
            }
        }
        window.handleApplicationImageChange = handleApplicationImageChange;

        // Função para remover imagem da aplicação
        function removeApplicationImage() {
            document.getElementById('application_image').value = '';
            document.getElementById('application_image_placeholder').classList.remove('hidden');
            document.getElementById('application_image_preview').classList.add('hidden');
            document.getElementById('application_image_dropzone').classList.remove('border-green-400', 'dark:border-green-500', 'bg-green-50/30', 'dark:bg-green-900/20');
            document.getElementById('application_image_dropzone').classList.add('border-gray-300', 'dark:border-slate-600');
        }
        window.removeApplicationImage = removeApplicationImage;

        // Função para lidar com mudança nos arquivos da arte
        function handleArtFilesChange(input) {
            const files = input.files;
            const fileList = document.getElementById('selected_files_list');
            
            if (!fileList) return; // Ensure fileList exists
            fileList.innerHTML = '';
            
            const artFilesPlaceholder = document.getElementById('art_files_placeholder');
            const artFilesDropzone = document.getElementById('art_files_dropzone');
            
            if (files.length > 0) {
                // Esconder placeholder
                if (artFilesPlaceholder) artFilesPlaceholder.style.opacity = '0.5';
                
                // Mostrar cada arquivo
                Array.from(files).forEach((file, index) => {
                    const fileSize = (file.size / 1024).toFixed(2);
                    const sizeUnit = fileSize > 1024 ? ((fileSize / 1024).toFixed(2) + ' MB') : (fileSize + ' KB');
                    
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'flex items-center justify-between bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 border border-purple-200 dark:border-purple-800';
                    fileDiv.innerHTML = `
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#7c3aed] dark:text-[#a78bfa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-purple-900 dark:text-purple-100 truncate">${file.name}</p>
                                <p class="text-xs text-purple-700 dark:text-purple-400">${sizeUnit}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-3 text-[#7c3aed] dark:text-[#a78bfa]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    `;
                    fileList.appendChild(fileDiv);
                });
                
                // Atualizar borda
                if (artFilesDropzone) {
                    artFilesDropzone.classList.remove('border-purple-200', 'dark:border-purple-800');
                    artFilesDropzone.classList.add('border-[#7c3aed]', 'dark:border-[#7c3aed]', 'bg-purple-100/30', 'dark:bg-purple-900/20');
                }
                
            } else {
                // Restaurar placeholder
                if (artFilesPlaceholder) artFilesPlaceholder.style.opacity = '1';
                if (artFilesDropzone) {
                    artFilesDropzone.classList.remove('border-[#7c3aed]', 'dark:border-[#7c3aed]', 'bg-purple-100/30', 'dark:bg-purple-900/20');
                    artFilesDropzone.classList.add('border-purple-200', 'dark:border-purple-800');
                }
            }
        }
        window.handleArtFilesChange = handleArtFilesChange;



        // Dados dos adicionais - carregados do banco de dados (com tipo para filtrar)
        @php
            $addonsData = $specialOptions->map(function($opt) {
                return [
                    'id' => $opt->id,
                    'name' => $opt->name,
                    'price_adjustment' => $opt->charge_type === 'fixed' ? (float)$opt->charge_value : 0,
                    'percentage' => $opt->charge_type === 'percentage' ? (float)$opt->charge_value : 0,
                    'charge_type' => $opt->charge_type,
                    'description' => $opt->description ?? $opt->name,
                    'personalization_type' => $opt->personalization_type,
                ];
            })->values();
        @endphp
        const availableAddons = @json($addonsData);




        function openAddonModal() {
            // Limpar modal anterior se existir
            const oldModal = document.getElementById('addonModal');
            if (oldModal) oldModal.remove();

            // Filtrar adicionais pelo tipo de personalização atual
            const normalizedCurrentType = normalizeTypeKey(currentPersonalizationType);
            const filteredAddons = availableAddons.filter(addon => {
                const addonType = normalizeTypeKey(addon.personalization_type);
                return addonType === normalizedCurrentType;
            });
            
            // Função auxiliar para formatar o valor do adicional
            const formatAddonPrice = (addon) => {
                if (addon.charge_type === 'percentage' && addon.percentage > 0) {
                    return `<span class="text-emerald-600 dark:text-emerald-400 font-bold">+${addon.percentage}%</span>`;
                } else if (addon.price_adjustment > 0) {
                    return `<span class="text-[#7c3aed] font-bold">+R$ ${addon.price_adjustment.toFixed(2).replace('.', ',')}</span>`;
                } else if (addon.price_adjustment < 0) {
                    return `<span class="text-green-600 dark:text-green-400 font-bold">-R$ ${Math.abs(addon.price_adjustment).toFixed(2).replace('.', ',')}</span>`;
                }
                return '<span class="text-gray-500 dark:text-slate-400">Grátis</span>';
            };

            // Criar modal dinâmico com design moderno
            const modalHtml = `
                <div id="addonModal" class="fixed inset-0 bg-black/60 dark:bg-black/70 z-[60] flex items-center justify-center p-4 backdrop-blur-sm animate-fadeIn">
                    <div class="relative w-full max-w-lg shadow-2xl rounded-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700/50 overflow-hidden animate-slideUp">
                        <!-- Header com gradiente -->
                        <div class="px-6 py-5 bg-gradient-to-r from-emerald-500/10 via-white to-white dark:from-emerald-600/20 dark:via-slate-900 dark:to-slate-900 border-b border-gray-200 dark:border-slate-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white stay-white" style="color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Opções Especiais</h3>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">${filteredAddons.length} ${filteredAddons.length === 1 ? 'opção disponível' : 'opções disponíveis'}</p>
                                    </div>
                                </div>
                                <button type="button" id="closeAddonModalX" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Body -->
                        <div class="p-4 max-h-[50vh] overflow-y-auto">
                            ${filteredAddons.length === 0 
                                ? `<div class="text-center py-12">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 dark:text-slate-300 font-medium mb-1">Nenhuma opção cadastrada</p>
                                    <p class="text-sm text-gray-500 dark:text-slate-500">Configure em Admin → Personalização → Opções Especiais</p>
                                </div>`
                                : `<div class="grid grid-cols-2 gap-3">
                                    ${filteredAddons.map(addon => `
                                        <label class="addon-item relative flex flex-col p-4 bg-white hover:bg-emerald-50 dark:bg-slate-800/50 dark:hover:bg-slate-800 rounded-xl cursor-pointer border-2 border-gray-200 dark:border-transparent hover:border-emerald-400/60 transition-all group">
                                            <input type="checkbox" class="addon-checkbox absolute top-3 right-3 w-5 h-5 text-emerald-600 bg-white border-gray-300 dark:text-emerald-500 dark:bg-slate-700 dark:border-slate-600 rounded focus:ring-emerald-500 focus:ring-offset-0 focus:ring-offset-white dark:focus:ring-offset-slate-900 cursor-pointer" 
                                                   value="${addon.id}" 
                                                   data-name="${addon.name}" 
                                                   data-price="${addon.price_adjustment}" 
                                                   data-percentage="${addon.percentage || 0}"
                                                   data-charge-type="${addon.charge_type}"
                                                   data-description="${addon.description}">
                                            <span class="addon-title text-gray-900 dark:text-white font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors pr-6">${addon.name}</span>
                                            ${addon.description && addon.description !== addon.name ? `<span class="text-xs text-gray-500 dark:text-slate-400 mt-1 line-clamp-2">${addon.description}</span>` : ''}
                                            <div class="mt-2">${formatAddonPrice(addon)}</div>
                                        </label>
                                    `).join('')}
                                </div>`
                            }
                        </div>
                        
                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700/50 bg-gray-50 dark:bg-slate-800/30 flex items-center justify-between">
                            <span id="selectedCount" class="text-sm text-gray-500 dark:text-slate-400">Nenhum selecionado</span>
                            <div class="flex gap-3">
                                <button type="button" id="cancelAddon" onclick="closeAddonModal()" class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 rounded-lg transition-all">
                                    Cancelar
                                </button>
                                <button type="button" id="confirmAddon" onclick="confirmAddAddon()" class="px-6 py-2.5 text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white stay-white rounded-lg transition-all shadow-lg shadow-emerald-900/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-emerald-600" style="color:#fff;" ${filteredAddons.length === 0 ? 'disabled' : ''}>
                                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
                    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                    .animate-fadeIn { animation: fadeIn 0.2s ease-out; }
                    .animate-slideUp { animation: slideUp 0.3s ease-out; }
                    .addon-item:has(.addon-checkbox:checked) { border-color: rgb(16 185 129); background-color: rgb(16 185 129 / 0.08); }
                    .addon-item:has(.addon-checkbox:checked) .addon-title { color: rgb(16 185 129); }
                    .dark .addon-item:has(.addon-checkbox:checked) { background-color: rgb(16 185 129 / 0.12); }
                    .dark .addon-item:has(.addon-checkbox:checked) .addon-title { color: rgb(110 231 183); }
                </style>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Eventos do modal (apenas o X de fechar, os botões usam onclick)
            document.getElementById('closeAddonModalX').addEventListener('click', closeAddonModal);
            
            // Atualizar contador de selecionados
            const updateSelectedCount = () => {
                const count = document.querySelectorAll('.addon-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = count === 0 
                    ? 'Nenhum selecionado' 
                    : `${count} selecionado${count > 1 ? 's' : ''}`;
            };
            
            document.querySelectorAll('.addon-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });
            
            // Fechar ao clicar fora
            document.getElementById('addonModal').addEventListener('click', (e) => {
                if (e.target.id === 'addonModal') closeAddonModal();
            });
            
            // Fechar com ESC
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    closeAddonModal();
                    document.removeEventListener('keydown', handleEsc);
                }
            };
            document.addEventListener('keydown', handleEsc);
        }
        window.openAddonModal = openAddonModal;

        function closeAddonModal() {
            const modal = document.getElementById('addonModal');
            if (modal) modal.remove();
        }
        window.closeAddonModal = closeAddonModal;

        function confirmAddAddon() {
            const checkboxes = document.querySelectorAll('.addon-checkbox:checked');
            const addonsList = document.getElementById('addonsList');
            const addonsSelect = document.getElementById('addons');
            
            checkboxes.forEach(checkbox => {
                const addonId = checkbox.value;
                const addonName = checkbox.dataset.name;
                const addonPrice = parseFloat(checkbox.dataset.price);
                const addonPercentage = parseFloat(checkbox.dataset.percentage) || 0;
                const chargeType = checkbox.dataset.chargeType;
                const addonDescription = checkbox.dataset.description;
                
                // Verificar se já foi adicionado
                if (document.querySelector(`[data-addon-id="${addonId}"]`)) {
                    return; // Já existe, pular
                }
                
                // Formatar exibição do preço
                let priceDisplay = '';
                if (chargeType === 'percentage' && addonPercentage > 0) {
                    priceDisplay = `<span class="text-emerald-600 dark:text-emerald-400 font-semibold">+${addonPercentage}%</span>`;
                } else if (addonPrice > 0) {
                    priceDisplay = `<span class="text-[#7c3aed] font-semibold">+R$ ${addonPrice.toFixed(2).replace('.', ',')}</span>`;
                } else if (addonPrice < 0) {
                    priceDisplay = `<span class="text-green-600 dark:text-green-400 font-semibold">-R$ ${Math.abs(addonPrice).toFixed(2).replace('.', ',')}</span>`;
                }
                
                // Criar elemento visual com estilo moderno
                const addonElement = document.createElement('div');
                addonElement.className = 'flex items-center justify-between p-3 bg-white hover:bg-emerald-50 dark:bg-slate-800 dark:hover:bg-slate-700/50 rounded-lg border border-emerald-200/70 dark:border-slate-600/50 transition-all group';
                addonElement.setAttribute('data-addon-id', addonId);
                addonElement.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/15 dark:bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">${addonName}</span>
                            <div class="text-sm">${priceDisplay}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-addon p-2 text-gray-400 hover:text-red-500 hover:bg-red-500/10 dark:text-slate-400 dark:hover:text-red-400 dark:hover:bg-red-500/10 rounded-lg transition-all opacity-0 group-hover:opacity-100" data-addon-id="${addonId}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                
                addonsList.appendChild(addonElement);
                
                // Adicionar ao select oculto
                const option = document.createElement('option');
                option.value = addonId;
                option.selected = true;
                option.textContent = addonName;
                addonsSelect.appendChild(option);
                
                // Evento para remover
                addonElement.querySelector('.remove-addon').addEventListener('click', function() {
                    addonElement.remove();
                    option.remove();
                    updateAddonsPrices();
                });
            });
            
            updateAddonsPrices();
            closeAddonModal();
        }
        window.confirmAddAddon = confirmAddAddon;

        function updateAddonsPrices() {
            const selectedAddons = Array.from(document.getElementById('addons').selectedOptions);
            const pricesContainer = document.getElementById('addons-prices');
            
            // Obter preço base do tamanho (sem cores e sem adicionais) para calcular percentuais
            const baseSizeInput = document.getElementById('base_size_price');
            const unitPriceEl = document.getElementById('unitPrice');
            let basePrice = baseSizeInput ? parseFloat(baseSizeInput.value) || 0 : 0;
            if (!basePrice && unitPriceEl) {
                basePrice = parseFloat(unitPriceEl.textContent.replace('R$', '').replace('.', '').replace(',', '.').trim()) || 0;
            }
            
            let totalAddonPrice = 0;
            let pricesHtml = '';
            
            // Adicionais selecionados - usar dados dos adicionais disponíveis
            if (selectedAddons.length > 0) {
                selectedAddons.forEach(option => {
                    // Buscar o adicional nos dados disponíveis pelo ID
                    const addonId = parseInt(option.value);
                    const addonData = availableAddons.find(a => a.id === addonId);
                    
                    if (addonData) {
                        let price = 0;
                        let displayValue = '';
                        
                        // Calcular preço baseado no tipo de cobrança
                        if (addonData.charge_type === 'percentage' && addonData.percentage > 0) {
                            price = basePrice * (addonData.percentage / 100);
                            displayValue = `+${addonData.percentage}% (R$ ${price.toFixed(2).replace('.', ',')})`;
                            pricesHtml += `<div class="text-xs text-emerald-600 dark:text-emerald-400">• ${addonData.name}: ${displayValue}</div>`;
                        } else {
                            price = addonData.price_adjustment;
                            const sign = price >= 0 ? '+' : '-';
                            displayValue = `${sign}R$ ${Math.abs(price).toFixed(2).replace('.', ',')}`;
                            pricesHtml += `<div class="text-xs text-gray-600 dark:text-slate-400">• ${addonData.name}: ${displayValue}</div>`;
                        }
                        
                        totalAddonPrice += price;
                    }
                });
            }
            
            if (totalAddonPrice !== 0) {
                const sign = totalAddonPrice >= 0 ? '+' : '-';
                pricesHtml += `<div class="text-sm font-medium text-gray-900 dark:text-white mt-2 pt-2 border-t border-gray-200 dark:border-slate-700">Total adicionais: ${sign}R$ ${Math.abs(totalAddonPrice).toFixed(2).replace('.', ',')}</div>`;
            }
            
            pricesContainer.innerHTML = pricesHtml;
            
            // Recalcular preço após atualizar adicionais
            calculatePrice();
        }
        window.updateAddonsPrices = updateAddonsPrices;


        function getSelectedEffects() {
            const effects = [];
            if (document.getElementById('effect_dourado')?.checked) effects.push('Dourado');
            if (document.getElementById('effect_prata')?.checked) effects.push('Prata');
            if (document.getElementById('effect_neon')?.checked) effects.push('Neon');
            return effects;
        }
        window.getSelectedEffects = getSelectedEffects;

        async function calculatePrice() {
            const persTypeRaw = document.getElementById('modal_personalization_type').value;
            const persType = normalizeTypeKey(persTypeRaw);
            let size = document.getElementById('size').value;
            const colorCount = parseInt(document.getElementById('color_count')?.value || 1);
            
            let quantity = 1;
            const quantityField = document.getElementById('quantity');
            if (quantityField) {
                quantity = parseInt(quantityField.value) || 1;
            }
            
            if (persType === 'SUB. TOTAL') {
                if (!persType || quantity === 0) {
                    document.getElementById('priceDisplay').classList.add('hidden');
                    return;
                }
            } else {
                if (!size) {
                    if (persType === 'SERIGRAFIA') {
                        size = 'ESCUDO';
                    } else if (persType === 'EMBORRACHADO') {
                        size = 'ESCUDO';
                    } else {
                        size = 'A4';
                    }
                }
                
                if (!persType) {
                    document.getElementById('priceDisplay').classList.add('hidden');
                    return;
                }
            }
            
            let apiType = persType;
            if (persType === 'SUB. LOCAL') apiType = 'SUB. LOCAL';
            if (persType === 'SUB. TOTAL') apiType = 'SUB. TOTAL';
            
            try {
                const sizeForApi = persType === 'SUB. TOTAL' ? 'CACHARREL' : size;
                const apiUrl = `/api/personalization-prices/price?type=${apiType}&size=${encodeURIComponent(sizeForApi)}&quantity=${quantity}`;
                
                const response = await fetch(apiUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.price) {
                    const baseSizePrice = parseFloat(data.price); // Preço base do tamanho (sem adicionais)
                    const baseSizeInput = document.getElementById('base_size_price');
                    if (baseSizeInput) baseSizeInput.value = baseSizePrice;
                    let unitPrice = baseSizePrice;
                    const qty = parseInt(quantity);
                    const currentColorCount = parseInt(document.getElementById('color_count')?.value || 1);
                    
                    // Calcular adicionais selecionados para TODOS os tipos de personalização
                    // IMPORTANTE: Percentuais são calculados sobre o preço BASE do tamanho!
                    const selectedAddons = Array.from(document.getElementById('addons').selectedOptions);
                    let addonsTotal = 0;
                    
                    selectedAddons.forEach(option => {
                        // Buscar o adicional nos dados disponíveis pelo ID
                        const addonId = parseInt(option.value);
                        const addonData = availableAddons.find(a => a.id === addonId);
                        
                        if (addonData) {
                            // Calcular preço baseado no tipo de cobrança
                            if (addonData.charge_type === 'percentage' && addonData.percentage > 0) {
                                // Percentual: calcula APENAS sobre o preço base do tamanho
                                addonsTotal += baseSizePrice * (addonData.percentage / 100);
                            } else {
                                // Valor fixo
                                addonsTotal += addonData.price_adjustment;
                            }
                        }
                    });
                    
                    unitPrice += addonsTotal;
                    
                    // Calcular custo de cores adicionais DEPOIS dos percentuais
                    // Cores são adicionadas ao preço final, não afetam os percentuais
                    const typeSetting = personalizationSettings[apiType] || personalizationSettings[persType];
                    const useColorCharge = typeSetting && typeSetting.charge_by_color;
                    
                    if (useColorCharge && currentColorCount > 1) {
                        // Usa color_price_per_unit das configurações or fallback para valor padrão
                        const colorPricePerUnit = typeSetting.color_price_per_unit || 2.00;
                        const extraColors = currentColorCount - 1;
                        unitPrice += colorPricePerUnit * extraColors;
                    }
                    
                    if (apiType === 'SERIGRAFIA' || apiType === 'EMBORRACHADO') {
                        let colorPrice = 0;
                        
                        if (currentColorCount > 1) {
                            try {
                                if (apiType === 'SERIGRAFIA') {
                                    // SERIGRAFIA: Use PersonalizationPrice API
                                    const colorPriceUrl = `/api/personalization-prices/price?type=${apiType}&size=COR&quantity=${quantity}`;
                                    const colorResp = await fetch(colorPriceUrl, { headers: { 'Accept': 'application/json' } });
                                    const colorData = await colorResp.json();
                                    
                                    if (colorData.success) {
                                        colorPrice = parseFloat(colorData.price);
                                    }
                                } else {
                                    // EMBORRACHADO (Legacy): Use SerigraphyColors API
                                    const colorsResponse = await fetch('/api/serigraphy-colors', {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                    if (!colorsResponse.ok) {
                                        throw new Error(`HTTP error! status: ${colorsResponse.status}`);
                                    }
                                    const colorsData = await colorsResponse.json();
                                    
                                    for (const color of colorsData) {
                                        const match = color.name.match(/\((\d+)-(\d+)\)/);
                                        if (match) {
                                            const from = parseInt(match[1]);
                                            const to = parseInt(match[2]);
                                            if (qty >= from && qty <= to) {
                                                colorPrice = parseFloat(color.price);
                                                break;
                                            }
                                        }
                                    }
                                    
                                    if (colorPrice === 0) {
                                        const colorName = `${colorCount} Cor${colorCount > 1 ? 'es' : ''}`;
                                        const matchingColor = colorsData.find(color => color.name === colorName);
                                        if (matchingColor) {
                                            colorPrice = parseFloat(matchingColor.price);
                                        }
                                    }
                                }
                                
                                const extraColors = currentColorCount - 1;
                                const colorCost = colorPrice * extraColors;
                                unitPrice += colorCost;
                            } catch (error) {
                                console.error('Erro ao buscar preços de cores:', error);
                            }
                        }
                        
                    }
                    
                    const selectedEffects = getSelectedEffects();
                    const effectsSurcharge = (apiType === 'SERIGRAFIA' || apiType === 'EMBORRACHADO') ? (parseFloat(data.price) * 0.5 * selectedEffects.length) : 0;
                    if (effectsSurcharge > 0) {
                        unitPrice += effectsSurcharge;
                    }
                    document.getElementById('effects_applied').value = selectedEffects.join(', ');

                    const total = unitPrice * qty;
                    
                    let formulaText = `R$ ${unitPrice.toFixed(2).replace('.', ',')} × ${qty} ${qty === 1 ? 'peça' : 'peças'}`;
                    
                    
                    document.getElementById('unitPrice').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
                    document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
                    document.getElementById('priceFormula').textContent = formulaText;
                    document.getElementById('unit_price').value = unitPrice;
                    document.getElementById('final_price').value = total;
                    document.getElementById('priceDisplay').classList.remove('hidden');
                } else {
                    showDefaultPrice(quantity, persType);
                }
            } catch (error) {
                console.error('Erro ao calcular preço:', error);
                showDefaultPrice(quantity, persType);
            }
        }
        window.calculatePrice = calculatePrice;

        function showDefaultPrice(quantity = null, persType = null) {
            if (persType === null) persType = document.getElementById('modal_personalization_type').value;
            if (quantity === null) quantity = parseInt(document.getElementById('quantity').value) || 0;
            const normalizedType = normalizeTypeKey(persType);
            
            const defaultPrices = {
                'SERIGRAFIA': 5.00,
                'BORDADO': 10.00,
                'EMBORRACHADO': 15.00,
                'SUBLIMAÇÃO': 8.00,
                'SUB. TOTAL': 2.50,
                'DTF': 4.00
            };
            
            let unitPrice = defaultPrices[normalizedType] || 5.00;
            const selectedEffects = getSelectedEffects();
            if (normalizedType === 'SERIGRAFIA' || normalizedType === 'EMBORRACHADO') {
                unitPrice += (unitPrice * 0.5 * selectedEffects.length);
            }
            document.getElementById('effects_applied').value = selectedEffects.join(', ');
            const baseSizeInput = document.getElementById('base_size_price');
            if (baseSizeInput) baseSizeInput.value = unitPrice;

            if (normalizedType === 'SUB. TOTAL') {
                const addonsSelect = document.getElementById('addons');
                let addonsTotal = 0;
                
                if (addonsSelect) {
                    const selectedAddons = Array.from(addonsSelect.selectedOptions);
                    const regataCheckbox = document.getElementById('regataCheckbox');
                    if (regataCheckbox && regataCheckbox.checked) {
                        addonsTotal += -3.00;
                    }
                    
                    selectedAddons.forEach(option => {
                        const addonId = parseInt(option.value);
                        const addonData = availableAddons.find(a => a.id === addonId);
                        if (addonData) {
                            addonsTotal += addonData.price_adjustment;
                        }
                    });
                }
                unitPrice += addonsTotal;
            }

            const total = unitPrice * quantity;
            document.getElementById('unitPrice').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('priceFormula').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')} × ${quantity} ${quantity === 1 ? 'peça' : 'peças'} (preço estimado)`;
            document.getElementById('unit_price').value = unitPrice;
            document.getElementById('final_price').value = total;
            document.getElementById('priceDisplay').classList.remove('hidden');
        }
        window.showDefaultPrice = showDefaultPrice;

        function displaySelectedFiles() {
            const fileInput = document.getElementById('art_files');
            const filesList = document.getElementById('selected_files_list');
            
            if (!fileInput || !filesList) return;
            
            if (fileInput.files.length > 0) {
                let html = '<div class="text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">Arquivos selecionados:</div>';
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                    html += `
                        <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-slate-800 px-2 py-1 rounded border border-gray-200 dark:border-slate-700">
                            <span class="truncate flex-1 text-gray-900 dark:text-white">📄 ${file.name}</span>
                            <span class="text-gray-500 dark:text-slate-400 ml-2">${sizeMB} MB</span>
                        </div>
                    `;
                }
                filesList.innerHTML = html;
            } else {
                filesList.innerHTML = '';
            }
        }
        window.displaySelectedFiles = displaySelectedFiles;

        async function updatePersonalizationsList() {
            try {
                showLoadingIndicator();
                const isEditing = {{ request()->routeIs('orders.edit.*') ? 'true' : 'false' }};
                const refreshUrl = isEditing 
                    ? '{{ route("orders.edit.customization") }}' 
                    : '{{ route("orders.wizard.customization.refresh") }}';
                
                const response = await fetch(refreshUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache'
                    }
                });
                
                if (response.ok) {
                    const html = await response.text();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    const newSummary = tempDiv.querySelector('.bg-gray-50.dark:bg-slate-800\\/50.rounded-lg.border.border-gray-200.dark:border-slate-700.p-5.mb-6');
                    if (newSummary) {
                        const currentSummary = document.querySelector('.bg-gray-50.dark:bg-slate-800\\/50.rounded-lg.border.border-gray-200.dark:border-slate-700.p-5.mb-6');
                        if (currentSummary) currentSummary.innerHTML = newSummary.innerHTML;
                    }
                    
                    const newItemsContainer = tempDiv.querySelector('.space-y-6');
                    if (newItemsContainer) {
                        const currentItemsContainer = document.querySelector('.space-y-6');
                        if (currentItemsContainer) currentItemsContainer.innerHTML = newItemsContainer.innerHTML;
                    }
                    
                    reapplyEventListeners();
                    hideLoadingIndicator();
                } else {
                    console.error('Erro ao atualizar lista:', response.status);
                    hideLoadingIndicator();
                    // Fallback to reload if AJAX fails
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erro ao atualizar lista:', error);
                hideLoadingIndicator();
                window.location.reload();
            }
        }
        window.updatePersonalizationsList = updatePersonalizationsList;

        function reapplyEventListeners() {
            document.removeEventListener('paste', window.handlePaste);
            document.addEventListener('paste', window.handlePaste);
            setupArtFilesDropzone();
        }
        window.reapplyEventListeners = reapplyEventListeners;

        function showLoadingIndicator() {
            const existing = document.querySelectorAll('.loading-indicator');
            existing.forEach(el => el.remove());
            
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-indicator fixed top-4 right-4 bg-[#7c3aed] text-white px-4 py-2 rounded-md shadow-lg z-50';
            loadingDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    <span>Atualizando...</span>
                </div>
            `;
            document.body.appendChild(loadingDiv);
        }
        window.showLoadingIndicator = showLoadingIndicator;

        function hideLoadingIndicator() {
            const indicators = document.querySelectorAll('.loading-indicator');
            indicators.forEach(el => el.remove());
        }
        window.hideLoadingIndicator = hideLoadingIndicator;

        function showSuccessMessage(message) {
            alert(message);
        }
        window.showSuccessMessage = showSuccessMessage;

        function removePersonalization(id) {
            pendingDeleteId = id;
            const modal = document.getElementById('deleteConfirmationModal');
            if (modal) {
                const infoEl = document.getElementById('delete-item-info');
                if (infoEl) infoEl.innerHTML = `Excluir personalização #${id}?`;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        window.removePersonalization = removePersonalization;

        function closeDeleteConfirmationModal() {
            const modal = document.getElementById('deleteConfirmationModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            pendingDeleteId = null;
        }
        window.closeDeleteConfirmationModal = closeDeleteConfirmationModal;

        async function confirmDeletePersonalization() {
            if (!pendingDeleteId) return;
            const id = pendingDeleteId;
            closeDeleteConfirmationModal();
            deletePersonalization(id);
        }
        window.confirmDeletePersonalization = confirmDeletePersonalization;

        function deletePersonalization(id) {
            const url = `/api/personalizations/${id}`;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao excluir');
                return data;
            })
            .then(data => {
                if (data.success) {
                    updatePersonalizationsList();
                } else {
                    alert('Erro: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro ao excluir:', error);
                alert('Erro ao excluir: ' + error.message);
            });
        }
        window.deletePersonalization = deletePersonalization;

        async function editPersonalization(id) {
            try {
                const response = await fetch(`/api/personalizations/${id}`);
                const data = await response.json();
                
                if (!data.success) throw new Error(data.message || 'Erro ao carregar');
                
                const pers = data.personalization;
                const persType = pers.application_type;
                const itemId = pers.order_item_id;
                const normalizedType = normalizeTypeKey(persType);

                document.getElementById('modal_item_id').value = itemId;
                document.getElementById('modal_personalization_type').value = persType;
                document.getElementById('modal_personalization_id').value = '';
                document.getElementById('editing_personalization_id').value = id;
                
                const typeSetting = personalizationSettings[normalizedType] || personalizationSettings[persType];
                const useColorCharge = typeSetting && typeSetting.charge_by_color;
                
                if (useColorCharge || normalizedType === "SERIGRAFIA" || normalizedType === "EMBORRACHADO") {
                    document.getElementById('colorCountField').classList.remove('hidden');
                    const effectsField = document.getElementById('effectsField');
                    if (effectsField) effectsField.classList.remove('hidden');
                    
                    const appliedEffects = pers.effects_applied ? pers.effects_applied.split(',').map(e => e.trim()) : [];
                    ['effect_dourado','effect_prata','effect_neon'].forEach(effId => {
                        const el = document.getElementById(effId);
                        const effectName = effId.replace('effect_', '').charAt(0).toUpperCase() + effId.replace('effect_', '').slice(1);
                        if (el) el.checked = appliedEffects.includes(effectName);
                    });
                } else {
                    document.getElementById('colorCountField').classList.add('hidden');
                    const effectsField = document.getElementById('effectsField');
                    if (effectsField) effectsField.classList.add('hidden');
                }
                
                if (normalizedType === 'SUB. TOTAL') {
                    document.getElementById('locationField').classList.add('hidden');
                    document.getElementById('sizeField').classList.add('hidden');
                    document.getElementById('colorDetailsField').classList.add('hidden');
                    document.getElementById('addonsField').classList.remove('hidden');
                    loadAddons();
                } else {
                    document.getElementById('locationField').classList.remove('hidden');
                    document.getElementById('sizeField').classList.remove('hidden');
                    document.getElementById('colorDetailsField').classList.remove('hidden');
                    document.getElementById('addonsField').classList.add('hidden');
                }
                
                loadSizes(normalizedType);
                await new Promise(resolve => setTimeout(resolve, 150));
                
                if (pers.location_id) document.getElementById('location').value = pers.location_id;
                if (pers.size_name) document.getElementById('size').value = pers.size_name;
                if (pers.quantity !== null && pers.quantity !== undefined) document.getElementById('quantity').value = pers.quantity;
                if (pers.color_count !== null && pers.color_count !== undefined) document.getElementById('color_count').value = pers.color_count;
                if (pers.color_details) document.getElementById('color_details').value = pers.color_details;
                if (pers.seller_notes) document.getElementById('seller_notes').value = pers.seller_notes;

                const unitPriceVal = parseFloat(pers.unit_price ?? 0) || 0;
                const totalPriceVal = parseFloat(pers.final_price ?? 0) || 0;
                document.getElementById('unit_price').value = unitPriceVal;
                document.getElementById('final_price').value = totalPriceVal;

                const unitPriceEl = document.getElementById('unitPrice');
                const totalPriceEl = document.getElementById('totalPrice');
                const priceFormulaEl = document.getElementById('priceFormula');
                if (unitPriceEl && totalPriceEl) {
                    unitPriceEl.textContent = `R$ ${unitPriceVal.toFixed(2).replace('.', ',')}`;
                    totalPriceEl.textContent = `R$ ${totalPriceVal.toFixed(2).replace('.', ',')}`;
                    if (priceFormulaEl) {
                        const qty = parseInt(pers.quantity ?? 1, 10) || 1;
                        priceFormulaEl.textContent = `R$ ${unitPriceVal.toFixed(2).replace('.', ',')} × ${qty} ${qty === 1 ? 'peça' : 'peças'} (valor atual)`;
                    }
                    document.getElementById('priceDisplay').classList.remove('hidden');
                }
                
                document.getElementById('modalTitle').textContent = `Editar ${persType}`;
                document.getElementById('personalizationModal').classList.remove('hidden');
                
            } catch (error) {
                console.error('Erro ao editar:', error);
                alert('Erro ao carregar: ' + error.message);
            }
        }
        window.editPersonalization = editPersonalization;

        function initCustomizationPage() {
            if (typeof renderOrderArtPreview === 'function' && artItemSelect) {
                renderOrderArtPreview(artItemSelect.value);
            }

            ['location', 'size', 'color_count', 'quantity'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.removeEventListener('change', calculatePrice);
                    el.addEventListener('change', calculatePrice);
                }
            });

            const pForm = document.getElementById('personalizationForm');
            if (pForm) {
                pForm.removeEventListener('submit', handleFormSubmit);
                pForm.addEventListener('submit', handleFormSubmit);
            }

            document.removeEventListener('paste', handlePaste);
            document.addEventListener('paste', handlePaste);
            setupArtFilesDropzone();

            const artFilesEl = document.getElementById('art_files');
            if (artFilesEl) {
                artFilesEl.removeEventListener('change', displaySelectedFiles);
                artFilesEl.addEventListener('change', displaySelectedFiles);
            }
        }
        window.initCustomizationPage = initCustomizationPage;

        function handlePaste(e) {
            if (document.getElementById('personalizationModal').classList.contains('hidden')) return;
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    const fileInput = document.getElementById('application_image');
                    if (fileInput) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(blob);
                        fileInput.files = dataTransfer.files;
                        handleApplicationImageChange(fileInput);
                    }
                    break;
                }
            }
        }
        window.handlePaste = handlePaste;

        function setupArtFilesDropzone() {
            const dropzone = document.getElementById('art_files_dropzone');
            const input = document.getElementById('art_files');
            if (!dropzone || !input) return;

            ['dragenter', 'dragover'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.add('border-[#7c3aed]', 'bg-purple-100/30');
                });
            });

            ['dragleave', 'drop'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.remove('border-[#7c3aed]', 'bg-purple-100/30');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                input.files = dt.files;
                handleArtFilesChange(input);
            });
        }
        window.setupArtFilesDropzone = setupArtFilesDropzone;

        document.addEventListener('DOMContentLoaded', initCustomizationPage);

        if (!window._customizationInitSetup) {
            window._customizationInitSetup = true;
            document.addEventListener('ajax-content-loaded', () => {
                if (typeof window.initCustomizationPage === 'function') window.initCustomizationPage();
            });
        }
    })(window);
</script>
@endsection
```
