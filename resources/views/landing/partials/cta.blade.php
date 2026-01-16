{{-- CTA Section --}}
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
                    <div class="relative flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center text-white stay-white font-bold text-xl group-hover:scale-110 transition-transform">
                            V
                        </div>
                        <span class="font-semibold text-xl text-white">Vestalize</span>
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-medium leading-tight mt-8 max-w-3xl">
                    <span class="text-adaptive-gradient">
                        A melhor forma de
                        <br>
                        gerenciar sua confecção
                    </span>
                </h2>

                {{-- Description --}}
                <p class="text-base md:text-lg text-muted mt-6 max-w-2xl">
                    Junte-se a centenas de confecções que já
                    <br class="hidden md:block">
                    organizam seus negócios com o Vestalize
                </p>

                {{-- CTA Button --}}
                <a href="{{ route('register') }}" class="btn-primary text-base py-3 px-8 mt-8 hover:scale-105 transition-transform">
                    Começar Gratuitamente
                </a>
            </div>
        </div>
    </div>
</section>
