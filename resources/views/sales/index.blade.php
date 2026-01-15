@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#f7f8fb] text-[#0f172a] dark:bg-[#0b1020] dark:text-white">
    <div class="max-w-6xl mx-auto px-4 py-10 space-y-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.25em] text-[#6b7280] font-semibold dark:text-gray-300">Central de Vendas</p>
                <h1 class="text-3xl md:text-4xl font-black leading-tight mt-1 text-[#0f172a] dark:text-white">Escolha o fluxo que deseja abrir</h1>
                <p class="text-[#4b5563] dark:text-gray-300 mt-2 max-w-3xl">Pedidos, orçamentos, PDV e acompanhamento visual reunidos em um hub único.</p>
            </div>
            <div class="px-4 py-3 rounded-2xl bg-white border border-gray-200 shadow-sm dark:bg-white/5 dark:border-white/10">
                <div class="text-xs uppercase tracking-wide text-[#6b7280] dark:text-gray-300">Atalhos</div>
                <div class="text-sm text-[#111827] dark:text-gray-100">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>

        @php
            $cards = [
                [
                    'title' => 'Pedidos',
                    'desc' => 'Gerencie, edite e acompanhe pedidos em andamento.',
                    'route' => route('orders.index'),
                    'accent' => '#1f2937',
                    'icon' => 'M3 3h18v4H3V3zm2 6h14l-1.5 12h-11L5 9zm4 2v6m6-6v6',
                ],
                [
                    'title' => 'Orçamentos',
                    'desc' => 'Crie e revise propostas comerciais em poucos cliques.',
                    'route' => route('budget.index'),
                    'accent' => '#4338ca',
                    'icon' => 'M12 8c-1.657 0-3 1.343-3 3v7h6v-7c0-1.657-1.343-3-3-3zm0-4a3 3 0 110 6 3 3 0 010-6z',
                ],
                [
                    'title' => 'Link de Orçamento',
                    'desc' => 'Disponibilize links públicos para aprovação de clientes.',
                    'route' => route('admin.quote-settings.index'),
                    'accent' => '#0ea5e9',
                    'icon' => 'M15 7h5v5m0-5l-8 8-4-4-6 6',
                ],
                [
                    'title' => 'PDV',
                    'desc' => 'Registre vendas rápidas em balcão com o ponto de venda.',
                    'route' => route('pdv.index'),
                    'accent' => '#ea580c',
                    'icon' => 'M4 7h16v2H4V7zm2 4h12v8H6v-8zm2-8h8v2H8V3z',
                ],
                [
                    'title' => 'Clientes',
                    'desc' => 'Acesse a base de clientes e consulte histórico.',
                    'route' => route('clients.index'),
                    'accent' => '#0f766e',
                    'icon' => 'M5 20h14M12 14a4 4 0 100-8 4 4 0 000 8z',
                ],
                [
                    'title' => 'Kanban',
                    'desc' => 'Visualize o pipeline de produção em tempo real.',
                    'route' => route('kanban.index'),
                    'accent' => '#7c3aed',
                    'icon' => 'M5 6h4v12H5V6zm5 0h4v8h-4V6zm5 0h4v10h-4V6z',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($cards as $card)
                <a href="{{ $card['route'] }}" class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-transform duration-150 hover:-translate-y-0.5 hover:shadow-lg dark:border-white/10 dark:bg-[#111827]">
                    <div class="absolute top-0 left-0 w-full h-1.5" style="background-color: {{ $card['accent'] }};"></div>
                    <div class="relative h-full px-5 py-6 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200 dark:bg-white/10 dark:border-white/10">
                                <svg class="w-6 h-6 text-[#111827] dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                                </svg>
                            </div>
                            <span class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold dark:text-gray-300">Acessar</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-extrabold text-[#0f172a] leading-tight dark:text-white">{{ $card['title'] }}</h3>
                            <p class="text-sm text-[#4b5563] leading-relaxed dark:text-gray-300">{{ $card['desc'] }}</p>
                        </div>
                        <div class="mt-auto flex items-center gap-2 text-sm font-semibold text-[#111827] dark:text-white">
                            <span class="group-hover:translate-x-1 transition-transform">Ir agora</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
