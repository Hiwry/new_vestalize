@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-bold tracking-widest uppercase mb-2">
                Central de Vendas
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white leading-none">
                Escolha o fluxo que <span class="text-gradient">deseja abrir</span>
            </h1>
            <p class="text-muted text-lg max-w-2xl font-medium">
                Pedidos, orçamentos, PDV e acompanhamento visual reunidos em um hub único e intuitivo.
            </p>
        </div>
        
        <div class="hidden md:flex items-center gap-4 px-6 py-4 rounded-2xl bg-white dark:bg-card-bg border border-gray-100 dark:border-border shadow-xl backdrop-blur-md">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center shadow-lg shadow-primary/30">
                <i class="fa-solid fa-calendar-day text-lg text-white" style="color: #ffffff !important;"></i>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Atalhos rápidos</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight capitalize">{{ now()->translatedFormat('l, d \d\e F') }}</p>
            </div>
        </div>
    </div>

    @php
        $cards = [
            [
                'title' => 'Pedidos',
                'desc' => 'Gerencie, edite e acompanhe pedidos em andamento com precisão total.',
                'route' => route('orders.index'),
                'accent' => '#7c3aed', // Purple
                'icon' => 'fa-shopping-bag',
                'delay' => 'delay-100'
            ],
            [
                'title' => 'Orçamentos',
                'desc' => 'Crie e revise propostas comerciais elegantes para seus clientes em segundos.',
                'route' => route('budget.index'),
                'accent' => '#3b82f6', // Blue
                'icon' => 'fa-file-invoice-dollar',
                'delay' => 'delay-200'
            ],
            [
                'title' => 'Link de Orçamento',
                'desc' => 'Lançamento em breve.',
                'route' => '#',
                'accent' => '#94a3b8', // Slate 400 (Gray)
                'icon' => 'fa-paper-plane',
                'delay' => 'delay-300',
                'disabled' => true,
                'badge' => 'Em Breve'
            ],
            [
                'title' => 'PDV',
                'desc' => 'Ponto de venda otimizado para vendas rápidas e checkout eficiente.',
                'route' => route('pdv.index'),
                'accent' => '#f97316', // Orange
                'icon' => 'fa-cash-register',
                'delay' => 'delay-400'
            ],
            [
                'title' => 'Personalizados',
                'desc' => 'Gestão de vendas de produtos personalizados e sob medida.',
                'route' => route('personalized.orders.index'),
                'accent' => '#ec4899', // Pink
                'icon' => 'fa-pen-ruler',
                'delay' => 'delay-500'
            ],
            [
                'title' => 'Clientes',
                'desc' => 'Base de dados CRM completa para gerir o relacionamento com seus clientes.',
                'route' => route('clients.index'),
                'accent' => '#10b981', // Emerald
                'icon' => 'fa-users',
                'delay' => 'delay-500'
            ],
            [
                'title' => 'Kanban',
                'desc' => 'Gestão visual da produção. Acompanhe cada etapa em tempo real.',
                'route' => route('kanban.index'),
                'accent' => '#d946ef', // Fuchsia
                'icon' => 'fa-layer-group',
                'delay' => 'delay-500'
            ],
        ];
    @endphp

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($cards as $card)
            <a href="{{ $card['disabled'] ?? false ? '#' : $card['route'] }}" 
               class="group relative h-64 rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 
                      {{ $card['disabled'] ?? false ? 'opacity-75 cursor-not-allowed grayscale' : 'hover:border-primary/50 hover:bg-card-hover hover-lift shadow-2xl' }} 
                      overflow-hidden {{ $card['delay'] }}">
                
                <!-- Decoration -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
                <div class="absolute top-0 left-0 w-1.5 h-full opacity-0 {{ $card['disabled'] ?? false ? '' : 'group-hover:opacity-100' }} transition-opacity" style="background: {{ $card['accent'] }}"></div>

                <div class="relative h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-center transition-all duration-300 {{ $card['disabled'] ?? false ? '' : 'group-hover:scale-110 group-hover:bg-primary/10 group-hover:border-primary/20' }}" style="color: {{ $card['accent'] }}">
                            <i class="fa-solid {{ $card['icon'] }} text-2xl"></i>
                        </div>
                        
                        @if($card['badge'] ?? false)
                            <span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                {{ $card['badge'] }}
                            </span>
                        @else
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                                <span class="text-[10px] uppercase tracking-tighter font-black text-primary">Acessar</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-primary"></i>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2 mt-4">
                        <h3 class="text-2xl font-bold text-white tracking-tight {{ $card['disabled'] ?? false ? '' : 'group-hover:text-primary' }} transition-colors">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-muted text-sm leading-relaxed line-clamp-2">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-xs font-bold text-muted transition-colors {{ $card['disabled'] ?? false ? '' : 'group-hover:text-white' }}">
                            {{ $card['disabled'] ?? false ? 'Indisponível' : 'Ir agora' }}
                        </span>
                        @if(!($card['disabled'] ?? false))
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center transition-all duration-300 group-hover:bg-primary group-hover:text-white">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<style>
    /* Custom Clamp if tailwind line-clamp plugin is missing */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
