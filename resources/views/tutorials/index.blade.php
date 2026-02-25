@extends('layouts.admin')

@section('content')
@php
    $categoriaAtual = $categoria ?? 'admin';
    $perfis = [
        'admin' => ['label' => 'Administrador', 'icon' => 'fa-user-shield', 'gradient' => 'from-purple-500 to-indigo-600'],
        'admin_loja' => ['label' => 'Admin de Loja', 'icon' => 'fa-store', 'gradient' => 'from-blue-500 to-cyan-600'],
        'vendedor' => ['label' => 'Vendedor', 'icon' => 'fa-user-tag', 'gradient' => 'from-amber-500 to-orange-600'],
        'producao' => ['label' => 'Produção', 'icon' => 'fa-industry', 'gradient' => 'from-emerald-500 to-teal-600'],
        'caixa' => ['label' => 'Caixa', 'icon' => 'fa-cash-register', 'gradient' => 'from-pink-500 to-rose-600'],
        'estoque' => ['label' => 'Estoque', 'icon' => 'fa-boxes-stacked', 'gradient' => 'from-sky-500 to-blue-600'],
    ];
@endphp

<div class="max-w-7xl mx-auto" x-data="{ isOpen: false, videoId: '', videoTitle: '', videoDesc: '', openVideo(id, title, desc) { this.videoId = id; this.videoTitle = title; this.videoDesc = desc; this.isOpen = true; document.body.style.overflow = 'hidden'; }, closeVideo() { this.isOpen = false; this.videoId = ''; document.body.style.overflow = ''; } }">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-graduation-cap text-xl" style="color:#ffffff"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tutoriais</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aprenda a usar o Vestalize com nossos vídeos</p>
                </div>
            </div>
            @if(auth()->user() && auth()->user()->tenant_id === null)
            <a href="{{ route('admin.tutorials.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-xs font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/20" style="color:#ffffff">
                <i class="fa-solid fa-gear"></i>
                <span class="hidden sm:inline">Gerenciar Tutoriais</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Category Tabs --}}
    <div class="flex flex-wrap gap-2 mb-8 p-1.5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl border border-gray-200 dark:border-gray-700/50 shadow-sm">
        @foreach($perfis as $key => $perfil)
            @php $isActive = $key === $categoriaAtual; @endphp
            <a href="{{ route('tutorials.index', ['categoria' => $key]) }}"
               class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all duration-300
               {{ $isActive 
                   ? 'bg-gradient-to-r ' . $perfil['gradient'] . ' shadow-lg scale-[1.02]' 
                   : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}"
               @if($isActive) style="color:#ffffff" @endif>
                <i class="fa-solid {{ $perfil['icon'] }} text-xs"></i>
                {{ $perfil['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Categories with Videos --}}
    @forelse($categorias as $cat)
        <div class="mb-10">
            {{-- Category Header --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-500 to-purple-600"></div>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                    <i class="fa-solid {{ $cat->icone }} text-xs text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ $cat->nome }}</h2>
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700">
                    {{ $cat->activeTutorials->count() }} {{ $cat->activeTutorials->count() === 1 ? 'vídeo' : 'vídeos' }}
                </span>
            </div>

            {{-- Video Carousel (Horizontal Scroll) --}}
            @if($cat->activeTutorials->count() > 0)
            <div class="relative group/carousel">
                {{-- Scroll Buttons --}}
                @if($cat->activeTutorials->count() > 3)
                <button onclick="scrollCarousel(this, -1)" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl flex items-center justify-center text-gray-700 dark:text-gray-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all opacity-0 group-hover/carousel:opacity-100 -translate-x-3">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button onclick="scrollCarousel(this, 1)" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl flex items-center justify-center text-gray-700 dark:text-gray-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all opacity-0 group-hover/carousel:opacity-100 translate-x-3">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
                @endif

                {{-- Scrollable Container --}}
                <div class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth tutorial-carousel">
                    @foreach($cat->activeTutorials as $video)
                    <div class="snap-start flex-shrink-0 w-[300px] sm:w-[320px] group cursor-pointer bg-white dark:bg-gray-900/50 rounded-2xl border border-gray-200 dark:border-gray-700/50 overflow-hidden hover:border-indigo-300 dark:hover:border-indigo-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                         @click="openVideo('{{ $video->youtube_id }}', '{{ addslashes($video->titulo) }}', '{{ addslashes($video->descricao ?? '') }}')">
                        {{-- Thumbnail --}}
                        <div class="block relative">
                            <div class="relative aspect-video bg-gray-900 overflow-hidden">
                                <img src="{{ $video->thumbnail }}" 
                                     alt="{{ $video->titulo }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                                {{-- Play Button Overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-colors duration-300">
                                    <div class="w-14 h-14 rounded-full bg-red-600 flex items-center justify-center shadow-2xl shadow-red-600/40 group-hover:scale-110 transition-transform duration-300">
                                        <i class="fa-solid fa-play text-white text-lg ml-1"></i>
                                    </div>
                                </div>
                                {{-- Duration Badge --}}
                                @if(!empty($video->duracao))
                                <div class="absolute bottom-2 right-2 px-2 py-0.5 bg-black/80 backdrop-blur-sm rounded-md text-[11px] font-bold text-white">
                                    {{ $video->duracao }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1.5 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $video->titulo }}
                            </h3>
                            @if($video->descricao)
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                {{ $video->descricao }}
                            </p>
                            @endif
                            <div class="inline-flex items-center gap-1.5 mt-3 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                <i class="fa-solid fa-play text-[9px]"></i>
                                Assistir agora
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Scroll Indicators --}}
                @if($cat->activeTutorials->count() > 3)
                <div class="flex justify-center gap-1.5 mt-2">
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-bold">
                        <i class="fa-solid fa-arrows-left-right text-indigo-400/40 mr-1"></i> Arraste para ver mais
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 rounded-2xl p-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum vídeo nesta categoria ainda.</p>
            </div>
            @endif
        </div>
    @empty
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                <i class="fa-solid fa-video text-3xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nenhum tutorial disponível</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Os tutoriais para esta categoria serão adicionados em breve.</p>
        </div>
    @endforelse

    {{-- ========== VIDEO PLAYER MODAL ========== --}}
    <div x-show="isOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-8"
         @keydown.escape.window="closeVideo()">
        
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/85 backdrop-blur-sm" @click="closeVideo()"></div>

        {{-- Modal Content --}}
        <div class="relative w-full max-w-5xl z-10"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-show="isOpen">
            
            {{-- Close Button --}}
            <button @click="closeVideo()" 
                    class="absolute -top-12 right-0 sm:-right-2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-md flex items-center justify-center text-white transition-all hover:scale-110 group"
                    title="Fechar (Esc)">
                <i class="fa-solid fa-xmark text-lg group-hover:rotate-90 transition-transform duration-300"></i>
            </button>

            {{-- Video Container --}}
            <div class="bg-black rounded-2xl overflow-hidden shadow-2xl shadow-black/50">
                {{-- 16:9 Aspect Ratio Container --}}
                <div class="relative w-full" style="padding-bottom: 56.25%;">
                    <iframe x-ref="videoFrame"
                            class="absolute inset-0 w-full h-full"
                            :src="isOpen ? 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1' : ''"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                    </iframe>
                </div>

                {{-- Video Info Bar --}}
                <div class="px-5 py-4 bg-gray-900 border-t border-gray-800 flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-4">
                        <h3 class="text-white font-bold text-sm sm:text-base truncate" x-text="videoTitle"></h3>
                        <p class="text-gray-400 text-xs sm:text-sm truncate mt-0.5" x-show="videoDesc" x-text="videoDesc"></p>
                    </div>
                    <a :href="'https://www.youtube.com/watch?v=' + videoId" target="_blank" rel="noopener noreferrer"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-all">
                        <i class="fa-brands fa-youtube"></i>
                        <span class="hidden sm:inline">YouTube</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.scrollCarousel = function(btn, direction) {
    var container = btn.closest('.group\\/carousel').querySelector('.tutorial-carousel');
    var cardWidth = 340;
    container.scrollBy({ left: direction * cardWidth * 2, behavior: 'smooth' });
};
</script>

<style>
.tutorial-carousel { scrollbar-width: thin; scrollbar-color: rgba(99, 102, 241, 0.3) transparent; }
.tutorial-carousel::-webkit-scrollbar { height: 4px; }
.tutorial-carousel::-webkit-scrollbar-track { background: transparent; }
.tutorial-carousel::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 99px; }
.tutorial-carousel::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.6); }
</style>
@endsection
