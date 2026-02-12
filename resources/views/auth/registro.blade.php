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

    {{-- Tailwind CDN fallback --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#7c3aed',
                        'primary-hover': '#6d28d9',
                        muted: 'var(--muted)',
                        foreground: 'var(--foreground)',
                        background: 'var(--background)',
                    }
                }
            }
        }
    </script>

    <style type="text/css">
        :root {
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --primary-light: rgba(124, 58, 237, 0.1);

            --background: #fafbff;
            --foreground: #1a1a2e;
            --muted: #5a5f7a;
            --border: rgba(15, 18, 34, 0.10);
            --card-bg: #ffffff;
            --card-hover: #f5f3ff;
            --input-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.92);
            --glow-opacity: 0.06;
            --shadow: 0 4px 16px rgba(124, 58, 237, 0.06), 0 1px 3px rgba(0,0,0,0.04);
            --heading-color: var(--foreground);
            --icon-bg: rgba(124, 58, 237, 0.08);
            --icon-color: #7c3aed;
            --section-alt-bg: #f3f0ff;
            --footer-heading: var(--foreground);
            --check-color: #7c3aed;
            --arrow-color: rgba(124, 58, 237, 0.3);
        }

        :root.dark,
        html.dark {
            --primary: #7c3aed;
            --primary-hover: #6d28d9;
            --primary-light: rgba(124, 58, 237, 0.18);

            --background: #0a0a0a;
            --foreground: #fafafa;
            --muted: #a1a1aa;
            --border: rgba(255, 255, 255, 0.08);
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-hover: rgba(255, 255, 255, 0.08);
            --input-bg: #121212;
            --navbar-bg: rgba(10, 10, 10, 0.8);
            --glow-opacity: 0.12;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            --heading-color: #ffffff;
            --icon-bg: rgba(147, 51, 234, 0.2);
            --icon-color: #c084fc;
            --section-alt-bg: transparent;
            --footer-heading: #ffffff;
            --check-color: #a78bfa;
            --arrow-color: rgba(255, 255, 255, 0.25);
        }

        body {
            background-color: var(--background);
            color: var(--foreground);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .landing-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(124, 58, 237, var(--glow-opacity)), transparent);
        }

        .auth-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
        }

        .input-theme {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border) !important;
            color: var(--foreground) !important;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            transform: translateY(-1px);
        }

        .text-muted { color: var(--muted) !important; }
        .text-foreground { color: var(--foreground) !important; }
        
        .text-gradient-primary {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .text-gradient-primary,
        html.dark .text-gradient-primary {
            background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInBlur {
            from { opacity: 0; filter: blur(10px); transform: translateY(10px); }
            to { opacity: 1; filter: blur(0); transform: translateY(0); }
        }

        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-fade-in-blur { animation: fadeInBlur 0.4s ease-out forwards; }

        [x-cloak] { display: none !important; }
    </style>
    <script>
        // Pre-check theme to avoid flash (sync with admin/landing "dark" key)
        (function() {
            const savedDark = localStorage.getItem('dark');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            let isDark = prefersDark;
            if (savedDark !== null) {
                isDark = savedDark === 'true' || savedDark === 'dark';
            }

            document.documentElement.classList.toggle('dark', isDark);
            
            // Add light class explicitly if not dark to help CSS selectors
            if (!isDark) {
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.remove('light');
            }

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
        {{-- Decorative Blobs (Premium Landing Style) --}}
        <div class="absolute top-0 inset-x-0 w-3/5 mx-auto h-20 rounded-full bg-purple-600 blur-[64px] opacity-20 -z-10"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-500/5 blur-[120px] rounded-full -z-10"></div>

        {{-- Logo --}}
        <div class="mb-8 animate-fade-in-blur">
            <a href="/" class="flex items-center">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-12 w-auto">
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-2xl px-0 sm:px-6 animate-fade-in-up">
            <div class="auth-card p-6 sm:p-10 shadow-2xl">
                <div class="text-center mb-10">
                    <h1 class="text-3xl font-bold text-gradient-primary mb-2">Comece seu teste grátis</h1>
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
                               class="mt-1 w-5 h-5 rounded border border-gray-300 dark:border-white/10 text-purple-600 focus:ring-purple-500 focus:ring-offset-0 bg-white/5"
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

    {{-- Scripts inlined for maximum reliability --}}
    {{-- landing.js omitted to avoid asset loading issues on restricted environments --}}
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
                            feedback.textContent = ` Indicado por: ${data.affiliate_name}`;
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
