@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 animate-fade-in-up">
    
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase mb-2">
                Painel Administrativo
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white leading-none">
                Gestão Global do <span class="text-gradient">Sistema</span>
            </h1>
            <p class="text-muted text-lg max-w-2xl font-medium">
                Monitore o crescimento, gerencie fluxos de trabalho e administre usuários de forma centralizada.
            </p>
        </div>
        
        <div class="hidden md:flex items-center gap-4 px-6 py-4 rounded-2xl bg-card-bg border border-border shadow-2xl backdrop-blur-md">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-widest text-muted font-bold">Status do Sistema</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-sm font-bold text-white tracking-tight">Operacional</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders -->
        <div class="group h-32 rounded-3xl bg-card-bg border border-border p-6 transition-all duration-300 hover:border-primary/50 hover-lift shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"></div>
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Total Pedidos</p>
                    <p class="text-3xl font-black text-white tracking-tighter">{{ number_format($stats['total_orders'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if(auth()->user()->tenant_id === null)
        <!-- Users -->
        <div class="group h-32 rounded-3xl bg-card-bg border border-border p-6 transition-all duration-300 hover:border-emerald-500/50 hover-lift shadow-2xl relative overflow-hidden">
             <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-2xl transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Usuários</p>
                    <p class="text-3xl font-black text-white tracking-tighter">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Sizes -->
        <div class="group h-32 rounded-3xl bg-card-bg border border-border p-6 transition-all duration-300 hover:border-blue-500/50 hover-lift shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 text-2xl transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Dimensões</p>
                    <p class="text-3xl font-black text-white tracking-tighter">{{ $stats['total_sizes'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Pending Delivery -->
        <div class="group h-32 rounded-3xl bg-card-bg border border-border p-6 transition-all duration-300 hover:border-amber-500/50 hover-lift shadow-2xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-2xl transition-transform group-hover:scale-110">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-muted uppercase tracking-widest mb-1">Sols. Entrega</p>
                    <p class="text-3xl font-black text-white tracking-tighter">{{ $stats['pending_delivery_requests'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Sections Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Management -->
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl flex flex-col">
            <div class="p-8 border-b border-white/5 bg-white/2 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-folder-tree text-primary"></i>
                    Gerenciamento de Pedidos
                </h2>
            </div>
            <div class="p-8 space-y-4">
                <a href="{{ route('orders.index') }}" class="flex items-center gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-primary/40 hover:bg-primary/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-list-check text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white">Lista de Pedidos</h3>
                        <p class="text-xs text-muted">Fluxo principal de gestão de pedidos</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-muted group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                </a>

                <a href="{{ route('admin.cancellations.index') }}" class="flex items-center gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-red-500/40 hover:bg-red-500/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform relative">
                        <i class="fa-solid fa-ban text-xl"></i>
                        @if(($stats['pending_cancellations'] ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">
                         <h3 class="font-bold text-white">Cancelamentos</h3>
                         <p class="text-xs text-muted">Aprovação/Rejeição de pedidos suspensos</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-muted group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('kanban'))
                <a href="{{ route('kanban.index') }}" class="flex items-center gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-primary/40 hover:bg-primary/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clapperboard text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white">Kanban de Produção</h3>
                        <p class="text-xs text-muted">Gestão visual das etapas fabris</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-muted group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                </a>
                @endif
                
                <a href="{{ route('delivery-requests.index') }}" class="flex items-center gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-primary/40 hover:bg-primary/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform relative">
                        <i class="fa-solid fa-truck-fast text-xl"></i>
                        @if(($stats['pending_delivery_requests'] ?? 0) > 0)
                             <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary/40 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white">Solicitações de Entrega</h3>
                        <p class="text-xs text-muted">Gestão logística e despacho</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-muted group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Financial Section -->
        @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('financial'))
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl flex flex-col">
            <div class="p-8 border-b border-white/5 bg-white/2">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-sack-dollar text-emerald-500"></i>
                    Gerenciamento Financeiro
                </h2>
            </div>
            <div class="p-8 space-y-4">
                <a href="{{ route('cash.index') }}" class="flex items-center gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cash-register text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-white">Controle de Caixa</h3>
                        <p class="text-xs text-muted">Fluxo de caixa e transações diárias</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-muted group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- System Configs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Section: Products -->
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl flex flex-col p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-muted border-b border-white/5 pb-4 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-tags text-blue-500"></i>
                Produtos & Preços
            </h3>
            <a href="{{ route('admin.product-options.index') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Opções de Produtos</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
            <a href="{{ route('admin.personalization-prices.index') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Preços de Personalização</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
        </div>

        <!-- Section: Catalog -->
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl flex flex-col p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-muted border-b border-white/5 pb-4 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-basket-shopping text-primary"></i>
                Catálogo & PDV
            </h3>
            @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('pdv'))
            <a href="{{ route('admin.quick-products.index') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Produtos PDV</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
            @endif
            <a href="{{ route('admin.catalog-items.index') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Gerenciar Catálogo</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
        </div>

        <!-- Section: System -->
        @if(auth()->user()->tenant_id === null)
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl flex flex-col p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-muted border-b border-white/5 pb-4 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-gears text-purple-500"></i>
                Sistema & Usuários
            </h3>
            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Gerenciar Usuários</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
            <a href="{{ route('admin.company.settings') }}" class="flex items-center justify-between p-4 rounded-xl bg-white/2 hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 group">
                <span class="text-sm font-bold text-white group-hover:text-primary transition-colors">Configurações Empresa</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] text-muted group-hover:text-primary transition-all"></i>
            </a>
        </div>
        @endif
    </div>

    <!-- Stores Management (Full Admin Only) -->
    @if(auth()->user()->tenant_id === null)
    <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl">
        <div class="p-8 border-b border-white/5 bg-white/2 flex items-center justify-between">
            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-shop text-primary"></i>
                Gestão de Lojas & Franquias
            </h2>
            <a href="{{ route('admin.stores.index') }}" class="text-xs font-black text-primary hover:underline underline-offset-4 tracking-widest uppercase">
                Ver Todas →
            </a>
        </div>
        <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                        <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-1">Total de Lojas</p>
                        <p class="text-3xl font-black text-white">{{ $stats['total_stores'] ?? 0 }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5">
                        <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-1">Sub-lojas</p>
                        <p class="text-3xl font-black text-white">{{ $stats['total_sub_stores'] ?? 0 }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.stores.index') }}" class="flex items-center justify-center gap-3 w-full py-4 rounded-xl bg-primary text-white font-bold hover:bg-primary-hover transition-colors shadow-lg shadow-primary/20">
                    <i class="fa-solid fa-plus-circle"></i>
                    Gerenciar Lojas do Sistema
                </a>
            </div>
            
            <div class="rounded-2xl bg-black/40 border border-white/5 p-4 max-h-[220px] overflow-y-auto">
                <h3 class="text-[10px] font-black uppercase text-muted tracking-widest mb-4">Lojas Ativas Recentemente</h3>
                <div class="space-y-3">
                    @foreach(($stores ?? []) as $store)
                        @if($store->isMain() || !$store->parent_id)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-white/2 border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-xs">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $store->name }}</p>
                                    @if($store->isMain()) <span class="text-[8px] uppercase tracking-tighter text-primary font-black">Main Hub</span> @endif
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $store->active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                                {{ $store->active ? 'LIVE' : 'DOWN' }}
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lists: Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-white/2 text-left">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    Pedidos Recentes
                </h3>
            </div>
            <div class="p-8">
                <div class="space-y-1">
                    @forelse($recent_orders as $order)
                    <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-white/2 transition-colors border border-transparent hover:border-white/5 group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-muted group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[10px] text-muted">{{ $order->client?->name ?? 'Cliente Avulso' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-white">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                            <p class="text-[10px] text-muted font-bold tracking-tighter">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-muted italic text-sm">Nenhum pedido processado hoje.</div>
                    @endforelse
                </div>
            </div>
        </div>

        @if(auth()->user()->tenant_id === null)
        <!-- Recent Users -->
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-white/2 text-left">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-clock text-emerald-500"></i>
                    Novos Usuários
                </h3>
            </div>
            <div class="p-8">
                <div class="space-y-1">
                    @forelse($recent_users as $user)
                    <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-white/2 transition-colors border border-transparent hover:border-white/5 group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-muted group-hover:bg-emerald-500/10 group-hover:text-emerald-500 transition-colors">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">{{ $user->name }}</p>
                                <p class="text-[10px] text-muted">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end gap-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black tracking-widest uppercase {{ $user->role === 'admin' ? 'bg-red-500/10 text-red-500' : 'bg-blue-500/10 text-blue-500' }}">
                                {{ $user->role }}
                            </span>
                            <p class="text-[10px] text-muted font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-muted italic text-sm">Nenhuma nova adesão recentemente.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Premium Hover Animation */
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
    }
    
    /* Custom Scrollbar for Recent Lists */
    ::-webkit-scrollbar {
        width: 4px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #1a1a1a;
        border-radius: 10px;
    }
</style>
@endpush
@endsection
