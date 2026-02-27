<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketplace | Vestalize</title>
    <meta name="description" content="Contrate designers especializados, compre ferramentas digitais e potencialize sua confecção.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Vite CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Landing Page CSS (design system) --}}
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="landing-page antialiased">
    {{-- Background --}}
    <div class="landing-bg"></div>
    
    {{-- Navbar --}}
    <div class="navbar-backdrop hidden lg:block"></div>
    <header class="landing-navbar" x-data="{ mobileOpen: false }">
        <div class="landing-navbar-inner">
            <a href="/" class="flex items-center gap-2 group -ml-2">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-8 w-auto transition-transform group-hover:scale-105">
            </a>

            <nav class="hidden lg:flex items-center gap-1 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <a href="{{ route('marketplace.home') }}" class="text-sm {{ request()->routeIs('marketplace.home') ? 'text-foreground font-semibold' : 'text-muted' }} hover:text-foreground font-medium transition-colors px-3 py-1.5">
                    Início
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
                    <a href="{{ route('dashboard') }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden lg:block text-sm text-muted hover:text-foreground font-medium transition-colors">
                        Login
                    </a>
                @endauth
                
                @auth
                    <a href="{{ route('marketplace.credits.index') }}" class="btn-primary text-sm py-2 px-4">
                        Meus Créditos
                    </a>
                @else
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
            <a href="{{ route('marketplace.home') }}" class="block py-2 text-muted hover:text-foreground">Início</a>
            <a href="{{ route('marketplace.services.index') }}" class="block py-2 text-muted hover:text-foreground">Serviços</a>
            <a href="{{ route('marketplace.tools.index') }}" class="block py-2 text-muted hover:text-foreground">Ferramentas</a>
            <a href="{{ route('marketplace.designers') }}" class="block py-2 text-muted hover:text-foreground">Designers</a>
            @guest
                <a href="{{ route('designer.register') }}" class="block py-2 text-primary font-bold">Seja Designer</a>
                <a href="{{ route('login') }}" class="block py-2 text-muted hover:text-foreground">Login</a>
            @endguest
        </div>
    </header>

    <main class="min-h-screen">
        <div class="h-8 lg:h-10"></div>

        {{-- ─── Hero Section ─────────────────────────────────────── --}}
        <section class="relative w-full flex flex-col items-center pt-20 lg:pt-24 pb-12">
            <div class="landing-wrapper relative z-10">
                <div class="flex flex-col items-center text-center">
                    {{-- Badge --}}
                    <div class="animate-fade-in-up badge-glow flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-purple-600 text-white stay-white shadow-sm shadow-purple-500/20">
                            Marketplace
                        </span>
                        <span class="text-sm text-foreground/80 font-medium">
                            Design Profissional
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="landing-title mt-8">
                        <span class="animate-word animate-fade-in-blur inline-block">Encontre</span>
                        <span class="animate-word animate-fade-in-blur inline-block delay-100">o</span>
                        <span class="animate-word animate-fade-in-blur inline-block delay-200">Designer</span>
                        <br class="hidden md:block">
                        <span class="animate-word animate-fade-in-blur inline-block delay-300 text-gradient-primary">Perfeito</span>
                        <span class="animate-word animate-fade-in-blur inline-block delay-400">para Sua</span>
                        <span class="animate-word animate-fade-in-blur inline-block delay-500 text-gradient-primary">Confecção</span>
                    </h1>

                    {{-- Description --}}
                    <p class="landing-desc mt-6 max-w-2xl animate-fade-in-up delay-300">
                        Contrate designers especializados em logos, estampas e vetores. Compre ferramentas digitais exclusivas para turbinar sua produção.
                    </p>

                    {{-- CTAs --}}
                    <div class="flex items-center gap-4 flex-wrap justify-center mt-8 animate-fade-in-up delay-400">
                        <a href="{{ route('marketplace.services.index') }}" class="btn-primary text-base py-3 px-6">
                            Explorar Serviços
                        </a>
                        <a href="{{ route('marketplace.tools.index') }}" class="btn-outline text-base py-3 px-6">
                            Loja de Ferramentas
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center gap-8 mt-12 animate-fade-in-up delay-500">
                        @php
                            $totalDesigners = $topDesigners->count();
                            $totalServices = $featuredServices->count() + $latestServices->count();
                        @endphp
                        <div class="text-center">
                            <span class="text-2xl font-semibold text-heading">{{ $totalDesigners }}+</span>
                            <p class="text-xs text-muted mt-1">Designers</p>
                        </div>
                        <div class="w-px h-8 bg-border opacity-50"></div>
                        <div class="text-center">
                            <span class="text-2xl font-semibold text-heading">{{ $totalServices }}+</span>
                            <p class="text-xs text-muted mt-1">Serviços</p>
                        </div>
                        <div class="w-px h-8 bg-border opacity-50"></div>
                        <div class="text-center">
                            <span class="text-2xl font-semibold text-heading">10%</span>
                            <p class="text-xs text-muted mt-1">Desconto Assinantes</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── Featured Services ────────────────────────────────── --}}
        <section class="w-full py-16 lg:py-24 relative">
            <div class="hidden lg:block absolute -z-10 bottom-0 -left-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

            <div class="landing-wrapper">
                <div class="flex flex-col items-center text-center">
                    <div class="section-badge scroll-animate">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Serviços em Destaque
                    </div>

                    <h2 class="landing-title mt-6 scroll-animate delay-100">
                        Talentos selecionados
                        <br>
                        <span class="text-gradient-primary">para sua produção</span>
                    </h2>

                    <p class="landing-desc mt-4 scroll-animate delay-200">
                        Designers especializados em logos, vetorização, estampas e identidade visual
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                    @forelse($featuredServices as $index => $service)
                        <a href="{{ route('marketplace.services.show', $service->id) }}" class="landing-card scroll-animate delay-{{ ($index % 3 + 3) * 100 }} group cursor-pointer">
                            {{-- Cover --}}
                            <div class="relative h-44 -mx-6 -mt-6 mb-4 overflow-hidden rounded-t-[1rem]">
                                <img src="{{ $service->cover_image ?? 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=800' }}"
                                     alt="{{ $service->title }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <span class="absolute top-3 left-3 section-badge text-xs !py-1 !px-2">
                                    {{ $service->category_icon }} {{ $service->category_label }}
                                </span>
                            </div>

                            {{-- Designer --}}
                            <div class="flex items-center gap-2.5 mb-3">
                                <img src="{{ $service->designer->avatar_url }}" alt="{{ $service->designer->display_name }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-border">
                                <span class="text-xs text-muted font-medium truncate">{{ $service->designer->display_name }}</span>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-base font-semibold text-heading mb-1 line-clamp-1 group-hover:text-primary transition-colors">
                                {{ $service->title }}
                            </h3>
                            <p class="text-sm text-muted leading-relaxed line-clamp-2 mb-4">{{ Str::limit($service->description, 80) }}</p>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between pt-3 border-t border-subtle">
                                <div>
                                    <span class="text-lg font-semibold text-heading">{{ $service->price_credits }}</span>
                                    <span class="text-xs text-muted ml-1">créditos</span>
                                </div>
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> {{ $service->delivery_days }}d
                                </span>
                            </div>
                        </a>
                    @empty
                        @for($i=0; $i<3; $i++)
                            <div class="landing-card flex flex-col items-center justify-center text-center py-16 scroll-animate delay-{{ ($i + 3) * 100 }}">
                                <div class="w-12 h-12 rounded-lg feature-icon-bg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-heading">Em breve</h3>
                                <p class="text-xs text-muted mt-1">Novos serviços sendo preparados</p>
                            </div>
                        @endfor
                    @endforelse
                </div>

                @if($featuredServices->isNotEmpty())
                    <div class="text-center mt-10 scroll-animate delay-500">
                        <a href="{{ route('marketplace.services.index') }}" class="btn-outline text-sm py-2.5 px-6">
                            Ver todos os serviços →
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- ─── Top Designers ────────────────────────────────────── --}}
        <section class="w-full py-16 lg:py-24 relative">
            <div class="hidden lg:block absolute -z-10 top-0 -right-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

            <div class="landing-wrapper">
                <div class="flex flex-col items-center text-center">
                    <div class="section-badge scroll-animate">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Designers
                    </div>

                    <h2 class="landing-title mt-6 scroll-animate delay-100">
                        Profissionais
                        <span class="text-gradient-primary">qualificados</span>
                    </h2>

                    <p class="landing-desc mt-4 scroll-animate delay-200">
                        Designers especializados prontos para ajudar sua confecção
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mt-12">
                    @foreach($topDesigners as $index => $designer)
                        <a href="{{ route('marketplace.designers.show', $designer->slug) }}" class="landing-card scroll-animate delay-{{ ($index % 3 + 3) * 100 }} text-center group cursor-pointer">
                            <div class="relative mx-auto w-16 h-16 mb-3">
                                <img src="{{ $designer->avatar_url }}" class="w-full h-full rounded-full object-cover ring-2 ring-border group-hover:ring-primary/40 transition-all">
                            </div>
                            <h4 class="text-sm font-semibold text-heading truncate mb-1">{{ $designer->display_name }}</h4>
                            <p class="text-xs text-muted truncate mb-2">{{ $designer->specialty_labels[0] ?? 'Design' }}</p>
                            <div class="flex justify-center gap-0.5">
                                @for($i=0; $i<5; $i++)
                                    <svg class="w-3 h-3 {{ $i < round($designer->rating_average) ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </a>
                    @endforeach
                    @if($topDesigners->isEmpty())
                        <div class="col-span-full landing-card text-center py-12 scroll-animate delay-300">
                            <p class="text-sm text-muted">Novos designers em breve.</p>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-10 scroll-animate delay-500">
                    <a href="{{ route('marketplace.designers') }}" class="btn-outline text-sm py-2.5 px-6">
                        Ver todos os designers →
                    </a>
                </div>
            </div>
        </section>

        {{-- ─── Digital Tools ────────────────────────────────────── --}}
        <section class="w-full py-16 lg:py-24 relative">
            <div class="landing-wrapper">
                <div class="flex flex-col items-center text-center">
                    <div class="section-badge scroll-animate">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Ferramentas Digitais
                    </div>

                    <h2 class="landing-title mt-6 scroll-animate delay-100">
                        Mockups, packs
                        <br>
                        <span class="text-gradient-primary">e muito mais</span>
                    </h2>

                    <p class="landing-desc mt-4 scroll-animate delay-200">
                        Tudo pronto para download: mockups, templates, fontes e packs de imagens
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-12">
                    @forelse($featuredTools as $index => $tool)
                        <a href="{{ route('marketplace.tools.show', $tool->id) }}" class="landing-card scroll-animate delay-{{ ($index % 4 + 3) * 100 }} group cursor-pointer">
                            <div class="relative h-36 -mx-6 -mt-6 mb-4 overflow-hidden rounded-t-[1rem]">
                                <img src="{{ $tool->cover_image ?? 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&q=80&w=400' }}"
                                     class="w-full h-full object-cover transition-transform group-hover:scale-105">
                            </div>
                            <span class="text-[10px] font-semibold tracking-wider text-primary uppercase">{{ $tool->category_label }}</span>
                            <h4 class="text-sm font-semibold text-heading truncate mt-1 mb-2">{{ $tool->title }}</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-semibold text-heading">
                                    {{ $tool->price_credits }} <span class="text-xs text-muted font-normal">créditos</span>
                                </span>
                                <span class="text-[10px] text-muted">
                                    <i class="fa-solid fa-download mr-0.5"></i> {{ $tool->total_downloads ?? 0 }}
                                </span>
                            </div>
                        </a>
                    @empty
                        @for($i=0; $i<4; $i++)
                            <div class="landing-card flex items-center justify-center py-16 scroll-animate delay-{{ ($i + 3) * 100 }}">
                                <p class="text-xs text-muted">Em breve</p>
                            </div>
                        @endfor
                    @endforelse
                </div>

                @if($featuredTools->isNotEmpty())
                    <div class="text-center mt-10 scroll-animate delay-500">
                        <a href="{{ route('marketplace.tools.index') }}" class="btn-outline text-sm py-2.5 px-6">
                            Ver todas as ferramentas →
                        </a>
                    </div>
                @endif
            </div>
        </section>

        {{-- ─── Credits CTA ──────────────────────────────────────── --}}
        <section class="cta-section w-full py-16 lg:py-24">
            <div class="cta-grid-bg"></div>
            <div class="cta-mask"></div>
            <div class="cta-glow animate-pulse-glow"></div>
            
            <div class="landing-wrapper relative z-10">
                <div class="flex flex-col items-center text-center">
                    <h2 class="landing-title scroll-animate">
                        Comece <span class="text-gradient-primary">agora</span>
                    </h2>
                    <p class="landing-desc mt-4 max-w-xl scroll-animate delay-100">
                        Compre créditos e contrate designers ou baixe ferramentas. 
                        @auth
                            Você possui <strong class="text-heading">{{ $userWallet?->balance ?? 0 }} créditos</strong>.
                        @endauth
                        Assinantes Vestalize ganham 10% de desconto.
                    </p>
                    <div class="flex items-center gap-4 mt-8 scroll-animate delay-200">
                        @auth
                            <a href="{{ route('marketplace.credits.index') }}" class="btn-primary text-base py-3 px-6">
                                Comprar Créditos
                            </a>
                        @else
                            <a href="{{ route('register.public') }}" class="btn-primary text-base py-3 px-6">
                                Criar Conta Grátis
                            </a>
                        @endauth
                        <a href="{{ route('marketplace.services.index') }}" class="btn-outline text-base py-3 px-6">
                            Explorar Marketplace
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    @include('landing.partials.footer')

    {{-- Landing JS (scroll animations + theme toggle) --}}
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
