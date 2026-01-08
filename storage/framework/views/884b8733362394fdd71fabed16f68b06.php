<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- ⚡ CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
    <!-- ⚡ CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
    <!-- ⚡ CRITICAL: Prevenir flash aplicando tema ANTES de qualquer renderização -->
    <script>
        (function() {
            try {
                // Obter preferência
                const storedTheme = localStorage.getItem('dark');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const shouldBeDark = storedTheme === 'true' || (storedTheme === null && prefersDark);
                
                const html = document.documentElement;
                
                // Se for dark mode, adicionar classe E injetar estilos de bloqueio
                if (shouldBeDark) {
                    html.classList.add('dark');
                    html.style.backgroundColor = '#111827'; // bg-gray-900
                    
                    // Injetar style para forçar background em tudo IMEDIATAMENTE
                    // Isso sobrescreve qualquer padrão do navegador ou do Tailwind enquanto carrega
                    const style = document.createElement('style');
                    style.innerHTML = `
                        html, body { background-color: #111827 !important; color: #f3f4f6 !important; }
                        /* Esconder corpo até que tudo esteja pronto para evitar flash de conteúdo branco */
                        body { visibility: hidden; opacity: 0; transition: opacity 0.2s ease-in; }
                    `;
                    document.head.appendChild(style);
                    
                    // Remove o estilo de bloqueio quando a página carregar
                    window.addEventListener('DOMContentLoaded', function() {
                        requestAnimationFrame(() => {
                            document.body.style.visibility = 'visible';
                            document.body.style.opacity = '1';
                        });
                    });
                } else {
                    html.classList.remove('dark');
                    html.style.backgroundColor = '#f9fafb'; // bg-gray-50
                }
            } catch (e) { console.error('Dark mode init error', e); }
        })();
    </script>
    
    <?php
        $p = auth()->user()->tenant->primary_color ?? '#4f46e5';
        $s = auth()->user()->tenant->secondary_color ?? '#7c3aed';
        
        // Luminance check
        $isLight = false;
        if (str_starts_with($p, '#') && strlen($p) >= 7) {
            $r = hexdec(substr($p, 1, 2));
            $g = hexdec(substr($p, 3, 2));
            $b = hexdec(substr($p, 5, 2));
            if ((0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 200) $isLight = true;
        }
    ?>
    <style>
        :root {
            --brand-primary: <?php echo e($p); ?>;
            --brand-secondary: <?php echo e($s); ?>;
            /* Use dark default for text if primary is too light */
            --brand-primary-text: <?php echo e($isLight ? '#4f46e5' : $p); ?>;
            /* Contrast color for text ON TOP of primary background */
            --brand-primary-content: <?php echo e($isLight ? '#111827' : '#ffffff'); ?>;
        }

        /* Aplicar cores de marca em elementos globais */
        .text-brand-primary { color: var(--brand-primary-text); }
        .bg-brand-primary { background-color: var(--brand-primary); color: var(--brand-primary-content); }
        .border-brand-primary { border-color: var(--brand-primary); }
        
        .text-brand-secondary { color: var(--brand-secondary); }
        .bg-brand-secondary { background-color: var(--brand-secondary); color: #ffffff; }
        .border-brand-secondary { border-color: var(--brand-secondary); }

        .hover\:bg-brand-primary:hover { background-color: var(--brand-primary); opacity: 0.9; }
        
        /* Sobrescrever algumas classes do Tailwind para usar a cor da marca */
        .text-indigo-600 { color: var(--brand-primary-text) !important; }
        .bg-indigo-600 { background-color: var(--brand-primary) !important; color: var(--brand-primary-content) !important; }
        .focus\:ring-indigo-500:focus { --tw-ring-color: var(--brand-primary) !important; }
        .border-indigo-500 { border-color: var(--brand-primary) !important; }
        /* Gradient uses primary BG color */
        .from-blue-600 { --tw-gradient-from: var(--brand-primary) !important; --tw-gradient-to: var(--brand-secondary, var(--brand-primary)) !important; }
        .to-purple-600 { --tw-gradient-to: var(--brand-secondary) !important; }

        /* Secondary Color Mappings (Purple -> Secondary) */
        .text-purple-600 { color: var(--brand-secondary) !important; }
        .bg-purple-600 { background-color: var(--brand-secondary) !important; }
        .border-purple-600 { border-color: var(--brand-secondary) !important; }
        .focus\:ring-purple-500:focus { --tw-ring-color: var(--brand-secondary) !important; }

        /* Blue Mappings (Blue -> Primary) - Harmonize Dashboard/Sidebar */
        .text-blue-600 { color: var(--brand-primary-text) !important; }
        .bg-blue-600 { background-color: var(--brand-primary) !important; }
        .border-blue-600 { border-color: var(--brand-primary) !important; }

        /* Prevenir flash durante carregamento - aplicar ANTES do Tailwind */
        html {
            background-color: #f9fafb;
            color: #111827;
        }
        
        html.dark {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
        
        html:not(.dark) body {
            background-color: #f9fafb;
            color: #111827;
        }
        
        html.dark body {
            background-color: #111827 !important;
            color: #f9fafb !important;
        }
        
        /* Prevenir flash em elementos comuns - remover transições durante carregamento */
        html.dark * {
            transition: background-color 0s, color 0s, border-color 0s;
        }
        
        /* Forçar background escuro imediatamente no dark mode */
        html.dark #main-content {
            background-color: #111827 !important;
        }
        
        html.dark #main-content main {
            background-color: #111827 !important;
        }
        
        /* Prevenir flash durante navegação */
        html.dark body {
            background-color: #111827 !important;
            transition: none !important;
        }
        
        /* Esconder conteúdo até Tailwind carregar */
        html:not(.tailwind-loaded) body {
            visibility: hidden;
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Suprimir aviso do Tailwind CDN ANTES de carregar o script -->
    <script>
        // Suprimir aviso do Tailwind CDN em produção
        if (typeof console !== 'undefined' && console.warn) {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) {
                    return; // Suprimir aviso do Tailwind CDN
                }
                originalWarn.apply(console, args);
            };
        }
    </script>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configuração do Tailwind após carregar o CDN
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        };
        
        // Marcar Tailwind como carregado e garantir dark mode
        (function() {
            const html = document.documentElement;
            const isDarkMode = localStorage.getItem('dark') === 'true';
            
            // Garantir que a classe dark está aplicada após Tailwind carregar
            if (isDarkMode) {
                html.classList.add('dark');
            }
            
            // Mostrar conteúdo (remove visibility: hidden)
            html.classList.add('tailwind-loaded');
        })();
    </script>
    <style>
        .dark p:not([class*="text-"]) { color: rgb(226 232 240); }
        .dark p.text-gray-900,
        .dark p.text-gray-800,
        .dark p.text-gray-700,
        .dark p.text-black { color: rgb(226 232 240) !important; }
        
        /* Forçar cor de texto dos inputs no dark mode */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="number"],
        .dark input[type="tel"],
        .dark input[type="url"],
        .dark input[type="search"],
        .dark textarea,
        .dark select {
            color: rgb(243 244 246) !important;
        }
        
        /* Placeholder no dark mode */
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: rgb(156 163 175) !important;
            opacity: 1;
        }
        
        /* Autocomplete no dark mode */
        .dark input:-webkit-autofill,
        .dark input:-webkit-autofill:hover,
        .dark input:-webkit-autofill:focus,
        .dark textarea:-webkit-autofill,
        .dark textarea:-webkit-autofill:hover,
        .dark textarea:-webkit-autofill:focus,
        .dark select:-webkit-autofill,
        .dark select:-webkit-autofill:hover,
        .dark select:-webkit-autofill:focus {
            -webkit-text-fill-color: rgb(243 244 246) !important;
            -webkit-box-shadow: 0 0 0px 1000px rgb(55 65 81) inset !important;
            box-shadow: 0 0 0px 1000px rgb(55 65 81) inset !important;
        }
        
        /* Dark mode styles para o Kanban */
        .dark .kanban-filter-container,
        .dark .bg-white {
            background-color: #1f2937 !important; /* gray-800 */
        }
        .dark .kanban-column-wrapper,
        .dark .bg-gray-50 {
            background-color: #1f2937 !important; /* gray-800 */
            border-color: #374151 !important; /* gray-700 */
        }
        .dark .kanban-column {
            background-color: #1f2937 !important; /* gray-800 */
        }
        .dark .kanban-card {
            background-color: #374151 !important; /* gray-700 */
            border-color: #4b5563 !important; /* gray-600 */
        }
        .dark .kanban-card h3 {
            color: #fff !important;
        }
        .dark .kanban-card .text-gray-600,
        .dark .kanban-card .text-gray-500 {
            color: #d1d5db !important; /* gray-300 */
        }
        .dark .kanban-card .text-gray-900 {
            color: #fff !important;
        }
        .dark .kanban-card .border-t {
            border-color: #4b5563 !important; /* gray-600 */
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <!-- Alpine.js para componentes interativos -->
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Kanban Component -->

    
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Garantir que Chart.js está disponível
        window.chartJsLoaded = typeof Chart !== 'undefined';
        if (!window.chartJsLoaded) {
            console.error('Chart.js não foi carregado corretamente');
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Paste Modal CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/paste-modal.css')); ?>">
    
    <!-- Dark Mode Script -->
    <script src="<?php echo e(asset('js/dark-mode.js')); ?>"></script>
    <!-- AJAX Navigation Script -->
    <script src="<?php echo e(asset('js/ajax-navigation.js')); ?>" defer></script>
    <!-- Paste Modal Script -->
    <script src="<?php echo e(asset('js/paste-modal.js')); ?>" defer></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php echo $__env->make('components.app-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Page Content -->
        <div id="main-content" class="h-screen overflow-y-auto bg-gray-50 dark:bg-gray-900">
            <main class="min-h-full pb-10 pt-20 px-4 md:pt-6 md:px-6 flex flex-col items-center md:block md:items-start w-full">
                
                <?php if(session('success')): ?>
                    <div class="mb-4 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900/50 dark:border-green-600 dark:text-green-300" role="alert">
                            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="mb-4 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/50 dark:border-red-600 dark:text-red-300" role="alert">
                            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php echo $__env->yieldContent('content'); ?>
                <?php echo $__env->yieldPushContent('page-scripts'); ?>
            </main>
        </div>
    </div>

    <!-- Notificações Flutuantes -->
    <?php echo $__env->make('components.notifications-bell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Scripts -->
    <script>
        // Inicializar margem do conteúdo baseado no estado da sidebar
        // Inicializar margem do conteúdo baseado no estado da sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            const mainContent = document.getElementById('main-content');
            
            function isMobile() {
                return window.innerWidth < 768;
            }

            if (mainContent) {
                if (isMobile()) {
                    mainContent.style.marginLeft = '0';
                } else {
                    mainContent.style.marginLeft = sidebarExpanded ? '16rem' : '4rem';
                }
            }
        });
    </script>

    
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    
    <script>
        // ============================================
        // GLOBAL NOTIFICATION SYSTEM
        // Use notify() para substituir alert()
        // ============================================
        
        function notify(message, type = 'info', duration = 5000) {
            const container = document.getElementById('toast-container');
            
            const colors = {
                success: 'bg-emerald-600',
                error: 'bg-red-600',
                warning: 'bg-amber-500',
                info: 'bg-blue-600'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const toast = document.createElement('div');
            toast.className = `${colors[type] || colors.info} text-white px-4 py-3 rounded-lg shadow-xl flex items-start gap-3 max-w-sm pointer-events-auto transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <i class="fa-solid ${icons[type] || icons.info} text-lg mt-0.5 shrink-0"></i>
                <span class="text-sm flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white shrink-0 -mr-1">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Alias for legacy code
        function showToast(message, type = 'info', duration = 5000) {
            notify(message, type, duration);
        }

        // Override global alert for automatic replacement
        const originalAlert = window.alert;
        window.alert = function(message) {
            // Detectar tipo baseado no conteúdo
            let type = 'info';
            const msgLower = message.toLowerCase();
            if (msgLower.includes('erro') || msgLower.includes('error') || msgLower.includes('falha') || msgLower.includes('failed')) {
                type = 'error';
            } else if (msgLower.includes('sucesso') || msgLower.includes('success') || msgLower.includes('criado') || msgLower.includes('salvo')) {
                type = 'success';
            } else if (msgLower.includes('atenção') || msgLower.includes('aviso') || msgLower.includes('importante') || msgLower.includes('warning')) {
                type = 'warning';
            }
            notify(message, type);
        };
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/layouts/admin.blade.php ENDPATH**/ ?>