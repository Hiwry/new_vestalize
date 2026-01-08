<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meu Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - {{ $companySettings->company_name ?? $order->tenant->name }}</title>
    
    <!-- Dynamic Favicon -->
    @php
        $tenantLogo = $order->tenant->logo_path ?? null;
        $settingsLogo = $companySettings->logo_path ?? null;
        $finalLogoUrl = null;
        
        if ($tenantLogo && file_exists(public_path('storage/' . $tenantLogo))) {
            $finalLogoUrl = asset('storage/' . $tenantLogo);
        } elseif ($settingsLogo && file_exists(public_path($settingsLogo))) {
            $finalLogoUrl = asset($settingsLogo);
        } elseif ($settingsLogo && file_exists(public_path('storage/' . $settingsLogo))) {
            $finalLogoUrl = asset('storage/' . $settingsLogo);
        }
    @endphp
    
    @if($finalLogoUrl)
    <link rel="icon" type="image/x-icon" href="{{ $finalLogoUrl }}">
    @endif

    <!-- OpenGraph / Social Media -->
    <meta property="og:title" content="Meu Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}">
    <meta property="og:description" content="Acompanhe o status do seu pedido na {{ $companySettings->company_name ?? $order->tenant->name }}">
    @if($finalLogoUrl)
    <meta property="og:image" content="{{ $finalLogoUrl }}">
    @endif
    <meta property="og:type" content="website">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    <style>
        :root {
            --primary-color: {{ $order->tenant->primary_color ?? '#312e81' }};
            --secondary-color: {{ $order->tenant->secondary_color ?? '#4338ca' }};
        }

        @media (max-width: 640px) {
            .mobile-padding { padding: 1rem; }
            .mobile-text-sm { font-size: 0.875rem; }
            .mobile-text-xs { font-size: 0.75rem; }
        }
        
        .cover-image {
            max-width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .dark .cover-image {
            border-color: rgb(71 85 105);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        @media (max-width: 640px) {
            .cover-image {
                max-height: 150px;
            }
        }
        
        @media (max-width: 480px) {
            .cover-image {
                max-height: 120px;
            }
        }
        
        /* Dynamic Theme Colors */
        .bg-primary { background-color: var(--primary-color); }
        .bg-secondary { background-color: var(--secondary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        
        /* Dark mode text colors */
        .dark .client-order p,
        .dark .client-order span,
        .dark .client-order div {
            color: rgb(203 213 225);
        }
        
        .dark .client-order .text-gray-900 {
            color: rgb(248 250 252) !important;
        }
        
        .dark .client-order .text-gray-800 {
            color: rgb(241 245 249) !important;
        }
        
        .dark .client-order .text-gray-700 {
            color: rgb(226 232 240) !important;
        }
        
        .dark .client-order .text-gray-600 {
            color: rgb(203 213 225) !important;
        }
        
        .dark .client-order .text-gray-500 {
            color: rgb(148 163 184) !important;
        }
        
        /* Backgrounds */
        .dark .bg-white {
            background-color: rgb(15 23 42) !important;
        }
        
        .dark .bg-gray-50 {
            background-color: rgb(30 41 59) !important;
        }
        
        .dark .bg-gray-100 {
            background-color: rgb(30 41 59) !important;
        }
        
        /* Borders */
        .dark .border-gray-200 {
            border-color: rgb(51 65 85) !important;
        }
        
        .dark .border-gray-300 {
            border-color: rgb(71 85 105) !important;
        }
        
        /* SVG icons */
        .dark .text-gray-400 {
            color: rgb(148 163 184) !important;
        }
    </style>
</head>
<body class="client-order bg-gray-50 dark:bg-slate-950 min-h-screen transition-colors duration-200">
    <!-- Header -->
    <div class="bg-primary text-white py-6 px-4 mobile-padding shadow-sm dark:shadow-lg dark:shadow-indigo-900/30">
        <div class="max-w-md mx-auto flex flex-col items-center">
            @if($order->tenant->logo_path)
                <img src="{{ asset('storage/' . $order->tenant->logo_path) }}" alt="Logo" class="h-12 w-auto mb-3 object-contain">
            @endif
            <h1 class="text-xl font-semibold text-center">Meu Pedido</h1>
            <p class="text-center text-gray-300 dark:text-indigo-200 mobile-text-sm">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div class="max-w-md mx-auto mobile-padding">
        @if(session('success'))
        <div class="bg-gray-100 dark:bg-green-900/20 border border-gray-300 dark:border-green-800 text-gray-700 dark:text-green-300 px-4 py-3 rounded mb-4 mobile-text-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Status do Pedido -->
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Status do Pedido</h2>
                <span class="px-3 py-1 text-xs font-medium rounded-full text-white" 
                      style="background-color: {{ $order->status->color }}">
                    {{ $order->status->name }}
                </span>
            </div>
            
            @if($order->client_confirmed)
            <div class="bg-gray-50 dark:bg-green-900/20 border border-gray-200 dark:border-green-800 rounded p-3 mb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700 dark:text-green-300 font-medium mobile-text-sm">Pedido Confirmado</span>
                </div>
                <p class="text-gray-500 dark:text-green-400 mobile-text-xs mt-1">
                    Confirmado em {{ $order->client_confirmed_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mobile-text-sm">
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Data do Pedido</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
                @if($order->delivery_date)
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Previsão de Entrega</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Dados do Cliente -->
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Seus Dados</h2>
            <div class="space-y-2 mobile-text-sm">
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Nome</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->client->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Telefone</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->client->phone_primary }}</p>
                </div>
                @if($order->client->email)
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Email</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->client->email }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Informações do Vendedor -->
        @if($order->user)
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Atendimento</h2>
            <div class="space-y-2 mobile-text-sm">
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Vendedor</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->user->name }}</p>
                </div>
                @if($order->user->store)
                <div>
                    <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Loja</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->user->store }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Itens do Pedido -->
        @foreach($order->items as $item)
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Item {{ $loop->iteration }} - {{ $item->print_type }}</h2>
            
            @if($item->art_name)
            <div class="mb-3">
                <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Nome da Arte</p>
                <p class="font-medium mobile-text-sm">{{ $item->art_name }}</p>
            </div>
            @endif

            <!-- Imagem de Capa -->
            @if($item->cover_image || $item->cover_image_url)
            <div class="mb-3">
                <p class="text-gray-600 dark:text-slate-400 mobile-text-xs mb-2">Imagem de Referência</p>
                <div class="text-center">
                    @php
                        $imageUrl = $item->cover_image_url;
                    @endphp
                    @if($imageUrl)
                    <img src="{{ $imageUrl }}" 
                         alt="Imagem de Referência" 
                         class="cover-image mx-auto cursor-pointer hover:opacity-80 transition-opacity"
                         onclick="openImageModal('{{ $imageUrl }}')"
                         onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                    @endif
                    
                    <div class="text-gray-500 dark:text-slate-400 text-sm py-4 {{ $imageUrl ? 'hidden' : '' }}">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-slate-300">Erro ao carregar imagem</p>
                        @if($item->cover_image)
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ basename($item->cover_image) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Especificações -->
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Especificações</p>
                <div class="grid grid-cols-2 gap-2 mobile-text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-slate-400">Tecido:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $item->fabric }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-slate-400">Cor:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $item->color }}</span>
                    </div>
                </div>
            </div>

            <!-- Aplicações -->
            @if($item->sublimations && $item->sublimations->count() > 0)
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Personalizações</p>
                @foreach($item->sublimations as $index => $sub)
                @php
                    $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                    $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                @endphp
                <div class="bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 p-3 rounded mb-2 mobile-text-sm">
                    <div class="font-medium">
                        {{ $index + 1 }}. 
                        @if($sizeName)
                            {{ $sizeName }}
                        @else
                            {{ $appType }}
                        @endif
                    </div>
                    <div class="text-gray-600 mobile-text-xs">
                        @if($locationName){{ $locationName }} | @endif
                        Qtd: {{ $sub->quantity }}
                        @if($sub->color_count > 0) | {{ $sub->color_count }} cores @endif
                        @if($sub->has_neon) | Neon @endif
                    </div>
                    
                    <!-- Imagem da Aplicação -->
                    @if($sub->application_image)
                    <div class="mt-2">
                        @php
                            // Tentar diferentes formas de acessar a imagem
                            $appImageUrl = null;
                            $disk = \Illuminate\Support\Facades\Storage::disk('public');
                            $imagePath = ltrim($sub->application_image, '/'); // Remover barra inicial se houver
                            $basename = basename($imagePath);
                            
                            // Lista de caminhos possíveis para tentar
                            $possiblePaths = [
                                // Caminho original como está salvo no banco
                                $imagePath,
                                // Caminhos completos esperados
                                'orders/applications/' . $basename,
                                'orders/sublimations/' . $basename,
                                'orders/items/applications/' . $basename,
                                'orders/items/sublimations/' . $basename,
                                // Sem prefixo orders
                                'applications/' . $basename,
                                'sublimations/' . $basename,
                                // Apenas o nome do arquivo
                                $basename,
                            ];
                            
                            // Tentar encontrar o arquivo em algum dos caminhos
                            $foundPath = null;
                            foreach ($possiblePaths as $possiblePath) {
                                if ($disk->exists($possiblePath)) {
                                    $foundPath = $possiblePath;
                                    break;
                                }
                            }
                            
                            // Usar a rota específica /imagens-aplicacao/ que é mais confiável
                            // Esta rota NÃO depende de .htaccess ou symlink
                            try {
                                $appImageUrl = route('application.image', ['filename' => $basename]);
                            } catch (\Exception $e) {
                                // Se a rota não existe, usar fallback
                                $appImageUrl = url('/imagens-aplicacao/' . $basename);
                            }
                            
                            // Garantir que temos uma URL válida
                            if (!$appImageUrl) {
                                if ($foundPath) {
                                    $publicPath = public_path('storage/' . $foundPath);
                                    if (file_exists($publicPath)) {
                                        $appImageUrl = asset('storage/' . $foundPath);
                                    } else {
                                        $appImageUrl = url('/storage/' . $foundPath);
                                    }
                                } else {
                                    // Fallback final: usar diretamente a URL da nova rota
                                    $appImageUrl = url('/imagens-aplicacao/' . $basename);
                                }
                            }
                            
                            // Debug: Log sempre (não apenas em debug mode) para identificar problema
                            \Log::info('Application image URL generation', [
                                'order_sublimation_id' => $sub->id,
                                'original_path' => $sub->application_image,
                                'basename' => $basename,
                                'found_path' => $foundPath ?? 'NOT_FOUND',
                                'generated_url' => $appImageUrl,
                                'disk_exists_original' => $disk->exists($imagePath),
                                'tried_paths' => $possiblePaths
                            ]);
                        @endphp
                        
                        @if($appImageUrl)
                            <p class="text-gray-600 dark:text-slate-400 mobile-text-xs mb-1">Imagem da Aplicação {{ $index + 1 }}</p>
                            <div class="text-center">
                                <img src="{{ $appImageUrl }}" 
                                 alt="Imagem da Aplicação {{ $index + 1 }}" 
                                     class="max-w-xs w-full mx-auto rounded border border-gray-200 dark:border-slate-700 cursor-pointer hover:opacity-80 transition-opacity shadow-sm"
                                     onclick="openImageModal('{{ $appImageUrl }}')"
                                     onerror="console.error('Erro ao carregar imagem:', this.src); this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="hidden text-center text-gray-500 dark:text-slate-400 text-xs mt-1">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p>Erro ao carregar imagem</p>
                                    <p class="text-xs mt-1">{{ basename($sub->application_image) }}</p>
                                    <p class="text-xs mt-1 opacity-75">URL tentada: {{ $appImageUrl }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-slate-400 text-xs mt-1">
                                <svg class="w-8 h-8 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p>Imagem não encontrada</p>
                                @if($sub->application_image)
                                <p class="text-xs mt-1">Arquivo: {{ basename($sub->application_image) }}</p>
                                <p class="text-xs mt-1 opacity-75">Caminho no BD: {{ $sub->application_image }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <!-- Tamanhos -->
            @php
                // Garantir que sizes seja um array
                $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) && !empty($item->sizes) ? json_decode($item->sizes, true) : []);
                $itemSizes = $itemSizes ?? [];

                // Verificação de tamanhos reais (mesma lógica do Kanban)
                $hasRealSizes = false;
                $totalQuantity = 0;
                
                if (!empty($itemSizes)) {
                    foreach ($itemSizes as $size => $quantity) {
                        $qty = (int)$quantity;
                        $totalQuantity += $qty;
                        if ($qty > 0 && strtoupper($size) !== 'ÚNICO' && strtoupper($size) !== 'UN' && strtoupper($size) !== 'UNICO') {
                            $hasRealSizes = true;
                        }
                    }
                }

                $isSimpleItem = !$hasRealSizes;
                $printType = trim($item->print_type ?? '');
                $fabric = trim($item->fabric ?? '');
                
                $shouldShowTotalOnly = (($printType === 'Sublimação Local' || $fabric === 'Produto Pronto') && $isSimpleItem);
            @endphp

            <div class="mb-3">
                <p class="text-gray-600 dark:text-slate-400 mobile-text-xs mb-2">Quantidades</p>
                
                @if($shouldShowTotalOnly)
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-slate-800/50 p-3 rounded border border-gray-200 dark:border-slate-700">
                        <span class="text-gray-700 dark:text-slate-300 font-medium text-sm">Quantidade Total</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalQuantity }}</span>
                    </div>
                @else
                    <div class="grid grid-cols-5 gap-1 mobile-text-xs">
                        @foreach(['PP', 'P', 'M', 'G', 'GG'] as $size)
                        <div class="text-center p-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $size }}</div>
                            <div>{{ $itemSizes[$size] ?? 0 }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if(isset($itemSizes['EXG']) || isset($itemSizes['G1']) || isset($itemSizes['G2']) || isset($itemSizes['G3']) || isset($itemSizes['ESPECIAL']))
                    <div class="grid grid-cols-5 gap-1 mobile-text-xs mt-1">
                        @foreach(['EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                        @if(isset($itemSizes[$size]) && $itemSizes[$size] > 0)
                        <div class="text-center p-1.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $size }}</div>
                            <div>{{ $itemSizes[$size] }}</div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                @endif
            </div>
        </div>
        @endforeach

        <!-- Resumo Financeiro -->
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Resumo Financeiro</h2>
            <div class="space-y-2 mobile-text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-slate-400">Total do Pedido:</span>
                    <span class="font-semibold text-base text-gray-900 dark:text-white">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
                @if($payment)
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-slate-400">Total Pago:</span>
                    <span class="font-medium text-gray-900 dark:text-green-400">R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-500 dark:text-slate-400">{{ $payment->remaining_amount < 0 ? 'Crédito do Cliente:' : 'Restante:' }}</span>
                    <span class="font-medium {{ $payment->remaining_amount > 0 ? 'text-gray-900 dark:text-orange-400' : 'text-gray-900 dark:text-green-400' }}">
                        R$ {{ number_format(abs($payment->remaining_amount), 2, ',', '.') }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Confirmação do Pedido -->
        @if(!$order->client_confirmed)
        <div class="bg-gray-50 dark:bg-yellow-900/20 border border-gray-200 dark:border-yellow-800 rounded p-4 mb-4">
            <h2 class="text-base font-semibold mb-3 text-gray-900 dark:text-yellow-300">Confirme Seu Pedido</h2>
            <p class="text-gray-600 dark:text-yellow-400 mobile-text-sm mb-4">
                Por favor, confirme se os dados do seu pedido estão corretos. Esta confirmação é importante para prosseguirmos com a produção.
            </p>
            
            <form method="POST" action="{{ route('client.order.confirm', $order->client_token) }}" id="client-confirm-form">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" 
                               name="client_name" 
                               value="{{ $order->client->name }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-indigo-400 mobile-text-sm"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="tel" 
                               name="client_phone" 
                               value="{{ $order->client->phone_primary }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-indigo-400 mobile-text-sm"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                        <textarea name="confirmation_notes" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:outline-none focus:border-gray-400 dark:focus:border-indigo-400 mobile-text-sm"
                                  placeholder="Alguma observação sobre o pedido..."></textarea>
                    </div>
                    
                    <!-- Termos e Condições -->
                    <div class="bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded p-4">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" 
                                   id="accept_terms" 
                                   name="accept_terms" 
                                   value="1"
                                   class="mt-1 h-4 w-4 text-gray-700 dark:text-indigo-500 focus:ring-gray-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 rounded"
                                   required>
                            <div class="flex-1">
                                <label for="accept_terms" class="text-sm font-semibold text-gray-700 dark:text-slate-300 cursor-pointer">
                                    Aceito os 
                                    <button type="button" 
                                            onclick="openTermsModal()" 
                                            class="underline font-bold" style="color: var(--secondary-color)">
                                        Termos e Condições
                                    </button>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    Ao confirmar o pedido, você concorda com nossos termos e condições de serviço.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full mt-4 bg-primary hover:opacity-90 text-white py-3 px-4 rounded dark:rounded-lg font-medium mobile-text-sm transition-colors dark:shadow-lg">
                    Confirmar Pedido
                </button>
            </form>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-green-900/20 border border-gray-200 dark:border-green-800 rounded p-4 mb-4">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <h2 class="text-base font-semibold text-gray-900 dark:text-green-300">Pedido Confirmado</h2>
            </div>
            <p class="text-gray-600 dark:text-green-400 mobile-text-sm">
                Obrigado pela confirmação! Seu pedido está em produção. Você pode acompanhar o status aqui mesmo.
            </p>
            @if($order->client_confirmation_notes)
            <div class="mt-3 p-3 bg-white rounded border">
                <p class="text-gray-500 dark:text-slate-400 mobile-text-xs">Suas observações:</p>
                <p class="mobile-text-sm">{{ $order->client_confirmation_notes }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Observações do Pedido -->
        @if($order->notes)
        <div class="bg-white dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-800 dark:shadow-2xl dark:shadow-black/20 p-4 mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Observações</h2>
            <p class="mobile-text-sm text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center py-6 mobile-text-xs text-gray-500 dark:text-slate-400">
            <p class="font-bold mb-1">{{ $companySettings->company_name ?? $order->tenant->name }}</p>
            <p>Para dúvidas, entre em contato conosco</p>
            <p>
                @if($companySettings->company_phone || $companySettings->company_email)
                    @if($companySettings->company_phone)Tel: {{ $companySettings->company_phone }}@endif
                    @if($companySettings->company_phone && $companySettings->company_email) | @endif
                    @if($companySettings->company_email)Email: {{ $companySettings->company_email }}@endif
                @else
                    Tel: (11) 99999-9999 | Email: contato@empresa.com
                @endif
            </p>
            @if($companySettings->company_cnpj)
            <p class="mt-1">CNPJ: {{ $companySettings->company_cnpj }}</p>
            @endif
            <p class="mt-2">
                <a href="#" onclick="openTermsModal()" class="font-bold underline" style="color: var(--secondary-color)">
                    Termos e Condições
                </a>
            </p>
        </div>
    </div>

    <!-- Modal de Zoom da Imagem -->
    <div id="imageModal" class="fixed inset-0 bg-black/70 dark:bg-black/90 dark:backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white text-2xl font-bold rounded-full w-10 h-10 flex items-center justify-center transition-colors z-10">
                ×
            </button>
            <img id="modalImage" src="" alt="Imagem em zoom" class="max-w-full max-h-full object-contain rounded">
        </div>
    </div>



    <script>
        // Funções para o modal de zoom
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Previne scroll da página
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restaura scroll da página
        }

        // Fechar modal ao clicar fora da imagem
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Fechar modal com tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
                closeTermsModal();
            }
        });

        // Funções para o modal de termos e condições
        function openTermsModal() {
            const modal = document.getElementById('termsModal');
            const content = document.getElementById('termsContent');
            
            // Mostrar modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Carregar conteúdo via AJAX com order_id
            const orderId = {{ $order->id ?? 'null' }};
            const url = orderId ? `/api/terms-conditions?order_id=${orderId}` : '/api/terms-conditions';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Se retornar combined_content (múltiplos termos), usar ele
                        if (data.combined_content) {
                            content.innerHTML = data.combined_content;
                        } else if (data.content) {
                            content.innerHTML = data.content.replace(/\n/g, '<br>');
                        } else {
                            content.innerHTML = '<p class="text-gray-500">Termos e condições não disponíveis no momento.</p>';
                        }
                    } else {
                        content.innerHTML = '<p class="text-gray-500">Termos e condições não disponíveis no momento.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar termos e condições:', error);
                    content.innerHTML = '<p class="text-red-500">Erro ao carregar termos e condições.</p>';
                });
        }

        function closeTermsModal() {
            const modal = document.getElementById('termsModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Fechar modal de termos ao clicar fora
        document.getElementById('termsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTermsModal();
            }
        });

        // Validação do formulário de confirmação
        document.addEventListener('DOMContentLoaded', function() {
            const confirmForm = document.getElementById('client-confirm-form');
            if (confirmForm) {
                confirmForm.addEventListener('submit', function(e) {
                    const acceptTerms = document.getElementById('accept_terms');
                    if (!acceptTerms || !acceptTerms.checked) {
                        e.preventDefault();
                        alert('Você deve aceitar os Termos e Condições para confirmar o pedido.');
                        return false;
                    }
                });
            }
        });

        // Auto-refresh da página a cada 30 segundos para atualizar o status
        setTimeout(function() {
            location.reload();
        }, 30000);

        });
    </script>

    <!-- Modal de Termos e Condições -->
    <div id="termsModal" class="fixed inset-0 bg-black/75 dark:bg-black/90 z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="relative max-w-4xl w-full max-h-[90vh] bg-white dark:bg-slate-900 dark:text-slate-200 rounded-lg shadow-2xl flex flex-col overflow-hidden">
            <!-- Header do Modal -->
            <div class="bg-gray-50 dark:bg-slate-800 px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Termos e Condições</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1" id="termsVersion"></p>
                </div>
                <button type="button" 
                        onclick="closeTermsModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Conteúdo do Modal -->
            <div class="p-8 overflow-y-auto flex-grow prose prose-sm dark:prose-invert max-w-none">
                <div id="termsContent" class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed space-y-4">
                    <div class="flex items-center justify-center py-10">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-3 text-gray-600 dark:text-slate-400 font-medium">Carregando termos...</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer do Modal -->
            <div class="bg-gray-50 dark:bg-slate-800 px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex justify-end">
                <button type="button" 
                        onclick="closeTermsModal()"
                        class="px-8 py-2.5 bg-primary text-white rounded-lg font-semibold shadow-lg hover:opacity-90 transition-all">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</body>
</html>
