<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro | Vestalize</title>

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
            <a href="/" class="flex items-center">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-12 w-auto">
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-2xl animate-fade-in-up">
            <div class="auth-card p-10 shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-foreground mb-2">Comece seu teste grátis</h1>
                    <p class="text-muted text-sm font-medium">Crie sua conta para testar a plataforma completa</p>
                </div>

                {{-- Alertas globais --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm text-center">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                        <div class="font-semibold mb-2">Por favor, corrija os erros abaixo:</div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.public.post') }}" class="space-y-6" x-data="{ showPass: false, showConfirm: false, accepted: {{ old('terms') ? 'true' : 'false' }} }">
                    @csrf

                    {{-- Empresa --}}
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Nome da Empresa
                        </label>
                        <input type="text"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               required
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                               placeholder="Minha Confecção LTDA">
                        @error('company_name')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Plano --}}
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Selecione o Plano
                        </label>
                        <div class="relative">
                            <select name="plan_id"
                                    required
                                    class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none appearance-none pr-10">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                        Plano {{ $plan->name }} (R$ {{ number_format($plan->price, 2, ',', '.') }}/mês)
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-muted">⌄</span>
                        </div>
                        @error('plan_id')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Código de Indicação --}}
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                            Código de Indicação (Opcional)
                        </label>
                        <input type="text"
                               name="referral_code"
                               value="{{ old('referral_code', $ref ?? '') }}"
                               class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                               placeholder="CÓDIGO INDICADOR">
                        @error('referral_code')
                            <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dados do usuário --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                                Nome Completo
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                                   placeholder="Seu nome completo">
                            @error('name')
                                <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                                E-mail
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   class="input-theme w-full px-4 py-4 rounded-xl focus:outline-none"
                                   placeholder="voce@email.com">
                            @error('email')
                                <p class="mt-2 text-xs text-red-400 italic font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                                Senha
                            </label>
                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'"
                                       name="password"
                                       required
                                       class="input-theme w-full px-4 py-4 pr-12 rounded-xl focus:outline-none"
                                       placeholder="Crie uma senha">
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
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">
                                Confirmar Senha
                            </label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'"
                                       name="password_confirmation"
                                       required
                                       class="input-theme w-full px-4 py-4 pr-12 rounded-xl focus:outline-none"
                                       placeholder="Repita a senha">
                                <button type="button"
                                        @click="showConfirm = !showConfirm"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors focus:outline-none">
                                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Termos --}}
                    <div class="flex items-start gap-3 bg-white/5 dark:bg-white/5 p-4 rounded-xl border" style="border-color: var(--border);" :class="accepted ? '' : 'border-red-500/50'">
                        <input type="checkbox"
                               name="terms"
                               id="terms"
                               value="1"
                               @click="accepted = !accepted"
                               class="mt-1 w-5 h-5 rounded border text-purple-600 focus:ring-purple-500 focus:ring-offset-0"
                               style="border-color: var(--border);"
                               {{ old('terms') ? 'checked' : '' }}
                               required>
                        <label for="terms" class="text-sm text-muted leading-relaxed">
                            Eu li e aceito os
                            <a href="{{ route('terms.show') }}" target="_blank" class="text-purple-600 font-semibold hover:text-purple-500">Termos e Condições de Uso</a>
                            e a
                            <a href="{{ route('privacy.show') }}" target="_blank" class="text-purple-600 font-semibold hover:text-purple-500">Política de Privacidade</a>.
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold shadow-xl shadow-purple-600/20 hover:shadow-purple-600/40 uppercase tracking-widest mt-2">
                        Criar conta e iniciar teste
                    </button>
                </form>

                {{-- Link login --}}
                <p class="mt-8 text-center text-sm text-muted">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="font-bold text-purple-600 hover:text-purple-500 transition-colors">
                        Fazer login
                    </a>
                </p>
            </div>

            {{-- Copyright --}}
            <p class="mt-8 text-center text-xs text-muted/50 tracking-wide">
                &copy; {{ date('Y') }} VESTALIZE. TODOS OS DIREITOS RESERVADOS.
            </p>
        </div>
    </div>

    <script src="{{ asset('js/landing.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referralInput = document.querySelector('input[name="referral_code"]');
            const referralLabel = referralInput.previousElementSibling;
            let timeout = null;

            function validateReferral(code) {
                if (!code) {
                    referralInput.classList.remove('border-green-500', 'border-red-500');
                    return;
                }

                fetch(`/api/affiliates/validate/${code}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            referralInput.classList.add('border-green-500');
                            referralInput.classList.remove('border-red-500');
                            // Adicionar feedback visual suave
                            let feedback = referralInput.parentNode.querySelector('.referral-feedback');
                            if (!feedback) {
                                feedback = document.createElement('p');
                                feedback.className = 'referral-feedback mt-2 text-xs text-green-400 font-medium italic';
                                referralInput.parentNode.appendChild(feedback);
                            }
                            feedback.textContent = `✓ Indicado por: ${data.affiliate_name}`;
                        }
                    })
                    .catch(error => {
                        referralInput.classList.add('border-red-500');
                        referralInput.classList.remove('border-green-500');
                        let feedback = referralInput.parentNode.querySelector('.referral-feedback');
                        if (feedback) feedback.remove();
                    });
            }

            referralInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    validateReferral(this.value.trim());
                }, 500);
            });

            // Validar se já vier preenchido (via URL)
            if (referralInput.value) {
                validateReferral(referralInput.value.trim());
            }
        });
    </script>
</body>
</html>
