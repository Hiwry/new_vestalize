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
    
    {{-- Tailwind Play CDN - Solution for shared hosting asset blocking --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#7c3aed',
                        'primary-hover': '#6d28d9',
                    }
                }
            }
        }
    </script>
    
    {{-- Custom Inlined Styles (for non-tailwind/advanced effects) --}}
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

        .landing-page { background-color: var(--background); color: var(--foreground); font-family: 'Inter', sans-serif; }
        .landing-bg { position: fixed; inset: 0; z-index: -1; background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(124, 58, 237, var(--glow-opacity)), transparent); }
        .landing-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }
        
        .navbar-scrolled { background: var(--navbar-bg); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); }
        .btn-primary { background: var(--primary); color: white !important; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .btn-outline { border: 1px solid var(--border); transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
        .btn-outline:hover { background: var(--card-bg); border-color: var(--primary); }
        .nav-link { color: var(--muted); font-weight: 500; transition: all 0.2s; }
        .nav-link:hover { color: var(--primary); }
        
        .text-gradient-primary { background: linear-gradient(to right, var(--primary), #a855f7); -webkit-background-clip: text; background-clip: text; color: transparent; }
        
        .landing-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 1.5rem; transition: all 0.3s; }
        .landing-card:hover { border-color: var(--primary); transform: translateY(-4px); box-shadow: var(--shadow); }
        
        .badge-glow { background: linear-gradient(to right, rgba(124, 58, 237, 0.1), rgba(124, 58, 237, 0.05)); border: 1px solid rgba(124, 58, 237, 0.2); }
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
