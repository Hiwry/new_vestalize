<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - {{ $plan->name }} | {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Mercado Pago SDK -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Vestalize</h1>
                <p class="text-indigo-200">Finalize sua Assinatura</p>
            </div>
            
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Plan Details -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Detalhes da Assinatura</h2>
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-500">Renovação mensal</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-indigo-600">
                                R$ {{ number_format($plan->price, 2, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500">/mês</p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods Info -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Métodos de Pagamento Disponíveis</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">PIX</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">Cartão</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-xs font-semibold text-gray-700">Boleto</p>
                        </div>
                    </div>
                </div>
                
                <!-- Error Message -->
                <div id="error-message" class="hidden mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span id="error-text"></span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col space-y-4">
                    <button 
                        id="checkout-btn"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-4 px-6 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all duration-150 hover:scale-105 text-lg"
                    >
                        Prosseguir para Pagamento
                    </button>
                    
                    <a 
                        href="{{ route('dashboard') }}"
                        class="text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                    >
                        ← Voltar ao Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Security Info -->
            <div class="text-center mt-6 text-white/80 text-sm">
                <p>🔒 Pagamento 100% seguro via Mercado Pago</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const btn = document.getElementById('checkout-btn');
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            // Inicializar Mercado Pago com a Public Key
            const mp = new MercadoPago('{{ config('services.mercadopago.public_key') }}');

            btn.addEventListener('click', async () => {
                btn.disabled = true;
                btn.textContent = 'Carregando...';
                errorDiv.classList.add('hidden');

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
                        throw new Error(data.error);
                    }

                    // Redirecionar para o checkout do Mercado Pago
                    // Em produção, use init_point; em sandbox, use sandbox_init_point
                    const checkoutUrl = data.init_point || data.sandbox_init_point;
                    window.location.href = checkoutUrl;

                } catch (error) {
                    console.error('Erro:', error);
                    errorText.textContent = error.message || 'Erro ao criar preferência de pagamento';
                    errorDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = 'Prosseguir para Pagamento';
                }
            });
        });
    </script>
</body>
</html>
