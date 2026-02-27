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
                        <a href="{{ route('marketplace.services.index') }}" class="text-gray-400 hover:text-primary transition-colors">Serviços</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-400 mx-2"></i>
                        <span class="text-gray-500 dark:text-gray-300">{{ $service->category_label }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left Column: Gallery & Details -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Main Gallery -->
                <div x-data="{ activeImage: '{{ $service->cover_image }}' }" class="space-y-4">
                    <div class="aspect-video rounded-[2.5rem] overflow-hidden bg-gray-200 dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700">
                        <img :src="activeImage" class="w-full h-full object-cover transition-all duration-500" alt="{{ $service->title }}">
                    </div>
                    
                    @if($service->images->count() > 1)
                    <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($service->images as $image)
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
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-6">{{ $service->title }}</h1>
                    
                    <div class="prose prose-indigo dark:prose-invert max-w-none mb-12 text-gray-600 dark:text-gray-300 font-medium leading-relaxed">
                        {!! nl2br(e($service->description)) !!}
                    </div>

                    @if($service->requirements)
                    <div class="p-8 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-3xl border border-indigo-100 dark:border-indigo-800/30">
                        <h3 class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest text-xs mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i> O que o designer precisa
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $service->requirements }}</p>
                    </div>
                    @endif
                </div>

                <!-- About Designer -->
                <div class="bg-gray-900 rounded-[2.5rem] p-8 md:p-12 text-white flex flex-col md:flex-row items-center gap-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                         <i class="fa-solid fa-quote-right text-8xl"></i>
                    </div>
                    
                    <div class="relative w-32 h-32 flex-shrink-0">
                        <img src="{{ $service->designer->avatar_url }}" class="w-full h-full rounded-3xl object-cover ring-4 ring-white/10">
                        <div class="absolute -bottom-2 -right-2 bg-emerald-500 w-8 h-8 rounded-full border-4 border-gray-900 flex items-center justify-center" title="Designer Verificado">
                            <i class="fa-solid fa-check text-[10px] text-white"></i>
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left relative z-10">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-2 block">Designer do Projeto</span>
                        <h2 class="text-3xl font-black mb-2">{{ $service->designer->display_name }}</h2>
                        <p class="text-sm text-gray-400 font-medium mb-6 line-clamp-2">{{ $service->designer->bio }}</p>
                        
                        <div class="flex flex-wrap justify-center md:justify-start gap-4">
                            <a href="{{ route('marketplace.designers.show', $service->designer->slug) }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                Ver Portfólio
                            </a>
                        </div>
                    </div>

                    <div class="md:border-l md:border-white/10 md:pl-8 flex flex-col items-center md:items-start md:w-48">
                         <div class="mb-4">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Avaliação</span>
                            <div class="flex items-center gap-1 text-amber-500">
                                @for($i=0; $i<5; $i++)
                                    <i class="fa-solid fa-star text-xs {{ $i < round($service->designer->rating_average) ? '' : 'opacity-20' }}"></i>
                                @endfor
                                <span class="ml-2 text-white font-black">{{ number_format($service->designer->rating_average, 1) }}</span>
                            </div>
                         </div>
                         <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Vendas</span>
                            <span class="text-white font-black">{{ $service->designer->total_sales }}</span>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Checkout -->
            <div class="space-y-6">
                
                <div class="sticky top-24 space-y-6">
                    <!-- Purchase Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                        <div class="absolute top-0 right-0 bg-primary/10 text-primary px-6 py-2 rounded-bl-3xl font-black text-[10px] uppercase tracking-widest">
                            Contratação
                        </div>
                        
                        <div class="mb-8">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Investimento do Projeto</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-5xl font-black text-gray-900 dark:text-white">{{ $service->price_credits }}</span>
                                <span class="text-lg font-bold text-gray-400 uppercase">Créditos</span>
                            </div>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-primary">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <span>Prazo: <strong>{{ $service->delivery_days }} dias úteis</strong></span>
                            </div>
                            <div class="flex items-center gap-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-primary">
                                    <i class="fa-solid fa-rotate"></i>
                                </div>
                                <span>Revisões: <strong>{{ $service->revisions }} ciclos</strong></span>
                            </div>
                            <div class="flex items-center gap-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-primary">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <span>Pagamento 100% Seguro</span>
                            </div>
                        </div>

                        @auth
                            @if($userWallet && $userWallet->hasBalance($service->price_credits))
                                <button onclick="document.getElementById('buy-modal').classList.remove('hidden')" 
                                        class="w-full py-6 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-primary/20 transition-all active:scale-95 mb-4">
                                    Contratar Agora
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
                                Entrar para Contratar
                            </a>
                        @endauth

                        <div class="border-t border-gray-100 dark:border-gray-700 mt-6 pt-6 text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Garantia Vestalize</p>
                            <p class="text-[9px] text-gray-500">O designer só recebe após você aprovar a entrega final.</p>
                        </div>
                    </div>

                    <!-- Side Links -->
                    <div class="bg-gray-100 dark:bg-gray-800/50 rounded-3xl p-6">
                        <h4 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-widest">Dúvidas?</h4>
                        <a href="#" class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-primary transition-all mb-2">
                            <span>Como funciona?</span>
                            <i class="fa-solid fa-circle-question opacity-40"></i>
                        </a>
                        <a href="#" class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-primary transition-all">
                            <span>Falar com suporte</span>
                            <i class="fa-brands fa-whatsapp opacity-40"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        @if($relatedServices->count() > 0)
        <div class="mt-24 border-t border-gray-200 dark:border-gray-700 pt-24 pb-12">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-12 text-center">Outros serviços que você pode gostar</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($relatedServices as $rel)
                    <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-md border border-gray-100 dark:border-gray-700 group">
                        <div class="h-40 overflow-hidden">
                            <img src="{{ $rel->cover_image }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                        </div>
                        <div class="p-5">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate mb-2">
                                <a href="{{ route('marketplace.services.show', $rel->id) }}">{{ $rel->title }}</a>
                            </h4>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black text-indigo-500">{{ $rel->price_credits }} créditos</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $rel->category_label }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Buy Modal (Hidden by Default) -->
@auth
<div id="buy-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 w-full max-w-xl shadow-2xl border border-white/10" x-data="{}">
        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-6">Confirmar Contratação</h3>
        
        <form action="{{ route('marketplace.orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="service">
            <input type="hidden" name="id" value="{{ $service->id }}">

            <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 font-bold">Serviço:</span>
                    <span class="text-gray-900 dark:text-white font-black">{{ $service->title }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 font-bold">Designer:</span>
                    <span class="text-gray-900 dark:text-white font-black">{{ $service->designer->display_name }}</span>
                </div>
                <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <span class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest text-xs">Total a Debitar:</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $service->price_credits }} Créditos</span>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Instruções para o Designer (Opcional)</label>
                <textarea name="buyer_instructions" rows="4" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner" placeholder="Descreva brevemente sua ideia ou envie links de referência..."></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl transition-all active:scale-95">
                    Confirmar e Pagar
                </button>
                <button type="button" onclick="document.getElementById('buy-modal').classList.add('hidden')" class="flex-1 py-5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endauth
@endsection
