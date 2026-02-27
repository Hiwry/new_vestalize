@extends('layouts.app')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    
    <!-- Hero Header Profile -->
    <div class="relative h-80 bg-gray-900 overflow-hidden">
        <!-- Abstract Background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary via-purple-600 to-pink-500 blur-3xl rounded-full scale-150 animate-pulse"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-end pb-12 relative z-10">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-8 w-full">
                <div class="relative w-48 h-48 md:w-56 md:h-56 -mb-24 md:-mb-28">
                    <div class="absolute inset-0 bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl p-2">
                        <img src="{{ $designer->avatar_url }}" class="w-full h-full rounded-[2.5rem] object-cover">
                    </div>
                </div>
                
                <div class="flex-1 text-center md:text-left text-white">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                        <h1 class="text-4xl md:text-5xl font-black tracking-tighter">{{ $designer->display_name }}</h1>
                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-[10px] shadow-lg shadow-emerald-500/20" title="Designer Verificado">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-6">
                        @foreach($designer->specialty_labels as $label)
                            <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-4 pb-4">
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/5 text-center min-w-[100px]">
                        <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Avaliação</span>
                        <div class="flex items-center justify-center gap-1 text-amber-500">
                            <i class="fa-solid fa-star text-xs"></i>
                            <span class="text-white font-black">{{ number_format($designer->rating_average, 1) }}</span>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/5 text-center min-w-[100px]">
                        <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Vendas</span>
                        <span class="text-white font-black">{{ $designer->total_sales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Sidebar Bio -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Sobre o Designer</h3>
                    <p class="text-gray-600 dark:text-gray-300 font-medium leading-relaxed mb-8">
                        {{ $designer->bio }}
                    </p>
                    
                    <div class="space-y-4 pt-8 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-gray-400">Desde</span>
                            <span class="text-gray-900 dark:text-white">{{ $designer->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contact/Actions -->
                <div class="bg-primary rounded-[2rem] p-8 text-white shadow-2xl shadow-primary/20">
                    <h4 class="text-lg font-black tracking-tight mb-2">Gostou do estilo?</h4>
                    <p class="text-xs font-medium text-white/80 mb-6">Contrate um dos serviços abaixo ou solicite um orçamento personalizado.</p>
                    <button class="w-full py-4 bg-white text-primary rounded-xl font-black text-[10px] uppercase tracking-widest hover:shadow-xl transition-all active:scale-95 mb-4">
                        Pedir Orçamento
                    </button>
                    <div class="flex justify-center gap-4 text-white/60">
                         <i class="fa-brands fa-instagram hover:text-white cursor-pointer transition-colors"></i>
                         <i class="fa-brands fa-behance hover:text-white cursor-pointer transition-colors"></i>
                         <i class="fa-brands fa-dribbble hover:text-white cursor-pointer transition-colors"></i>
                    </div>
                </div>
            </div>

            <!-- Services & Portfolio -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Services -->
                <section>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-8">Serviços Oferecidos</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @forelse($designer->services->where('status', 'active') as $service)
                        <div class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all">
                            <div class="h-40 overflow-hidden relative">
                                <img src="{{ $service->cover_image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                <div class="absolute top-3 right-3">
                                    <span class="bg-gray-900/60 backdrop-blur-md text-white px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest">{{ $service->category_label }}</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <h4 class="font-bold text-gray-900 dark:text-white truncate mb-4">
                                    <a href="{{ route('marketplace.services.show', $service->id) }}">{{ $service->title }}</a>
                                </h4>
                                <div class="flex items-center justify-between border-t border-gray-50 dark:border-gray-700 pt-4">
                                     <span class="text-sm font-black text-primary">{{ $service->price_credits }} créditos</span>
                                     <a href="{{ route('marketplace.services.show', $service->id) }}" class="p-2 bg-gray-50 dark:bg-gray-700 hover:bg-primary hover:text-white transition-all rounded-lg text-gray-400">
                                         <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                     </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-12 text-center bg-gray-100 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-300 dark:border-gray-700">
                             <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Nenhum serviço ativo no momento</p>
                        </div>
                        @endforelse
                    </div>
                </section>

                <!-- Reviews -->
                <section>
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Avaliações de Clientes</h2>
                        <div class="flex items-center gap-2 text-amber-500">
                            <i class="fa-solid fa-star"></i>
                            <span class="text-gray-900 dark:text-white font-black">{{ number_format($designer->rating_average, 1) }}</span>
                            <span class="text-gray-400 text-xs font-bold">({{ $designer->reviews->count() }} reviews)</span>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        @forelse($designer->reviews->take(5) as $review)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center font-black text-primary text-xs">{{ strtoupper(substr($review->buyer->name,0,2)) }}</div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $review->buyer->name }}</h4>
                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-1 text-amber-500">
                                    @for($i=0; $i<5; $i++)
                                        <i class="fa-solid fa-star text-[10px] {{ $i < $review->rating ? '' : 'opacity-20' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">"{{ $review->comment }}"</p>
                        </div>
                        @empty
                         <p class="text-center py-8 text-gray-400 font-medium">Ainda não há avaliações para este designer.</p>
                        @endforelse
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection
