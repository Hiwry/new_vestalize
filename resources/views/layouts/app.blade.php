<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!--  CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
        <script>
            (function() {
                const isDarkMode = localStorage.getItem('dark') === 'true';
                const html = document.documentElement;
                if (isDarkMode) {
                    html.classList.add('dark');
                    html.style.colorScheme = 'dark';
                } else {
                    html.classList.remove('dark');
                    html.style.colorScheme = 'light';
                }
            })();
        </script>
        
        <style>
            /* Prevenir flash durante carregamento */
            html:not(.dark) body {
                background-color: #f9fafb;
                color: #111827;
            }
            
            html.dark body {
                background-color: #111827;
                color: #f9fafb;
            }

            @php
                $p = Auth::user()?->tenant?->primary_color ?? '#4f46e5';
                $s = Auth::user()?->tenant?->secondary_color ?? '#7c3aed';
                
                // Luminance check
                $isLight = false;
                if (str_starts_with($p, '#') && strlen($p) >= 7) {
                    $r = hexdec(substr($p, 1, 2));
                    $g = hexdec(substr($p, 3, 2));
                    $b = hexdec(substr($p, 5, 2));
                    if ((0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 200) $isLight = true;
                }
            @endphp

            :root {
                --brand-primary: {{ $p }};
                --brand-secondary: {{ $s }};
                --brand-primary-text: {{ $isLight ? '#4f46e5' : $p }};
                --brand-primary-content: {{ $isLight ? '#111827' : '#ffffff' }};
            }

            /* Global Branding Classes */
            .text-brand-primary { color: var(--brand-primary-text); }
            .bg-brand-primary { background-color: var(--brand-primary); color: var(--brand-primary-content); }
            .border-brand-primary { border-color: var(--brand-primary); }
            
            .text-brand-secondary { color: var(--brand-secondary); }
            .bg-brand-secondary { background-color: var(--brand-secondary); color: #ffffff; }
            .border-brand-secondary { border-color: var(--brand-secondary); }
            
            /* Tailwind Class Overrides - Primary (Blue/Indigo) */
            .text-indigo-600, .text-blue-600 { color: var(--brand-primary-text) !important; }
            .bg-indigo-600, .bg-blue-600 { background-color: var(--brand-primary) !important; color: var(--brand-primary-content) !important; }
            .border-indigo-600, .border-blue-600 { border-color: var(--brand-primary) !important; }
            .focus\:ring-indigo-500:focus, .focus\:ring-blue-500:focus { --tw-ring-color: var(--brand-primary) !important; }
            .border-indigo-500 { border-color: var(--brand-primary) !important; }
            
            /* Tailwind Class Overrides - Secondary (Purple) */
            .text-purple-600 { color: var(--brand-secondary) !important; }
            .bg-purple-600 { background-color: var(--brand-secondary) !important; }
            .border-purple-600 { border-color: var(--brand-secondary) !important; }
            .focus\:ring-purple-500:focus { --tw-ring-color: var(--brand-secondary) !important; }

            /* Gradients */
            .from-blue-600 { --tw-gradient-from: var(--brand-primary) !important; --tw-gradient-to: var(--brand-secondary, var(--brand-primary)) !important; }
            .to-purple-600 { --tw-gradient-to: var(--brand-secondary) !important; }

            .btn-primary { background-color: var(--brand-primary); }
            .text-tenant-primary { color: var(--brand-primary-text); }
            .bg-tenant-primary { background-color: var(--brand-primary); }
        </style>

        <!-- Fonts - Using Inter for lighter weight -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS via CDN (enquanto npm não estiver disponível) -->
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
        
        <!-- Dark Mode Script -->
        <script src="{{ asset('js/dark-mode.js') }}"></script>
    </head>
    <body class="h-full bg-gray-50 dark:bg-gray-900">
        <div class="min-h-full">
            @include('layouts.navigation')

            {{-- Banner de Assinatura Expirada --}}
            @auth
                @php
                    $tenant = Auth::user()->tenant;
                    $subscriptionExpired = $tenant && !$tenant->isActive();
                    $daysExpired = 0;
                    if ($subscriptionExpired && $tenant->subscription_ends_at) {
                        $daysExpired = (int) now()->diffInDays($tenant->subscription_ends_at);
                    }
                @endphp
                @if($subscriptionExpired)
                <div x-data="{ show: true }" x-show="show" x-transition class="relative">
                    <div class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500 shadow-lg">
                        <div class="max-w-7xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center flex-1 min-w-0">
                                    <span class="flex p-2 rounded-lg bg-red-800/30">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </span>
                                    <p class="ml-3 text-sm text-white font-medium">
                                        <span class="font-bold">⚠️ Sua assinatura expirou{{ $daysExpired > 0 ? ' há ' . $daysExpired . ' dia(s)' : '' }}!</span>
                                        <span class="hidden sm:inline ml-1">Renove para continuar usando todas as funcionalidades do sistema.</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="https://wa.me/5500000000000?text=Olá!%20Gostaria%20de%20renovar%20minha%20assinatura%20do%20Vestalize." 
                                       target="_blank"
                                       class="flex items-center px-4 py-2 rounded-md bg-white text-red-600 text-sm font-bold shadow-sm hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.616l4.528-1.463A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.234 0-4.308-.724-5.994-1.953l-.42-.312-2.686.868.896-2.632-.343-.452A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                                        Renovar Agora
                                    </a>
                                    <button @click="show = false" class="flex p-1 rounded-md hover:bg-red-700/50 text-white transition-colors" title="Fechar">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>

    </body>
</html>
