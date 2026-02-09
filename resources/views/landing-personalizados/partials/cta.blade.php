{{-- CTA Section --}}
<section class="w-full py-12 lg:py-24 relative cta-section">
    <div class="landing-wrapper px-4">
        <div class="relative max-w-5xl mx-auto scroll-animate">
            {{-- Grid background --}}
            <div class="cta-grid-bg rounded-2xl lg:rounded-3xl"></div>
            
            {{-- Mask overlay --}}
            <div class="cta-mask rounded-2xl lg:rounded-3xl"></div>
            
            {{-- Glow effect --}}
            <div class="cta-glow animate-pulse-glow"></div>

            {{-- Content --}}
            <div class="relative z-40 flex flex-col items-center text-center py-10 lg:py-20 px-4 lg:px-6">
                {{-- Logo --}}
                <div class="relative cursor-pointer group">
                    <div class="absolute inset-0 bg-purple-600/40 rounded-2xl blur-2xl animate-pulse-glow"></div>
                    <div class="relative flex items-center">
                        <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-8 lg:h-10 w-auto brightness-0 invert">
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-medium leading-tight mt-6 lg:mt-8 max-w-3xl">
                    <span class="text-adaptive-gradient">
                        Pare de perder tempo
                        <br>
                        com planilhas e WhatsApp
                    </span>
                </h2>

                {{-- Description --}}
                <p class="text-sm sm:text-base md:text-lg text-muted mt-4 lg:mt-6 max-w-2xl">
                    Organize seus pedidos de personalizados, controle artes, materiais e entregas.
                    <br class="hidden md:block">
                    <strong class="text-white">Teste grátis por 14 dias, sem cartão de crédito.</strong>
                </p>

                {{-- CTA Button --}}
                <a href="{{ route('register') }}" class="btn-primary text-sm sm:text-base py-3 px-6 lg:px-8 mt-6 lg:mt-8 hover:scale-105 transition-transform w-full sm:w-auto text-center">
                    Começar Agora — É Grátis
                </a>

                {{-- Trust badges --}}
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6 lg:mt-8 text-xs sm:text-sm text-muted">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Suporte via WhatsApp
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Cancele quando quiser
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Dados seguros
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
