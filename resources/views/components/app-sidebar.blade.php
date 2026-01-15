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
<div class="md:hidden fixed top-0 inset-x-0 h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm z-50 flex items-center justify-between px-4 transition-all duration-300">
    <button @click.stop="toggle()"
            class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-md">
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
             class="absolute top-12 right-0 w-80 max-h-[80vh] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col z-[100]">
            
             <!-- Header -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    Notificações
                    <span x-show="unreadCount > 0" x-text="'(' + unreadCount + ')'" class="ml-1 text-xs text-indigo-600 dark:text-indigo-400"></span>
                </h3>
                 <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">Lidas</button>
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
            isMobile() ? 'w-full max-w-full' : (expanded ? 'w-64' : 'w-16'),
            (mobileOpen || !isMobile()) ? 'translate-x-0' : '-translate-x-full'
       ]"
       class="fixed top-0 left-0 z-[60] h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 ease-in-out transform md:translate-x-0 shadow-xl md:shadow-none">
    
    <!-- Header do Sidebar com Botão Toggle -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-300"
         :class="expanded ? '' : 'justify-center px-0'">
        <div class="flex items-center overflow-hidden" x-show="expanded">
            @if(auth()->user()->tenant && auth()->user()->tenant->logo_path)
                <img src="{{ Storage::url(auth()->user()->tenant->logo_path) }}" alt="Logo" class="h-8 w-auto object-contain">
            @else
                <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-violet-500 whitespace-nowrap">
                    {{ auth()->user()->tenant->name ?? 'Vestalize' }}
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
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
           :class="expanded ? 'justify-start' : 'justify-center'"
           title="Dashboard">
            <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="whitespace-nowrap overflow-hidden transition-all duration-300 ml-3 font-semibold"
                  x-show="expanded">
                Tela Inicial
            </span>
        </a>

        @if(Auth::user()->isEstoque() && !Auth::user()->isAdmin())
            <!-- Sidebar Simplificada para Estoque (Sem grupos) -->
            <!-- ... Itens estoque ... -->
             <a href="{{ route('stocks.index') }}" 
               class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('stocks*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
               :class="expanded ? 'justify-start' : 'justify-center'">
               <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span class="ml-3 font-semibold" x-show="expanded">Estoque</span>
            </a>
             <a href="{{ route('stock-requests.index') }}" 
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('stock-requests.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                 <span class="ml-3 font-semibold" x-show="expanded">Solicitações</span>
             </a>
              <a href="{{ route('sewing-machines.index') }}" 
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('sewing-machines.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                 <span class="ml-3 font-semibold" x-show="expanded">Máq. Costura</span>
             </a>
              <a href="{{ route('production-supplies.index') }}" 
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('production-supplies.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                 <span class="ml-3 font-semibold" x-show="expanded">Suprimentos</span>
             </a>
              <a href="{{ route('uniforms.index') }}" 
                class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('uniforms.*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
                :class="expanded ? 'justify-start' : 'justify-center'">
                <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                 <span class="ml-3 font-semibold" x-show="expanded">Uniformes/EPI</span>
             </a>
        @else
            <!-- Sidebar Completa com Grupos -->
            
            <div class="mt-2 text-nowrap">
                <a href="{{ route('sales.index') }}"
                   class="flex items-center w-full px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('sales.index') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400' }}"
                   :class="expanded ? 'justify-start' : 'justify-center'">
                    <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="ml-3" x-show="expanded">Vendas</span>
                </a>
            </div>

            <!-- GRUPO: ESTOQUE -->
             @if((Auth::user()->isAdmin() || Auth::user()->isEstoque() || Auth::user()->isVendedor()) && (Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('stock')))
             <div class="mt-1">
                <button @click="toggleGroup('estoque')"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:text-gray-500 dark:group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="ml-3 font-semibold" x-show="expanded">Estoque</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.estoque ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                
                <div x-show="openGroups.estoque && expanded" x-collapse x-cloak class="space-y-1 mt-1 bg-transparent dark:bg-gray-900/10 rounded-md overflow-hidden">
                    <a href="{{ Auth::user()->isVendedor() ? route('stocks.view') : route('stocks.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ (request()->routeIs('stocks.*') || request()->routeIs('stocks.view')) ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-purple-500/10' }}">
                        
                        Geral
                    </a>
                    <a href="{{ route('fabric-pieces.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ request()->routeIs('fabric-pieces.*') ? 'bg-purple-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-purple-500/10' }}">
                        
                        Peças de Tecido
                    </a>
                    <a href="{{ route('sewing-machines.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Máq. Costura
                    </a>
                    <a href="{{ route('production-supplies.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Suprimentos
                    </a>
                    <a href="{{ route('uniforms.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Uniformes/EPI
                    </a>
                    @if(!Auth::user()->isVendedor())
                    <a href="{{ route('stock-requests.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Solicitações
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- GRUPO: CATÃLOGO (Apenas Admin) -->
             @if(Auth::user()->isAdmin())
            <div class="mt-1">
                <button @click="toggleGroup('catalogo')"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:text-gray-500 dark:group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="ml-3 font-semibold" x-show="expanded">Catálogo</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.catalogo ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                
                <div x-show="openGroups.catalogo && expanded" x-collapse x-cloak class="space-y-1 mt-1 bg-transparent dark:bg-gray-900/10 rounded-md overflow-hidden">
                    <a href="{{ route('admin.products.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Produtos
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Categorias
                    </a>
                    <a href="{{ route('admin.tecidos.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Tecidos
                    </a>
                    <a href="{{ route('admin.modelos.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Modelos
                    </a>
                    @if(Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('sublimation_total'))
                    <a href="{{ route('admin.sublimation-products.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Sublimação Total
                    </a>
                    @endif
                    <a href="{{ route('admin.sub-local-products.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Sublimação Local
                    </a>
                </div>
            </div>
            @endif

            <!-- GRUPO: PRODUÃÃO -->
             @if(Auth::user()->isProducao() || Auth::user()->isAdmin())
            <div class="mt-1">
                <button @click="toggleGroup('producao')"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:text-gray-500 dark:group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="ml-3 font-semibold" x-show="expanded">Produção</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.producao ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                
                <div x-show="openGroups.producao && expanded" x-collapse x-cloak class="space-y-1 mt-1 bg-transparent dark:bg-gray-900/10 rounded-md overflow-hidden">
                    <a href="{{ route('production.dashboard') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Dashboard
                    </a>
                    <a href="{{ route('production.index') }}" class="flex items-center pl-10 pr-3 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Ordens
                    </a>
                </div>
            </div>
            @endif

            <!-- GRUPO: FINANCEIRO -->
             @if((Auth::user()->isAdmin() || Auth::user()->isCaixa()) && (Auth::user()->tenant_id === null || Auth::user()->tenant?->canAccess('financial')))
            <div class="mt-1">
                <button @click="toggleGroup('financeiro')"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:text-gray-500 dark:group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 font-semibold" x-show="expanded">Financeiro</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.financeiro ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                
                <div x-show="openGroups.financeiro && expanded" x-collapse x-cloak class="space-y-1 mt-1">
                    @php 
                        $currentPath = request()->path();
                        $dashActive = ($currentPath === 'financeiro');
                        $cashActive = ($currentPath === 'cash');
                        $approvalsActive = str_starts_with($currentPath, 'cash/approvals');
                        $invoicesActive = str_starts_with($currentPath, 'financeiro/nfe');
                        $configActive = ($currentPath === 'settings/nfe');
                    @endphp
                    
                    {{-- Dashboard --}}
                    <a href="{{ route('financial.dashboard') }}" 
                       data-no-js-nav
                       class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ request()->routeIs('financial.dashboard') ? 'bg-purple-600 text-white rounded-md' : 'text-gray-400 hover:text-white hover:bg-purple-500/10' }}">
                        
                        Dashboard
                    </a>
                    
                    {{-- Caixa --}}
                    <a href="{{ route('cash.index') }}" 
                       data-no-js-nav
                       class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ $cashActive ? 'bg-blue-600 text-white rounded-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        
                        Caixa
                    </a>
                    
                    {{-- Aprovações --}}
                    <a href="{{ route('cash.approvals.index') }}" 
                       data-no-js-nav
                       class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ $approvalsActive ? 'bg-blue-600 text-white rounded-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        
                        Aprovações
                    </a>
                    
                    {{-- Notas Emitidas --}}
                    <a href="{{ route('admin.invoices.index') }}" 
                       data-no-js-nav
                       class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ $invoicesActive ? 'bg-blue-600 text-white rounded-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        
                        Notas Emitidas
                    </a>
                    
                    {{-- Configuração NF-e --}}
                    <a href="{{ route('admin.invoice-config.edit') }}" 
                       data-no-js-nav
                       class="flex items-center pl-10 pr-3 py-2.5 text-sm transition {{ $configActive ? 'bg-blue-600 text-white rounded-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        
                        Configuração NF-e
                    </a>
                </div>
            </div>
            @endif

            {{-- MINHA ASSINATURA (Apenas para usuários de tenant com permissão) --}}
            @if(Auth::user()->tenant_id !== null && auth()->user()->tenant?->canAccess('subscription_module'))
            <div class="mt-2">
                <a href="{{ route('subscription.index') }}"
                   class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 group
                          {{ request()->is('subscription*') 
                             ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' 
                             : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                   :class="expanded ? 'justify-start' : 'justify-center'">
                    <svg class="flex-shrink-0 h-5 w-5 {{ request()->is('subscription*') ? 'text-indigo-600' : 'text-gray-500 group-hover:text-indigo-600 dark:text-gray-400 dark:group-hover:text-indigo-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="ml-3" x-show="expanded">Minha Assinatura</span>
                </a>
            </div>
            @endif



            <!-- GRUPO: ASSINATURAS (Apenas Super Admin) -->
            {{-- GRUPO: ASSINATURAS (Super Admin apenas - sem tenant_id) --}}
            @if(Auth::user()->isAdmin() && Auth::user()->tenant_id === null)
            <div class="mt-1">
                <button @click="toggleGroup('assinaturas')"
                        class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-500 group-hover:text-indigo-600 dark:text-gray-400 dark:group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="ml-3" x-show="expanded">Assinaturas</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.assinaturas ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div x-show="openGroups.assinaturas && expanded" x-collapse x-cloak class="space-y-1 mt-1 bg-transparent dark:bg-gray-900/10 rounded-md overflow-hidden">
                    <a href="{{ route('admin.tenants.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Gerenciar
                    </a>
                    <a href="{{ route('admin.plans.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Planos
                    </a>
                    <a href="{{ route('admin.subscription-payments.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Pagamentos
                    </a>
                    <a href="{{ route('admin.affiliates.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Afiliados 👥
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        
                        Lista VIP 🚀
                    </a>
                </div>
            </div>
            @endif

             <!-- GRUPO: SISTEMA -->
            <div class="mt-1">
                 <button @click="toggleGroup('sistema')"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/10 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200 group"
                        :class="expanded ? 'justify-between' : 'justify-center'">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-purple-600 dark:text-gray-500 dark:group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="ml-3 font-semibold" x-show="expanded">Sistema</span>
                    </div>
                    <svg x-show="expanded" class="w-4 h-4 transition-transform duration-200" :class="openGroups.sistema ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                 <div x-show="openGroups.sistema && expanded" x-collapse x-cloak class="space-y-1 mt-1 bg-transparent dark:bg-gray-900/10 rounded-md overflow-hidden">
                    @if(!Auth::user()->isVendedor())
                    <a href="{{ route('settings.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                         
                        Configurações
                    </a>
                    @endif
                     <a href="{{ route('links.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                         
                        Links Úteis
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                         
                        Usuários
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="flex items-center pl-10 pr-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                         
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
                 class="w-full flex items-center justify-center p-2 mb-4 rounded-xl transition-all"
                 :class="isDark ? 'text-purple-400 bg-purple-500/10' : 'text-purple-600 bg-purple-50'">
             <i class="fa-solid" :class="isDark ? 'fa-moon' : 'fa-sun'"></i>
         </button>

        <button @click="showProfileModal = true" 
                class="profile-btn-custom"
                :class="expanded ? 'justify-start' : 'justify-center'">
            <div class="profile-avatar-custom">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex flex-col items-start ml-3 overflow-hidden" x-show="expanded">
                <span class="text-xs font-bold text-gray-800 dark:text-gray-100 truncate w-full max-w-[140px]">
                    {{ auth()->user()->name }}
                </span>
                <span class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                    Ver Perfil
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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        <!-- Header com gradiente -->
        <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-600">
            <button @click="showProfileModal = false" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="absolute -bottom-10 left-6">
                 <div class="flex items-center justify-center h-20 w-20 rounded-full bg-white dark:bg-gray-800 border-4 border-white dark:border-gray-800 text-blue-600 text-3xl font-bold shadow-md">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </div>

        <div class="pt-12 px-6 pb-6">
            <div class="flex justify-between items-start">
                <div>
                     <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                     <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <!-- Badge Role -->
                 @if(auth()->user()->isAdmin())
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Admin</span>
                @elseif(auth()->user()->isProducao())
                     <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">Produção</span>
                @else
                     <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Vendedor</span>
                @endif
            </div>

            <div class="mt-6 space-y-4">
                 @if(auth()->user()->store)
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="p-2 bg-white dark:bg-gray-800 rounded-md shadow-sm mr-3">
                         <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Loja Vinculada</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->store }}</p>
                    </div>
                </div>
                @endif
                
                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="p-2 bg-white dark:bg-gray-800 rounded-md shadow-sm mr-3">
                         <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Membro desde</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg font-medium transition duration-200 border border-transparent">
                        Sair do Sistema
                    </button>
                </form>
                 <button @click="showProfileModal = false" class="flex-1 py-2.5 px-4 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition duration-200">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

</div>
