{{-- Footer --}}
<footer class="landing-footer py-12 lg:py-16">
    <div class="landing-wrapper">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1 space-y-4">
                <a href="/" class="flex items-center gap-2">
                    <img
                        src="{{ asset('vestalize.svg') }}"
                        alt="Vestalize"
                        class="h-8 w-auto"
                    >
                </a>
                <p class="text-sm text-muted leading-relaxed max-w-xs">
                    A plataforma completa para gestão de negócios de personalizados que une vendas, produção e financeiro.
                </p>
                
                {{-- Social Links --}}
                <div class="flex items-center gap-3">
                    <a href="#" class="text-muted hover:text-purple-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-muted hover:text-purple-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </a>
                    <a href="#" class="text-muted hover:text-purple-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Links Columns --}}
            <div>
                <h4 class="font-semibold text-white mb-4">Plataforma</h4>
                <ul class="space-y-3 text-sm text-muted">
                    <li><a href="#features" class="hover:text-purple-400 transition-colors">Funcionalidades</a></li>
                    <li><a href="#pricing" class="hover:text-purple-400 transition-colors">Planos</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Integrações</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Atualizações</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Empresa</h4>
                <ul class="space-y-3 text-sm text-muted">
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Sobre Nós</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Blog</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Carreiras</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Contato</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Suporte</h4>
                <ul class="space-y-3 text-sm text-muted">
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Central de Ajuda</a></li>
                    <li><a href="#" class="hover:text-purple-400 transition-colors">Tutoriais</a></li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        suporte@vestalize.com
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-white/10 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-muted">
                &copy; {{ date('Y') }} Vestalize Tecnologia. Todos os direitos reservados.
            </p>
            <div class="flex items-center gap-6 text-sm text-muted">
                <a href="#" class="hover:text-purple-400 transition-colors">Privacidade</a>
                <a href="#" class="hover:text-purple-400 transition-colors">Termos de Uso</a>
            </div>
        </div>
    </div>
</footer>
