@extends('layouts.marketplace')

@section('title', 'Serviços de Design')
@section('description', 'Encontre designers especializados em logos, estampas, vetorização e identidade visual.')

@section('content')
<div class="landing-wrapper">
    {{-- Header --}}
    <div class="flex flex-col items-center text-center mb-12">
        <div class="section-badge scroll-animate">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            Serviços
        </div>
        <h1 class="landing-title mt-6 scroll-animate delay-100">
            Serviços de <span class="text-gradient-primary">Design</span>
        </h1>
        <p class="landing-desc mt-4 scroll-animate delay-200">
            Transforme suas ideias em realidade com nossos especialistas
        </p>
    </div>

    {{-- Filters --}}
    <form action="{{ route('marketplace.services.index') }}" method="GET" class="flex flex-wrap justify-center gap-3 mb-10 scroll-animate delay-300">
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
            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Melhor Avaliados</option>
        </select>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $index => $service)
            <a href="{{ route('marketplace.services.show', $service->id) }}" class="landing-card scroll-animate delay-{{ ($index % 3 + 3) * 100 }} group cursor-pointer">
                {{-- Cover --}}
                <div class="relative h-44 -mx-6 -mt-6 mb-4 overflow-hidden rounded-t-[1rem]">
                    <img src="{{ $service->cover_image ?? 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=800' }}"
                         alt="{{ $service->title }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <span class="absolute top-3 left-3 section-badge text-xs !py-1 !px-2">
                        {{ $service->category_label }}
                    </span>
                </div>

                {{-- Designer --}}
                <div class="flex items-center gap-2.5 mb-3">
                    <img src="{{ $service->designer->avatar_url }}" alt="{{ $service->designer->display_name }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-border">
                    <span class="text-xs text-muted font-medium truncate">{{ $service->designer->display_name }}</span>
                    @if($service->rating_average > 0)
                        <div class="ml-auto flex items-center gap-1 text-amber-500">
                            <i class="fa-solid fa-star text-[10px]"></i>
                            <span class="text-xs font-semibold">{{ number_format($service->rating_average, 1) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Title --}}
                <h3 class="text-base font-semibold text-heading mb-1 line-clamp-1 group-hover:text-primary transition-colors">
                    {{ $service->title }}
                </h3>

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-3 mt-3 border-t border-subtle">
                    <div>
                        <span class="text-lg font-semibold text-heading">{{ $service->price_credits }}</span>
                        <span class="text-xs text-muted ml-1">créditos</span>
                    </div>
                    <span class="text-xs text-muted flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i> {{ $service->delivery_days }}d
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full landing-card text-center py-16 scroll-animate delay-300">
                <div class="w-12 h-12 rounded-lg feature-icon-bg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-heading">Nenhum serviço encontrado</h3>
                <p class="text-sm text-muted mt-1">Tente ajustar seus filtros de busca.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-12">
        {{ $services->links() }}
    </div>
</div>
@endsection
