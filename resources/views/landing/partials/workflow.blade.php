{{-- Workflow Section (3 Steps) --}}
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
                Do caos à clareza
                <br>
                em três passos
            </h2>

            {{-- Description --}}
            <p class="landing-desc mt-4 scroll-animate delay-200">
                Organize toda sua confecção com o Vestalize
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-16 md:gap-y-8 gap-x-8 lg:gap-x-16 mx-auto relative mt-12">
            @php
                $steps = [
                    [
                        'number' => '1',
                        'title' => 'Cadastre',
                        'description' => 'Clientes, pedidos, artes e materiais centralizados em um só lugar'
                    ],
                    [
                        'number' => '2',
                        'title' => 'Organize',
                        'description' => 'Acompanhe status, prazos e produção de forma automática'
                    ],
                    [
                        'number' => '3',
                        'title' => 'Entregue',
                        'description' => 'Controle entregas, pagamentos e relatórios em tempo real'
                    ]
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="flex flex-col items-center text-center relative scroll-animate delay-{{ ($index + 3) * 100 }}">
                    {{-- Large Number --}}
                    <div class="workflow-number">
                        {{ $step['number'] }}
                    </div>

                    {{-- Content --}}
                    <div class="-mt-8 flex flex-col items-center">
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
                        <svg class="w-8 h-8 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
