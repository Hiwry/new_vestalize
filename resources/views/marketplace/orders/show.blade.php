@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header & Top Actions -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('marketplace.orders.index') }}" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-500 hover:text-primary shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-xs font-black uppercase tracking-widest text-primary">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest bg-{{ $order->status_color }}-500/10 text-{{ $order->status_color }}-500 border border-{{ $order->status_color }}-500/20">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $orderableModel->title }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                @if($isDesigner && in_array($order->status, ['in_progress', 'revision_requested']))
                    <button onclick="document.getElementById('delivery-modal').classList.remove('hidden')" class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all">
                        Entregar Trabalho
                    </button>
                @endif

                @if(!$isDesigner && $order->status === 'delivered')
                    <form action="{{ route('marketplace.orders.complete', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-500/20 transition-all">
                            Confirmar e Concluir
                        </button>
                    </form>
                    <button onclick="document.getElementById('revision-modal').classList.remove('hidden')" class="px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-500/20 transition-all">
                        Solicitar Revisão
                    </button>
                @endif

                @if(!$isDesigner && $order->status === 'completed' && !$order->review && $order->designer_id)
                    <button onclick="document.getElementById('review-modal').classList.remove('hidden')" class="px-8 py-3 bg-primary hover:bg-primary-hover text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 transition-all">
                        Avaliar Designer
                    </button>
                @endif

                @if($order->isTool() && in_array($order->status, ['completed', 'in_progress', 'delivered']))
                     <a href="{{ route('marketplace.tools.download', $order->orderable_id) }}" class="px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-primary dark:hover:bg-primary hover:text-white transition-all shadow-lg active:scale-95">
                        Download Arquivo
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left Column: Interaction & History -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Timeline / Log -->
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 shadow-xl border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-list-check text-primary"></i> Linha do Tempo
                    </h2>

                    <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-100 dark:before:via-gray-700 before:to-transparent">
                        
                        <!-- Created -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400 group-[.is-active]:text-primary group-[.is-active]:border-primary/50 shadow-sm z-10">
                                <i class="fa-solid fa-cart-plus text-xs"></i>
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-3xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-1">
                                    <time class="text-[9px] font-black uppercase text-gray-400 tracking-widest">{{ $order->created_at->format('d/m/Y H:i') }}</time>
                                </div>
                                <div class="text-sm font-bold text-gray-600 dark:text-gray-300">Pedido realizado e créditos reservados</div>
                            </div>
                        </div>

                        <!-- Payment Confirmed (Simulated since we bypass for now) -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                             <div class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400 group-[.is-active]:text-emerald-500 group-[.is-active]:border-emerald-500/50 shadow-sm z-10">
                                <i class="fa-solid fa-check text-xs"></i>
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-3xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                                <div class="text-sm font-bold text-gray-600 dark:text-gray-300">Pagamento confirmado com créditos</div>
                            </div>
                        </div>

                        <!-- Delivered -->
                        @if($order->delivered_at)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400 group-[.is-active]:text-indigo-500 group-[.is-active]:border-indigo-500/50 shadow-sm z-10">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-500">Trabalho Entregue</span>
                                    <time class="text-[9px] font-black uppercase text-indigo-400 tracking-widest">{{ $order->delivered_at->format('d/m/Y H:i') }}</time>
                                </div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-4 whitespace-pre-line">{{ $order->delivery_message }}</p>
                                
                                @if($order->delivery_file)
                                <a href="{{ asset('storage/' . $order->delivery_file) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">
                                    <i class="fa-solid fa-download"></i> Arquivo da Entrega
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Completed -->
                        @if($order->completed_at)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400 group-[.is-active]:text-emerald-500 group-[.is-active]:border-emerald-500/50 shadow-sm z-10">
                                <i class="fa-solid fa-star text-xs"></i>
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-3xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30">
                                <div class="text-sm font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Pedido Concluído</div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Buyer Instructions (Keep visible) -->
                @if($order->buyer_instructions)
                <div class="bg-gray-100 dark:bg-gray-800/50 rounded-3xl p-8 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Instruções do Comprador</h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $order->buyer_instructions }}</p>
                </div>
                @endif
            </div>

            <!-- Right Column: Context & Summary -->
            <div class="space-y-8">
                
                <!-- Order Profile Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center gap-4">
                        <img src="{{ $orderableModel->cover_image }}" class="w-16 h-16 rounded-xl object-cover">
                        <div>
                             <span class="block text-[8px] font-black uppercase tracking-[0.2em] text-gray-400">{{ $order->orderable_type === 'service' ? 'Serviço' : 'Ferramenta' }}</span>
                             <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[140px]">{{ $orderableModel->title }}</h4>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center text-sm">
                             <span class="text-gray-400 font-bold">Valor do Pedido:</span>
                             <span class="text-gray-900 dark:text-white font-black">{{ $order->price_credits }} Créditos</span>
                        </div>
                        
                        @if($isDesigner)
                        <div class="flex justify-between items-center text-sm">
                             <span class="text-gray-400 font-bold">Líquido a Receber:</span>
                             <span class="text-gray-900 dark:text-white font-black">{{ $order->credits_to_designer }} Créditos</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center text-sm">
                             <span class="text-gray-400 font-bold">Data do Pedido:</span>
                             <span class="text-gray-900 dark:text-white font-black">{{ $order->created_at->format('d/m/Y') }}</span>
                        </div>

                        @if($order->deadline_at && !$order->completed_at)
                        <div class="flex justify-between items-center text-sm pt-4 border-t border-gray-100 dark:border-gray-700">
                             <span class="text-gray-400 font-bold">Prazo de Entrega:</span>
                             <span class="text-indigo-600 dark:text-indigo-400 font-black">{{ $order->deadline_at->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- User Info -->
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5"> <i class="fa-solid fa-user text-6xl"></i> </div>
                    
                    @if($isDesigner)
                        <span class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-4 block">Dados do Comprador</span>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center font-black">{{ strtoupper(substr($order->buyer->name,0,2)) }}</div>
                            <div>
                                <h4 class="font-bold text-white">{{ $order->buyer->name }}</h4>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $order->buyer->email }}</span>
                            </div>
                        </div>
                    @else
                        <span class="text-[8px] font-black uppercase tracking-widest text-gray-400 mb-4 block">Dados do Designer</span>
                        <div class="flex items-center gap-4 mb-6">
                            <img src="{{ $order->designer?->avatar_url }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-white/10">
                            <div>
                                <h4 class="font-bold text-white">{{ $order->designer?->display_name ?? 'Sistema' }}</h4>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $order->designer?->specialty_labels[0] ?? 'Freelancer' }}</span>
                            </div>
                        </div>
                    @endif
                    
                    <button class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Enviar Mensagem (Chat Offline)
                    </button>
                </div>

                <!-- Review Section -->
                @if($order->review)
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 shadow-lg">
                    <div class="flex items-center gap-2 text-amber-500 mb-4">
                        @for($i=0; $i<5; $i++)
                            <i class="fa-solid fa-star text-xs {{ $i < $order->review->rating ? '' : 'opacity-20' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm font-medium italic text-gray-600 dark:text-gray-300">"{{ $order->review->comment }}"</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Modals -->

<!-- Delivery Modal -->
<div id="delivery-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 w-full max-w-xl shadow-2xl border border-white/10">
        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-6">Entregar Trabalho</h3>
        <form action="{{ route('marketplace.orders.deliver', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Mensagem de Entrega</label>
                <textarea name="delivery_message" rows="4" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner" placeholder="Descreva os arquivos enviados ou envie links externos..."></textarea>
            </div>
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Arquivo (Vetor, PDF, ZIP)</label>
                <input type="file" name="delivery_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all">Enviar Entrega</button>
                <button type="button" onclick="document.getElementById('delivery-modal').classList.add('hidden')" class="flex-1 py-5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-widest transition-all">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Revision Modal -->
<div id="revision-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 w-full max-w-xl shadow-2xl border border-white/10">
        <h3 class="text-3xl font-black text-amber-500 tracking-tighter mb-6">Solicitar Revisão</h3>
        <form action="{{ route('marketplace.orders.revision', $order->id) }}" method="POST">
            @csrf
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">O que precisa ser ajustado?</label>
                <textarea name="revision_note" rows="4" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner" placeholder="Seja específico sobre as mudanças necessárias..."></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl transition-all">Pedir Ajustes</button>
                <button type="button" onclick="document.getElementById('revision-modal').classList.add('hidden')" class="flex-1 py-5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-widest transition-all">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 md:p-12 w-full max-w-xl shadow-2xl border border-white/10" x-data="{ rating: 0 }">
        <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-6">Avaliar Trabalho</h3>
        <form action="{{ route('marketplace.orders.review', $order->id) }}" method="POST">
            @csrf
            <div class="flex justify-center gap-4 mb-8">
                <template x-for="i in 5">
                   <button type="button" @click="rating = i" class="text-4xl transition-all hover:scale-125" :class="rating >= i ? 'text-amber-500' : 'text-gray-200 dark:text-gray-700'">
                       <i class="fa-solid fa-star"></i>
                   </button>
                </template>
                <input type="hidden" name="rating" :value="rating" required>
            </div>
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Seu Depoimento</label>
                <textarea name="comment" rows="3" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner" placeholder="Como foi sua experiência com o designer?"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl transition-all" :disabled="rating === 0">Publicar Avaliação</button>
                <button type="button" onclick="document.getElementById('review-modal').classList.add('hidden')" class="flex-1 py-5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-2xl font-black text-sm uppercase tracking-widest transition-all">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@endsection
