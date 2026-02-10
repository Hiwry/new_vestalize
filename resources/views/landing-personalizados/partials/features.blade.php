{{-- Features Section - Personalizados --}}
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
                Tudo que seu negócio de
                <br>
                <span class="text-gradient-primary">personalizados precisa</span>
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                Do pedido à entrega, cada etapa do seu processo em uma interface feita para quem personaliza
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            @php
                $features = [
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                        'title' => 'Gestão de Artes e Mockups',
                        'description' => 'Anexe artes, mockups e arquivos de produção a cada pedido. Aprovação do cliente integrada ao fluxo.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                        'title' => 'Kanban de Produção',
                        'description' => 'Quadro visual para acompanhar cada pedido: recebido, arte aprovada, em produção, pronto, entregue.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'title' => 'Controle Financeiro',
                        'description' => 'Registre pagamentos parciais, saldo devedor, custos de insumo e saiba sua margem real por pedido.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'title' => 'Orçamentos em PDF',
                        'description' => 'Gere propostas profissionais com sua logo, envie por WhatsApp e converta mais vendas automaticamente.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'title' => 'CRM de Clientes',
                        'description' => 'Histórico completo de cada cliente: pedidos anteriores, preferências, datas especiais e contato rápido.'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                        'title' => 'Dashboard & Relatórios',
                        'description' => 'Métricas em tempo real: pedidos do dia, faturamento mensal, produtos mais vendidos e prazos atrasados.'
                    ]
                ];
            @endphp

            @foreach ($features as $index => $feature)
                <div class="landing-card scroll-animate delay-{{ ($index % 3 + 3) * 100 }}">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-lg feature-icon-bg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                    
                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-heading mb-2">
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
