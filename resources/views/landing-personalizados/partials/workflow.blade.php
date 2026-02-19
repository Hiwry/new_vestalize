{{-- Workflow Section (3 Steps) --}}
<section id="workflow" class="w-full py-12 lg:py-24 relative">
    {{-- Background glow --}}
    <div class="hidden lg:block absolute -z-10 top-0 -right-1/4 w-1/3 h-1/3 bg-purple-600/10 rounded-full blur-[128px]"></div>

    <div class="landing-wrapper px-4">
        <div class="flex flex-col items-center text-center">
            {{-- Badge --}}
            <div class="section-badge scroll-animate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Como Funciona
            </div>

            {{-- Title --}}
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mt-4 lg:mt-6 scroll-animate delay-100">
                De pedido bagunçado
                <br>
                <span class="text-gradient-primary">a entrega organizada</span>
            </h2>

            {{-- Description --}}
            <p class="text-sm sm:text-base text-muted mt-3 lg:mt-4 scroll-animate delay-200 max-w-lg">
                Veja como o Vestalize transforma a rotina de quem trabalha com personalizados
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-y-8 gap-x-8 lg:gap-x-16 mx-auto relative mt-8 lg:mt-12">
            @php
                $steps = [
                    [
                        'number' => '1',
                        'title' => 'Cliente Pede',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                        'description' => 'Cadastre o pedido com todos os detalhes: quantidade, tamanhos, cores, tipo de personalização e anexe a arte'
                    ],
                    [
                        'number' => '2',
                        'title' => 'Você Produz',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'description' => 'Acompanhe cada pedido no Kanban: aprovação de arte, produção, controle de materiais e prazo de entrega'
                    ],
                    [
                        'number' => '3',
                        'title' => 'Cliente Recebe',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                        'description' => 'Registre a entrega, confirme o pagamento e mantenha o histórico completo do cliente'
                    ]
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="flex flex-col items-center text-center relative scroll-animate delay-{{ ($index + 3) * 100 }}">
                    {{-- Icon Circle --}}
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-purple-600/20 border-2 border-purple-500/30 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $step['icon'] !!}
                        </svg>
                    </div>

                    {{-- Number Badge --}}
                    <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $step['number'] }}
                    </div>

                    {{-- Content --}}
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg sm:text-xl font-semibold text-white">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-xs sm:text-sm text-muted max-w-[280px] leading-relaxed mt-2">
                            {{ $step['description'] }}
                        </p>
                    </div>

                    {{-- Arrow (mobile) --}}
                    @if ($index < count($steps) - 1)
                        <div class="flex md:hidden relative my-4">
                            <svg class="w-6 h-6 text-purple-400/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Use Cases --}}
        <div class="mt-10 lg:mt-16 scroll-animate delay-600">
            <p class="text-center text-muted mb-4 lg:mb-6 text-sm">Ideal para quem trabalha com:</p>
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                @php
                    $useCases = [
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>', 'text' => 'Canecas'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>', 'text' => 'Camisetas'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>', 'text' => 'Brindes'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'text' => 'Sublimação'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>', 'text' => 'Serigrafia'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>', 'text' => 'DTF'],
                    ];
                @endphp
                @foreach ($useCases as $case)
                    <span class="px-4 py-2.5 !rounded-full !bg-black/80 !backdrop-blur-md !border !border-white/20 text-xs sm:text-sm !text-white !font-semibold flex items-center gap-2 !shadow-xl hover:scale-105 transition-transform cursor-pointer">
                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $case['icon'] !!}</svg>
                        {{ $case['text'] }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</section>
