{{-- Showcase Section - Veja na Prática --}}
<section id="showcase" class="w-full py-16 lg:py-24 relative">
    {{-- Background glow --}}
    <div class="hidden lg:block absolute -z-10 top-1/4 -right-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

    <div class="landing-wrapper">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Veja na Prática
            </div>

            {{-- Title --}}
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mt-4 lg:mt-6 scroll-animate delay-100">
                Feito para quem
                <br>
                <span class="text-gradient-primary">personaliza de verdade</span>
            </h2>

            {{-- Description --}}
            <p class="text-sm sm:text-base text-muted mt-3 lg:mt-4 scroll-animate delay-200 max-w-lg">
                Veja como o Vestalize organiza cada etapa do seu negócio de personalizados
            </p>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12 items-center">
            {{-- Left: Product Image --}}
            <div class="scroll-animate delay-300">
                <div class="showcase-hero-image">
                    <img
                        src="{{ asset('images/landing/produtos-personalizados.png') }}"
                        alt="Produtos personalizados: canecas sublimadas, camisetas estampadas, brindes corporativos"
                        class="w-full h-auto rounded-xl"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent rounded-xl"></div>
                    <div class="absolute bottom-4 left-4 right-4 z-10">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm font-medium">
                            <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Sublimação · DTF · Serigrafia · Brindes
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right: Feature Cards --}}
            <div class="space-y-4">
                @php
                    $showcaseFeatures = [
                        [
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                            'title' => 'Pedido completo em 2 minutos',
                            'description' => 'Cadastre quantidade, cores, tamanhos, tipo de personalização e anexe a arte do cliente'
                        ],
                        [
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                            'title' => 'Mockups e artes organizados',
                            'description' => 'Cada pedido com seus arquivos, aprovações e versões em um só lugar'
                        ],
                        [
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            'title' => 'Saiba quanto lucra de verdade',
                            'description' => 'Custo do material, tempo de produção e margem real por pedido'
                        ],
                    ];
                @endphp

                @foreach ($showcaseFeatures as $index => $feat)
                    <div class="landing-card flex items-start gap-4 scroll-animate delay-{{ ($index + 4) * 100 }}">
                        <div class="w-10 h-10 rounded-lg feature-icon-bg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $feat['icon'] !!}
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-heading mb-1">{{ $feat['title'] }}</h3>
                            <p class="text-sm text-muted leading-relaxed">{{ $feat['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom Screenshots --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
            <div class="showcase-card scroll-animate delay-500">
                <div class="showcase-card-image">
                    <img
                        src="{{ asset('images/landing/kanban.png') }}"
                        alt="Kanban de produção para personalizados - acompanhe pedidos visualmente"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                    <div class="showcase-card-overlay"></div>
                </div>
                <div class="p-5">
                    <h3 class="text-base font-semibold text-heading mb-1">Kanban de Produção</h3>
                    <p class="text-sm text-muted leading-relaxed">Arraste e solte pedidos entre etapas: recebido → arte aprovada → produção → pronto</p>
                </div>
            </div>
            <div class="showcase-card scroll-animate delay-600">
                <div class="showcase-card-image">
                    <img
                        src="{{ asset('images/landing/financeiro.png') }}"
                        alt="Controle financeiro para loja de personalizados - faturamento e custos"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                    <div class="showcase-card-overlay"></div>
                </div>
                <div class="p-5">
                    <h3 class="text-base font-semibold text-heading mb-1">Controle Financeiro</h3>
                    <p class="text-sm text-muted leading-relaxed">Pagamentos, saldo devedor e relatórios de faturamento em tempo real</p>
                </div>
            </div>
        </div>
    </div>
</section>
