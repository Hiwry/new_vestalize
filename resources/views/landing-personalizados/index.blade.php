<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Personalizados</title>
    <meta name="description" content="O sistema completo para gerenciar pedidos, produção, estoque e financeiro do seu negócio de personalizados. Teste grátis por 14 dias.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Inlined CSS to bypass server 406 errors on subdomain --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        :root {
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --primary-light: rgba(124, 58, 237, 0.1);
            --background: #f7f9fd;
            --foreground: #0b1023;
            --muted: #4a4f6a;
            --border: rgba(15, 18, 34, 0.12);
            --card-bg: #ffffff;
            --card-hover: #f1f2f8;
            --input-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.9);
            --glow-opacity: 0.05;
            --shadow: 0 12px 30px rgba(15, 18, 34, 0.08);
        }

        .dark {
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --primary-light: rgba(124, 58, 237, 0.18);
            --background: #0a0a0a;
            --foreground: #fafafa;
            --muted: #a1a1aa;
            --border: rgba(255, 255, 255, 0.08);
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-hover: rgba(255, 255, 255, 0.08);
            --input-bg: #121212;
            --navbar-bg: rgba(10, 10, 10, 0.8);
            --glow-opacity: 0.12;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        .landing-page { background-color: var(--background); color: var(--foreground); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .landing-bg { position: fixed; inset: 0; z-index: -1; background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(124, 58, 237, var(--glow-opacity)), transparent); }
        .landing-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }
        
        /* Utility classes replacement (Tailwind-like) */
        .fixed { position: fixed; } .absolute { position: absolute; } .relative { position: relative; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .z-50 { z-index: 50; } .z-40 { z-index: 40; }
        .flex { display: flex; } .hidden { display: none; }
        .items-center { align-items: center; } .justify-between { justify-content: space-between; }
        .flex-col { flex-direction: column; } .gap-2 { gap: 0.5rem; } .gap-4 { gap: 1rem; } .gap-8 { gap: 2rem; }
        .h-8 { height: 2rem; } .w-auto { width: auto; } .h-16 { height: 4rem; } 
        .p-6 { padding: 1.5rem; } .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; } .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .text-sm { font-size: 0.875rem; } .text-lg { font-size: 1.125rem; } .font-medium { font-weight: 500; } .font-semibold { font-weight: 600; }
        .rounded-lg { border-radius: 0.5rem; } .rounded-2xl { border-radius: 1rem; }
        .bg-white { background-color: #ffffff; } .text-white { color: #ffffff; }
        .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        
        /* Filters for Logo */
        .brightness-0 { filter: brightness(0); }
        .invert { filter: invert(100%); }

        /* Specific Landing Styles */
        .landing-navbar { width: 100%; z-index: 50; transition: all 0.3s; }
        .navbar-scrolled { background: var(--navbar-bg); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); }
        .btn-primary { background: var(--primary); color: white !important; padding: 0.625rem 1.25rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .nav-link { color: var(--muted); font-weight: 500; transition: all 0.2s; }
        .nav-link:hover { color: var(--primary); }
        
        /* Content Sections */
        .section-padding { padding: 5rem 0; }
        .landing-title { font-size: 3rem; font-weight: 700; line-height: 1.1; margin-bottom: 1.5rem; color: var(--foreground); }
        .text-gradient-primary { background: linear-gradient(to right, var(--primary), #a855f7); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .landing-desc { font-size: 1.125rem; color: var(--muted); margin-bottom: 2rem; max-width: 40rem; }
        
        @media (max-width: 768px) {
            .landing-title { font-size: 2.25rem; }
            .hidden.lg\:flex { display: none !important; }
            .lg\:hidden { display: flex !important; }
        }
        
        /* Rest of landing.css inlined compactly */
        .landing-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 1.5rem; padding: 2rem; transition: all 0.3s; }
        .landing-card:hover { border-color: var(--primary); transform: translateY(-4px); box-shadow: var(--shadow); }
    </style>
    
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
    @include('landing-personalizados.partials.navbar')
    
    {{-- Main Content --}}
    <main class="min-h-screen">
        {{-- Spacer para compensar o navbar fixo --}}
        <div class="h-8 lg:h-10"></div>

        {{-- Hero --}}
        @include('landing-personalizados.partials.hero')
        
        {{-- Workflow (3 Steps) --}}
        @include('landing-personalizados.partials.workflow')
        
        {{-- Features --}}
        @include('landing-personalizados.partials.features')
        
        {{-- Pricing --}}
        @include('landing-personalizados.partials.pricing')
        
        {{-- CTA --}}
        @include('landing-personalizados.partials.cta')
    </main>
    
    {{-- Footer --}}
    @include('landing-personalizados.partials.footer')
    
    {{-- Landing Page JS --}}
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
