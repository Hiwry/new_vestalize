@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm font-bold uppercase tracking-widest" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('marketplace.home') }}" class="text-gray-400 hover:text-primary transition-colors">Marketplace</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 mx-2"></i>
                        <a href="{{ route('marketplace.tools.index') }}" class="text-gray-400 hover:text-primary transition-colors">Loja de Ferramentas</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 mx-2"></i>
                        <span class="text-gray-500 dark:text-gray-300">{{ $tool->category_label }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left Column: Media & Info -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Gallery -->
                <div x-data="{ activeImage: '{{ $tool->cover_image }}' }" class="space-y-4">
                    <div class="aspect-video rounded-[2.5rem] overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700">
                        <img :src="activeImage" class="w-full h-full object-cover transition-all duration-500" alt="{{ $tool->title }}">
                    </div>
                    
                    @if($tool->images->count() > 1)
                    <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                        @foreach($tool->images as $image)
                        <button @click="activeImage = '{{ asset('storage/' . $image->path) }}'" 
                                class="flex-shrink-0 w-24 h-24 rounded-2xl overflow-hidden border-4 transition-all"
                                :class="activeImage === '{{ asset('storage/' . $image->path) }}' ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img src="{{ asset('storage/' . $image->path) }}" class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 shadow-lg border border-gray-100 dark:border-gray-700">
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-4">{{ $tool->title }}</h1>
                    
                    <div class="flex flex-wrap gap-4 mb-8">
                        <span class="px-4 py-2 bg-gray-50 dark:bg-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 border border-gray-100 dark:border-gray-700">
                            <i class="fa-solid fa-file-export mr-2 text-primary"></i> Formato: {{ $tool->file_format }}
                        </span>
                        <span class="px-4 py-2 bg-gray-50 dark:bg-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 border border-gray-100 dark:border-gray-700">
                            <i class="fa-solid fa-weight-hanging mr-2 text-primary"></i> {{ $tool->formatted_size }}
                        </span>
                        <span class="px-4 py-2 bg-gray-50 dark:bg-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 border border-gray-100 dark:border-gray-700">
                            <i class="fa-solid fa-download mr-2 text-primary"></i> {{ $tool->total_downloads }} Downloads
                        </span>
                    </div>

                    <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 font-medium leading-relaxed">
                        {!! nl2br(e($tool->description)) !!}
                    </div>
                </div>

                <!-- Usage Terms -->
                <div class="p-8 bg-gray-100 dark:bg-gray-800/50 rounded-3xl border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i> Licença de Uso
                    </h3>
                    <p class="text-sm text-gray-500 font-medium">Ao adquirir esta ferramenta, você recebe uma licença comercial vitalícia. Você pode usar este arquivo em seus projetos próprios ou de clientes, mas não pode revendê-lo ou distribuí-lo como seu.</p>
                </div>
            </div>

            <!-- Right Column: Sidebar Checkout -->
            <div class="space-y-6">
                
                <div class="sticky top-24 space-y-6">
                    <!-- Purchase Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                        <div class="absolute top-0 right-0 bg-indigo-600 text-white px-6 py-2 rounded-bl-3xl font-black text-[10px] uppercase tracking-widest">
                            Digital Tool
                        </div>
                        
                        <div class="mb-8">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Preço Único</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-5xl font-black text-gray-900 dark:text-white">{{ $tool->price_credits }}</span>
                                <span class="text-lg font-bold text-gray-400 uppercase">Créditos</span>
                            </div>
                        </div>

                        @auth
                            @if($hasPurchased)
                                <div class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-6 text-center mb-4">
                                     <p class="text-xs text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-widest mb-4">Você já possui este item!</p>
                                     <a href="{{ route('marketplace.tools.download', $tool->id) }}" class="block w-full py-4 bg-emerald-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                                        Fazer Download <i class="fa-solid fa-download ml-2"></i>
                                     </a>
                                </div>
                            @elseif($userWallet && $userWallet->hasBalance($tool->price_credits))
                                <button onclick="document.getElementById('buy-tool-modal').classList.remove('hidden')" 
                                        class="w-full py-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-indigo-600/20 transition-all active:scale-95 mb-4">
                                    Adquirir Agora
                                </button>
                                <p class="text-center text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
                                    <i class="fa-solid fa-wallet mr-1"></i> Seu saldo: {{ $userWallet->balance }} créditos
                                </p>
                            @else
                                <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30 rounded-2xl p-4 mb-4">
                                     <p class="text-xs text-amber-700 dark:text-amber-400 font-bold mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Saldo insuficiente ({{ $userWallet?->balance ?? 0 }} credits)
                                     </p>
                                     <a href="{{ route('marketplace.credits.index') }}" class="block w-full text-center py-3 bg-amber-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all">
                                        Comprar Créditos
                                     </a>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center py-6 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl transition-all active:scale-95 mb-4">
                                Entrar para Comprar
                            </a>
                        @endauth

                        <div class="border-t border-gray-100 dark:border-gray-700 mt-6 pt-6 text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Acesso Imediato</p>
                            <p class="text-[9px] text-gray-500">O download é liberado logo após a confirmação.</p>
                        </div>
                    </div>

                    <!-- Tech Specs -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-xs font-black uppercase text-gray-400 mb-6 tracking-widest">Especificações</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-gray-400">Versão</span>
                                <span class="text-gray-900 dark:text-white">v1.2</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-gray-400">Última Atualização</span>
                                <span class="text-gray-900 dark:text-white">{{ $tool->updated_at->format('M Y') }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-gray-400">Compatibilidade</span>
                                <span class="text-gray-900 dark:text-white">Ai, PSD, CDR</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Modal -->
@auth
<div id="buy-tool-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 w-full max-w-xl shadow-2xl border border-white/10">
        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-6">Confirmar Compra</h3>
        
        <form action="{{ route('marketplace.orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="tool">
            <input type="hidden" name="id" value="{{ $tool->id }}">

            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between mb-4">
                    <span class="text-gray-500 font-bold">Ferramenta:</span>
                    <span class="text-gray-900 dark:text-white font-black">{{ $tool->title }}</span>
                </div>
                <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <span class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest text-xs">Total a Debitar:</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $tool->price_credits }} Créditos</span>
                </div>
            </div>

            <p class="text-xs text-gray-500 font-medium mb-8 text-center italic">Ao confirmar, o valor será deduzido do seu saldo e o acesso ao download será liberado imediatamente.</p>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl transition-all active:scale-95">
                    Confirmar Compra
                </button>
                <button type="button" onclick="document.getElementById('buy-tool-modal').classList.add('hidden')" class="flex-1 py-5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-widest transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endauth
@endsection
