@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-bold tracking-widest uppercase mb-2">
                Gestão do Catálogo
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 dark:text-white leading-none">
                Administre sua <span class="text-gradient">vitrine digital</span>
            </h1>
            <p class="text-muted text-lg max-w-2xl font-medium">
                Gerencie pedidos do catálogo e produtos em um único painel.
            </p>
        </div>
        
        <div class="hidden md:flex flex-col items-end gap-3">
            <div class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-white dark:bg-card-bg border border-gray-100 dark:border-border shadow-xl backdrop-blur-md">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-primary flex items-center justify-center shadow-lg shadow-primary/30">
                    <i class="fa-solid fa-store text-lg text-white" style="color: #ffffff !important;"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Catálogo Ativo</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">{{ $tenant->name ?? 'Minha Loja' }}</p>
                </div>
            </div>

            @php
                $storeCode = auth()->user()->tenant->store_code ?? null;
                $catalogUrl = $storeCode ? route('catalog.show', ['storeCode' => strtolower($storeCode)]) : null;
            @endphp

            @if($catalogUrl)
            <div class="flex items-center gap-2 w-full">
                <div class="flex items-center gap-2 flex-1 px-4 py-2.5 rounded-xl bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-600 text-sm">
                    <i class="fa-solid fa-link text-indigo-400 text-xs"></i>
                    <input type="text" id="catalog-url-input" value="{{ $catalogUrl }}" readonly 
                           class="bg-transparent text-gray-600 dark:text-gray-300 text-xs font-mono flex-1 outline-none border-none cursor-text w-48" />
                </div>
                <button onclick="copyCatalogUrl()" id="copy-catalog-btn"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-500/20 flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-copy" id="copy-icon"></i>
                    <span id="copy-text">Copiar Link</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    @php
        $cards = [
            [
                'title' => 'Pedidos do Catálogo',
                'desc' => 'Visualize e gerencie as solicitações feitas pelos clientes através do catálogo público.',
                'route' => route('admin.catalog-orders.index'),
                'accent' => '#6366f1', // Indigo
                'icon' => 'fa-shopping-cart',
                'delay' => 'delay-100'
            ],
            [
                'title' => 'Produtos',
                'desc' => 'Adicione, edite e configure quais produtos aparecem no seu catálogo online.',
                'route' => route('admin.products.index'),
                'accent' => '#8b5cf6', // Violet
                'icon' => 'fa-box',
                'delay' => 'delay-200'
            ],
            [
                'title' => 'Gateway de Pagamento',
                'desc' => 'Configure o gateway do catálogo e libere geração do pedido interno só quando o pagamento for aprovado.',
                'route' => route('admin.catalog-gateway.edit'),
                'accent' => '#10b981', // Emerald
                'icon' => 'fa-credit-card',
                'delay' => 'delay-300'
            ],
        ];
    @endphp

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($cards as $card)
            <a href="{{ $card['route'] }}" 
               class="group relative h-64 rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 hover:border-primary/50 hover:bg-card-hover hover-lift shadow-2xl overflow-hidden {{ $card['delay'] }}">
                
                <!-- Decoration -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                <div class="absolute top-0 left-0 w-1.5 h-full opacity-0 group-hover:opacity-100 transition-opacity" style="background: {{ $card['accent'] }}"></div>

                <div class="relative h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/5 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:bg-primary/10 group-hover:border-primary/20" style="color: {{ $card['accent'] }}">
                            <i class="fa-solid {{ $card['icon'] }} text-2xl"></i>
                        </div>
                        
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                            <span class="text-[10px] uppercase tracking-tighter font-black text-primary">Acessar</span>
                            <i class="fa-solid fa-arrow-right text-[10px] text-primary"></i>
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight group-hover:text-primary transition-colors">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-muted text-sm leading-relaxed line-clamp-2">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-white/5 flex items-center justify-between">
                        <span class="text-xs font-bold text-muted transition-colors group-hover:text-gray-900 dark:group-hover:text-white">
                            Ir agora
                        </span>
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:text-white">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@endsection
