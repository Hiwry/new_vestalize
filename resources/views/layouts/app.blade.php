<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- ⚡ CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
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
                $p = Auth::user()->tenant->primary_color ?? '#4f46e5';
                $s = Auth::user()->tenant->secondary_color ?? '#7c3aed';
                
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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS via CDN (enquanto npm não estiver disponível) -->
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
        
        <!-- Dark Mode Script -->
        <script src="{{ asset('js/dark-mode.js') }}"></script>
    </head>
    <body class="h-full bg-gray-50 dark:bg-gray-900">
        <div class="min-h-full">
            @include('layouts.navigation')

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
            </main>
        </div>

    </body>
</html>
