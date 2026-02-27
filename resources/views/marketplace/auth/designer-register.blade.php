<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Designer | Vestalize Marketplace</title>
    <meta name="description" content="Cadastre-se como designer no marketplace Vestalize e ofereça seus serviços para confecções.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('vestalize.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="landing-page antialiased">
    <div class="landing-bg"></div>

    {{-- Navbar simples --}}
    <div class="navbar-backdrop hidden lg:block"></div>
    <header class="landing-navbar">
        <div class="landing-navbar-inner">
            <a href="/" class="flex items-center gap-2 group -ml-2">
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-8 w-auto transition-transform group-hover:scale-105">
            </a>
            <nav class="hidden lg:flex items-center gap-1 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <a href="{{ route('marketplace.home') }}" class="text-sm text-muted hover:text-foreground font-medium transition-colors px-3 py-1.5">Marketplace</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('designer.login') }}" class="text-sm text-muted hover:text-foreground font-medium transition-colors">
                    Já tenho conta
                </a>
            </div>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center pt-32 pb-16 px-4">
        <div class="w-full max-w-lg">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="section-badge mx-auto mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    Marketplace
                </div>
                <h1 class="landing-title text-2xl md:text-3xl">
                    Cadastre-se como <span class="text-gradient-primary">Designer</span>
                </h1>
                <p class="landing-desc mt-3 text-sm mx-auto">
                    Ofereça seus serviços de design para confecções em todo o Brasil
                </p>
            </div>

            {{-- Form --}}
            <div class="landing-card" x-data="{ step: 1 }">
                <form method="POST" action="{{ route('designer.register.post') }}" class="space-y-5">
                    @csrf

                    {{-- Erros --}}
                    @if ($errors->any())
                        <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Step 1: Dados básicos --}}
                    <div x-show="step === 1" x-transition>
                        <div class="space-y-4">
                            {{-- Nome artístico --}}
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-heading mb-1.5">Nome artístico *</label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                    placeholder="Como você quer ser conhecido">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-heading mb-1.5">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                    placeholder="seu@email.com">
                            </div>

                            {{-- Senha --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-heading mb-1.5">Senha *</label>
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                    placeholder="Mínimo 8 caracteres">
                            </div>

                            {{-- Confirmar senha --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-heading mb-1.5">Confirmar senha *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                    placeholder="Repita a senha">
                            </div>

                            <button type="button" @click="step = 2" class="btn-primary w-full py-3 text-sm">
                                Continuar →
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Perfil profissional --}}
                    <div x-show="step === 2" x-transition x-cloak>
                        <div class="space-y-4">
                            {{-- Especialidades --}}
                            <div>
                                <label class="block text-sm font-medium text-heading mb-2">Especialidades *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($specialties as $key => $label)
                                        <label class="flex items-center gap-2 p-3 rounded-xl border border-subtle hover:border-primary/40 cursor-pointer transition-colors text-sm">
                                            <input type="checkbox" name="specialties[]" value="{{ $key }}"
                                                {{ in_array($key, old('specialties', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-500 text-purple-600 focus:ring-purple-500">
                                            <span class="text-heading text-xs">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Bio --}}
                            <div>
                                <label for="bio" class="block text-sm font-medium text-heading mb-1.5">Bio / Sobre você</label>
                                <textarea name="bio" id="bio" rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm resize-none"
                                    placeholder="Conte sobre sua experiência e o que você pode oferecer...">{{ old('bio') }}</textarea>
                            </div>

                            {{-- Redes sociais --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="instagram" class="block text-sm font-medium text-heading mb-1.5">Instagram</label>
                                    <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                                        class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                        placeholder="@seuuser">
                                </div>
                                <div>
                                    <label for="behance" class="block text-sm font-medium text-heading mb-1.5">Behance</label>
                                    <input type="text" name="behance" id="behance" value="{{ old('behance') }}"
                                        class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                        placeholder="behance.net/seuuser">
                                </div>
                            </div>

                            {{-- Portfólio --}}
                            <div>
                                <label for="portfolio_url" class="block text-sm font-medium text-heading mb-1.5">Link do portfólio</label>
                                <input type="url" name="portfolio_url" id="portfolio_url" value="{{ old('portfolio_url') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                                    placeholder="https://seuportfolio.com">
                            </div>

                            {{-- Notificações --}}
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-subtle hover:border-primary/40 cursor-pointer transition-colors">
                                <input type="checkbox" name="notify_new_orders" value="1" checked
                                    class="w-4 h-4 rounded border-gray-500 text-purple-600 focus:ring-purple-500">
                                <div>
                                    <span class="text-sm font-medium text-heading">Receber notificações</span>
                                    <p class="text-xs text-muted mt-0.5">Ser avisado por email quando houver novas artes para fazer</p>
                                </div>
                            </label>

                            <div class="flex gap-3">
                                <button type="button" @click="step = 1" class="btn-outline flex-1 py-3 text-sm">
                                    ← Voltar
                                </button>
                                <button type="submit" class="btn-primary flex-1 py-3 text-sm">
                                    Criar minha conta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <p class="text-center text-xs text-muted mt-6">
                Ao se cadastrar, você concorda com nossos termos de uso. 
                Seu perfil será analisado antes de ficar ativo.
            </p>
        </div>
    </main>

    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
