{{-- Hero Section --}}
<section class="relative w-full flex flex-col items-center pt-16 lg:pt-24 pb-12 overflow-hidden">
    <div class="landing-wrapper relative z-10">
        <div class="flex flex-col items-center text-center px-4">
            {{-- Badge --}}
            <div class="animate-fade-in-up badge-glow flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full">
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-purple-600 text-white stay-white shadow-sm shadow-purple-500/20">
                    Novo
                </span>
                <span class="text-xs sm:text-sm text-foreground/80 font-medium">
                    Feito para quem vende personalizados
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mt-6 lg:mt-8">
                <span class="animate-word animate-fade-in-blur inline-block">Chega</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-100">de</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-200">Planilha</span>
                <br class="sm:hidden">
                <span class="animate-word animate-fade-in-blur inline-block delay-300">e</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-400">WhatsApp</span>
                <br>
                <span class="animate-word animate-fade-in-blur inline-block delay-500 text-gradient-primary">Organize Seus Pedidos</span>
            </h1>

            {{-- Description --}}
            <p class="text-sm sm:text-base lg:text-lg text-muted mt-4 lg:mt-6 max-w-2xl animate-fade-in-up delay-300 px-2">
                Sistema completo para quem trabalha com <strong class="text-white">sublimação, serigrafia, DTF, canecas, camisetas e brindes</strong>. 
                Controle pedidos, artes, materiais e entregas.
            </p>

            {{-- Pain Points --}}
            <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-2 sm:gap-3 mt-4 lg:mt-6 animate-fade-in-up delay-400">
                <span class="px-3 py-1.5 text-xs rounded-full bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Pedidos perdidos no WhatsApp
                </span>
                <span class="px-3 py-1.5 text-xs rounded-full bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Artes sem aprovação
                </span>
                <span class="px-3 py-1.5 text-xs rounded-full bg-red-500/10 border border-red-500/20 text-red-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Entregas atrasadas
                </span>
            </div>

            {{-- CTAs --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 w-full sm:w-auto justify-center mt-6 lg:mt-8 animate-fade-in-up delay-500 px-4">
                <a href="{{ route('register.public') }}" class="btn-primary text-sm sm:text-base py-3 px-6 w-full sm:w-auto text-center">
                    Testar Grátis por 14 Dias
                </a>
                <a href="#workflow" class="btn-outline text-sm sm:text-base py-3 px-6 w-full sm:w-auto text-center">
                    Ver Como Funciona
                </a>
            </div>

            {{-- Social Proof Mini --}}
            <div class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs sm:text-sm text-muted mt-4 lg:mt-6 animate-fade-in-up delay-600">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Sem cartão de crédito
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Cancele quando quiser
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Suporte via WhatsApp
                </span>
            </div>
        </div>

        {{-- Dashboard Preview --}}
        <div class="mt-8 lg:mt-20 relative animate-fade-in-up delay-500 px-4">
            <div class="dashboard-preview">
                <div class="dashboard-preview-inner">
                    <img 
                        src="{{ asset('images/landing/dashboard.png') }}" 
                        alt="Dashboard Vestalize - Kanban de Pedidos" 
                        class="w-full h-auto"
                        onerror="this.onerror=null;this.src='https://placehold.co/1400x900/1a1a1a/333?text=Dashboard+Preview';"
                    >
                </div>
            </div>
            
            {{-- Gradient overlay --}}
            <div class="hero-gradient-overlay"></div>

            {{-- Top glow --}}
            <div class="absolute top-0 inset-x-0 w-3/5 mx-auto h-20 rounded-full bg-purple-600 blur-[64px] opacity-40 -z-10"></div>

            {{-- Floating Badges (desktop only) --}}
            <div class="hidden lg:block">
                <div class="floating-badge flex items-center gap-2" style="top: 10%; left: 3%; animation-delay: 0s;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Sublimação
                </div>
                <div class="floating-badge flex items-center gap-2" style="top: 20%; right: 5%; animation-delay: 0.5s;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Camisetas
                </div>
                <div class="floating-badge flex items-center gap-2" style="top: 45%; left: 8%; animation-delay: 1s;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Canecas
                </div>
                <div class="floating-badge flex items-center gap-2" style="top: 35%; right: 10%; animation-delay: 1.5s;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    Brindes
                </div>
                <div class="floating-badge flex items-center gap-2" style="top: 60%; right: 3%; animation-delay: 2s;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    DTF / Silk
                </div>
            </div>
        </div>
    </div>
</section>
