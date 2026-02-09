{{-- Navbar --}}
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" 
        x-data="{ scrolled: false, mobileOpen: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
        :class="scrolled ? 'navbar-scrolled' : ''">
    <div class="landing-wrapper">
        <nav class="flex items-center justify-between h-16 lg:h-20">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 z-50">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-8 w-auto brightness-0 invert">
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="#workflow" class="nav-link">Como Funciona</a>
                <a href="#features" class="nav-link">Funcionalidades</a>
                <a href="#pricing" class="nav-link">Planos</a>
            </div>

            {{-- Desktop CTAs --}}
            <div class="hidden lg:flex items-center gap-4">
                <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                <a href="{{ route('register.public') }}" class="btn-primary text-sm py-2 px-4">
                    Teste Grátis
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileOpen = !mobileOpen" 
                    class="lg:hidden relative z-50 w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors">
                <svg x-show="!mobileOpen" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </nav>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 lg:hidden">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="mobileOpen = false"></div>
        
        {{-- Menu Content --}}
        <div class="absolute top-20 left-4 right-4 bg-surface-elevated rounded-2xl border border-white/10 p-6 shadow-2xl">
            <nav class="flex flex-col gap-4">
                <a href="#workflow" @click="mobileOpen = false" class="text-white text-lg font-medium py-2 hover:text-purple-400 transition-colors">Como Funciona</a>
                <a href="#features" @click="mobileOpen = false" class="text-white text-lg font-medium py-2 hover:text-purple-400 transition-colors">Funcionalidades</a>
                <a href="#pricing" @click="mobileOpen = false" class="text-white text-lg font-medium py-2 hover:text-purple-400 transition-colors">Planos</a>
                
                <hr class="border-white/10 my-2">
                
                <a href="{{ route('login') }}" class="text-white text-lg font-medium py-2 hover:text-purple-400 transition-colors">Entrar</a>
                <a href="{{ route('register.public') }}" class="btn-primary text-center py-3 mt-2">
                    Teste Grátis
                </a>
            </nav>
        </div>
    </div>
</header>
