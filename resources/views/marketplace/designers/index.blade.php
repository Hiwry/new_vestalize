@extends('layouts.marketplace')

@section('title', 'Nossos Designers')
@section('description', 'Encontre o talento perfeito para levar sua marca ao próximo nível.')

@section('content')
<div class="landing-wrapper">
    {{-- Header --}}
    <div class="flex flex-col items-center text-center mb-12">
        <div class="section-badge scroll-animate">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Designers
        </div>
        <h1 class="landing-title mt-6 scroll-animate delay-100">
            Nossos <span class="text-gradient-primary">Designers</span>
        </h1>
        <p class="landing-desc mt-4 scroll-animate delay-200">
            Encontre o talento perfeito para levar sua marca ao próximo nível
        </p>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($designers as $index => $designer)
            <a href="{{ route('marketplace.designers.show', $designer->slug) }}" class="landing-card scroll-animate delay-{{ ($index % 4 + 3) * 100 }} group cursor-pointer text-center">
                {{-- Avatar --}}
                <div class="relative w-20 h-20 mx-auto mb-4">
                    <img src="{{ $designer->avatar_url }}" 
                         alt="{{ $designer->display_name }}" 
                         class="w-full h-full rounded-full object-cover ring-2 ring-border group-hover:ring-primary/40 transition-all">
                    @if($designer->rating_average > 0)
                        <div class="absolute -bottom-1 -right-1 bg-amber-500 text-white w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold ring-2 ring-background">
                            {{ number_format($designer->rating_average, 1) }}
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <h3 class="text-base font-semibold text-heading mb-1 truncate">
                    {{ $designer->display_name }}
                </h3>

                <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                    @foreach(array_slice($designer->specialty_labels, 0, 2) as $label)
                        <span class="section-badge text-[10px] !py-0.5 !px-2">{{ $label }}</span>
                    @endforeach
                </div>

                <p class="text-sm text-muted line-clamp-2 mb-4">{{ $designer->bio }}</p>

                {{-- Stats --}}
                <div class="flex justify-center gap-6 pt-3 border-t border-subtle">
                    <div class="text-center">
                        <span class="block text-heading font-semibold">{{ $designer->services_count ?? $designer->services->count() }}</span>
                        <span class="block text-[10px] text-muted">Serviços</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-heading font-semibold">{{ $designer->total_sales }}</span>
                        <span class="block text-[10px] text-muted">Vendas</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full landing-card text-center py-16 scroll-animate delay-300">
                <div class="w-12 h-12 rounded-lg feature-icon-bg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 feature-icon-color" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-heading">Nenhum designer encontrado</h3>
                <p class="text-sm text-muted mt-1">Novos talentos serão adicionados em breve.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $designers->links() }}
    </div>
</div>
@endsection
