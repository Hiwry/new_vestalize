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

            :root {
                --primary-color: {{ Auth::user()->tenant->primary_color ?? '#4f46e5' }};
                --secondary-color: {{ Auth::user()->tenant->secondary_color ?? '#7c3aed' }};
            }

            .btn-primary { background-color: var(--primary-color); }
            .text-tenant-primary { color: var(--primary-color); }
            .bg-tenant-primary { background-color: var(--primary-color); }
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
