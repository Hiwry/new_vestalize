{{-- Navbar Partial --}}
<div class="navbar-backdrop hidden lg:block"></div>

<header class="landing-navbar" x-data="{ mobileOpen: false }">
    <div class="landing-navbar-inner">
        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2 group -ml-2">
            <img
                src="{{ asset('vestalize.svg') }}"
                alt="Vestalize"
                class="h-8 w-auto transition-transform group-hover:scale-105"
            >
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hidden lg:flex items-center gap-1 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <a href="#workflow" class="text-sm text-muted hover:text-foreground font-medium transition-colors px-3 py-1.5">
                Como Funciona
            </a>
            <a href="#features" class="text-sm text-muted hover:text-foreground font-medium transition-colors px-3 py-1.5">
                Funcionalidades
            </a>
            <a href="#pricing" class="text-sm text-muted hover:text-foreground font-medium transition-colors px-3 py-1.5">
                Planos
            </a>
        </nav>

        {{-- CTA Buttons --}}
        <div class="flex items-center gap-3">
            {{-- Theme Toggle Button --}}
            <button id="theme-toggle" class="p-2 text-muted hover:text-foreground transition-colors" title="Alternar Tema">
                <svg id="sun-icon" class="w-5 h-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg id="moon-icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <a href="{{ config('app.url') . '/login' }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                Login
            </a>
            <a href="{{ config('app.url') . '/registro' }}" class="hidden lg:inline-flex btn-primary text-sm py-2 px-4">
                Teste Grátis
            </a>
            
            {{-- Mobile Menu Button --}}
            <button 
                @click="mobileOpen = !mobileOpen" 
                class="lg:hidden p-2 text-muted hover:text-foreground"
            >
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div 
        x-show="mobileOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        x-cloak
        class="lg:hidden px-4 pb-4 space-y-2"
    >
        <a href="#workflow" @click="mobileOpen = false" class="block py-2 text-muted hover:text-foreground">
            Como Funciona
        </a>
        <a href="#features" @click="mobileOpen = false" class="block py-2 text-muted hover:text-foreground">
            Funcionalidades
        </a>
        <a href="#pricing" @click="mobileOpen = false" class="block py-2 text-muted hover:text-foreground">
            Planos
        </a>
        <a href="{{ config('app.url') . '/login' }}" class="block py-2 text-muted hover:text-foreground">
            Login
        </a>
        <a href="{{ config('app.url') . '/registro' }}" class="block w-full text-center btn-primary text-sm py-2.5 mt-2">
            Teste Grátis
        </a>
    </div>
</header>
