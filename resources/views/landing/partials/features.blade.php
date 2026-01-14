{{-- Features Section --}}
<section id="features" class="w-full py-16 lg:py-24 relative">
    {{-- Background glow --}}
    <div class="hidden lg:block absolute -z-10 bottom-0 -left-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

    <div class="landing-wrapper">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Funcionalidades
            </div>

            {{-- Title --}}
            <h2 class="landing-title mt-6 scroll-animate delay-100">
                Tudo que sua confecção
                <br>
                <span class="text-gradient-primary">precisa para crescer</span>
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                O Vestalize centraliza todas as etapas da sua produção em uma interface intuitiva
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            @php
                $features = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                        'title' => 'Kanban de Pedidos',
                        'description' => 'Visualize todos os pedidos em um quadro intuitivo. Arraste e solte para atualizar status instantaneamente.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
                        'title' => 'Gestão de Estoque',
                        'description' => 'Controle tecidos, malhas e materiais com baixa automática conforme a produção avança.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'title' => 'Orçamentos em PDF',
                        'description' => 'Gere propostas profissionais personalizadas e envie por WhatsApp com um clique.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'title' => 'Controle Financeiro',
                        'description' => 'Saiba exatamente quanto custa cada peça e qual sua margem de lucro real.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                        'title' => 'Dashboard de Produção',
                        'description' => 'Acompanhe métricas em tempo real: pedidos pendentes, entregas do dia, faturamento.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'title' => 'Multi-Usuários',
                        'description' => 'Acesso controlado para vendedores, costureiras, produção e administração.'
                    ]
                ];
            @endphp

            @foreach ($features as $index => $feature)
                <div class="landing-card scroll-animate delay-{{ ($index % 3 + 3) * 100 }}">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-lg bg-purple-600/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                    
                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-white mb-2">
                        {{ $feature['title'] }}
                    </h3>
                    
                    {{-- Description --}}
                    <p class="text-sm text-muted leading-relaxed">
                        {{ $feature['description'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
