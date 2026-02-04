<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos e Condições | Vestalize</title>

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
                        <h1 class="text-3xl font-bold text-foreground mt-2">Termos e Condições de Uso</h1>
                        <p class="text-sm text-muted mt-1">Última atualização: {{ date('d/m/Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-600 text-white shadow-card">Legal</span>
                </div>

                <div class="space-y-8 legal-section leading-relaxed text-sm md:text-base">
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">1. Aceitação dos Termos</h2>
                        <p>Ao acessar e criar uma conta no Vestalize ("Plataforma"), você concorda em cumprir estes Termos e Condições de Uso. Se você não concordar com qualquer parte destes termos, você não poderá usar nossos serviços.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">2. Descrição do Serviço</h2>
                        <p>O Vestalize é uma plataforma SaaS (Software as a Service) destinada à gestão de confecções, estamparias e negócios têxteis. Oferecemos ferramentas para controle de pedidos, estoque, financeiro e produção.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">3. Assinatura e Pagamentos</h2>
                        <p>O serviço é oferecido mediante assinatura mensal. Os pagamentos são processados via Mercado Pago ou PIX. O não pagamento da mensalidade poderá resultar na suspensão temporária ou no cancelamento do acesso à conta.</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>O período de teste gratuito é de 7 dias.</li>
                            <li>Após o período de teste, a cobrança será iniciada automaticamente caso os dados de pagamento tenham sido fornecidos.</li>
                            <li>Não há reembolso para períodos parciais de uso.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">4. Responsabilidades do Usuário</h2>
                        <p>O usuário é inteiramente responsável pela segurança de suas credenciais de acesso e por todas as atividades que ocorram sob sua conta. Você concorda em:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Fornecer informações verdadeiras e atualizadas.</li>
                            <li>Não utilizar a plataforma para fins ilegais.</li>
                            <li>Não tentar violar a segurança do sistema.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">5. Propriedade Intelectual</h2>
                        <p>Todo o conteúdo, design, código e software da plataforma são de propriedade exclusiva do Vestalize e estão protegidos pelas leis de direitos autorais e propriedade intelectual.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">6. Limitação de Responsabilidade</h2>
                        <p>A plataforma é fornecida "como está". Não garantimos que o serviço será ininterrupto ou livre de erros. Em nenhuma circunstância seremos responsáveis por danos indiretos, incidentais ou consequentes decorrentes do uso do serviço.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold">7. Disposições Finais</h2>
                        <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. Alterações significativas serão notificadas aos usuários. O uso continuado da plataforma após as alterações constitui aceitação dos novos termos.</p>
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
