@extends('layouts.admin')

@section('content')
<style>
/* Animações Premium */
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
@keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
@keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); } 50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.6); } }
@keyframes float { 0%, 100% { transform: translateY(0) rotate(3deg); } 50% { transform: translateY(-5px) rotate(0deg); } }
@keyframes checkmark { 0% { stroke-dashoffset: 100; } 100% { stroke-dashoffset: 0; } }

.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-slide-left { animation: slideInLeft 0.5s ease-out forwards; }
.animate-slide-right { animation: slideInRight 0.5s ease-out forwards; }
.animate-scale-in { animation: scaleIn 0.4s ease-out forwards; }
.animate-float { animation: float 3s ease-in-out infinite; }
.animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

.delay-100 { animation-delay: 0.1s; opacity: 0; }
.delay-200 { animation-delay: 0.2s; opacity: 0; }
.delay-300 { animation-delay: 0.3s; opacity: 0; }
.delay-400 { animation-delay: 0.4s; opacity: 0; }
.delay-500 { animation-delay: 0.5s; opacity: 0; }

.confirm-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.4); }
.dark .confirm-card { background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); }
.step-indicator { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
.status-badge { background: rgba(79, 70, 229, 0.1); color: #6366f1; border: 1px solid rgba(79, 70, 229, 0.2); }
.dark .status-badge { background: rgba(129, 140, 248, 0.15); color: #818cf8; border: 1px solid rgba(129, 140, 248, 0.25); }
.hover-lift { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.hover-lift:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -15px rgba(0,0,0,0.15); }
.validation-ok { border-color: #10b981 !important; }
.validation-ok::after { content: '✓'; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #10b981; font-weight: bold; }
.validation-warning { border-color: #f59e0b !important; }

@media (max-width: 640px) {
    .confirm-card { border-radius: 1.5rem !important; }
    .confirm-card .px-8 { padding-left: 1rem !important; padding-right: 1rem !important; }
    .confirm-card .py-8, .confirm-card .py-6 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .confirm-card .p-6, .confirm-card .p-8 { padding: 1rem !important; }
}
</style>

<div class="confirm-page max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-0">
    <!-- Progress Bar Premium -->
    <div class="mb-6 sm:mb-8 relative animate-fade-in-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 step-indicator text-white rounded-xl sm:rounded-2xl flex items-center justify-center text-base sm:text-lg font-black shadow-xl shadow-indigo-500/30 animate-float">
                    5
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Confirmação <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Final</span></h1>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400 font-medium">Revise os detalhes para iniciar a produção</p>
                </div>
            </div>
            <div class="flex items-center bg-white/80 dark:bg-slate-800/60 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-slate-700 shadow-lg animate-slide-right">
                <div class="text-right mr-3 sm:mr-4">
                    <div class="text-[9px] sm:text-[10px] uppercase tracking-wider text-gray-400 dark:text-slate-500 font-bold">Wizard</div>
                    <div class="text-lg sm:text-xl font-black text-indigo-600 dark:text-indigo-400 leading-none">100%</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-4 border-indigo-100 dark:border-indigo-900/30 flex items-center justify-center relative animate-pulse-glow">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 36 36">
                        <path class="stroke-indigo-600" stroke-dasharray="100, 100" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <i class="fa-solid fa-check absolute text-[10px] sm:text-xs text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-1.5 sm:h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 h-full rounded-full w-full shadow-[0_0_20px_rgba(99,102,241,0.5)]"></div>
        </div>
    </div>

        <!-- Messages Premium -->
        @if(session('success'))
        <div class="mb-4 sm:mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800/50 rounded-xl sm:rounded-2xl p-4 animate-fade-in-up">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-green-500 flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-check text-sm sm:text-base"></i>
                </div>
                <p class="text-xs sm:text-sm font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 sm:mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800/50 rounded-xl sm:rounded-2xl p-4 animate-fade-in-up">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-red-500 flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-xmark text-sm sm:text-base"></i>
                </div>
                <p class="text-xs sm:text-sm font-bold text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8">
            <!-- Coluna Principal - Resumo do Pedido -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <div class="confirm-card rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden animate-fade-in-up delay-100">
                    <!-- Header -->
                    <div class="px-4 sm:px-8 py-4 sm:py-6 bg-gradient-to-r from-gray-50/50 to-transparent dark:from-slate-800/30 dark:to-transparent border-b border-gray-100 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
                                    <i class="fa-solid fa-file-invoice text-base sm:text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Detalhes do Pedido</h2>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-slate-800 text-[9px] sm:text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">ID #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        <span class="status-badge px-2 py-0.5 rounded-lg text-[9px] sm:text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                            Aguardando
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                        
                    <div class="px-4 sm:px-8 py-6 sm:py-8 space-y-8 sm:space-y-10">
                        
                        <!-- ETAPA 1: Dados do Cliente -->
                        <div class="space-y-4 animate-fade-in-up delay-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30">
                                        <i class="fa-solid fa-user-check text-sm sm:text-base"></i>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Informações do Cliente</h3>
                                </div>
                                <span class="text-[9px] font-black text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg uppercase tracking-widest hidden sm:inline-flex items-center gap-1">
                                    <i class="fa-solid fa-check-circle"></i> Verificado
                                </span>
                            </div>
 
                            <div class="bg-gray-50/50 dark:bg-slate-800/30 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 hover-lift">
                                <div>
                                    <span class="text-[9px] sm:text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Nome Completo</span>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $order->client->name }}</p>
                                </div>
                                <div>
                                    <span class="text-[9px] sm:text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Contato Principal</span>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <i class="fa-brands fa-whatsapp text-green-500"></i>
                                        {{ $order->client->phone_primary }}
                                    </p>
                                </div>
                                @if($order->client->email)
                                <div>
                                    <span class="text-[9px] sm:text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">E-mail</span>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate" title="{{ $order->client->email }}">{{ $order->client->email }}</p>
                                </div>
                                @endif
                                @if($order->client->cpf_cnpj)
                                <div>
                                    <span class="text-[9px] sm:text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Documento</span>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $order->client->cpf_cnpj }}</p>
                                </div>
                                @endif
                                @if($order->client->address)
                                <div class="sm:col-span-2">
                                    <span class="text-[9px] sm:text-[10px] uppercase font-black text-gray-400 dark:text-slate-500 tracking-widest block mb-1">Localização</span>
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-relaxed flex items-start gap-2">
                                        <i class="fa-solid fa-location-dot text-indigo-500 mt-0.5"></i>
                                        {{ $order->client->address }}
                                    </p>
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
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" onclick="closeConfirmModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-[2rem] sm:rounded-[2.5rem] max-w-lg w-full shadow-2xl overflow-hidden animate-scale-in border border-gray-100 dark:border-slate-800">
        <!-- Header com Gradiente -->
        <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-6 sm:p-8 text-white text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
            </div>
            <div class="relative z-10">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-4 animate-pulse-glow">
                    <i class="fa-solid fa-rocket text-2xl sm:text-3xl"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-black tracking-tight">Lançar em Produção?</h3>
                <p class="text-xs sm:text-sm font-medium opacity-90 mt-1">O pedido será enviado para a fila do Kanban</p>
            </div>
        </div>
        
        <!-- Checklist de Validação -->
        <div class="p-4 sm:p-6 space-y-3 border-b border-gray-100 dark:border-slate-800">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Checklist de Validação</p>
            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                <div class="flex items-center gap-2 p-2 sm:p-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                    <i class="fa-solid fa-circle-check text-green-500 text-xs"></i>
                    <span class="text-[10px] sm:text-xs font-bold text-green-700 dark:text-green-400">Cliente OK</span>
                </div>
                <div class="flex items-center gap-2 p-2 sm:p-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                    <i class="fa-solid fa-circle-check text-green-500 text-xs"></i>
                    <span class="text-[10px] sm:text-xs font-bold text-green-700 dark:text-green-400">Itens OK</span>
                </div>
                <div class="flex items-center gap-2 p-2 sm:p-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                    <i class="fa-solid fa-circle-check text-green-500 text-xs"></i>
                    <span class="text-[10px] sm:text-xs font-bold text-green-700 dark:text-green-400">Artes OK</span>
                </div>
                <div id="payment-check" class="flex items-center gap-2 p-2 sm:p-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30">
                    <i class="fa-solid fa-circle-check text-green-500 text-xs"></i>
                    <span class="text-[10px] sm:text-xs font-bold text-green-700 dark:text-green-400">Pagamento OK</span>
                </div>
            </div>
        </div>
        
        <!-- Botões -->
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <button type="button" onclick="closeConfirmModal()" 
                        class="px-4 sm:px-6 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-all border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 active:scale-95">
                    <i class="fa-solid fa-xmark mr-1"></i> Cancelar
                </button>
                <button type="button" onclick="confirmFinalize()" 
                        class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-lg shadow-indigo-600/30 hover:shadow-xl hover:shadow-indigo-600/40 hover:scale-[1.02] active:scale-95 transition-all">
                    <i class="fa-solid fa-check mr-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Alerta Premium -->
<div id="alertModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" onclick="closeAlertModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-[2rem] sm:rounded-[2.5rem] max-w-sm w-full shadow-2xl overflow-hidden animate-scale-in border border-gray-100 dark:border-slate-800">
        <div class="p-6 sm:p-8 text-center">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-orange-100 to-amber-100 dark:from-orange-900/30 dark:to-amber-900/30 flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <i class="fa-solid fa-triangle-exclamation text-xl sm:text-2xl text-orange-500"></i>
            </div>
            <h3 id="alertModalTitle" class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight mb-2">Atenção</h3>
            <p id="alertModalMessage" class="text-xs sm:text-sm text-gray-500 dark:text-slate-400 font-medium leading-relaxed mb-6"></p>
            
            <button type="button" onclick="closeAlertModal()" 
                    class="w-full py-3 sm:py-4 bg-gray-900 dark:bg-slate-800 text-white rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest hover:bg-black dark:hover:bg-slate-700 transition-all active:scale-95">
                <i class="fa-solid fa-check mr-1"></i> Entendi
            </button>
        </div>
    </div>
</div>
@endsection

