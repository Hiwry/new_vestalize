@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Minha Assinatura') }}
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        {{-- Botão de Teste 7 dias alert (se não estiver em trial) --}}
        @if(!$tenant->trial_ends_at || $tenant->trial_ends_at->isPast())
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-700 dark:to-purple-700 rounded-xl p-6 text-white shadow-lg overflow-hidden relative">
                {{-- Decorative elements --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shrink-0 shadow-inner">
                            <i class="fa-solid fa-gift text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Experimente o Plano Médio por 7 Dias Grátis!</h3>
                            <p class="text-white/80 text-sm mt-0.5">Explore todas as ferramentas de produtividade sem compromisso.</p>
                        </div>
                    </div>
                    @php $medioPlan = $allPlans->where('name', 'Plano Medio')->first() ?? $allPlans->sortByDesc('price')->first(); @endphp
                    @if($medioPlan && (!$currentPlan || $medioPlan->price > $currentPlan->price))
                    <form action="{{ route('subscription.trial', $medioPlan) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-white hover:bg-gray-100 text-indigo-700 px-6 py-3 rounded-lg font-bold transition-all shadow-lg hover:shadow-xl whitespace-nowrap hover:scale-105 transform">
                            <i class="fa-solid fa-rocket mr-2"></i> Ativar Teste Agora
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Manual PIX Payment Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- PIX Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-emerald-500/30 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-qrcode text-2xl text-emerald-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pagamento Instantâneo PIX</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">Pague e libere sua conta na hora</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Selecione um plano abaixo e clique em <span class="font-bold text-emerald-500">"Pagar com PIX"</span> para gerar seu QR Code.</p>

                    <div id="pix-display-area" class="hidden animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-emerald-500/20 mb-4 flex flex-col items-center">
                            <img id="pix-qr-code" src="" alt="QR Code PIX" class="w-48 h-48 mb-4 border-4 border-white dark:border-gray-700 rounded-lg shadow-sm">
                            
                            <a id="pix-ticket-url" href="#" target="_blank" class="text-emerald-600 dark:text-emerald-400 text-xs font-bold hover:underline mb-4 flex items-center">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i> Abrir QR Code em nova aba
                            </a>

                            <div class="w-full">
                                <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold block mb-1">Copia e Cola PIX</span>
                                <div class="flex items-center justify-between gap-2 bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <code class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 break-all line-clamp-2" id="pix-copy-paste"></code>
                                    <button onclick="copyContent('pix-copy-paste')" class="shrink-0 p-2 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Support Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-center">
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">Precisa de Ajuda?</h4>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Contate nosso suporte para ativação ou dúvidas sobre pagamentos.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="https://wa.me/5500000000000" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                        <i class="fa-brands fa-whatsapp mr-2 text-sm"></i> WhatsApp
                    </a>
                    <a href="mailto:contato@vestalize.com" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-lg transition-colors">
                        <i class="fa-solid fa-envelope mr-2 text-sm"></i> E-mail
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Seu Plano Atual</span>
                            @if($tenant->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-baseline gap-4">
                            @php
                                $isTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
                                $remainingTrialDays = $isTrial ? ceil(now()->diffInHours($tenant->trial_ends_at) / 24) : 0;
                            @endphp
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                                @if($isTrial)
                                    Teste por {{ (int)$remainingTrialDays }} {{ $remainingTrialDays > 1 ? 'dias' : 'dia' }}
                                    <span class="text-lg font-normal text-indigo-500">({{ $currentPlan?->name }})</span>
                                @else
                                    {{ $currentPlan ? $currentPlan->name : 'Sem Plano' }}
                                @endif
                            </h3>
                            @if($currentPlan && !$isTrial)
                            <span class="text-lg text-gray-600 dark:text-gray-400">
                                R$ {{ number_format($currentPlan->price, 2, ',', '.') }}<span class="text-sm">/mês</span>
                            </span>
                            @endif
                        </div>

                        {{-- Usage Stats (Minimal) --}}
                         @if($currentPlan && $currentPlan->limits)
                            <div class="mt-6 flex gap-6 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-gray-400"></i>
                                    <span>
                                        <strong>{{ $tenant->stores()->count() }}</strong> / {{ $currentPlan->limits['stores'] ?? 1 }} Lojas
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-gray-400"></i>
                                    <span>
                                        <strong>{{ $tenant->users()->count() }}</strong> / {{ $currentPlan->limits['users'] ?? 2 }} Usuários
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Expiration Info --}}
                    <div class="text-left md:text-right">
                        @php
                            $endDate = $isTrial ? $tenant->trial_ends_at : $tenant->subscription_ends_at;
                        @endphp
                        
                        @if($endDate)
                            @php
                                $daysLeft = ceil(now()->diffInHours($endDate, false) / 24);
                                $isExpired = $daysLeft < 0;
                                $isExpiring = $daysLeft >= 0 && $daysLeft <= 7;
                            @endphp
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                {{ $isTrial ? 'Expira em' : 'Renovação em' }}
                            </p>
                            <p class="text-xl font-semibold {{ $isExpired ? 'text-red-600' : ($isExpiring ? 'text-amber-600' : 'text-gray-900 dark:text-white') }}">
                                {{ $endDate->format('d/m/Y') }}
                            </p>
                            <p class="text-sm mt-1 {{ $isExpired ? 'text-red-500' : ($isExpiring ? 'text-amber-500' : 'text-gray-400') }}">
                                @if($isExpired)
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Expirado há {{ (int)abs($daysLeft) }} dias
                                @else
                                    <i class="fa-solid fa-clock mr-1"></i> {{ (int)$daysLeft }} {{ (int)$daysLeft > 1 ? 'dias restantes' : 'dia restante' }}
                                @endif
                            </p>

                            @if(!$isTrial && now()->diffInDays($tenant->subscription_ends_at, false) <= 30)
                                <div class="mt-4">
                                     <form action="{{ route('subscription.renew') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                                            <i class="fa-solid fa-rotate mr-2"></i> Renovar Agora
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">Assinatura vitalícia ou não definida</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Available Plans Grid --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Planos Disponíveis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($allPlans as $plan)
                    @php
                        $onTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
                        $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id && !$onTrial;
                        $isUpgrade = !$currentPlan || $plan->price > $currentPlan->price || $onTrial;
                    @endphp
                    
                    <div class="flex flex-col bg-white dark:bg-gray-800 rounded-lg {{ ($currentPlan && $currentPlan->id === $plan->id) ? 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-900' : 'border border-gray-100 dark:border-gray-700' }} shadow-sm transition-all hover:shadow-md">
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg">{{ $plan->name }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 min-h-[40px]">{{ $plan->description }}</p>
                                </div>
                                @if($currentPlan && $currentPlan->id === $plan->id)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400">
                                        @if($onTrial)
                                            <i class="fa-solid fa-clock text-xs"></i>
                                        @else
                                            <i class="fa-solid fa-check text-xs"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mb-6">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">/mês</span>
                            </div>
                            
                            @if($plan->features)
                                @php
                                    $planFeatures = $plan->features ?? [];
                                    $hasAll = in_array('*', $planFeatures);
                                    $displayFeatures = collect(\App\Models\Plan::AVAILABLE_FEATURES)
                                        ->except(['subscription_module'])
                                        ->toArray();
                                    $showLocked = $plan->slug === 'start';
                                @endphp
                                <ul class="space-y-3 mb-6">
                                    @if($showLocked)
                                        @foreach($displayFeatures as $featureKey => $label)
                                            @php $included = $hasAll || in_array($featureKey, $planFeatures); @endphp
                                            <li class="flex items-start text-sm {{ $included ? 'text-gray-600 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">
                                                @if($included)
                                                    <i class="fa-solid fa-check text-green-500 mt-0.5 mr-2.5"></i>
                                                @else
                                                    <i class="fa-solid fa-lock text-gray-400 mt-0.5 mr-2.5"></i>
                                                @endif
                                                <span>{{ $label }}</span>
                                                @if(!$included)
                                                    <span class="ml-2 text-[10px] uppercase tracking-widest text-gray-400">Não incluso</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    @else
                                        @foreach($plan->features as $feature)
                                            <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                @if($feature === '*')
                                                     <i class="fa-solid fa-star text-indigo-500 mt-0.5 mr-2.5"></i>
                                                     <span class="font-medium">Acesso Completo</span>
                                                @else
                                                    <i class="fa-solid fa-check text-green-500 mt-0.5 mr-2.5"></i>
                                                    {{ \App\Models\Plan::AVAILABLE_FEATURES[$feature] ?? $feature }}
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                    
                                    {{-- Limites explícitos --}}
                                    @if($plan->limits)
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300 pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                                            <i class="fa-solid fa-store text-gray-400 mr-2.5"></i>
                                            {{ $plan->limits['stores'] ?? 1 }} Lojas
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fa-solid fa-users text-gray-400 mr-2.5"></i>
                                            {{ $plan->limits['users'] ?? 2 }} Usuários
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </div>

                        <div class="p-6 pt-0 mt-auto">
                            @if($isCurrentPlan)
                                <button disabled class="w-full py-2.5 px-4 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50 cursor-default">
                                    Sua Assinatura Atual
                                </button>
                            @elseif($isUpgrade || $onTrial)
                                <div class="space-y-3" x-data="{ coupon: '', discountPrice: null, couponMessage: '', couponValid: false }">
                                    {{-- Coupon Input --}}
                                    <div class="mb-2">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="coupon" placeholder="Cupom de Desconto" 
                                                   class="flex-1 text-xs border border-gray-200 dark:border-gray-700 dark:bg-gray-900 rounded-md px-2 py-1.5 focus:ring-0 focus:border-indigo-500">
                                            <button type="button" @click="
                                                if(!coupon) return;
                                                const res = await fetch('{{ route('subscription.validate-coupon') }}', {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                    body: JSON.stringify({ code: coupon, plan_id: '{{ $plan->id }}' })
                                                });
                                                const data = await res.json();
                                                couponValid = data.success;
                                                couponMessage = data.message;
                                                if(data.success) discountPrice = data.discount_price;
                                                showToast(data.message, data.success ? 'success' : 'error');
                                            " class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline uppercase">
                                                Aplicar
                                            </button>
                                        </div>
                                        <template x-if="couponValid">
                                            <p class="text-[10px] text-green-600 mt-1 font-medium">
                                                Preço com desconto: <strong>R$ <span x-text="discountPrice.toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span></strong>
                                            </p>
                                        </template>
                                    </div>

                                    <button type="button" @click="
                                        const btn = $el;
                                        btn.disabled = true;
                                        btn.textContent = 'Carregando...';
                                        
                                        try {
                                            const response = await fetch('{{ route('mercadopago.create-preference', $plan) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ coupon_code: coupon })
                                            });
                                            const data = await response.json();
                                            if (data.error) {
                                                alert(data.error);
                                                btn.disabled = false;
                                                btn.textContent = 'Pagar com Mercado Pago';
                                            } else {
                                                window.location.href = data.init_point;
                                            }
                                        } catch (error) {
                                            alert('Erro ao processar pagamento');
                                            btn.disabled = false;
                                            btn.textContent = 'Pagar com Mercado Pago';
                                        }
                                    " class="inline-flex w-full justify-center py-2.5 px-4 bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                        Pagar com Mercado Pago
                                    </button>

                                    <button type="button" @click="generatePix('{{ $plan->id }}', coupon, $event)" class="inline-flex w-full justify-center py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                        <i class="fa-solid fa-qrcode mr-2"></i> Pagar com PIX
                                    </button>
                                    
                                    <form action="{{ route('subscription.trial', $plan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 px-4 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium transition-colors">
                                           <i class="fa-solid fa-gift mr-1.5 text-indigo-500"></i> Testar 7 Dias
                                        </button>
                                    </form>
                                </div>
                            @else
                                 <button disabled class="w-full py-2.5 px-4 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50 cursor-default">
                                    Plano Inferior
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Add-ons --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6 md:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Adicionais (mensais)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                        <i class="fa-solid fa-list-ul text-indigo-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Catálogo</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ 50,00/mês</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                        <i class="fa-solid fa-file-invoice text-indigo-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Notas Fiscais (NF-e)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ 250,00/mês</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                        <i class="fa-solid fa-store text-indigo-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Loja extra</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ 100,00/mês</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                        <i class="fa-solid fa-user-plus text-indigo-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Usuário extra</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ 30,00/mês</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Todos os valores são mensais.</p>
            </div>
        </div>

        {{-- Contact/Support Snippet --}}
        <div class="text-center pt-8 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Precisa de um plano personalizado? 
                <a href="mailto:suporte@vestalize.com" class="text-indigo-600 hover:text-indigo-500 font-medium inline-flex items-center">
                    Fale conosco <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </p>
        </div>
        
    </div>
</div>

{{-- Histórico de Pagamentos (últimos 10) --}}
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Histórico de Pagamentos</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Mostrando os últimos 10</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cupom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($subscriptionPayments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ optional($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->plan->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        R$ {{ number_format($payment->amount, 2, ',', '.') }} {{ strtoupper($payment->currency) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        @if($payment->coupon_code)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                {{ $payment->coupon_code }}
                                            </span>
                                            <div class="text-[10px] text-green-600 font-medium">- R$ {{ number_format($payment->discount_amount, 2, ',', '.') }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $payment->status === 'succeeded' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->payment_intent_id ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Nenhum pagamento registrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.copyContent = function copyContent(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            notify('Copiado para a área de transferência!', 'success', 2000);
        }).catch(err => {
            notify('Erro ao copiar', 'error');
        });
    }

    window.generatePix = async function generatePix(planId, coupon = '', event = null) {
        const btn = event && event.currentTarget ? event.currentTarget : null;
        const originalHtml = btn ? btn.innerHTML : null;
        
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Gerando PIX...';
        }

        try {
            const response = await fetch(`/mercadopago/pix/${planId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ coupon_code: coupon })
            });

            const data = await response.json();

            if (data.error) {
                showToast(data.error, 'error');
            } else {
                document.getElementById('pix-qr-code').src = `data:image/png;base64,${data.qr_code_base64}`;
                document.getElementById('pix-copy-paste').innerText = data.qr_code;
                
                // Ajustar link do ticket (pode ser # no fallback)
                const ticketLink = document.getElementById('pix-ticket-url');
                if (data.ticket_url && data.ticket_url !== '#') {
                    ticketLink.href = data.ticket_url;
                    ticketLink.classList.remove('hidden');
                } else {
                    ticketLink.classList.add('hidden');
                }
                
                document.getElementById('pix-display-area').classList.remove('hidden');
                
                // Se for fallback, mostrar nota especial
                if (data.source === 'pixservice') {
                    showToast('PIX gerado! Após o pagamento, envie o comprovante para ativar seu plano. Chave: ' + data.pix_key, 'warning', 8000);
                } else {
                    showToast('PIX gerado com sucesso! Escaneie o QR Code abaixo.', 'success');
                }
                
                // Rolar até a área do PIX
                document.getElementById('pix-display-area').scrollIntoView({ behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Error generating PIX:', error);
            showToast('Erro ao gerar PIX. Tente novamente mais tarde.', 'error');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    }
    
    // Toast Notification System
    function showToast(message, type = 'info', duration = 5000) {
        const container = document.getElementById('toast-container') || createToastContainer();
        
        const colors = {
            success: 'bg-emerald-600',
            error: 'bg-red-600',
            warning: 'bg-amber-500',
            info: 'bg-blue-600'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-start gap-3 max-w-md transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <i class="fa-solid ${icons[type]} text-lg mt-0.5 shrink-0"></i>
            <span class="text-sm">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white shrink-0">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush
