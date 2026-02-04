<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vestalize | Sistema de Gestão para Confecções</title>
    <meta name="description" content="O sistema completo para gerenciar pedidos, produção, estoque e financeiro da sua confecção. Teste grátis por 14 dias.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Vite CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Landing Page CSS --}}
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
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
    @include('landing.partials.navbar')
    
    {{-- Main Content --}}
    <main class="min-h-screen">
        {{-- Spacer para compensar o navbar fixo --}}
        <div class="h-8 lg:h-10"></div>

        {{-- Hero --}}
        @include('landing.partials.hero')
        
        {{-- Workflow (3 Steps) --}}
        @include('landing.partials.workflow')
        
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
