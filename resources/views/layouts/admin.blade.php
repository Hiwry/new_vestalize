<!DOCTYPE html>
<html lang="pt-BR" class="h-full avento-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8b5cf6">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/img/icons/icon-192x192.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tema global + Avento Theme -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/avento-theme.css') }}">

    <!-- Meta Tags & Branding -->
    @php
        $tenantLogo = auth()->user()->tenant->logo_path ?? null;
        $faviconUrl = $tenantLogo ? Storage::url($tenantLogo) : asset('favicon.ico');
        $companyName = auth()->user()->tenant->name ?? config('app.name');
    @endphp
    
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <meta property="og:title" content="{{ $companyName }} - Painel Administrativo">
    <meta property="og:type" content="website">
    @if($tenantLogo)
        <meta property="og:image" content="{{ asset('storage/' . $tenantLogo) }}">
    @endif

    <!-- Tema Sync -->
    <script>
        (function() {
            const isDarkMode = localStorage.getItem('dark') === 'true';
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    @php
        // Cores para tema claro
        $pLight = auth()->user()->tenant->primary_color ?? '#4f46e5';
        $sLight = auth()->user()->tenant->secondary_color ?? '#7c3aed';
        // Cores para tema escuro (com fallback para versões mais claras das cores light)
        $pDark = auth()->user()->tenant->primary_color_dark ?? '#818cf8';
        $sDark = auth()->user()->tenant->secondary_color_dark ?? '#a78bfa';
        
        // Luminance check para tema claro
        $isLightBg = false;
        if (str_starts_with($pLight, '#') && strlen($pLight) >= 7) {
            $r = hexdec(substr($pLight, 1, 2));
            $g = hexdec(substr($pLight, 3, 2));
            $b = hexdec(substr($pLight, 5, 2));
            if ((0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 200) $isLightBg = true;
        }
        
        // Luminance check para tema escuro
        $isDarkBgLight = false;
        if (str_starts_with($pDark, '#') && strlen($pDark) >= 7) {
            $r = hexdec(substr($pDark, 1, 2));
            $g = hexdec(substr($pDark, 3, 2));
            $b = hexdec(substr($pDark, 5, 2));
            if ((0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 200) $isDarkBgLight = true;
        }
    @endphp
    <style>
        /* Alinha dashboard ao design system da landing */
        /* Sincronizar variáveis globais com o tema Avento */
        :root {
            --background: var(--avento-bg-primary, #f8fafc);
            --card-bg: var(--avento-bg-card, #ffffff);
            --border: var(--avento-border, rgba(0, 0, 0, 0.05));
            --foreground: var(--avento-text-primary, #0f172a);
            --muted: var(--avento-text-secondary, #475569);
            --shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        .dark {
            --background: #000000;
            --card-bg: #030303;
            --border: rgba(255, 255, 255, 0.03);
            --foreground: #ffffff;
            --muted: #94a3b8;
            --shadow: 0 20px 50px -12px rgba(0, 0, 0, 1);
        }

        /* Alinha dashboard ao design system da landing */
        body {
            background-color: var(--background);
            color: var(--foreground);
        }
        /* Backgrounds - FORÇA TOTAL NO PRETO ABSOLUTO */
        .bg-gray-50, .bg-slate-50, .bg-zinc-50, .bg-neutral-50, .bg-stone-50,
        .bg-gray-100, .bg-slate-100, .bg-zinc-100,
        .bg-gray-900, .bg-slate-900, .bg-zinc-900, .bg-neutral-900, .bg-stone-900,
        .dark .bg-gray-900, .dark .bg-slate-900, .dark .bg-zinc-900, .dark .bg-neutral-900, .dark .bg-stone-900,
        .dark .bg-gray-950, .dark .bg-slate-950, .dark .bg-zinc-950, .dark .bg-neutral-950 {
            background-color: var(--background) !important;
        }

        /* Cards e Widgets - FORÇA TOTAL NO PRETO CARD */
        .bg-white,
        .bg-gray-200, .bg-slate-200, .bg-zinc-200,
        .bg-gray-800, .bg-slate-800, .bg-zinc-800, .bg-neutral-800, .bg-stone-800,
        .dark .bg-gray-800, .dark .bg-slate-800, .dark .bg-zinc-800, .dark .bg-neutral-800, .dark .bg-stone-800,
        .dark [class*="bg-slate-800"], .dark [class*="bg-gray-800"], .dark [class*="bg-zinc-800"] {
            background-color: var(--card-bg) !important;
            box-shadow: var(--shadow) !important;
            border: 1px solid var(--border) !important;
        }

        /* Texto */
        .dark .text-gray-900, .dark .text-slate-900, .dark .text-zinc-900, .dark .text-white { color: var(--foreground) !important; }
        .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400,
        .text-slate-800, .text-slate-700, .text-slate-600, .text-slate-500, .text-slate-400,
        .dark .text-gray-300, .dark .text-gray-400, .dark .text-zinc-400, .dark .text-slate-400 {
            color: var(--muted) !important;
        }
        /* Cards e sombras */
        .shadow, .shadow-sm, .shadow-md, .shadow-lg { box-shadow: var(--shadow) !important; }
        .rounded-lg, .rounded-xl, .rounded-2xl { border-radius: 16px !important; }
        /* Inputs */
        input, select, textarea {
            background-color: var(--input-bg) !important;
            color: var(--foreground) !important;
            border-color: var(--border) !important;
        }

        /* Cores do tema CLARO */
        :root {
            --brand-primary: {{ $pLight }};
            --brand-secondary: {{ $sLight }};
            --brand-primary-text: {{ $isLightBg ? '#4f46e5' : $pLight }};
            --brand-primary-content: {{ $isLightBg ? '#111827' : '#ffffff' }};
        }

        /* Cores do tema ESCURO */
        html.dark {
            --brand-primary: {{ $pDark }};
            --brand-secondary: {{ $sDark }};
            --brand-primary-text: {{ $pDark }};
            --brand-primary-content: {{ $isDarkBgLight ? '#111827' : '#ffffff' }};
        }

        /* Aplicar cores de marca em elementos globais */
        .text-brand-primary { color: var(--brand-primary-text); }
        .bg-brand-primary { background-color: var(--brand-primary); color: var(--brand-primary-content); }
        .border-brand-primary { border-color: var(--brand-primary); }
        
        .text-brand-secondary { color: var(--brand-secondary); }
        .bg-brand-secondary { background-color: var(--brand-secondary); color: #ffffff; }
        .border-brand-secondary { border-color: var(--brand-secondary); }

        .hover\:bg-brand-primary:hover { background-color: var(--brand-primary); opacity: 0.9; }
        
        /* Sobrescrever algumas classes do Tailwind para usar a cor da marca */
        .text-indigo-600 { color: var(--brand-primary-text) !important; }
        .bg-indigo-600 { background-color: var(--brand-primary) !important; color: var(--brand-primary-content) !important; }
        .focus\:ring-indigo-500:focus { --tw-ring-color: var(--brand-primary) !important; }
        .border-indigo-500 { border-color: var(--brand-primary) !important; }
        /* Gradient uses primary BG color */
        .from-blue-600 { --tw-gradient-from: var(--brand-primary) !important; --tw-gradient-to: var(--brand-secondary, var(--brand-primary)) !important; }
        .to-purple-600 { --tw-gradient-to: var(--brand-secondary) !important; }

        /* Secondary Color Mappings (Purple -> Secondary) */
        .text-purple-600 { color: var(--brand-secondary) !important; }
        .bg-purple-600 { background-color: var(--brand-secondary) !important; }
        .border-purple-600 { border-color: var(--brand-secondary) !important; }
        .focus\:ring-purple-500:focus { --tw-ring-color: var(--brand-secondary) !important; }

        /* Blue Mappings (Blue -> Primary) - Harmonize Dashboard/Sidebar */
        .text-blue-600 { color: var(--brand-primary-text) !important; }
        .bg-blue-600 { background-color: var(--brand-primary) !important; }
        .border-blue-600 { border-color: var(--brand-primary) !important; }

        /* Sidebar visibilidade (nova skin) */
        #sidebar {
            background: linear-gradient(180deg, #f5f7ff 0%, #f9fbff 100%) !important;
            border-right: 1px solid #e5e7eb !important;
            box-shadow: 0 20px 60px -35px rgba(31, 41, 55, 0.25);
        }
        #sidebar nav a {
            color: #1f2937 !important;
            border-radius: 14px;
            padding: 10px 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        #sidebar nav a svg {
            color: inherit !important;
        }
        #sidebar nav a:hover {
            background: #eef2ff !important;
            color: #1d4ed8 !important;
            box-shadow: 0 12px 32px -18px rgba(29, 78, 216, 0.35);
        }
        /* Tratar links já marcados com bg roxo como "ativos" */
        #sidebar nav a[class*="bg-purple-600"] {
            background: #1d4ed8 !important;
            color: #fff !important;
            box-shadow: 0 14px 32px -14px rgba(29, 78, 216, 0.45) !important;
        }
        #sidebar nav a[class*="bg-purple-600"] svg {
            color: #fff !important;
        }
        .dark #sidebar {
            background: #0b0f1a !important;
            border-color: #1f2937 !important;
            box-shadow: none;
        }
        .dark #sidebar nav a {
            color: #e5e7eb !important;
        }
        .dark #sidebar nav a:hover {
            background: rgba(124, 58, 237, 0.1) !important;
            color: #c4d4ff !important;
            box-shadow: none;
        }
        .dark #sidebar nav a[class*="bg-purple-600"] {
            background: linear-gradient(135deg, #7c3aed, #4f46e5) !important;
            color: #fff !important;
        }


        /* Prevenir flash durante carregamento - aplicar ANTES do Tailwind */
        /* Prevenir flash durante carregamento - aplicar ANTES do Tailwind */
        html {
            background-color: var(--avento-bg-primary, #f8fafc);
            color: var(--avento-text-primary, #0f172a);
        }
        
        body {
            background-color: var(--avento-bg-primary, #f8fafc);
            color: var(--avento-text-primary, #0f172a);
        }
        
        /* Prevenir flash em elementos comuns - remover transições durante carregamento */
        html:not(.tailwind-loaded) * {
            transition: none !important;
        }
        
        /* Forçar background correto imediatamente */
        #main-content, main, body, html {
            background-color: var(--avento-bg-primary) !important;
            transition: none !important;
        }
        
        /* Esconder conteúdo até Tailwind carregar */
        html:not(.tailwind-loaded) body {
            visibility: hidden;
        }
        
        /* ========================================
           ANIMAÇÕES GLOBAIS PREMIUM
           ======================================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-fade-in-down { animation: fadeInDown 0.5s ease-out forwards; }
        .animate-slide-left { animation: slideInLeft 0.4s ease-out forwards; }
        .animate-slide-right { animation: slideInRight 0.4s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.3s ease-out forwards; }
        .animate-pulse-soft { animation: pulse-soft 2s ease-in-out infinite; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-shimmer { 
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        .animate-spin-slow { animation: spin 3s linear infinite; }
        
        /* Animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        
        /* ========================================
           TRANSIÇÕES E HOVER EFFECTS
           ======================================== */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
        }
        .dark .hover-lift:hover {
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4);
        }
        
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .hover-scale:active {
            transform: scale(0.98);
        }
        
        /* ========================================
           GLASSMORPHISM
           ======================================== */
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* ========================================
           SCROLLBAR CUSTOMIZADA
           ======================================== */
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { 
            background: rgba(156, 163, 175, 0.5); 
            border-radius: 3px; 
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.7); }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(75, 85, 99, 0.6); }
        .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(75, 85, 99, 0.8); }
        
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* ========================================
           SKELETON LOADING
           ======================================== */
        .skeleton {
            background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
            border-radius: 0.5rem;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 200% 100%;
        }
        
        /* ========================================
           UTILITIES
           ======================================== */
        .text-gradient {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-gradient-brand {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        }
        
        .border-gradient {
            border: 2px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) border-box;
        }
        .dark .border-gradient {
            background: linear-gradient(#1f2937, #1f2937) padding-box,
                        linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) border-box;
        }

        [x-cloak] { display: none !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Suprimir aviso do Tailwind CDN ANTES de carregar o script -->
    <script>
        // Suprimir aviso do Tailwind CDN em produção
        if (typeof console !== 'undefined' && console.warn) {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) {
                    return; // Suprimir aviso do Tailwind CDN
                }
                originalWarn.apply(console, args);
            };
        }
    </script>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configuração do Tailwind após carregar o CDN
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        };
        
        // Marcar Tailwind como carregado e garantir dark mode
        (function() {
            const html = document.documentElement;
            const isDarkMode = localStorage.getItem('dark') === 'true';
            
            // Garantir que a classe dark está aplicada após Tailwind carregar
            if (isDarkMode) {
                html.classList.add('dark');
            }
            
            // Mostrar conteúdo (remove visibility: hidden)
            html.classList.add('tailwind-loaded');
        })();
    </script>
    <style>
        .dark p:not([class*="text-"]) { color: rgb(226 232 240); }
        .dark p.text-gray-900,
        .dark p.text-gray-800,
        .dark p.text-gray-700,
        .dark p.text-black { color: rgb(226 232 240) !important; }
        
        /* Forçar cor de texto dos inputs no dark mode */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="number"],
        .dark input[type="tel"],
        .dark input[type="url"],
        .dark input[type="search"],
        .dark textarea,
        .dark select {
            color: rgb(243 244 246) !important;
        }
        
        /* Placeholder no dark mode */
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: rgb(156 163 175) !important;
            opacity: 1;
        }
        
        /* Autocomplete no dark mode */
        .dark input:-webkit-autofill,
        .dark input:-webkit-autofill:hover,
        .dark input:-webkit-autofill:focus,
        .dark textarea:-webkit-autofill,
        .dark textarea:-webkit-autofill:hover,
        .dark textarea:-webkit-autofill:focus,
        .dark select:-webkit-autofill,
        .dark select:-webkit-autofill:hover,
        .dark select:-webkit-autofill:focus {
            -webkit-text-fill-color: rgb(243 244 246) !important;
            -webkit-box-shadow: 0 0 0px 1000px rgb(55 65 81) inset !important;
            box-shadow: 0 0 0px 1000px rgb(55 65 81) inset !important;
        }
        
        /* Dark mode styles para o Kanban */
        .dark .kanban-filter-container,
        .dark .bg-white {
            background-color: #1f2937 !important; /* gray-800 */
        }
        .dark .kanban-column-wrapper,
        .dark .bg-gray-50 {
            background-color: #1f2937 !important; /* gray-800 */
            border-color: #374151 !important; /* gray-700 */
        }
        .dark .kanban-column {
            background-color: #1f2937 !important; /* gray-800 */
        }
        .dark .kanban-card {
            background-color: #374151 !important; /* gray-700 */
            border-color: #4b5563 !important; /* gray-600 */
        }
        .dark .kanban-card h3 {
            color: #fff !important;
        }
        .dark .kanban-card .text-gray-600,
        .dark .kanban-card .text-gray-500 {
            color: #d1d5db !important; /* gray-300 */
        }
        .dark .kanban-card .text-gray-900 {
            color: #fff !important;
        }
        .dark .kanban-card .border-t {
            border-color: #4b5563 !important; /* gray-600 */
        }
    </style>
    
    @stack('styles')
    
    <!-- Alpine.js para componentes interativos -->
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Kanban Component -->

    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Garantir que Chart.js está disponível
        window.chartJsLoaded = typeof Chart !== 'undefined';
        if (!window.chartJsLoaded) {
            console.error('Chart.js não foi carregado corretamente');
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Paste Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/paste-modal.css') }}">
    
    <!-- Dark Mode Script -->
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    <!-- Side Panel (edições rápidas) -->
    <script src="{{ asset('js/side-panel.js') }}"></script>
    <!-- Atalhos de Teclado -->
    <script src="{{ asset('js/keyboard-shortcuts.js') }}"></script>
    <!-- Skeleton Loaders -->
    <script src="{{ asset('js/skeleton-loader.js') }}"></script>
    <!-- Onboarding Tour -->
    <script src="{{ asset('js/onboarding-tour.js') }}"></script>
    <!-- AJAX Navigation Script -->
    <script src="{{ asset('js/ajax-navigation.js') }}" defer></script>
    <!-- Paste Modal Script -->
    <script src="{{ asset('js/paste-modal.js') }}" defer></script>
</head>
<body class="h-full landing-page antialiased">
    <div class="landing-bg"></div>
    <div class="h-screen overflow-hidden relative">
        <!-- Sidebar -->
        @include('components.app-sidebar')

        <!-- Page Content -->
        <div id="main-content" class="h-screen overflow-y-auto bg-gray-50 dark:bg-gray-900 transition-all duration-300 ease-in-out">
            <main class="min-h-full pb-24 md:pb-10 pt-20 px-4 md:pt-6 md:px-6 w-full">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900/50 dark:border-green-600 dark:text-green-300" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/50 dark:border-red-600 dark:text-red-300" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                @yield('content')
                @stack('page-scripts')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    @include('components.mobile-bottom-nav')

    <!-- Notificações Flutuantes -->
    @include('components.notifications-bell')

    <!-- Side Panel Global -->
    @include('components.side-panel')


    <!-- Scripts -->
    <script>
        // Inicializar margem do conteúdo baseado no estado da sidebar
        // Inicializar margem do conteúdo baseado no estado da sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            const mainContent = document.getElementById('main-content');
            
            function isMobile() {
                return window.innerWidth < 768;
            }

            if (mainContent) {
                if (isMobile()) {
                    mainContent.style.marginLeft = '0';
                } else {
                    mainContent.style.marginLeft = sidebarExpanded ? '16rem' : '4rem';
                }
            }
        });
    </script>

    {{-- Global Toast Notification System --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    
    <script>
        // ============================================
        // GLOBAL NOTIFICATION SYSTEM
        // Use notify() para substituir alert()
        // ============================================
        
        function notify(message, type = 'info', options = {}) {
            const container = document.getElementById('toast-container');
            const duration = options.duration || 5000;
            const action = options.action || null; // { label: 'Desfazer', callback: () => {} }
            
            const colors = {
                success: 'bg-emerald-600',
                error: 'bg-red-600',
                warning: 'bg-amber-500',
                info: 'bg-indigo-600'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const toast = document.createElement('div');
            toast.className = `${colors[type] || colors.info} text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[300px] max-w-sm pointer-events-auto transform transition-all duration-500 translate-y-4 opacity-0 border border-white/10`;
            
            let actionHtml = '';
            if (action) {
                actionHtml = `
                    <button id="toast-action" class="ml-2 px-3 py-1 bg-white/20 hover:bg-white/30 rounded-lg text-xs font-bold transition-all whitespace-nowrap">
                        ${action.label}
                    </button>
                `;
            }

            toast.innerHTML = `
                <div class="p-2 bg-white/20 rounded-xl">
                    <i class="fa-solid ${icons[type] || icons.info} text-lg"></i>
                </div>
                <span class="text-sm font-medium flex-1">${message}</span>
                ${actionHtml}
                <button onclick="this.parentElement.remove()" class="p-1 hover:bg-white/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;
            
            if (action && action.callback) {
                setTimeout(() => {
                    const actionBtn = toast.querySelector('#toast-action');
                    if (actionBtn) {
                        actionBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            action.callback();
                            toast.classList.add('translate-y-4', 'opacity-0');
                            setTimeout(() => toast.remove(), 500);
                        });
                    }
                }, 10);
            }

            container.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
            });
            
            // Auto remove
            const timeout = setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, duration);

            // Cancel auto-remove if user hovers
            toast.onmouseenter = () => clearTimeout(timeout);
        }

        // Alias for legacy code
        function showToast(message, type = 'info', duration = 5000) {
            notify(message, type, duration);
        }

        // Override global alert for automatic replacement
        const originalAlert = window.alert;
        window.alert = function(message) {
            // Detectar tipo baseado no conteúdo
            let type = 'info';
            const msgLower = message.toLowerCase();
            if (msgLower.includes('erro') || msgLower.includes('error') || msgLower.includes('falha') || msgLower.includes('failed')) {
                type = 'error';
            } else if (msgLower.includes('sucesso') || msgLower.includes('success') || msgLower.includes('criado') || msgLower.includes('salvo')) {
                type = 'success';
            } else if (msgLower.includes('atenção') || msgLower.includes('aviso') || msgLower.includes('importante') || msgLower.includes('warning')) {
                type = 'warning';
            }
            notify(message, type);
        };

        // --- PWA Service Worker Registration ---
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('VESTALIZE: PWA Service Worker registrado com sucesso.');
                }).catch(err => {
                    console.log('VESTALIZE: Falha ao registrar Service Worker PWA.', err);
                });
            });
        }
    </script>

    @stack('scripts')
</body>
</html>

