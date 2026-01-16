@extends('layouts.admin')

@section('content')

@if(request('view') === 'table')
    @include('stocks.table')
@else
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-bold tracking-widest uppercase mb-2">
                Gestão de Estoques
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white leading-none">
                Central de <span class="text-gradient">Estoque</span>
            </h1>
            <p class="text-muted text-lg max-w-2xl font-medium">
                Controle total de insumos, maquinário e solicitações em um único lugar.
            </p>
        </div>
        
        <div class="hidden md:flex items-center gap-4 px-6 py-4 rounded-2xl bg-white dark:bg-card-bg border border-gray-100 dark:border-border shadow-xl backdrop-blur-md">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center shadow-lg shadow-primary/30">
                <i class="fa-solid fa-boxes-stacked text-lg text-white" style="color: #ffffff !important;"></i>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Resumo Rápido</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight capitalize">{{ now()->translatedFormat('l, d \d\e F') }}</p>
            </div>
        </div>
    </div>

    @php
        $cards = [
            [
                'title' => 'Dashboard',
                'desc' => 'Visão geral com gráficos e indicadores de desempenho do estoque.',
                'route' => route('stocks.dashboard'),
                'accent' => '#0ea5e9', // Sky
                'icon' => 'fa-chart-pie',
                'delay' => 'delay-100'
            ],
            [
                'title' => 'Inventário Geral',
                'desc' => 'Lista completa de tecidos, cortes e materiais com gestão de quantidade.',
                'route' => route('stocks.index', ['view' => 'table']),
                'accent' => '#10b981', // Emerald
                'icon' => 'fa-clipboard-list',
                'delay' => 'delay-200'
            ],
            [
                'title' => 'Solicitações',
                'desc' => 'Central de aprovação para pedidos de insumos e materiais.',
                'route' => route('stock-requests.index'),
                'accent' => '#f59e0b', // Amber
                'icon' => 'fa-file-invoice',
                'delay' => 'delay-300'
            ],
            [
                'title' => 'Histórico',
                'desc' => 'Log completo de todas as movimentações de entrada e saída.',
                'route' => route('stocks.history'),
                'accent' => '#64748b', // Slate
                'icon' => 'fa-clock-rotate-left',
                'delay' => 'delay-400'
            ],
            [
                'title' => 'Máquinas',
                'desc' => 'Cadastro e manutenção de máquinas de costura e equipamentos.',
                'route' => route('sewing-machines.index'),
                'accent' => '#8b5cf6', // Violet
                'icon' => 'fa-scissors',
                'delay' => 'delay-500'
            ],
            [
                'title' => 'Suprimentos',
                'desc' => 'Gerencie linhas, agulhas, aviamentos e tintas de produção.',
                'route' => route('production-supplies.index'),
                'accent' => '#ec4899', // Pink
                'icon' => 'fa-spool-of-thread', // Alternativo se fa-thread não existir
                'delay' => 'delay-500'
            ],
            [
                'title' => 'Uniformes e EPI',
                'desc' => 'Controle de entrega e estoque de uniformes para colaboradores.',
                'route' => route('uniforms.index'), // Ajustar se rota diferir
                'accent' => '#f43f5e', // Rose
                'icon' => 'fa-shirt',
                'delay' => 'delay-500'
            ],
        ];
    @endphp

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
        @foreach($cards as $card)
            <a href="{{ $card['route'] }}" 
               class="group relative h-64 rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 hover:border-emerald-500/50 hover:bg-card-hover hover-lift shadow-2xl overflow-hidden {{ $card['delay'] }}">
                
                <!-- Decoration -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="absolute top-0 left-0 w-1.5 h-full opacity-0 group-hover:opacity-100 transition-opacity" style="background: {{ $card['accent'] }}"></div>

                <div class="relative h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:bg-emerald-500/10 group-hover:border-emerald-500/20" style="color: {{ $card['accent'] }}">
                            <i class="fa-solid {{ $card['icon'] }} text-2xl"></i>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                            <span class="text-[10px] uppercase tracking-tighter font-black text-emerald-500">Acessar</span>
                            <i class="fa-solid fa-arrow-right text-[10px] text-emerald-500"></i>
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <h3 class="text-2xl font-bold text-white tracking-tight group-hover:text-emerald-400 transition-colors">
                            {{ $card['title'] }}
                        </h3>
                        <p class="text-muted text-sm leading-relaxed line-clamp-2">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                        <span class="text-xs font-bold text-muted transition-colors group-hover:text-white">Gerenciar</span>
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </div>
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
@endif

@endsection
