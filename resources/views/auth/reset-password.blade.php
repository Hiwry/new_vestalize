<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Definir nova senha | Vestalize</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Vite & Landing theme --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: var(--background);
            color: var(--foreground);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .auth-card {
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
        .input-theme:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2) !important;
        }
        input:-webkit-autofill {
            -webkit-text-fill-color: var(--foreground) !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        html:not(.light) input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #121212 inset !important;
        }
        html.light input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
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
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-purple-600/20 blur-[120px] rounded-full -z-10"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-600/10 blur-[150px] rounded-full -z-10"></div>

        {{-- Logo --}}
        <div class="mb-8 animate-fade-in-blur">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-xl bg-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-500/30">
                    V
                </div>
                <span class="font-bold text-2xl text-foreground tracking-tight">Vestalize</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md animate-fade-in-up">
            <div class="auth-card p-10 shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-foreground mb-2">Definir nova senha</h1>
                    <p class="text-muted text-sm font-medium">Crie uma nova senha para acessar sua conta.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <div>
                        <label for="email" class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Email
                        </label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email', request('email')) }}"
                               required
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                               placeholder="seu@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Nova senha
                        </label>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                               placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Confirmar senha
                        </label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold shadow-xl shadow-purple-600/20 hover:shadow-purple-600/40 uppercase tracking-widest mt-2">
                        Salvar nova senha
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-purple-600 hover:text-purple-500 transition-colors uppercase tracking-wider">
                            Voltar para login
                        </a>
                    </div>
                </form>
            </div>

            <p class="mt-8 text-center text-xs text-muted/50 tracking-wide">
                &copy; {{ date('Y') }} VESTALIZE. TODOS OS DIREITOS RESERVADOS.
            </p>
        </div>
    </div>

    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
