<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Buscar configurações da loja principal (mesma forma que a página de edição da loja)
        $mainStore = \App\Models\Store::where('is_main', true)->first();
        if ($mainStore) {
            $companySettings = \App\Models\CompanySetting::getSettings($mainStore->id);
        } else {
            // Fallback: buscar configurações globais
            $companySettings = \App\Models\CompanySetting::whereNull('store_id')->first();
            if (!$companySettings) {
                $companySettings = new \App\Models\CompanySetting();
            }
        }
    @endphp
    <title>Catálogo{{ $companySettings->company_name ? ' - ' . $companySettings->company_name : '' }}</title>

    <!--  CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
    <script>
        (function() {
            try {
                const isDarkMode = localStorage.getItem('dark') === 'true';
                const html = document.documentElement;
                
                if (isDarkMode) {
                    html.classList.add('dark');
                    html.style.colorScheme = 'dark';
                    html.style.backgroundColor = '#111827';
                } else {
                    html.classList.remove('dark');
                    html.style.colorScheme = 'light';
                    html.style.backgroundColor = '#f9fafb';
                }
            } catch (e) {
                // Fallback silencioso
            }
        })();
    </script>
    
    <style>
        html:not(.dark) body {
            background-color: #f9fafb;
            color: #111827;
        }
        
        html.dark body {
            background-color: #111827;
            color: #f9fafb;
        }
        
        /* Otimizações Mobile */
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Melhorar touch targets em mobile */
        @media (max-width: 640px) {
            button, a, input, select {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Melhorar legibilidade em mobile */
        @media (max-width: 640px) {
            body {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
        }
        
        /* Prevenir zoom em inputs iOS */
        @media screen and (max-width: 640px) {
            input[type="text"],
            input[type="email"],
            input[type="number"],
            select,
            textarea {
                font-size: 16px !important;
            }
        }
        
        /* Melhorar performance de scroll */
        .grid {
            will-change: transform;
        }
        
        /* Loading state para imagens */
        img {
            transition: opacity 0.3s ease;
        }
        
        img[loading="lazy"] {
            opacity: 0;
            animation: fadeIn 0.3s ease forwards;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>

    <!-- Fonts - Using Inter for lighter weight -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif']
                    },
                    fontWeight: {
                        normal: '400',
                        medium: '400',
                        semibold: '500',
                        bold: '500',
                        extrabold: '600',
                        black: '600'
                    }
                }
            }
        }
    </script>
    <!-- Aggressive font-weight override BUT excluding icons -->
    <style>
        /* Aplicar fonte Inter globalmente, EXCETO em ícones */
        *:not(.fa):not(.fas):not(.far):not(.fal):not(.fad):not(.fab):not([class*="fa-"]) {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }
        
        /* Reset de pesos mais equilibrado */
        body, p, span, div, input, button { font-weight: 400; }
        
        /* Títulos e negritos */
        h1, h2, h3, h4, h5, h6 { font-weight: 600 !important; }
        strong, b, .font-bold, .font-semibold { font-weight: 600 !important; }
        .font-medium { font-weight: 500 !important; }
        .font-extrabold, .font-black { font-weight: 700 !important; }
        
        /* Ícones devem manter seu peso padrão */
        .fa, .fas, .far, .fal, .fad, .fab { font-weight: 900 !important; }
        .far, .fal { font-weight: 400 !important; }
    </style>
    
    <!-- Alpine.js para componentes interativos -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="min-h-full">
        <!-- Header Público -->
        <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
                <div class="flex justify-center items-center py-3 sm:py-4">
                    <a href="{{ route('catalog.index') }}" class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                        <img src="{{ asset('vestalize.svg') }}"
                             alt="Vestalize"
                             class="h-12 sm:h-16 lg:h-20 w-auto object-contain max-w-[180px] sm:max-w-[240px] lg:max-w-[300px]">
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6 lg:py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-8 sm:mt-12">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-6">
                <p class="text-center text-gray-600 dark:text-gray-400 text-xs sm:text-sm">
                    Copyright {{ date('Y') }} {{ $companySettings->company_name ?? config('app.name', 'Laravel') }}. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>

