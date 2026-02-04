<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade | Vestalize</title>

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
        .legal-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow);
        }
        .legal-section h2 {
            color: var(--foreground);
        }
        .legal-section p,
        .legal-section li {
            color: var(--muted);
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light');
        }
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
                <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-10 w-auto">
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-4xl animate-fade-in-up">
            <div class="legal-card p-10 md:p-12">
                <div class="flex items-start justify-between gap-4 mb-10">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-muted">Documentação Legal</p>
                        <h1 class="text-3xl font-bold text-foreground mt-2">Política de Privacidade</h1>
                        <p class="text-sm text-muted mt-1">Última atualização: {{ date('d/m/Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-600 text-white shadow-card">Privacidade</span>
                </div>

                <div class="space-y-8 legal-section leading-relaxed text-sm md:text-base">
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">1. Introdução</h2>
                        <p>O Vestalize ("nós", "nosso") respeita a sua privacidade. Esta Política de Privacidade explica como coletamos, usamos, divulgamos e protegemos suas informações quando você utiliza nossa plataforma SaaS.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">2. Coleta de Informações</h2>
                        <p>Coletamos informações que você nos fornece diretamente, como:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Dados de cadastro (Nome, E-mail, Nome da Empresa).</li>
                            <li>Dados de pagamento (processados de forma segura por terceiros).</li>
                            <li>Dados inseridos na plataforma durante o uso (pedidos, clientes, produtos).</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">3. Uso das Informações</h2>
                        <p>Usamos suas informações para:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Fornecer, operar e manter nosso serviço.</li>
                            <li>Melhorar, personalizar e expandir nossa plataforma.</li>
                            <li>Processar transações e enviar notificações relacionadas.</li>
                            <li>Enviar e-mails de suporte, segurança e atualizações.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">4. Compartilhamento de Dados</h2>
                        <p>Não vendemos seus dados pessoais. Podemos compartilhar informações com:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Provedores de serviço terceirizados (ex: gateways de pagamento, hospedagem) estritamente para a operação do serviço.</li>
                            <li>Autoridades legais, se exigido por lei.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">5. Segurança de Dados</h2>
                        <p>Implementamos medidas de segurança técnicas e organizacionais para proteger seus dados. No entanto, nenhum método de transmissão pela Internet é 100% seguro, e não podemos garantir segurança absoluta.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">6. Seus Direitos</h2>
                        <p>Você tem o direito de acessar, corrigir ou excluir seus dados pessoais. Para exercer esses direitos, entre em contato com nosso suporte.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">7. Cookies</h2>
                        <p>Utilizamos cookies para melhorar a funcionalidade do site e sua experiência. Você pode configurar seu navegador para recusar cookies, mas algumas partes do serviço podem não funcionar corretamente.</p>
                    </section>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 mt-10 pt-6 border-t" style="border-color: var(--border);">
                    <p class="text-sm text-muted">Em caso de dúvidas, entre em contato com nosso suporte.</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="btn-outline px-4 py-3">Voltar ao site</a>
                        <a href="javascript:window.close()" class="btn-primary px-4 py-3">Fechar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
