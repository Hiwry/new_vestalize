@extends('layouts.marketplace')

@section('title', 'Loja de Ferramentas')
@section('description', 'Mockups, packs de imagens, templates e fontes para turbinar sua produção.')

@section('content')
<div class="landing-wrapper">
    {{-- Header --}}
    <div class="flex flex-col items-center text-center mb-12">
        <div class="section-badge scroll-animate">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Ferramentas
        </div>
        <h1 class="landing-title mt-6 scroll-animate delay-100">
            Loja de <span class="text-gradient-primary">Ferramentas</span>
        </h1>
        <p class="landing-desc mt-4 scroll-animate delay-200">
            Arquivos premium para acelerar seu processo criativo
        </p>
    </div>

    {{-- Filters --}}
    <form action="{{ route('marketplace.tools.index') }}" method="GET" class="flex flex-wrap justify-center gap-3 mb-10 scroll-animate delay-300">
        <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-subtle bg-transparent text-heading text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors cursor-pointer">
            <option value="">Todas Categorias</option>
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="sort" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-subtle bg-transparent text-heading text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors cursor-pointer">
            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mais Recentes</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
            <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>Mais Baixados</option>
        </select>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($tools as $index => $tool)
            <a href="{{ route('marketplace.tools.show', $tool->id) }}" class="landing-card scroll-animate delay-{{ ($index % 4 + 3) * 100 }} group cursor-pointer">
                <div class="relative h-36 -mx-6 -mt-6 mb-4 overflow-hidden rounded-t-[1rem]">
                    <img src="{{ $tool->cover_image ?? 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&q=80&w=400' }}"
                         alt="{{ $tool->title }}"
                         class="w-full h-full object-cover transition-transform group-hover:scale-105">
                    <span class="absolute top-3 left-3 section-badge text-[10px] !py-0.5 !px-2">
                        {{ $tool->category_label }}
                    </span>
                </div>

                <h4 class="text-sm font-semibold text-heading truncate mb-2">{{ $tool->title }}</h4>

                <div class="flex items-center justify-between">
                    <span class="text-base font-semibold text-heading">
                        {{ $tool->price_credits }} <span class="text-xs text-muted font-normal">créditos</span>
                    </span>
                    <span class="text-[10px] text-muted">
                        <i class="fa-solid fa-download mr-0.5"></i> {{ $tool->total_downloads ?? 0 }}
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full landing-card text-center py-16 scroll-animate delay-300">
                <div class="w-12 h-12 rounded-lg feature-icon-bg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-heading">Loja vazia no momento</h3>
                <p class="text-sm text-muted mt-1">Novas ferramentas estão sendo adicionadas.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $tools->links() }}
    </div>
</div>
@endsection
