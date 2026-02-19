{{-- Hero Section - Personalizados --}}
<section class="relative w-full flex flex-col items-center pt-20 lg:pt-24 pb-12 overflow-hidden">
    <div class="landing-wrapper relative z-10">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="animate-fade-in-up badge-glow flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full">
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-purple-600 text-white stay-white shadow-sm shadow-purple-500/20">
                    Novo
                </span>
                <span class="text-sm text-foreground/80 font-medium">
                    Sistema para Personalizados
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mt-6 lg:mt-8 px-2 max-w-full">
                <span class="animate-word animate-fade-in-blur inline-block">Chega</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-100">de</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-200">Planilha</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-300">e</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-300">WhatsApp</span>
                <br class="hidden md:block">
                <span class="animate-word animate-fade-in-blur inline-block delay-400 text-gradient-primary">Organize</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-500 text-gradient-primary">Seus</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-600 text-gradient-primary">Pedidos</span>
            </h1>

            {{-- Description --}}
            <p class="text-sm sm:text-base lg:text-lg text-muted mt-4 lg:mt-6 max-w-2xl animate-fade-in-up delay-300 px-4">
                Sistema completo para quem trabalha com <strong class="stay-white">sublimação, serigrafia, DTF, canecas, camisetas e brindes</strong>. 
                Controle pedidos, artes, materiais e entregas.
            </p>

            {{-- CTAs --}}
            <div class="flex items-center gap-4 flex-wrap justify-center mt-8 animate-fade-in-up delay-400">
                <a href="{{ config('app.url') . '/registro' }}" class="btn-primary text-base py-3 px-6">
                    Comece com 14 dias grátis
                </a>
                <a href="#features" class="btn-outline text-base py-3 px-6">
                    Ver Funcionalidades
                </a>
            </div>
        </div>

        {{-- Dashboard Preview --}}
        <div class="mt-12 lg:mt-20 relative animate-fade-in-up delay-500">
            <div class="dashboard-preview">
                <div class="dashboard-preview-inner">
                    <img 
                        src="{{ asset('images/landing/dashboard.png') }}" 
                        alt="Dashboard Vestalize - Personalizados" 
                        class="w-full h-auto"
                        onerror="this.onerror=null;this.src='https://placehold.co/1400x900/1a1a1a/333?text=Dashboard+Personalizados';"
                    >
                </div>
            </div>
            
            {{-- Gradient overlay --}}
            <div class="hero-gradient-overlay"></div>

            {{-- Top glow --}}
            <div class="absolute top-0 inset-x-0 w-3/5 mx-auto h-20 rounded-full bg-purple-600 blur-[64px] opacity-40 -z-10"></div>

            {{-- Floating Badges (desktop only) --}}
            <div class="hidden lg:block">
                <div class="floating-badge flex items-center gap-2 !rounded-full !py-2 !px-4 !bg-black/80 !backdrop-blur-md !border !border-white/20 !text-white !shadow-2xl !w-fit !whitespace-nowrap" style="top: 15%; left: 10%; animation-delay: 0s;">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="font-semibold tracking-wide">Sublimação</span>
                </div>
                <div class="floating-badge flex items-center gap-2 !rounded-full !py-2 !px-4 !bg-black/80 !backdrop-blur-md !border !border-white/20 !text-white !shadow-2xl !w-fit !whitespace-nowrap" style="top: 25%; right: 12%; animation-delay: 0.5s;">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span class="font-semibold tracking-wide">Camisetas</span>
                </div>
                <div class="floating-badge flex items-center gap-2 !rounded-full !py-2 !px-4 !bg-black/80 !backdrop-blur-md !border !border-white/20 !text-white !shadow-2xl !w-fit !whitespace-nowrap" style="top: 50%; left: 15%; animation-delay: 1s;">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span class="font-semibold tracking-wide">Canecas</span>
                </div>
                <div class="floating-badge flex items-center gap-2 !rounded-full !py-2 !px-4 !bg-black/80 !backdrop-blur-md !border !border-white/20 !text-white !shadow-2xl !w-fit !whitespace-nowrap" style="top: 40%; right: 18%; animation-delay: 1.5s;">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    <span class="font-semibold tracking-wide">Brindes</span>
                </div>
                <div class="floating-badge flex items-center gap-2 !rounded-full !py-2 !px-4 !bg-black/80 !backdrop-blur-md !border !border-white/20 !text-white !shadow-2xl !w-fit !whitespace-nowrap" style="top: 65%; right: 10%; animation-delay: 2s;">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    <span class="font-semibold tracking-wide">DTF / Silk</span>
                </div>
            </div>
        </div>
    </div>
</section>
