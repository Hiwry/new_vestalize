@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 text-center md:text-left">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-4">Adicionar Créditos</h1>
            <p class="text-lg text-gray-500 font-medium">Use créditos para contratar serviços e comprar ferramentas exclusivas.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            
            <!-- User Balance Card -->
            <div class="lg:col-span-1">
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden sticky top-24">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary/20 rounded-full blur-2xl"></div>
                    
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2 block">Seu Saldo Atual</span>
                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-5xl font-black">{{ $wallet->balance }}</span>
                        <span class="text-sm font-bold text-gray-400 uppercase">Créditos</span>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-xs font-bold text-gray-400">Total Comprado</span>
                            <span class="text-xs font-black">{{ $wallet->total_purchased }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-xs font-bold text-gray-400">Total Gasto</span>
                            <span class="text-xs font-black">{{ $wallet->total_spent }}</span>
                        </div>
                    </div>

                    @if($isSubscriber)
                    <div class="p-6 bg-emerald-500/10 rounded-3xl border border-emerald-500/20 text-emerald-400">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fa-solid fa-crown text-sm"></i>
                            <span class="font-black text-xs uppercase tracking-widest">Assinante VIP</span>
                        </div>
                        <p class="text-[10px] leading-relaxed font-medium">Você tem direito a <strong>10% de desconto</strong> em todos os pacotes de créditos.</p>
                    </div>
                    @else
                    <div class="p-6 bg-indigo-500/10 rounded-3xl border border-indigo-500/20 text-indigo-300">
                        <p class="text-[10px] leading-relaxed font-medium">Assine o <strong>Vestalize Pro</strong> e ganhe 10% de desconto nesta compra!</p>
                        <a href="{{ route('subscription.index') }}" class="inline-block mt-3 text-[10px] font-black uppercase tracking-widest text-white underline hover:text-primary transition-colors">Saiba Mais</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Packages Grid -->
            <div class="lg:col-span-3 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($packages as $package)
                    <div class="group relative bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-700 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col {{ $package->is_featured ? 'ring-4 ring-primary ring-opacity-10' : '' }}">
                        
                        @if($package->badge)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-primary to-purple-600 text-white rounded-full text-[10px] font-black uppercase tracking-[0.25em] shadow-lg">
                            {{ $package->badge }}
                        </div>
                        @endif

                        <div class="text-center mb-8">
                            <h3 class="text-lg font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">{{ $package->name }}</h3>
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $package->credits }}</span>
                                <span class="text-xs font-bold text-gray-400 mt-4 uppercase">Créditos</span>
                            </div>
                        </div>

                        <div class="mt-auto pt-8 border-t border-gray-100 dark:border-gray-700">
                            @if($isSubscriber)
                                <div class="text-center mb-6">
                                    <span class="block text-xs font-bold text-gray-400 line-through mb-1">R$ {{ number_format($package->price, 2, ',', '.') }}</span>
                                    <span class="block text-4xl font-black text-emerald-500 tracking-tighter">R$ {{ number_format($package->subscriber_price, 2, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="text-center mb-6">
                                    <span class="block text-4xl font-black text-gray-900 dark:text-white tracking-tighter">R$ {{ number_format($package->price, 2, ',', '.') }}</span>
                                </div>
                            @endif

                            <button onclick="purchasePackage({{ $package->id }})" 
                                    class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary dark:hover:bg-primary hover:text-white shadow-xl transition-all active:scale-95 group-hover:shadow-primary/20">
                                Selecionar Pacote
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Features Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-12">
                    <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700">
                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center text-indigo-600 mb-4">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Liberação Instantânea</h4>
                        <p class="text-xs text-gray-500">Pague via PIX e receba seus créditos na hora em sua carteira.</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700">
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-emerald-600 mb-4">
                            <i class="fa-solid fa-infinity text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Sem Prazo de Validade</h4>
                        <p class="text-xs text-gray-500">Seus créditos adquiridos nunca expiram. Use quando e como quiser.</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-50 dark:border-gray-700">
                        <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-2xl flex items-center justify-center text-purple-600 mb-4">
                            <i class="fa-solid fa-receipt text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Controle Total</h4>
                        <p class="text-xs text-gray-500">Gerencie seu saldo e histórico de pedidos em um só lugar.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function purchasePackage(packageId) {
    if(!confirm('Deseja iniciar a compra deste pacote de créditos?')) return;
    
    // Mostra loading state
    const btn = event.currentTarget;
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Processando...';

    fetch(`/marketplace/creditos/comprar/${packageId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ package_id: packageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect_url) {
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'Ocorreu um erro ao processar a compra.');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro de conexão. Tente novamente.');
        btn.disabled = false;
        btn.innerText = originalText;
    });
}
</script>
@endsection
