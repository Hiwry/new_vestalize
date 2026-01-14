{{-- Pricing Section --}}
<section id="pricing" class="w-full py-16 lg:py-24 relative">
    <div class="landing-wrapper">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Planos
            </div>

            {{-- Title --}}
            <h2 class="landing-title mt-6 scroll-animate delay-100">
                Escolha o plano ideal
                <br>
                <span class="text-gradient-primary">para sua confecção</span>
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                Comece grátis por 14 dias. Sem cartão de crédito.
            </p>

            {{-- Toggle - CSS puro com checkbox --}}
            <div class="flex items-center gap-4 mt-8 scroll-animate delay-300">
                <span id="label-mensal" class="text-sm font-medium text-white transition-colors duration-300">Mensal</span>
                
                <label class="pricing-toggle-label relative cursor-pointer">
                    <input type="checkbox" id="pricing-toggle-input" class="sr-only peer">
                    <div class="toggle-track w-14 h-7 rounded-full bg-white/10 border border-white/20 transition-all duration-300 peer-checked:bg-purple-600 peer-checked:border-purple-500"></div>
                    <div class="toggle-thumb absolute top-1 left-1 w-5 h-5 rounded-full bg-white shadow-md transition-all duration-300 peer-checked:left-8 peer-checked:shadow-purple-500/50"></div>
                </label>
                
                <span id="label-anual" class="text-sm font-medium text-muted transition-colors duration-300">
                    Anual <span class="text-green-400 text-xs ml-1 font-bold">-20%</span>
                </span>
            </div>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto mt-12">
            {{-- Starter Plan --}}
            <div class="pricing-card scroll-animate delay-400">
                <div class="text-sm text-muted font-medium mb-2">Starter</div>
                <div class="flex items-baseline gap-1 mb-4">
                    <span class="price-monthly text-4xl font-bold text-white">R$ 97</span>
                    <span class="price-yearly text-4xl font-bold text-white hidden">R$ 77</span>
                    <span class="text-muted">/mês</span>
                </div>
                <p class="text-sm text-muted mb-6">
                    Perfeito para confecções que estão começando a organizar seus processos
                </p>
                
                <ul class="space-y-3 mb-8">
                    @foreach ([
                        'Até 3 usuários',
                        '100 pedidos/mês',
                        'Kanban de produção',
                        'Orçamentos em PDF',
                        'Controle de estoque básico',
                        'Suporte por email'
                    ] as $feature)
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                
                <a href="{{ route('register') }}" class="btn-outline w-full text-center">
                    Começar Teste Grátis
                </a>
            </div>

            {{-- Pro Plan --}}
            <div class="pricing-card popular scroll-animate delay-500">
                <div class="text-sm text-purple-400 font-medium mb-2">Pro</div>
                <div class="flex items-baseline gap-1 mb-4">
                    <span class="price-monthly text-4xl font-bold text-white">R$ 197</span>
                    <span class="price-yearly text-4xl font-bold text-white hidden">R$ 157</span>
                    <span class="text-muted">/mês</span>
                </div>
                <p class="text-sm text-muted mb-6">
                    Para confecções que querem escalar e ter controle total do negócio
                </p>
                
                <ul class="space-y-3 mb-8">
                    @foreach ([
                        'Usuários ilimitados',
                        'Pedidos ilimitados',
                        'Tudo do Starter +',
                        'Dashboard completo',
                        'Relatórios avançados',
                        'PDV integrado',
                        'Multi-lojas',
                        'Suporte prioritário 24/7'
                    ] as $feature)
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <svg class="w-5 h-5 text-purple-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                
                <a href="{{ route('register') }}" class="btn-primary w-full text-center">
                    Começar Teste Grátis
                </a>
            </div>
        </div>
    </div>
</section>
