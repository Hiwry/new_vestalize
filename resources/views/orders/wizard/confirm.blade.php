@extends('layouts.admin')

@section('content')
<style>
    .confirm-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .confirm-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .step-indicator {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    }
    .status-badge {
        background: rgba(79, 70, 229, 0.1);
        color: var(--brand-primary);
        border: 1px solid rgba(79, 70, 229, 0.2);
    }
    .dark .status-badge {
        background: rgba(129, 140, 248, 0.1);
        color: #818cf8;
        border: 1px solid rgba(129, 140, 248, 0.2);
    }
</style>

<div class="confirm-page max-w-7xl mx-auto px-4 sm:px-0">
    <!-- Progress Bar -->
    <div class="mb-8 relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 step-indicator text-white rounded-2xl flex items-center justify-center text-lg font-black shadow-lg shadow-indigo-500/20 rotate-3 hover:rotate-0 transition-transform duration-300">
                    5
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Confirmação Final</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 font-medium">Revise os detalhes para iniciar a produção</p>
                </div>
            </div>
            <div class="flex items-center bg-white dark:bg-slate-800/50 px-4 py-2 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm">
                <div class="text-right mr-4">
                    <div class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-slate-500 font-bold">Progresso do Wizard</div>
                    <div class="text-xl font-black text-indigo-600 dark:text-indigo-400 leading-none">100%</div>
                </div>
                <div class="w-12 h-12 rounded-full border-4 border-indigo-100 dark:border-indigo-900/30 flex items-center justify-center relative">
                    <svg class="w-8 h-8 text-indigo-600" viewBox="0 0 36 36">
                        <path class="stroke-current text-indigo-600" stroke-dasharray="100, 100" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <i class="fa-solid fa-check absolute text-[10px] text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-full rounded-full w-full shadow-[0_0_15px_rgba(79,70,229,0.5)]"></div>
        </div>
    </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-300 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Principal - Resumo do Pedido -->
            <div class="lg:col-span-2 space-y-6">
                <div class="confirm-card rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                    <!-- Header -->
                    <div class="px-8 py-6 bg-gradient-to-r from-gray-50/50 to-transparent dark:from-slate-800/30 dark:to-transparent border-b border-gray-100 dark:border-slate-800">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                    <i class="fa-solid fa-file-invoice text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Detalhes do Pedido</h2>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-slate-800 text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">ID #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        <span class="status-badge px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest">Aguardando Confirmação</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        
                    <div class="px-8 py-8 space-y-10">
                        
                        <!-- ETAPA 1: Dados do Cliente -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                        <i class="fa-solid fa-user-check text-base"></i>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Informações do Cliente</h3>
                                </div>
                            </div>
 
                            <div class="bg-gray-50/50 dark:bg-slate-800/30 rounded-2xl p-6 border border-gray-100 dark:border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Nome Completo</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->client->name }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Contato Principal</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->client->phone_primary }}</p>
                                </div>
                                @if($order->client->email)
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">E-mail</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate" title="{{ $order->client->email }}">{{ $order->client->email }}</p>
                                </div>
                                @endif
                                @if($order->client->cpf_cnpj)
                                <div>
                                    <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Documento</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->client->cpf_cnpj }}</p>
                                </div>
                                @endif
                                @if($order->client->address)
                                <div class="md:col-span-2">
                                    <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Localização</span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white leading-relaxed">{{ $order->client->address }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- ETAPA 2: Itens de Costura/Produto -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                    <i class="fa-solid fa-shirt text-base"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Itens de Produção</h3>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                @foreach($order->items as $index => $item)
                                @php
                                    $isSubLocal = $item->print_type === 'Sublimação Local';
                                @endphp
                                <div class="bg-white dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-3 bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Item #0{{ $index + 1 }}</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-700 text-[10px] font-bold text-gray-500 dark:text-slate-300 border border-gray-100 dark:border-slate-600 uppercase">{{ $isSubLocal ? 'Pronta Entrega' : 'Personalizado' }}</span>
                                    </div>
                                    
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                            <!-- Info do Item -->
                                            <div class="md:col-span-3 space-y-6">
                                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                                    <div>
                                                        <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Processo</span>
                                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->print_type }}</p>
                                                    </div>
                                                    @if($isSubLocal)
                                                    <div>
                                                        <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Modelo</span>
                                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->model }}</p>
                                                    </div>
                                                    @else
                                                    <div>
                                                        <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Tecido</span>
                                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->fabric }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Coloração</span>
                                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->color }}</p>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(!$isSubLocal)
                                                        @if($item->collar)
                                                        <div>
                                                            <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Gola</span>
                                                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->collar }}</p>
                                                        </div>
                                                        @endif
                                                        @if($item->detail)
                                                        <div>
                                                            <span class="text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Punho/Vira</span>
                                                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ $item->detail }}</p>
                                                        </div>
                                                        @endif
                                                    @endif
                                                </div>

                                                <!-- Tamanhos Horizontal -->
                                                <div class="bg-gray-50/50 dark:bg-slate-900/50 rounded-xl p-4 border border-gray-100 dark:border-slate-800">
                                                    <span class="text-[9px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-3">Distribuição de Tamanhos</span>
                                                    <div class="flex flex-wrap gap-2">
                                                        @php
                                                            $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                                                            $itemSizes = $itemSizes ?? [];
                                                            $availableSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'un'];
                                                        @endphp
                                                        @foreach($availableSizes as $size)
                                                            @php
                                                                $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? $itemSizes[strtoupper($size)] ?? 0;
                                                                $qty = (int)$qty;
                                                            @endphp
                                                            @if($qty > 0)
                                                            <div class="flex items-center bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-lg overflow-hidden shadow-sm">
                                                                <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-[10px] font-black text-indigo-600 dark:text-indigo-300 border-r border-gray-100 dark:border-slate-700 uppercase">{{ $size }}</span>
                                                                <span class="px-2 py-1 text-xs font-black text-gray-900 dark:text-white">{{ $qty }}</span>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                        <div class="flex items-center bg-indigo-600 text-white rounded-lg shadow-md px-3 py-1 ml-auto">
                                                            <span class="text-[10px] font-black mr-2">TOTAL</span>
                                                            <span class="text-sm font-black">{{ $item->quantity }} UN</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preço lateral -->
                                            <div class="flex flex-col justify-center items-end border-l border-gray-100 dark:border-slate-800 pl-6 h-full">
                                                <span class="text-[9px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Subtotal Item</span>
                                                <div class="text-right">
                                                    <p class="text-[10px] text-gray-500 line-through">R$ {{ number_format(($item->unit_price + ($item->sublimations->sum('final_price') / max($item->quantity, 1))) * 1.1, 2, ',', '.') }}</p>
                                                    <p class="text-lg font-black text-indigo-600 dark:text-indigo-400 leading-none">R$ {{ number_format($item->unit_price * $item->quantity + $item->sublimations->sum('final_price'), 2, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                <!-- ETAPA 3: Personalização -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-palette text-base"></i>
                         </div>
                         <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Personalização e Artes</h3>
                    </div>

                    @foreach($order->items as $index => $item)
                    @if($item->sublimations->count() > 0)
                    <div class="bg-white dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Configuração Arte Item #0{{ $index + 1 }}</span>
                        </div>
                        
                        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Lado Esquerdo: Imagem e Nome -->
                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2 block">Nome Identificador da Arte</label>
                                    @php
                                        $artName = $item->sublimations->first()->art_name ?? null;
                                    @endphp
                                    <div class="relative">
                                        <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                                        <input type="text" 
                                               name="items[{{ $item->id }}][art_name]" 
                                               value="{{ $artName ?? $item->art_name }}" 
                                               form="finalize-form"
                                               class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                                               placeholder="Ex: Uniforme Interclasse 2024">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 block">Visualização (Mockup/Layout)</label>
                                    @php
                                        $imageUrl = $item->cover_image_url;
                                        $imageExists = !empty($imageUrl);
                                    @endphp
                                    <div class="relative group aspect-[4/3] rounded-2xl overflow-hidden bg-gray-50 dark:bg-slate-900 border-2 border-dashed border-gray-200 dark:border-slate-700 hover:border-indigo-400 transition-colors flex flex-col items-center justify-center p-4">
                                        <img id="preview-img-{{ $item->id }}" 
                                             src="{{ $imageUrl ?? '' }}" 
                                             alt="Capa" 
                                             class="max-w-full max-h-full object-contain {{ $imageExists ? '' : 'hidden' }} drop-shadow-2xl">
                                        
                                        <div id="no-image-{{ $item->id }}" class="{{ $imageExists ? 'hidden' : '' }} text-center">
                                            <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mx-auto mb-3">
                                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-indigo-400"></i>
                                            </div>
                                            <p class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-wider">Clique para enviar imagem</p>
                                        </div>

                                        <input type="file" 
                                               id="cover-input-{{ $item->id }}"
                                               name="items[{{ $item->id }}][cover_image]" 
                                               form="finalize-form"
                                               accept="image/*"
                                               class="absolute inset-0 opacity-0 cursor-pointer"
                                               onchange="previewImage(this, '{{ $item->id }}')">
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-2 italic text-center">Formatos aceitos: JPG, PNG, PDF (Máx 10MB)</p>
                                </div>
                            </div>

                            <!-- Lado Direito: Aplicações -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Aplicações Registradas</label>
                                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-indigo-200 dark:scrollbar-thumb-slate-700">
                                    @foreach($item->sublimations as $sub)
                                    @php
                                        $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                                        $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                        $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                    @endphp
                                    <div class="bg-gray-50/80 dark:bg-slate-900/50 border border-gray-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-4 group/sub hover:border-indigo-300 transition-colors">
                                        <div class="w-12 h-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-xs shrink-0">
                                            {{ substr($appType, 0, 3) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="text-xs font-black text-gray-900 dark:text-white truncate">{{ $locationName }}</h4>
                                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">R$ {{ number_format($sub->final_price, 2, ',', '.') }}</span>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded border border-gray-100 dark:border-slate-700">Dimensões: {{ $sizeName }}</span>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded border border-gray-100 dark:border-slate-700">{{ $sub->color_count }} Cores {{ $sub->has_neon ? '+ Neon' : '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                        @if($item->files->count() > 0)
                        <div>
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2">Arquivos da Arte:</span>
                            <div class="space-y-1">
                                @foreach($item->files as $file)
                                        <div class="flex items-center text-xs text-gray-700 dark:text-slate-400">
                                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $file->file_name }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Resumo Total do Item -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="w-5 h-5 rounded-md flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-medium text-gray-900 dark:text-white">Resumo Total - Item {{ $index + 1 }}</h2>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                        @php
                            $costuraSubtotal = $item->unit_price * $item->quantity;
                            $personalizacaoSubtotal = $item->sublimations->sum('final_price');
                            $itemTotal = $costuraSubtotal + $personalizacaoSubtotal;
                            
                            // Calcular valor unitário por camisa
                            $costuraUnitaria = $item->unit_price;
                            $personalizacaoUnitaria = $item->quantity > 0 ? ($personalizacaoSubtotal / $item->quantity) : 0;
                            $valorPorCamisa = $costuraUnitaria + $personalizacaoUnitaria;
                        @endphp
                        
                        <!-- Resumo Financeiro do Item -->
                        <div class="mt-8 pt-8 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-6">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Unitário Base</span>
                                        <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($costuraUnitaria, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-100 dark:bg-slate-800"></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Aplicações</span>
                                        <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">R$ {{ number_format($personalizacaoUnitaria, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-100 dark:bg-slate-800"></div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Por Peça</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($valorPorCamisa, 2, ',', '.') }}</span>
                                            <span class="text-[9px] font-black bg-gray-100 dark:bg-slate-800 text-gray-500 px-1.5 py-0.5 rounded">x{{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-indigo-50 dark:bg-indigo-900/20 px-6 py-4 rounded-2xl border border-indigo-100 dark:border-indigo-900/50">
                                    <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block mb-1">Total do Item</span>
                                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter tabular-nums leading-none">R$ {{ number_format($itemTotal, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
                @endforeach

                <!-- ETAPA 4: Pagamento -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <i class="fa-solid fa-credit-card text-base"></i>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Fluxo de Pagamento</h3>
                    </div>

                    @if($payment && $payment->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 p-6">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Métodos Utilizados</span>
                            <div class="space-y-3">
                                @foreach($payment as $paymentItem)
                                <div class="flex items-center justify-between p-3 bg-gray-50/50 dark:bg-slate-900 border border-gray-100 dark:border-slate-700 rounded-xl">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 flex items-center justify-center text-indigo-500 shadow-sm border border-gray-100 dark:border-slate-700">
                                            @php
                                                $method = strtolower($paymentItem->payment_method);
                                                $icon = 'fa-solid fa-money-bill-wave';
                                                if(str_contains($method, 'pix')) $icon = 'fa-brands fa-pix';
                                                elseif(str_contains($method, 'cartão') || str_contains($method, 'credito') || str_contains($method, 'debito')) $icon = 'fa-solid fa-credit-card';
                                                elseif(str_contains($method, 'dinheiro')) $icon = 'fa-solid fa-money-bill-1-wave';
                                            @endphp
                                            <i class="{{ $icon }} text-xs"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white capitalize">{{ $paymentItem->payment_method }}</span>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $paymentItem->entry_date ? \Carbon\Carbon::parse($paymentItem->entry_date)->format('d/M/Y') : 'Data Indefinida' }}</span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($paymentItem->entry_amount, 2, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-indigo-600 dark:bg-indigo-500 rounded-2xl p-6 text-white shadow-xl shadow-indigo-500/20 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10">
                                <span class="text-[10px] font-black text-indigo-100 uppercase tracking-widest block mb-1 opacity-80">Status Financeiro</span>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-2xl font-black italic tracking-tighter">CONSOLIDADO</h4>
                                    <i class="fa-solid fa-shield-check text-indigo-200"></i>
                                </div>
                            </div>
                            @php
                                $totalPago = 0;
                                foreach($payment as $p) {
                                    if($p->payment_methods && is_array($p->payment_methods) && count($p->payment_methods) > 0) {
                                        foreach($p->payment_methods as $method) $totalPago += floatval($method['amount'] ?? 0);
                                    } else {
                                        $totalPago += floatval($p->entry_amount ?? 0);
                                    }
                                }
                                $saldoRestante = $order->total - $totalPago;
                            @endphp
                            <div class="relative z-10 mt-8 pt-4 border-t border-white/20">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <span class="text-[10px] text-indigo-100 uppercase font-bold opacity-70">Total Recebido</span>
                                        <p class="text-2xl font-black tabular-nums">R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-indigo-100 uppercase font-bold opacity-70">{{ $saldoRestante > 0 ? 'Pendente' : 'Troco/Crédito' }}</span>
                                        <p class="text-lg font-black tabular-nums {{ $saldoRestante > 0 ? 'text-orange-200' : 'text-green-200' }}">R$ {{ number_format(abs($saldoRestante), 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800/30 rounded-2xl p-6 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-orange-900 dark:text-orange-200 uppercase tracking-wider">Atenção Necessária</h4>
                            <p class="text-xs text-orange-700 dark:text-orange-300/80 leading-relaxed mt-1">Este pedido ainda não possui informações de pagamento vinculadas. É altamente recomendado revisar a etapa financeira antes de finalizar.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
                        @else
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-md p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-orange-900 dark:text-orange-100">Nenhum pagamento registrado</p>
                                        <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                            Este pedido ainda não possui informações de pagamento. 
                                            Clique em "Voltar para Pagamento" abaixo para adicionar as formas de pagamento.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
            <!-- Coluna Lateral - Checkout Premium -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-gray-200/50 dark:shadow-black/40 border border-gray-100 dark:border-slate-800 sticky top-6 overflow-hidden">
                    <!-- Header do Checkout -->
                    <div class="px-8 py-8 bg-gradient-to-br from-indigo-50/50 to-white dark:from-slate-800/50 dark:to-slate-900 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                                <i class="fa-solid fa-cart-shopping text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Checkout</h3>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Resumo da Ordem #{{ $order->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Composição de Custos -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center group">
                                <span class="text-xs font-black text-gray-400 uppercase tracking-wider group-hover:text-indigo-500 transition-colors">Subtotal Base</span>
                                <span class="text-sm font-black text-gray-900 dark:text-white">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>

                            @if(!empty($sizeSurcharges))
                            <div class="space-y-2 pl-4 border-l-2 border-orange-100 dark:border-slate-800">
                                @foreach($sizeSurcharges as $size => $surcharge)
                                <div class="flex justify-between items-center italic">
                                    <span class="text-[10px] font-bold text-orange-400 uppercase">Extra {{ $size }}</span>
                                    <span class="text-[11px] font-black text-orange-600 dark:text-orange-400">+ R$ {{ number_format($surcharge, 2, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($order->delivery_fee > 0)
                            <div class="flex justify-between items-center group">
                                <span class="text-xs font-black text-gray-400 uppercase tracking-wider group-hover:text-indigo-500 transition-colors">Serviço Entrega</span>
                                <span class="text-sm font-black text-gray-900 dark:text-white">+ R$ {{ number_format($order->delivery_fee, 2, ',', '.') }}</span>
                            </div>
                            @endif

                            <div class="pt-6 border-t-2 border-dashed border-gray-100 dark:border-slate-800">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] block mb-1">Total Final</span>
                                        <p class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter tabular-nums">
                                            R$ {{ number_format($order->total, 2, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="text-right pb-1">
                                        <span class="text-[9px] font-black text-green-500 uppercase bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">Protegido</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configurações e Finalização -->
                        <div class="space-y-4">
                            <form method="POST" action="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.finalize') : route('orders.wizard.finalize') }}" id="finalize-form" onsubmit="return handleFinalize(this)" enctype="multipart/form-data">
                                @csrf
                                
                                <label class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-800/40 rounded-3xl border border-transparent hover:border-indigo-200 dark:hover:border-indigo-900/50 transition-all cursor-pointer group mb-6">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" name="is_event" value="1" 
                                               {{ old('is_event', ($order->is_event ?? false)) ? 'checked' : '' }}
                                               class="peer hidden">
                                        <div class="w-6 h-6 rounded-xl border-2 border-gray-200 dark:border-slate-700 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                            <i class="fa-solid fa-bolt text-[10px] text-white opacity-0 peer-checked:opacity-100"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">Prioridade Evento</span>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Status de produção acelerada</p>
                                    </div>
                                </label>

                                <div class="space-y-3">
                                    <button type="submit" id="finalize-btn"
                                            class="w-full group relative flex items-center justify-center gap-3 px-8 py-5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-[2rem] font-black uppercase text-xs tracking-[0.15em] shadow-2xl shadow-indigo-600/30 hover:scale-[1.02] active:scale-95 transition-all duration-300">
                                        <span id="finalize-text" class="flex items-center gap-2">
                                            Confirmar e Produzir <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1 transition-transform"></i>
                                        </span>
                                        <span id="finalize-loading" class="hidden flex items-center gap-2">
                                            <i class="fa-solid fa-spinner fa-spin"></i> Processando...
                                        </span>
                                    </button>

                                    <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment') }}" 
                                       class="w-full flex items-center justify-center gap-2 px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-indigo-500 transition-colors">
                                        <i class="fa-solid fa-chevron-left text-[8px]"></i> Ajustar Pagamento
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Info de Segurança -->
                        <div class="flex items-center justify-center gap-4 pt-4 opacity-30">
                            <i class="fa-solid fa-lock text-sm"></i>
                            <div class="h-px w-8 bg-gray-400"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest">Ambiente Seguro</span>
                            <div class="h-px w-8 bg-gray-400"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        let formSubmitted = false;

        function handleFinalize(form) {
            if (formSubmitted) {
                return false;
            }

            // Mostrar modal de confirmação
            openConfirmModal();
            return false;
        }

        function confirmFinalize() {
            formSubmitted = true;
            
            // Fechar modal
            closeConfirmModal();
            
            // Desabilitar botão e mostrar loading
            const btn = document.getElementById('finalize-btn');
            const text = document.getElementById('finalize-text');
            const loading = document.getElementById('finalize-loading');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.classList.add('hidden');
            loading.classList.remove('hidden');

            // Enviar o formulário após um pequeno delay para mostrar o loading
            setTimeout(() => {
                document.getElementById('finalize-form').submit();
            }, 500);
        }

        function openConfirmModal() {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openAlertModal(title, message) {
            document.getElementById('alertModalTitle').textContent = title;
            document.getElementById('alertModalMessage').textContent = message;
            document.getElementById('alertModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAlertModal() {
            document.getElementById('alertModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function previewImage(input, itemId) {
            const previewContainer = document.getElementById(`preview-container-${itemId}`);
            const noImageContainer = document.getElementById(`no-image-${itemId}`);
            const previewImg = document.getElementById(`preview-img-${itemId}`);
            const previewText = document.getElementById(`preview-text-${itemId}`);

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    noImageContainer.classList.add('hidden');
                    if (previewText) previewText.innerText = "Nova Imagem Selecionada";
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Verificar eventos ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            
            // Prevenir múltiplos envios
            const finalizeForm = document.getElementById('finalize-form');
            if (finalizeForm) {
                finalizeForm.addEventListener('submit', function(e) {
                    if (formSubmitted) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
            
            // Fechar modais ao clicar fora
            const confirmModal = document.getElementById('confirmModal');
            if (confirmModal) {
                confirmModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeConfirmModal();
                    }
                });
            }
            
            const alertModal = document.getElementById('alertModal');
            if (alertModal) {
                alertModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAlertModal();
                    }
                });
            }
            
            // Fechar modais com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                    closeAlertModal();
                }
            });
        });
</script>
@endpush

<!-- Modal de Confirmação Premium -->
<div id="confirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-[2.5rem] max-w-md w-full shadow-2xl overflow-hidden scale-95 transition-transform duration-300">
        <div class="p-10 text-center">
            <div class="w-20 h-20 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mx-auto mb-8">
                <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-4">Lançar em Produção?</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest mb-10">O pedido será enviado para a fila do Kanban imediatamente.</p>
            
            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="closeConfirmModal()" 
                        class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors border border-gray-100 dark:border-slate-800">
                    Cancelar
                </button>
                <button type="button" onclick="confirmFinalize()" 
                        class="px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-600/30 hover:scale-[1.05] transition-transform">
                    Sim, Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Alerta Premium -->
<div id="alertModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-[2.5rem] max-w-sm w-full shadow-2xl overflow-hidden">
        <div class="p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center mx-auto mb-6 text-orange-600">
                <i class="fa-solid fa-circle-exclamation text-2xl"></i>
            </div>
            <h3 id="alertModalTitle" class="text-xl font-black text-gray-900 dark:text-white tracking-tight mb-2">Atenção</h3>
            <p id="alertModalMessage" class="text-xs text-gray-500 dark:text-slate-400 font-bold leading-relaxed mb-6"></p>
            
            <button type="button" onclick="closeAlertModal()" 
                    class="w-full py-4 bg-gray-900 dark:bg-slate-800 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-colors">
                Entendi
            </button>
        </div>
    </div>
</div>
@endsection
