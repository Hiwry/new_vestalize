<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Marketplace') | Vestalize</title>
    <meta name="description" content="@yield('description', 'Marketplace de design da Vestalize — serviços, ferramentas e designers para sua confecção.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="landing-page antialiased">
    <div class="landing-bg"></div>

    {{-- ─── Navbar ──────────────────────────────────────────────── --}}
    <div class="navbar-backdrop hidden lg:block"></div>
    <header class="landing-navbar" x-data="{ mobileOpen: false }">
        <div class="landing-navbar-inner">
            <a href="/" class="flex items-center gap-2 group -ml-2">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-8 w-auto transition-transform group-hover:scale-105">
            </a>

            <nav class="hidden lg:flex items-center gap-1 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <a href="{{ route('marketplace.home') }}" class="text-sm {{ request()->routeIs('marketplace.home') ? 'text-foreground font-semibold' : 'text-muted' }} hover:text-foreground font-medium transition-colors px-3 py-1.5">
                    Marketplace
                </a>
                <a href="{{ route('marketplace.services.index') }}" class="text-sm {{ request()->routeIs('marketplace.services.*') ? 'text-foreground font-semibold' : 'text-muted' }} hover:text-foreground font-medium transition-colors px-3 py-1.5">
                    Serviços
                </a>
                <a href="{{ route('marketplace.tools.index') }}" class="text-sm {{ request()->routeIs('marketplace.tools.*') ? 'text-foreground font-semibold' : 'text-muted' }} hover:text-foreground font-medium transition-colors px-3 py-1.5">
                    Ferramentas
                </a>
                <a href="{{ route('marketplace.designers') }}" class="text-sm {{ request()->routeIs('marketplace.designers*') ? 'text-foreground font-semibold' : 'text-muted' }} hover:text-foreground font-medium transition-colors px-3 py-1.5">
                    Designers
                </a>
                @guest
                    <a href="{{ route('designer.register') }}" class="text-sm text-primary hover:text-foreground font-medium transition-colors px-3 py-1.5 flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                        Seja Designer
                    </a>
                @endguest
            </nav>

            <div class="flex items-center gap-3">
                <button id="theme-toggle" class="p-2 text-muted hover:text-foreground transition-colors" title="Alternar Tema">
                    <svg id="sun-icon-mkt" class="w-5 h-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg id="moon-icon-mkt" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @auth
                    @if(Auth::user()->isDesigner())
                        <a href="{{ route('marketplace.home') }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                            Meu Painel
                        </a>
                        <form method="POST" action="{{ route('designer.logout') }}" class="hidden lg:block">
                            @csrf
                            <button type="submit" class="text-sm text-muted hover:text-foreground font-medium transition-colors">Sair</button>
                        </form>
                    @else
                        <a href="{{ route('dashboard') }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                            Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('designer.login') }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register.public') }}" class="btn-primary text-sm py-2 px-4">
                        Cadastre-se
                    </a>
                @endauth

                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-muted hover:text-foreground">
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
        <div x-show="mobileOpen" x-transition x-cloak class="lg:hidden px-4 pb-4 space-y-2">
            <a href="{{ route('marketplace.home') }}" class="block py-2 text-muted hover:text-foreground">Marketplace</a>
            <a href="{{ route('marketplace.services.index') }}" class="block py-2 text-muted hover:text-foreground">Serviços</a>
            <a href="{{ route('marketplace.tools.index') }}" class="block py-2 text-muted hover:text-foreground">Ferramentas</a>
            <a href="{{ route('marketplace.designers') }}" class="block py-2 text-muted hover:text-foreground">Designers</a>
            @guest
                <a href="{{ route('designer.register') }}" class="block py-2 text-primary font-bold">Seja Designer</a>
                <a href="{{ route('login') }}" class="block py-2 text-muted hover:text-foreground">Login</a>
            @endguest
        </div>
    </header>

    {{-- ─── Content ─────────────────────────────────────────────── --}}
    <main class="min-h-screen pt-40 md:pt-48 pb-16">
        @if(session('success'))
            <div class="landing-wrapper mb-6">
                <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ─── Footer ──────────────────────────────────────────────── --}}
    @include('landing.partials.footer')

    <script src="{{ asset('js/landing.js') }}"></script>
    @stack('scripts')
</body>
</html>
