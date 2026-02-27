@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Administração do Marketplace</h1>
            <p class="text-lg text-gray-500 font-medium">Controle de designers, serviços, ferramentas e transações.</p>
        </div>

        <!-- Global Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Total em Créditos Circulando</span>
                <span class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['total_credits'] ?? 0, 0, ',', '.') }}</span>
                <i class="fa-solid fa-coins absolute right-8 bottom-8 text-primary/10 text-4xl"></i>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Designers Ativos</span>
                <span class="text-3xl font-black text-emerald-500">{{ $stats['active_designers'] ?? 0 }}</span>
                <i class="fa-solid fa-palette absolute right-8 bottom-8 text-emerald-500/10 text-4xl"></i>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Serviços na Vitrine</span>
                <span class="text-3xl font-black text-indigo-500">{{ $stats['active_services'] ?? 0 }}</span>
                <i class="fa-solid fa-briefcase absolute right-8 bottom-8 text-indigo-500/10 text-4xl"></i>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-2">Vendas Totais</span>
                <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['total_orders'] ?? 0 }}</span>
                <i class="fa-solid fa-cart-shopping absolute right-8 bottom-8 text-gray-400/10 text-4xl"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Pending Designers -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                        <i class="fa-solid fa-user-clock text-amber-500"></i> Designers Pendentes
                    </h2>
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-widest rounded-full">Aguardando</span>
                </div>

                <div class="space-y-4">
                    @forelse($pendingDesigners as $pending)
                    <div class="group flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-transparent hover:border-primary/20 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center font-black text-primary border border-gray-100 dark:border-gray-700">{{ strtoupper(substr($pending->display_name,0,2)) }}</div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $pending->display_name }}</h4>
                                <span class="text-[10px] text-gray-400 font-medium">Solicitado em {{ $pending->created_at->format('d/m/y') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.marketplace.designer.status', $pending->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-emerald-500/20" title="Aprovar">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.marketplace.designer.status', $pending->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="w-10 h-10 bg-red-500 text-white rounded-xl flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-red-500/20" title="Rejeitar">
                                    <i class="fa-solid fa-times text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p class="text-center py-12 text-gray-400 font-medium text-sm">Nenhuma solicitação pendente.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-gray-900 rounded-[3rem] p-8 text-white shadow-2xl overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 opacity-10"> <i class="fa-solid fa-receipt text-9xl"></i> </div>
                
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <h2 class="text-xl font-black">Últimas Transações</h2>
                    <a href="#" class="text-[10px] font-black uppercase tracking-[0.2em] text-primary hover:text-white transition-colors">Ver Todas</a>
                </div>

                <div class="space-y-6 relative z-10">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between pb-6 border-b border-white/5 last:border-0 last:pb-0">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-primary">
                                <i class="fa-solid fa-{{ $order->isService() ? 'briefcase' : 'download' }} text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold truncate max-w-[150px]">{{ $order->orderable_title }}</h4>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">{{ $order->buyer->name }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="block text-sm font-black text-primary">{{ $order->price_credits }} Cr.</span>
                             <span class="text-[8px] font-black uppercase tracking-widest text-{{ $order->status_color }}-500">{{ $order->status_label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Management Links -->
        <div class="mt-12 grid grid-cols-2 lg:grid-cols-4 gap-6">
             <a href="{{ route('admin.marketplace.services') }}" class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center group hover:bg-primary transition-all">
                <i class="fa-solid fa-briefcase text-2xl mb-3 text-primary group-hover:text-white transition-colors"></i>
                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-white/60">Serviços</span>
             </a>
             <a href="{{ route('admin.marketplace.tools.index') }}" class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center group hover:bg-primary transition-all">
                <i class="fa-solid fa-screwdriver-wrench text-2xl mb-3 text-primary group-hover:text-white transition-colors"></i>
                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-white/60">Ferramentas</span>
             </a>
             <a href="{{ route('admin.marketplace.packages.index') }}" class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center group hover:bg-primary transition-all">
                <i class="fa-solid fa-box-archive text-2xl mb-3 text-primary group-hover:text-white transition-colors"></i>
                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-white/60">Pacotes de Crédito</span>
             </a>
             <a href="#" class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center group hover:bg-primary transition-all">
                <i class="fa-solid fa-chart-line text-2xl mb-3 text-primary group-hover:text-white transition-colors"></i>
                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-white/60">Relatórios Financeiros</span>
             </a>
        </div>

    </div>
</div>
@endsection
