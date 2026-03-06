<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Confecções - Pedidos, Produção e Estoque</title>
    <meta name="description" content="Sistema completo para confecções: gerencie pedidos no Kanban, controle produção, estoque de tecidos e financeiro. Orçamentos em PDF, multi-usuários. Teste grátis 14 dias.">
    <meta name="keywords" content="sistema para confecção, gestão de confecção, controle de produção têxtil, sistema de pedidos, kanban confecção, estoque de tecidos, ERP confecção, software confecção, gestão de produção">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Vestalize Tecnologia">
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Vestalize | Sistema de Gestão para Confecções">
    <meta property="og:description" content="Sistema completo para confecções: pedidos, produção, estoque e financeiro em um só lugar. Teste grátis por 14 dias.">
    <meta property="og:image" content="{{ asset('images/landing/dashboard.png') }}">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Vestalize">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Vestalize | Sistema de Gestão para Confecções">
    <meta name="twitter:description" content="Gerencie pedidos, produção, estoque e financeiro da sua confecção. Teste grátis.">
    <meta name="twitter:image" content="{{ asset('images/landing/dashboard.png') }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Fix Font Awesome broken by global font-weight: 400 !important in app.css */
        .fa, .fas, .fa-solid, .fab, .fa-brands, .far, .fa-regular {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
            font-weight: 900 !important;
        }
        .fa-regular, .far {
            font-weight: 400 !important;
        }
        [x-cloak] { display: none !important; }
    </style>
    
    {{-- Schema.org Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "Vestalize",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Sistema completo para gestão de confecções. Gerencie pedidos, produção, estoque e financeiro.",
        "url": "{{ url('/') }}",
        "image": "{{ asset('images/landing/dashboard.png') }}",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "BRL",
            "description": "Teste grátis por 14 dias"
        },
        "creator": {
            "@@type": "Organization",
            "name": "Vestalize Tecnologia",
            "url": "{{ url('/') }}"
        }
    }
    </script>
    @stack('styles')
</head>
<body class="landing-page antialiased">
    {{-- Background --}}
    <div class="landing-bg"></div>
    
    {{-- Navbar --}}
    @include('landing.partials.navbar')
    
    {{-- Main Content --}}
    <main class="min-h-screen">
        {{-- Spacer para compensar o navbar fixo --}}
        <div class="h-8 lg:h-10"></div>

        {{-- Hero --}}
        @include('landing.partials.hero')
        
        {{-- Workflow (3 Steps) --}}
        @include('landing.partials.workflow')

        {{-- Showcase - Conheça o Sistema --}}
        @include('landing.partials.showcase')
        
        {{-- Features --}}
        @include('landing.partials.features')
        
        {{-- Pricing --}}
        @include('landing.partials.pricing')
        
        {{-- CTA --}}
        @include('landing.partials.cta')
    </main>
    
    {{-- Footer --}}
    @include('landing.partials.footer')
    
    {{-- Landing Page JS --}}
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
