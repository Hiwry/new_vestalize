<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Vestalize</title>
    
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
        :root {
            --primary: #7c3aed;
            --primary-hover: #8b5cf6;
            --primary-light: rgba(124, 58, 237, 0.1);

            --background: #ffffff;
            --foreground: #0f172a;
            --muted: #64748b;
            --border: rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
            --card-hover: #f9fafb;
            --input-bg: #f8fafc;
        }

        .dark {
            --primary: #7c3aed;
            --primary-hover: #8b5cf6;
            --primary-light: rgba(124, 58, 237, 0.18);

            --background: #000000;
            --foreground: #fafafa;
            --muted: #a1a1aa;
            --border: #1a1a1a;
            --card-bg: #030303;
            --card-hover: #080808;
            --input-bg: #050505;
            color-scheme: dark;
        }

        body {
            background-color: var(--background);
            color: var(--foreground);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            backdrop-filter: blur(16px);
            border-radius: 1.5rem;
        }
        .input-theme {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border) !important;
            color: var(--foreground) !important;
            transition: all 0.3s ease;
        }
        
        .dark .input-theme {
            background-color: #050505 !important;
            color: #fafafa !important;
        }

        .input-theme:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2) !important;
        }
        /* Forçar cores no Autofill do Navegador */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-text-fill-color: var(--foreground) !important;
            -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
    <script>
        // Pre-check theme to avoid flash (sync with admin/landing "dark" key)
        (function() {
            const savedDark = localStorage.getItem('dark');
            let isDark = false;

            if (savedDark !== null) {
                isDark = savedDark === 'true' || savedDark === 'dark';
            } else {
                const legacyTheme = localStorage.getItem('theme');
                if (legacyTheme !== null) {
                    isDark = legacyTheme === 'dark';
                }
            }

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.classList.toggle('light', !isDark);

            const syncBodyClasses = () => {
                if (!document.body) return;
                document.body.classList.toggle('dark', isDark);
                document.body.classList.toggle('light', !isDark);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', syncBodyClasses, { once: true });
            } else {
                syncBodyClasses();
            }
        })();
    </script>
</head>
<body class="landing-page antialiased">
    <div class="landing-bg"></div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
        {{-- Decorative Glows --}}
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-purple-600/20 blur-[120px] rounded-full -z-10"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-600/10 blur-[150px] rounded-full -z-10"></div>

        {{-- Logo --}}
        <div class="mb-8 animate-fade-in-blur">
            <a href="/" class="flex items-center">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-12 w-auto">
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md animate-fade-in-up">
            <div class="login-card p-10 shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-foreground mb-2">Bem-vindo de volta</h1>
                    <p class="text-muted text-sm font-medium">Acesse sua conta para gerenciar sua confecção</p>
                </div>

                {{-- Session Alerts --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm text-center">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPass: false, rememberState: false }">
                    @csrf
                    <input type="hidden" name="remember" :value="rememberState ? 'on' : ''">

                    {{-- Store Code --}}
                    <div>
                        <label for="store_code" class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Código da Loja
                        </label>
                        <input id="store_code" 
                               name="store_code" 
                               type="text" 
                               maxlength="6"
                               value="{{ old('store_code') }}"
                               style="text-transform: uppercase;"
                               oninput="this.value = this.value.toUpperCase()"
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none tracking-[0.2em] font-mono text-center text-xl" 
                               placeholder="ABC123">
                        @error('store_code')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Seu Email
                        </label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               required 
                               value="{{ old('email') }}"
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none" 
                               placeholder="exemplo@vmail.com">
                        @error('email')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-bold text-muted uppercase tracking-widest">
                                Senha
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-purple-600 hover:text-purple-500 transition-colors uppercase tracking-wider">
                                    Esqueceu?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   :type="showPass ? 'text' : 'password'" 
                                   required 
                                   class="input-theme w-full px-4 py-4 pr-12 rounded-xl focus:outline-none" 
                                   placeholder="••••••••">
                            <button type="button" 
                                    @click="showPass = !showPass"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors focus:outline-none">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <button type="button" @click="rememberState = !rememberState" class="flex items-center group select-none outline-none">
                            <div class="w-6 h-6 rounded-lg border-2 transition-all flex items-center justify-center"
                                 :class="rememberState ? 'bg-purple-600 border-purple-600' : 'bg-[var(--input-bg)] border-[var(--border)]'">
                                <svg x-show="rememberState" x-cloak class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-medium text-muted group-hover:text-[var(--foreground)] transition-colors font-semibold">Lembrar de mim</span>
                        </button>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold shadow-xl shadow-purple-600/20 hover:shadow-purple-600/40 uppercase tracking-widest mt-4">
                        Entrar na Plataforma
                    </button>
                </form>

                {{-- Sign up link --}}
                <p class="mt-8 text-center text-sm text-muted">
                    Novo por aqui? 
                    <a href="{{ route('register.public') }}" class="font-bold text-[var(--foreground)] hover:text-purple-400 transition-colors">
                        Comece seu teste grátis
                    </a>
                </p>
            </div>
            
            {{-- Copyright --}}
            <p class="mt-8 text-center text-xs text-muted/50 tracking-wide">
                &copy; {{ date('Y') }} VESTALIZE. TODOS OS DIREITOS RESERVADOS.
            </p>
        </div>
    </div>

    {{-- Script para animações extras --}}
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
