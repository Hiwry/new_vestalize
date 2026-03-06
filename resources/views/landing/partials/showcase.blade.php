{{-- Showcase Section - Conheça o Sistema --}}
<section id="showcase" class="w-full py-16 lg:py-24 relative">
    {{-- Background glow --}}
    <div class="hidden lg:block absolute -z-10 top-1/4 -left-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>
    <div class="hidden lg:block absolute -z-10 bottom-1/4 -right-1/4 w-1/4 h-1/4 bg-purple-600/8 rounded-full blur-[100px]"></div>

    <div class="landing-wrapper">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Conheça o Sistema
            </div>

            {{-- Title --}}
            <h2 class="landing-title mt-6 scroll-animate delay-100">
                Veja o Vestalize
                <br>
                <span class="text-gradient-primary">em ação</span>
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                Cada módulo foi desenhado para simplificar a gestão da sua confecção
            </p>
        </div>

        {{-- Showcase Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            @php
                $showcases = [
                    [
                        'image' => asset('images/landing/kanban.png'),
                        'title' => 'Kanban de Pedidos',
                        'description' => 'Visualize e gerencie todos os pedidos com drag & drop intuitivo',
                        'alt' => 'Sistema de gestão de pedidos com Kanban board - Vestalize OS'
                    ],
                    [
                        'image' => asset('images/landing/financeiro.png'),
                        'title' => 'Dashboard Financeiro',
                        'description' => 'Acompanhe receitas, custos e margem de lucro em tempo real',
                        'alt' => 'Dashboard financeiro para confecções - controle de custos e receitas'
                    ],
                    [
                        'image' => asset('images/landing/estoque.png'),
                        'title' => 'Controle de Estoque',
                        'description' => 'Gerencie materiais, tecidos e insumos com alertas automáticos',
                        'alt' => 'Sistema de controle de estoque para confecções - gestão de materiais'
                    ]
                ];
            @endphp

            @foreach ($showcases as $index => $item)
                <div class="showcase-card scroll-animate delay-{{ ($index + 3) * 100 }}">
                    <div class="showcase-card-image">
                        <img
                            src="{{ $item['image'] }}"
                            alt="{{ $item['alt'] }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                        <div class="showcase-card-overlay"></div>
                    </div>
                    <div class="p-5">
                        <h3 class="text-base font-semibold text-heading mb-1">{{ $item['title'] }}</h3>
                        <p class="text-sm text-muted leading-relaxed">{{ $item['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Large Feature Image --}}
        <div class="mt-12 scroll-animate delay-500">
            <div class="showcase-hero-image">
                <img
                    src="{{ asset('images/landing/producao.png') }}"
                    alt="Ambiente de produção de confecção organizado com sistema Vestalize"
                    class="w-full h-auto rounded-xl"
                    loading="lazy"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent rounded-xl"></div>
                <div class="absolute bottom-6 left-6 right-6 z-10">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Produção organizada = mais entregas no prazo
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
