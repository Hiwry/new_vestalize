{{-- Workflow Section - Personalizados --}}
<section id="workflow" class="w-full py-16 lg:py-24 relative">
    {{-- Background glow --}}
    <div class="hidden lg:block absolute -z-10 top-0 -right-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

    <div class="landing-wrapper">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Como Funciona
            </div>

            {{-- Title --}}
            <h2 class="landing-title mt-6 scroll-animate delay-100">
                De pedido bagunçado
                <br>
                <span class="text-gradient-primary">a entrega organizada</span>
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                Organize seu negócio de personalizados em três passos simples
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-16 md:gap-y-8 gap-x-8 lg:gap-x-16 mx-auto relative mt-12">
            @php
                $steps = [
                    [
                        'number' => '1',
                        'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        'title' => 'Receba e cadastre pedidos',
                        'description' => 'Cliente faz o pedido pelo WhatsApp? Cadastre em segundos com todos os detalhes: arte, cores, quantidades e prazo.'
                    ],
                    [
                        'number' => '2',
                        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                        'title' => 'Acompanhe a produção',
                        'description' => 'Kanban visual para saber onde cada pedido está: arte aprovada, em produção, pronto para entrega.'
                    ],
                    [
                        'number' => '3',
                        'icon' => 'M5 13l4 4L19 7',
                        'title' => 'Entregue e receba',
                        'description' => 'Controle entregas, pagamentos parciais, saldo devedor e gere comprovantes profissionais.'
                    ]
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="flex flex-col items-center text-center relative scroll-animate delay-{{ ($index + 3) * 100 }}">
                    {{-- Icon Circle --}}
                    <div class="w-20 h-20 sm:w-20 sm:h-20 rounded-full feature-icon-bg flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 sm:w-10 sm:h-10 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex flex-col items-center">
                        <h3 class="text-xl md:text-2xl font-semibold text-heading">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-sm text-muted max-w-[280px] leading-relaxed mt-2">
                            {{ $step['description'] }}
                        </p>
                    </div>

                    {{-- Arrow (mobile) --}}
                    @if ($index < count($steps) - 1)
                        <div class="flex md:hidden relative my-4 rotate-90">
                            <svg class="w-8 h-8 arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Arrow (desktop) --}}
                @if ($index < count($steps) - 1)
                    <div class="hidden md:flex absolute top-1/2 -translate-y-1/2" style="left: calc({{ ($index + 1) * 33.333 }}% - 1rem);">
                        <svg class="w-8 h-8 arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
