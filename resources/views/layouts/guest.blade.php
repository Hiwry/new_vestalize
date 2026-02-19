<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!--  Dark Mode Script INLINE - Executar ANTES de qualquer renderização -->
        <script>
            (function() {
                const isDarkMode = localStorage.getItem('dark') === 'true';
                const html = document.documentElement;
                if (isDarkMode) {
                    html.classList.add('dark');
                    html.style.colorScheme = 'dark';
                }
            })();
        </script>

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

            /* Fix para inputs no Dark Mode */
            .dark { color-scheme: dark; }
            .dark input:not([type="checkbox"]):not([type="radio"]) {
                background-color: #0a0a0a !important;
                color: #ffffff !important;
                border-color: #1a1a1a !important;
            }
        </style>
    <!-- Dark Mode Script -->
        <script src="{{ asset('js/dark-mode.js') }}"></script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </body>
</html>
