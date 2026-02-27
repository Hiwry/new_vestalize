@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Meus Pedidos</h1>
                <p class="text-lg text-gray-500 font-medium">Acompanhe suas contratações e vendas no marketplace.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-4 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Saldo</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">{{ $wallet->balance }} <span class="text-[10px] text-gray-400 uppercase">créditos</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-8">
                <a href="{{ route('marketplace.orders.index', ['tab' => 'buying']) }}" 
                   class="pb-4 text-sm font-black uppercase tracking-widest transition-all relative {{ $tab === 'buying' ? 'text-primary' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                    Minhas Compras
                    @if($tab === 'buying') <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div> @endif
                </a>
                
                @if($designer)
                <a href="{{ route('marketplace.orders.index', ['tab' => 'selling']) }}" 
                   class="pb-4 text-sm font-black uppercase tracking-widest transition-all relative {{ $tab === 'selling' ? 'text-primary' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                    Minhas Vendas
                    @if($tab === 'selling') <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div> @endif
                </a>
                @endif
            </div>
        </div>

        <!-- Orders Table/Cards -->
        <div class="space-y-6">
            @php 
                $activeOrders = $tab === 'buying' ? $buyingOrders : $sellingOrders; 
            @endphp

            @forelse($activeOrders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all flex flex-col md:flex-row items-center gap-8 group">
                    
                    <!-- Item Cover -->
                    <div class="w-full md:w-32 h-32 flex-shrink-0 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                        @php $item = $order->orderable_model; @endphp
                        <img src="{{ $item?->cover_image ?? 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=400' }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                    </div>

                    <!-- Main Info -->
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-primary">{{ $order->order_number }}</span>
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-{{ $order->status_color }}-500/10 text-{{ $order->status_color }}-500 border border-{{ $order->status_color }}-500/20">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $item?->title ?? 'Produto Removido' }}</h3>
                        <p class="text-xs text-gray-500 font-medium">
                            @if($tab === 'buying')
                                Designer: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $order->designer?->display_name ?? 'Sistema' }}</span>
                            @else
                                Comprador: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $order->buyer->name }}</span>
                            @endif
                            • {{ $order->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Price & Action -->
                    <div class="flex flex-col items-center md:items-end gap-4 md:border-l md:border-gray-100 dark:md:border-gray-700 md:pl-8">
                        <div class="text-center md:text-right">
                            <span class="block text-[9px] font-black uppercase tracking-widest text-gray-400">Total</span>
                            <span class="text-2xl font-black text-gray-900 dark:text-white">
                                @if($tab === 'selling')
                                    {{ $order->credits_to_designer }}
                                @else
                                    {{ $order->price_credits }}
                                @endif
                                <span class="text-xs text-gray-400 font-bold uppercase ml-1">créditos</span>
                            </span>
                        </div>
                        
                        <a href="{{ route('marketplace.orders.show', $order->id) }}" class="px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-primary dark:hover:bg-primary hover:text-white transition-all active:scale-95 shadow-lg shadow-gray-200 dark:shadow-none">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-24 text-center bg-white dark:bg-gray-800 rounded-[2.5rem] border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-6 shadow-inner">
                        <i class="fa-solid fa-box-open text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nenhum pedido encontrado</h3>
                    <p class="text-gray-500 text-sm mt-2">Você ainda não realizou transações nesta aba.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $activeOrders->appends(['tab' => $tab])->links() }}
        </div>

    </div>
</div>
@endsection
