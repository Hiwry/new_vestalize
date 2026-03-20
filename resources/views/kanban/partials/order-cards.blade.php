{{--
    Partial: kanban/partials/order-cards.blade.php
    Variables:
      $orders       – Collection of Order models (with items, client, store, etc.)
      $viewType     – 'production' | 'personalized'
      $columnIndex  – (optional) column position for staggered animation (default 0)
--}}
@foreach($orders as $order)
    @php
        $firstItem = $order->items->first();
        $coverItem = $order->items->first(fn($item) => filled($item->cover_image));
        $coverImage = $order->cover_image_url ?: $coverItem?->cover_image_url;
        $artName = $firstItem?->art_name;
        $productName = $firstItem?->print_type ?? $firstItem?->art_name ?? 'Personalizado';
        $customNote = $firstItem?->print_desc ?? $firstItem?->art_notes;
        $personalizationLabel = $customNote ?: (($firstItem?->art_name && $firstItem?->art_name !== $productName) ? $firstItem?->art_name : 'Sem personalização');
        $displayName = $viewType === 'personalized'
            ? ($order->client?->name ?? $productName)
            : ($artName ?? ($order->client?->name ?? 'Sem cliente'));
        $storeName = $order->store?->name ?? 'Loja Principal';
        $filesCount = $order->items->sum(fn($item) => $item->files->count() + ($item->corel_file_path ? 1 : 0));
        $commentsCount = (int) ($order->comments_count ?? 0);
        $printType = $order->items
            ->pluck('print_type')
            ->filter()
            ->flatMap(function ($value) {
                return collect(explode(',', $value))
                    ->map(fn($part) => trim($part))
                    ->filter();
            })
            ->unique()
            ->values()
            ->join(', ');
        $printType = $printType !== '' ? $printType : 'Sem personalização';
        $entryDate = $order->entry_date
            ? \Carbon\Carbon::parse($order->entry_date)
            : ($order->created_at ? \Carbon\Carbon::parse($order->created_at) : null);
        $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date) : null;
        $quantityTotal = $order->items->sum('quantity');

        // Verificar se tem sublimação local (para vendas PDV)
        $hasSublocal = false;
        $sublocalInfo = [];
        if ($order->is_pdv) {
            foreach ($order->items as $item) {
                if ($item->sublimations) {
                    foreach ($item->sublimations as $sub) {
                        if ($sub->location_id || $sub->location_name) {
                            $hasSublocal = true;
                            $locationName = $sub->location ? $sub->location->name : ($sub->location_name ?? 'Local não informado');
                            $sizeName = $sub->size ? $sub->size->name : ($sub->size_name ?? '');
                            $sublocalInfo[] = [
                                'location' => $locationName,
                                'size'     => $sizeName,
                                'quantity' => $sub->quantity,
                            ];
                        }
                    }
                }
            }
        }

        $animDelay = (($columnIndex ?? 0) * 100) + ($loop->index * 50);
    @endphp
    <div class="kanban-card group bg-white/50 dark:bg-slate-800/60 rounded-2xl border border-gray-100 dark:border-white/5 p-0 shadow-sm hover-lift cursor-pointer relative overflow-hidden animate-fade-in-up transition-all duration-300"
         style="box-shadow: var(--kanban-card-shadow) !important; animation-delay: {{ $animDelay }}ms"
         data-order-id="{{ $order->id }}">

        <!-- Efeito Shimmer no Hover -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite] pointer-events-none"></div>

        {{-- Imagem de Capa Premium --}}
        @if($coverImage)
        <div class="px-3 pt-3">
            <div class="h-44 bg-gray-100/50 dark:bg-slate-900/50 overflow-hidden rounded-xl border border-gray-200 dark:border-white/5 relative group-hover/img shadow-inner">
                <img src="{{ $coverImage }}"
                     alt="Capa do Pedido"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                     onerror="this.parentElement.innerHTML='<div class=\'h-full w-full bg-gradient-to-br from-indigo-500/10 to-purple-500/10 flex items-center justify-center\'><i class=\'fa-solid fa-image text-purple-500/20 text-3xl\'></i></div>'">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </div>
        @endif

        <!-- Conteúdo do Card Premium -->
        <div class="p-4 space-y-4">
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-lg text-[10px] font-black border border-purple-500/20 uppercase tracking-widest">
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                        @if($order->edit_status === 'requested')
                            <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-amber-500/10 text-amber-600 border border-amber-500/20 uppercase tracking-widest">Editado</span>
                        @endif
                    </div>
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">
                        {{ $order->created_at->format('d/m/Y') }}
                    </div>
                </div>
                @php
                    $stockBadgeLabel = 'Pendente';
                    $stockBadgeColor = 'amber';
                    $stockIcon = 'box';

                    if ($order->stock_separation_status === 'in_separation') {
                        $stockBadgeLabel = 'Em Separação';
                        $stockBadgeColor = 'blue';
                        $stockIcon = 'hand-holding-box';
                    } elseif ($order->stock_status === 'none') {
                        $stockBadgeLabel = 'Sem Estoque';
                        $stockBadgeColor = 'rose';
                        $stockIcon = 'circle-xmark';
                    } elseif ($order->stock_status === 'total') {
                        $stockBadgeLabel = 'Estoque OK';
                        $stockBadgeColor = 'emerald';
                        $stockIcon = 'circle-check';
                    } elseif ($order->stock_status === 'partial') {
                        $stockBadgeLabel = 'Parcial';
                        $stockBadgeColor = 'orange';
                        $stockIcon = 'triangle-exclamation';
                    } elseif ($order->stock_separation_status === 'completed') {
                        $stockBadgeLabel = 'Separado';
                        $stockBadgeColor = 'indigo';
                        $stockIcon = 'box-check';
                    }
                @endphp
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 border
                        @if($stockBadgeColor == 'amber') bg-amber-500/10 text-amber-600 border-amber-500/20
                        @elseif($stockBadgeColor == 'blue') bg-blue-500/10 text-blue-600 border-blue-500/20
                        @elseif($stockBadgeColor == 'rose') bg-rose-500/10 text-rose-600 border-rose-500/20 animate-pulse-soft
                        @elseif($stockBadgeColor == 'emerald') bg-emerald-500/10 text-emerald-600 border-emerald-500/20
                        @elseif($stockBadgeColor == 'orange') bg-orange-500/10 text-orange-600 border-orange-500/20
                        @elseif($stockBadgeColor == 'indigo') bg-indigo-500/10 text-indigo-600 border-indigo-500/20
                        @endif">
                        <i class="fa-solid fa-{{ $stockIcon }}"></i>
                        {{ $stockBadgeLabel }}
                    </span>
                    @if($order->is_event)
                        <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-fuchsia-500/10 text-fuchsia-600 border border-fuchsia-500/20 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-star"></i> Evento
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informações de Arquivos e Comentários -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg border {{ $filesCount > 0 ? 'bg-sky-500/10 text-sky-600 border-sky-500/20' : 'bg-gray-100/50 text-gray-400 border-gray-200 dark:border-white/5' }} text-[9px] font-black uppercase tracking-widest">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>{{ $filesCount }}</span>
                    </div>
                    <div id="comment-badge-{{ $order->id }}"
                         data-count="{{ $commentsCount }}"
                         class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-purple-500/10 text-purple-600 border border-purple-500/20 text-[9px] font-black uppercase tracking-widest {{ $commentsCount > 0 ? '' : 'hidden' }}">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span data-comment-count>{{ $commentsCount }}</span>
                    </div>
                </div>
                @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
                    <span class="kanban-drag-handle p-2 rounded-lg text-gray-300 hover:text-purple-500 hover:bg-purple-500/10 transition-colors cursor-grab active:cursor-grabbing" onclick="event.stopPropagation();">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </span>
                @endif
            </div>

            <!-- Informações Texto Premium -->
            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Cliente / Identificação</p>
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wide truncate">
                        {{ $displayName }}
                    </h3>
                    <div class="flex items-center gap-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                        <i class="fa-solid fa-shop text-[8px]"></i>
                        {{ $storeName }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2.5 text-[10px]">
                    @if($viewType === 'personalized')
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Produto</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $productName }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Personalização</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300 truncate">{{ $personalizationLabel }}</span>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Tecido</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300 truncate">{{ $firstItem?->fabric ?? 'N/A' }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Gola</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300 truncate">{{ $firstItem?->collar ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Modelo / Corte</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $firstItem?->model ?? 'N/A' }}</span>
                        </div>
                    @endif

                    <div class="pt-2 flex items-center justify-between border-t border-gray-100 dark:border-white/5">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Quantidade</span>
                            <span class="font-black text-purple-600 dark:text-purple-400">{{ $quantityTotal }} UNID</span>
                        </div>
                        <div class="flex flex-col items-end gap-0.5">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Total</span>
                            <span class="font-black text-emerald-500">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Datas de Prazo Premium -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/5">
                    @if($deliveryDate)
                        <div class="flex-1 px-3 py-2 rounded-xl bg-purple-500/5 border border-purple-500/10 flex flex-col gap-0.5">
                            <span class="text-[7px] font-black text-purple-500/60 uppercase tracking-widest leading-none">Prazo de Entrega</span>
                            <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase">
                                {{ $deliveryDate->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif
                    <div class="w-10 h-10 rounded-xl bg-gray-100/50 dark:bg-slate-700/30 flex items-center justify-center text-gray-400 group-hover:bg-purple-500 group-hover:text-white transition-all shadow-inner">
                        <i class="fa-solid fa-arrow-right-long transition-transform group-hover:translate-x-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
