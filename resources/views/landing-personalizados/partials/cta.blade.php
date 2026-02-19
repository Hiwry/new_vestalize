{{-- CTA Section - Personalizados --}}
<section class="w-full py-16 lg:py-24 relative cta-section">
    <div class="landing-wrapper">
        <div class="relative max-w-5xl mx-auto scroll-animate">
            {{-- Grid background --}}
            <div class="cta-grid-bg rounded-3xl"></div>
            
            {{-- Mask overlay --}}
            <div class="cta-mask rounded-3xl"></div>
            
            {{-- Glow effect --}}
            <div class="cta-glow animate-pulse-glow"></div>

            {{-- Content --}}
            <div class="relative z-40 flex flex-col items-center text-center py-16 lg:py-20 px-6">
                {{-- Logo --}}
                <div class="relative cursor-pointer group">
                    <div class="absolute inset-0 bg-purple-600/40 rounded-2xl blur-2xl animate-pulse-glow"></div>
                    <div class="relative flex items-center">
                        <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="h-10 w-auto">
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium leading-tight mt-8 max-w-3xl">
                    <span class="text-adaptive-gradient">
                        Pare de perder tempo
                        <br>
                        com planilhas e WhatsApp
                    </span>
                </h2>

                {{-- Description --}}
                <p class="text-base md:text-lg text-muted mt-6 max-w-2xl">
                    Organize seus pedidos de personalizados, controle a produção
                    <br class="hidden md:block">
                    e veja seus lucros crescerem com o Vestalize
                </p>

                {{-- CTA Buttons --}}
                <div class="flex items-center gap-4 flex-wrap justify-center mt-8">
                    <a href="{{ config('app.url') . '/registro' }}" class="btn-primary text-sm sm:text-base py-3 px-8 hover:scale-105 transition-transform !rounded-full !shadow-lg !shadow-purple-500/20">
                        Começar Gratuitamente
                    </a>
                    <a href="#pricing" class="btn-outline text-sm sm:text-base py-3 px-8">
                        Ver Planos
                    </a>
                </div>

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
