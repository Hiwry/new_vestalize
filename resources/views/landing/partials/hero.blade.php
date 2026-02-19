{{-- Hero Section --}}
<section class="relative w-full flex flex-col items-center pt-20 lg:pt-24 pb-12 overflow-hidden">
    <div class="landing-wrapper relative z-10">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="animate-fade-in-up badge-glow flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full">
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-purple-600 text-white stay-white shadow-sm shadow-purple-500/20">
                    Novo
                </span>
                <span class="text-sm text-foreground/80 font-medium">
                    Conheça o Vestalize OS
                </span>
            </div>

            {{-- Title --}}
            <h1 class="landing-title mt-8">
                <span class="animate-word animate-fade-in-blur inline-block">O</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-100">Sistema</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-200">para</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-300">Confecções</span>
                <br class="hidden md:block">
                <span class="animate-word animate-fade-in-blur inline-block delay-400">de</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-500 text-gradient-primary">Alta</span>
                <span class="animate-word animate-fade-in-blur inline-block delay-600 text-gradient-primary">Performance</span>
            </h1>

            {{-- Description --}}
            <p class="landing-desc mt-6 max-w-2xl animate-fade-in-up delay-300">
                Pare de usar planilhas e WhatsApp. Vestalize centraliza pedidos, produção, estoque e financeiro em um só lugar, permitindo que você foque no que importa: vender mais.
            </p>

            {{-- CTAs --}}
            <div class="flex items-center gap-4 flex-wrap justify-center mt-8 animate-fade-in-up delay-400">
                <a href="{{ route('register.public') }}" class="btn-primary text-base py-3 px-6">
                    Teste Grátis
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
                        alt="Dashboard Vestalize" 
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
                <div class="floating-badge" style="top: 15%; left: 5%; animation-delay: 0s;">
                    Gestão de Pedidos
                </div>
                <div class="floating-badge" style="top: 25%; right: 8%; animation-delay: 0.5s;">
                    Controle de Estoque
                </div>
                <div class="floating-badge" style="top: 55%; left: 10%; animation-delay: 1s;">
                    Orçamentos em PDF
                </div>
                <div class="floating-badge" style="top: 65%; right: 15%; animation-delay: 1.5s;">
                    Kanban de Produção
                </div>
            </div>
        </div>
    </div>
</section>
