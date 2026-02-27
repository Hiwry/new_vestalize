<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Designer | Vestalize Marketplace</title>
    <meta name="description" content="Acesse sua conta de designer no marketplace Vestalize.">
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
                <a href="{{ route('designer.register') }}" class="btn-primary text-sm py-2 px-4">
                    Criar conta
                </a>
            </div>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center pt-32 pb-16 px-4">
        <div class="w-full max-w-md">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="section-badge mx-auto mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    Designer
                </div>
                <h1 class="landing-title text-2xl md:text-3xl">
                    Acesse sua <span class="text-gradient-primary">conta</span>
                </h1>
                <p class="landing-desc mt-3 text-sm mx-auto">
                    Entre na sua conta para gerenciar seus serviços e pedidos
                </p>
            </div>

            {{-- Form --}}
            <div class="landing-card">
                <form method="POST" action="{{ route('designer.login.post') }}" class="space-y-5">
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

                    {{-- Sucesso --}}
                    @if (session('status'))
                        <div class="p-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-heading mb-1.5">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                            placeholder="seu@email.com">
                    </div>

                    {{-- Senha --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-heading mb-1.5">Senha</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 rounded-xl border border-subtle bg-transparent text-heading placeholder-muted focus:border-primary focus:ring-1 focus:ring-primary transition-colors text-sm"
                            placeholder="Sua senha">
                    </div>

                    {{-- Lembrar --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-500 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-muted">Lembrar de mim</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                            Esqueceu a senha?
                        </a>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-sm">
                        Entrar
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-subtle text-center">
                    <p class="text-sm text-muted">
                        Ainda não tem conta?
                        <a href="{{ route('designer.register') }}" class="text-primary font-medium hover:underline">
                            Cadastre-se como designer
                        </a>
                    </p>
                </div>
            </div>

            {{-- Login vestalize --}}
            <p class="text-center text-xs text-muted mt-6">
                É cliente Vestalize? <a href="{{ route('login') }}" class="text-primary hover:underline">Use o login principal</a>
            </p>
        </div>
    </main>

    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
