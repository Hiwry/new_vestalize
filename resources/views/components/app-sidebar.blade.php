<!-- Wrapper para Sidebar e Modal -->
<div x-data="{
        expanded: localStorage.getItem('sidebarExpanded') === 'true',
        mobileOpen: false,
        isDark: localStorage.getItem('dark') === 'true',
        openGroups: {
            'vendas': localStorage.getItem('sidebarGroupVendas') === 'true',
            'estoque': localStorage.getItem('sidebarGroupEstoque') === 'true',
            'producao': localStorage.getItem('sidebarGroupProducao') === 'true',
            'financeiro': localStorage.getItem('sidebarGroupFinanceiro') === 'true',
            'assinaturas': localStorage.getItem('sidebarGroupAssinaturas') === 'true',
            'sistema': localStorage.getItem('sidebarGroupSistema') === 'true',
            'catalogo': localStorage.getItem('sidebarGroupCatalogo') === 'true'
        },
        isMobile() { return window.innerWidth < 768; },
        updateLayout() {
            const mainContent = document.getElementById('main-content');
            if (!mainContent) return;
            if (this.isMobile()) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = this.expanded ? '16rem' : '4rem';
            }
        },
        toggle() {
            if (this.isMobile()) {
                this.mobileOpen = !this.mobileOpen;
                this.expanded = true; // garantir menu expandido no mobile
                this.mobileOpen ? this.lockScroll() : this.unlockScroll();
                return;
            }
            this.expanded = !this.expanded;
            localStorage.setItem('sidebarExpanded', this.expanded);
            this.updateLayout();
        },
        openMobile() { 
            this.mobileOpen = true; 
            this.expanded = true; // sempre expandido no mobile para mostrar grupos
            this.lockScroll();
        },
        closeMobile() { 
            this.mobileOpen = false; 
            this.unlockScroll();
        },
        toggleGroup(group) {
            if (!this.expanded && !this.isMobile()) {
                this.toggle(); // Expandir sidebar se estiver fechada ao abrir um grupo
                setTimeout(() => {
                    this.openGroups[group] = !this.openGroups[group];
                    localStorage.setItem('sidebarGroup' + group.charAt(0).toUpperCase() + group.slice(1), this.openGroups[group]);
                }, 150);
            } else {
                this.openGroups[group] = !this.openGroups[group];
                localStorage.setItem('sidebarGroup' + group.charAt(0).toUpperCase() + group.slice(1), this.openGroups[group]);
            }
        },
        showProfileModal: false,
        lockScroll() { document.body.style.overflow = 'hidden'; },
        unlockScroll() { document.body.style.overflow = ''; },
        init() {
            this.updateLayout();
            window.addEventListener('resize', () => {
                this.updateLayout();
                if (!this.isMobile()) {
                    this.mobileOpen = false;
                    this.unlockScroll();
                }
            });
        }
    }"
    @keydown.escape.window="showProfileModal = false; mobileOpen = false"
    @toggle-sidebar.window="toggle()">


<!-- Barra Mobile Superior com botão único -->
<!-- Barra Mobile Superior Suave (Neutral BG + Indigo Button) -->
<div class="md:hidden fixed top-0 inset-x-0 h-16 bg-black/80 backdrop-blur-lg border-b border-white/5 z-50 flex items-center justify-between px-4 transition-all duration-300">
    <button @click.stop="toggle()"
            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary hover:bg-primary-hover text-white focus:outline-none transition-all duration-200 shadow-lg shadow-primary/20 active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    
    <!-- Mobile Notification Bell -->
    <div x-data="notificationBell()" x-init="init()" class="relative">
        <button @click="togglePanel()" class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-6 h-6 transition-transform" :class="{ 'animate-bounce': hasNew }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <!-- Badge -->
            <span x-show="unreadCount > 0" 
                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                  class="absolute top-1 right-1 min-w-[16px] h-4 px-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
            </span>
        </button>

        <!-- Mobile Notification Panel -->
        <div x-show="showPanel" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             @click.away="showPanel = false"
             class="absolute top-12 right-0 w-80 max-h-[80vh] bg-card-bg backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 overflow-hidden flex flex-col z-[100]">
            
             <!-- Header -->
            <div class="px-4 py-3 bg-white/5 border-b border-white/10 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center">
                    Notificações
                    <span x-show="unreadCount > 0" x-text="'(' + unreadCount + ')'" class="ml-1 text-xs text-primary"></span>
                </h3>
                 <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] text-primary font-bold uppercase tracking-widest">Lidas</button>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto">
                 <template x-if="loading"><div class="p-4 text-center text-xs text-gray-500">Carregando...</div></template>
                 <template x-if="!loading && notifications.length === 0">
                    <div class="p-8 text-center text-gray-500 text-xs">Nenhuma notificação</div>
                 </template>
                 <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="handleNotificationClick(notification)" :class="{ 'bg-indigo-50 dark:bg-indigo-900/10': !notification.read }" class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate" x-text="notification.title"></p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2" x-text="notification.message"></p>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                 </div>
            </div>
            
             <div x-show="notifications.length > 0" class="p-2 border-t border-gray-200 dark:border-gray-600 text-center bg-gray-50 dark:bg-gray-800">
                <button @click="clearAll()" class="text-xs text-red-500 hover:text-red-700">Limpar tudo</button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Mobile -->
<div x-show="mobileOpen && isMobile()" x-cloak @click="closeMobile()" class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" 
       :class="[
            isMobile() ? 'w-full max-w-full' : (expanded ? 'w-64' : 'w-20'),
            (mobileOpen || !isMobile()) ? 'translate-x-0' : '-translate-x-full'
       ]"
       class="fixed top-0 left-0 z-[60] h-screen bg-black border-r border-white/5 overflow-hidden transition-all duration-300 ease-in-out transform md:translate-x-0 shadow-2xl">
    
    <!-- Header do Sidebar com Botão Toggle -->
    <div class="flex items-center justify-between h-20 px-4 border-b border-white/5 bg-black transition-all duration-300"
         :class="expanded ? '' : 'justify-center px-0'">
        <div class="flex items-center overflow-hidden" x-show="expanded">
            @if(auth()->user()->tenant && auth()->user()->tenant->logo_path)
                <img src="{{ Storage::url(auth()->user()->tenant->logo_path) }}" alt="Logo" class="h-10 w-auto object-contain">
            @else
                <h1 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-primary to-purple-400 whitespace-nowrap tracking-tighter italic">
                    {{ auth()->user()->tenant->name ?? 'VESTALIZE' }}
                </h1>
            @endif
        </div>
        <div class="flex items-center" :class="expanded ? 'gap-2' : ''">
            <button @click="toggle()" 
                    class="flex-shrink-0 p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 active:scale-95 transition-all duration-200"
                    :class="expanded ? '' : 'mx-auto'">
                <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     :class="expanded ? '' : 'rotate-180'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Menu Items -->
    <nav class="flex flex-col px-2 py-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)] scrollbar-thin">
        
        <!-- Dashboard (Always visible) -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3.5 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->is('dashboard') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
           :class="expanded ? 'justify-start' : 'justify-center'"
           title="Dashboard">
            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="whitespace-nowrap overflow-hidden transition-all duration-300 ml-4"
                  x-show="expanded">
                Tela Inicial
            </span>
        </a>

        @if(Auth::user()->isEstoque() && !Auth::user()->isAdmin())
            <!-- Sidebar Simplificada para Estoque (Sem grupos) -->
            <!-- ... Itens estoque ... -->
             <a href="{{ route('stocks.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->is('stocks*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
               :class="expanded ? 'justify-start' : 'justify-center'">
               <i class="fa-solid fa-boxes-stacked h-5 w-5 flex items-center justify-center text-lg"></i>
                <span class="ml-4" x-show="expanded">Estoque</span>
            </a>
             <a href="{{ route('stock-requests.index') }}" 
                class="flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->routeIs('stock-requests.*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <i class="fa-solid fa-file-invoice h-5 w-5 flex items-center justify-center text-lg"></i>
                 <span class="ml-4" x-show="expanded">Solicitações</span>
             </a>
              <a href="{{ route('sewing-machines.index') }}" 
                class="flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->routeIs('sewing-machines.*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <i class="fa-solid fa-scissors h-5 w-5 flex items-center justify-center text-lg"></i>
                 <span class="ml-4" x-show="expanded">Máquinas</span>
             </a>
              <a href="{{ route('production-supplies.index') }}" 
                class="flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->routeIs('production-supplies.*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <i class="fa-solid fa-thread h-5 w-5 flex items-center justify-center text-lg"></i>
                 <span class="ml-4" x-show="expanded">Suprimentos</span>
             </a>
              <a href="{{ route('uniforms.index') }}" 
                class="flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->routeIs('uniforms.*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <i class="fa-solid fa-shirt h-5 w-5 flex items-center justify-center text-lg"></i>
                 <span class="ml-4" x-show="expanded">Uniformes/EPI</span>
             </a>
        @else
            <!-- Sidebar Completa com Grupos -->
            
            <div class="mt-2 text-nowrap">
                <a href="{{ route('sales.index') }}"
                   class="flex items-center w-full px-4 py-3.5 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->routeIs('sales.index') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                   :class="expanded ? 'justify-start' : 'justify-center'">
                    <i class="fa-solid fa-cart-shopping h-5 w-5 flex items-center justify-center text-lg"></i>
                    <span class="ml-4" x-show="expanded">Vendas</span>
                </a>
            </div>

            <!-- GRUPO: ESTOQUE -->
             @if((Auth::user()->isAdmin() || Auth::user()->isEstoque() || Auth::user()->isVendedor()) && (Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('stock')))
             <div class="mt-1">
                <button @click="toggleGroup('estoque')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-boxes-stacked h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Estoque</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.estoque ? 'rotate-90 text-primary' : ''"></i>
                </button>
                
                <div x-show="openGroups.estoque && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    <a href="{{ Auth::user()->isVendedor() ? route('stocks.view') : route('stocks.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ (request()->routeIs('stocks.*') || request()->routeIs('stocks.view')) ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Geral
                    </a>
                    <a href="{{ route('fabric-pieces.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('fabric-pieces.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Peças de Tecido
                    </a>
                    <a href="{{ route('sewing-machines.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('sewing-machines.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Máquinas
                    </a>
                    <a href="{{ route('production-supplies.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('production-supplies.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Suprimentos
                    </a>
                    <a href="{{ route('uniforms.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('uniforms.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Uniformes/EPI
                    </a>
                    @if(!Auth::user()->isVendedor())
                    <a href="{{ route('stock-requests.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('stock-requests.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Solicitações
                    </a>
                    @endif
                </div>
            </div>
            @endif

             @if(Auth::user()->isAdmin())
            <div class="mt-1">
                <button @click="toggleGroup('catalogo')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-list-ul h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Catálogo</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.catalogo ? 'rotate-90 text-primary' : ''"></i>
                </button>
                
                <div x-show="openGroups.catalogo && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Produtos
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Categorias
                    </a>
                    <a href="{{ route('admin.tecidos.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.tecidos.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Tecidos
                    </a>
                    <a href="{{ route('admin.modelos.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.modelos.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Modelos
                    </a>
                    @if(Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('sublimation_total'))
                    <a href="{{ route('admin.sublimation-products.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.sublimation-products.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Sublimação Total
                    </a>
                    @endif
                    <a href="{{ route('admin.sub-local-products.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.sub-local-products.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Sublimação Local
                    </a>
                </div>
            </div>
            @endif

            <!-- GRUPO: PRODUÃÃO -->
             @if(Auth::user()->isProducao() || Auth::user()->isAdmin())
            <div class="mt-1">
                <button @click="toggleGroup('producao')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-industry h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Produção</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.producao ? 'rotate-90 text-primary' : ''"></i>
                </button>
                
                <div x-show="openGroups.producao && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    <a href="{{ route('production.dashboard') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('production.dashboard') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('production.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('production.index') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Ordens
                    </a>
                </div>
            </div>
            @endif

             @if((Auth::user()->isAdmin() || Auth::user()->isCaixa()) && (Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('financial')))
            <div class="mt-1">
                <button @click="toggleGroup('financeiro')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-coins h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Financeiro</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.financeiro ? 'rotate-90 text-primary' : ''"></i>
                </button>
                
                <div x-show="openGroups.financeiro && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    <a href="{{ route('financial.dashboard') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('financial.dashboard') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('cash.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('cash.index') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Caixa
                    </a>
                    <a href="{{ route('cash.approvals.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('cash.approvals.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Aprovações PDV
                    </a>
                    <a href="{{ route('admin.invoices.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.invoices.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Notas Fiscais
                    </a>
                    <a href="{{ route('admin.invoice-config.edit') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.invoice-config.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Configuração NF-e
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::user()->tenant_id !== null && auth()->user()->tenant?->canAccess('subscription_module'))
            <div class="mt-2 text-nowrap">
                <a href="{{ route('subscription.index') }}"
                   class="flex items-center w-full px-4 py-3.5 text-sm font-bold rounded-2xl transition-all duration-300 {{ request()->is('subscription*') ? 'bg-gradient-to-r from-primary to-purple-600 text-white shadow-lg shadow-primary/30' : 'text-muted hover:bg-white/5 hover:text-white' }}"
                   :class="expanded ? 'justify-start' : 'justify-center'">
                    <i class="fa-solid fa-credit-card h-5 w-5 flex items-center justify-center text-lg"></i>
                    <span class="ml-4" x-show="expanded">Minha Assinatura</span>
                </a>
            </div>
            @endif



            <!-- GRUPO: ASSINATURAS (Apenas Super Admin) -->
            {{-- GRUPO: ASSINATURAS (Super Admin apenas - sem tenant_id) --}}
            @if(Auth::user()->isAdmin() && Auth::user()->tenant_id === null)
            <div class="mt-1">
                <button @click="toggleGroup('assinaturas')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-users-gear h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Assinaturas</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.assinaturas ? 'rotate-90 text-primary' : ''"></i>
                </button>
                <div x-show="openGroups.assinaturas && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    <a href="{{ route('admin.tenants.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.tenants.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Gerenciar Tenants
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.plans.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Planos
                    </a>
                    <a href="{{ route('admin.subscription-payments.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.subscription-payments.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Pagamentos
                    </a>
                    <a href="{{ route('admin.affiliates.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.affiliates.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Afiliados
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.leads.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Lista VIP 🚀
                    </a>
                </div>
            </div>
            @endif

             <!-- GRUPO: SISTEMA -->
            <div class="mt-1">
                 <button @click="toggleGroup('sistema')"
                        class="flex items-center w-full px-4 py-3.5 text-sm font-bold text-muted rounded-2xl hover:bg-white/5 hover:text-white transition-all duration-300 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <i class="fa-solid fa-gears h-5 w-5 flex items-center justify-center text-lg group-hover:text-primary transition-colors"></i>
                        <span class="ml-4" x-show="expanded">Sistema</span>
                    </div>
                    <i x-show="expanded" class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300" :class="openGroups.sistema ? 'rotate-90 text-primary' : ''"></i>
                </button>
                 <div x-show="openGroups.sistema && expanded" x-collapse x-cloak class="space-y-1 my-1 px-2">
                    @if(!Auth::user()->isVendedor())
                    <a href="{{ route('settings.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('settings.index') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Configurações
                    </a>
                    @endif
                     <a href="{{ route('links.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('links.index') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Links Úteis
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Usuários
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="flex items-center pl-10 pr-4 py-2.5 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('admin.audit.index') ? 'bg-primary/20 text-primary border border-primary/20' : 'text-muted hover:text-white hover:bg-white/5' }}">
                        Auditoria
                    </a>
                    @endif
                </div>
            </div>

        @endif
        
    </nav>
    
    <!-- Botão de Tema (Floating or Fixed at bottom right of nav in expanded?) -->
    <!-- Put it in Footer actually for unified interactions or keep it floating -->
    
    <!-- Footer do Sidebar (Perfil e Tema) -->
    <div class="sidebar-footer-custom">
         
         <!-- Theme Toggle Pill -->
         <div class="mb-4 px-1" x-show="expanded">
             <div class="theme-toggle-track-custom" @click="isDark = !isDark; localStorage.setItem('dark', isDark); isDark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')">
                <div class="theme-toggle-pill-custom" :class="{ 'dark': isDark }"></div>
                
                <div class="theme-toggle-btn-custom" :class="!isDark ? 'active' : ''" title="Modo Claro">
                    <i class="fa-solid fa-sun text-lg"></i>
                </div>
                
                <div class="theme-toggle-btn-custom" :class="isDark ? 'active' : ''" title="Modo Escuro">
                    <i class="fa-solid fa-moon text-lg"></i>
                </div>
             </div>
         </div>

         <!-- Mini Toggle (Collapsed Sidebar) -->
         <button x-show="!expanded" @click="isDark = !isDark; localStorage.setItem('dark', isDark); isDark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')"
                 class="w-12 h-12 flex items-center justify-center mx-auto mb-6 rounded-2xl transition-all group"
                 :class="isDark ? 'text-primary bg-primary/10 hover:bg-primary/20' : 'text-primary bg-primary/5 hover:bg-primary/10'">
             <i class="fa-solid text-lg transition-transform group-hover:rotate-12" :class="isDark ? 'fa-moon' : 'fa-sun'"></i>
         </button>

        <button @click="showProfileModal = true" 
                class="flex items-center p-2 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all w-full group"
                :class="expanded ? 'justify-start' : 'justify-center'">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center text-white font-black text-xs shadow-lg group-hover:scale-110 transition-transform">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex flex-col items-start ml-3 overflow-hidden" x-show="expanded">
                <span class="text-xs font-black text-white truncate w-full max-w-[140px] tracking-tight">
                    {{ auth()->user()->name }}
                </span>
                <span class="text-[9px] font-bold text-muted uppercase tracking-widest">
                    Meu Perfil
                </span>
            </div>
        </button>
    </div>
</aside>

<!-- Modal Profile (Mantido igual basicamente, só small tweaks) -->
<div x-show="showProfileModal" 
    @click.self="showProfileModal = false"
    style="display: none;"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    
    <!-- Conteúdo do Modal -->
    <div class="bg-card-bg border border-white/10 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all backdrop-blur-2xl"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        <!-- Header com gradiente -->
        <div class="relative h-32 bg-gradient-to-br from-primary via-purple-600 to-indigo-700">
            <button @click="showProfileModal = false" class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors h-8 w-8 flex items-center justify-center rounded-full bg-black/20 backdrop-blur-md">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="absolute -bottom-10 left-6">
                 <div class="flex items-center justify-center h-20 w-20 rounded-2xl bg-card-bg border-4 border-black text-primary text-3xl font-black shadow-2xl">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </div>

        <div class="pt-14 px-8 pb-8">
            <div class="flex justify-between items-start">
                <div>
                     <h3 class="text-2xl font-black text-white tracking-tighter">{{ auth()->user()->name }}</h3>
                     <p class="text-sm font-medium text-muted">{{ auth()->user()->email }}</p>
                </div>
                <!-- Badge Role -->
                 @if(auth()->user()->isAdmin())
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">Admin</span>
                @elseif(auth()->user()->isProducao())
                     <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Produção</span>
                @else
                     <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500 border border-amber-500/20">Vendedor</span>
                @endif
            </div>

            <div class="mt-8 space-y-3">
                 @if(auth()->user()->store)
                <div class="flex items-center p-4 bg-white/5 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center text-muted mr-4">
                         <i class="fa-solid fa-store text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-muted uppercase tracking-widest">Unidade</p>
                        <p class="text-sm font-bold text-white">{{ auth()->user()->store }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex items-center p-4 bg-white/5 rounded-2xl border border-white/5">
                    <div class="w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center text-muted mr-4">
                         <i class="fa-solid fa-calendar text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-muted uppercase tracking-widest">Início</p>
                        <p class="text-sm font-bold text-white">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-4 px-6 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 active:scale-95">
                        Sair
                    </button>
                </form>
                 <button @click="showProfileModal = false" class="flex-1 py-4 px-6 bg-white/5 text-white hover:bg-white/10 rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-300 active:scale-95">
                    Voltar
                </button>
            </div>
        </div>
    </div>
</div>

</div>
