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

    <!-- ⚡ CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    
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
                        @if($companySettings->logo_path && file_exists(public_path($companySettings->logo_path)))
                            <img src="{{ asset($companySettings->logo_path) }}" 
                                 alt="{{ $companySettings->company_name ?? 'Logo' }}"
                                 class="h-12 sm:h-16 lg:h-20 w-auto object-contain max-w-[180px] sm:max-w-[240px] lg:max-w-[300px]">
                        @else
                            <h1 class="text-xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $companySettings->company_name ?? config('app.name', 'Catálogo') }}
                            </h1>
                        @endif
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
                    © {{ date('Y') }} {{ $companySettings->company_name ?? config('app.name', 'Laravel') }}. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>

