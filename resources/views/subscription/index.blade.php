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
            <div class="bg-indigo-900/20 border border-indigo-500/30 rounded-xl p-6 text-white shadow-sm overflow-hidden relative">
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-gift text-indigo-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Experimente o Plano Médio por 7 Dias Grátis!</h3>
                            <p class="text-gray-400 text-sm mt-0.5">Explore todas as ferramentas de produtividade sem compromisso.</p>
                        </div>
                    </div>
                    @php $medioPlan = $allPlans->where('name', 'Plano Medio')->first() ?? $allPlans->sortByDesc('price')->first(); @endphp
                    @if($medioPlan && (!$currentPlan || $medioPlan->price > $currentPlan->price))
                    <form action="{{ route('subscription.trial', $medioPlan) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition-all shadow-sm whitespace-nowrap">
                            Ativar Teste Agora
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
                                <ul class="space-y-3 mb-6">
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
                                <div class="space-y-3">
                                    <form action="{{ route('mercadopago.create-preference', $plan) }}" method="POST" id="mp-form-{{ $plan->id }}">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full justify-center py-2.5 px-4 bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                            Pagar com Mercado Pago
                                        </button>
                                    </form>
                                    <button type="button" onclick="generatePix('{{ $plan->id }}')" class="inline-flex w-full justify-center py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                        <i class="fa-solid fa-qrcode mr-2"></i> Pagar com PIX
                                    </button>
                                    <script>
                                    document.getElementById('mp-form-{{ $plan->id }}').addEventListener('submit', async (e) => {
                                        e.preventDefault();
                                        const btn = e.target.querySelector('button');
                                        btn.disabled = true;
                                        btn.textContent = 'Carregando...';
                                        
                                        try {
                                            const response = await fetch('{{ route('mercadopago.create-preference', $plan) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
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
                                    });
                                    </script>
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
    function copyContent(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Copiado para a área de transferência!');
        }).catch(err => {
            console.error('Erro ao copiar: ', err);
        });
    }

    async function generatePix(planId) {
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Gerando PIX...';

        try {
            const response = await fetch(`/mercadopago/pix/${planId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.error) {
                alert(data.error);
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
                    alert('PIX gerado com sucesso!\n\n⚠️ IMPORTANTE: ' + (data.note || 'Após o pagamento, envie o comprovante para ativar seu plano.') + '\n\nChave PIX: ' + data.pix_key);
                } else {
                    alert('PIX gerado com sucesso! Veja o QR Code abaixo.');
                }
                
                // Rolar até a área do PIX
                document.getElementById('pix-display-area').scrollIntoView({ behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Error generating PIX:', error);
            alert('Erro ao gerar PIX. Tente novamente mais tarde.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
</script>
@endpush
