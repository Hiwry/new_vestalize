@php
    $p = Auth::user()->tenant->primary_color ?? '#667eea';
    $s = Auth::user()->tenant->secondary_color ?? '#764ba2';
    
    // Luminance check
    $isLight = false;
    if (str_starts_with($p, '#') && strlen($p) >= 7) {
        $r = hexdec(substr($p, 1, 2));
        $g = hexdec(substr($p, 3, 2));
        $b = hexdec(substr($p, 5, 2));
        if ((0.2126 * $r + 0.7152 * $g + 0.0722 * $b) > 200) $isLight = true;
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escolha o Método de Pagamento | {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --brand-primary: {{ $p }};
            --brand-secondary: {{ $s }};
            --brand-primary-text: {{ $isLight ? '#4f46e5' : $p }};
            --brand-primary-content: {{ $isLight ? '#111827' : '#ffffff' }};
        }
        
        body { background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%); }
        
        /* Overrides */
        .text-indigo-600, .text-blue-600 { color: var(--brand-primary-text) !important; }
        .bg-indigo-600, .bg-blue-600 { background-color: var(--brand-primary) !important; color: var(--brand-primary-content) !important; }
        .focus\:ring-indigo-500:focus { --tw-ring-color: var(--brand-primary) !important; }
        
        /* Secondary overrides */
        .text-purple-600 { color: var(--brand-secondary) !important; }
        .bg-purple-600 { background-color: var(--brand-secondary) !important; }
        .from-indigo-600 { --tw-gradient-from: var(--brand-primary) !important; } 
        .to-purple-600 { --tw-gradient-to: var(--brand-secondary) !important; }

        .payment-option {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .payment-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .payment-option.selected {
            border: 3px solid var(--brand-primary);
            background: #eef2ff;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Vestalize</h1>
                <p class="text-indigo-200">Escolha como você quer pagar</p>
            </div>
            
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Plan Summary -->
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Resumo da Assinatura</h2>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $plan->name }}</span>
                        <span class="text-2xl font-bold text-indigo-600">R$ {{ number_format($plan->price, 2, ',', '.') }}/mês</span>
                    </div>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Selecione o método de pagamento:</h3>
                
                <!-- Payment Methods Grid -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- PIX -->
                    <div class="payment-option bg-white border-2 border-gray-200 rounded-xl p-6 text-center" data-method="pix">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-2">PIX</h4>
                        <p class="text-sm text-gray-600 mb-3">Aprovação instantânea</p>
                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Recomendado</span>
                    </div>
                    
                    <!-- Cartão -->
                    <div class="payment-option bg-white border-2 border-gray-200 rounded-xl p-6 text-center" data-method="card">
                        <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-2">Cartão de Crédito</h4>
                        <p class="text-sm text-gray-600 mb-3">Aprovação imediata</p>
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">Stripe</span>
                    </div>
                    
                    <!-- Boleto -->
                    <div class="payment-option bg-white border-2 border-gray-200 rounded-xl p-6 text-center" data-method="boleto">
                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-2">Boleto Bancário</h4>
                        <p class="text-sm text-gray-600 mb-3">Compensação em até 2 dias</p>
                        <span class="inline-block bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">Stripe</span>
                    </div>
                </div>
                
                <!-- Continue Button -->
                <div class="flex flex-col space-y-4">
                    <button 
                        id="continue-btn"
                        disabled
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-4 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition-all duration-150 hover:scale-105 text-lg"
                    >
                        Selecione um método de pagamento
                    </button>
                    
                    <a 
                        href="{{ route('dashboard') }}"
                        class="text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                    >
                        ← Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const options = document.querySelectorAll('.payment-option');
        const continueBtn = document.getElementById('continue-btn');
        let selectedMethod = null;

        options.forEach(option => {
            option.addEventListener('click', () => {
                // Remove selected from all
                options.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected to clicked
                option.classList.add('selected');
                selectedMethod = option.dataset.method;
                
                // Enable button
                continueBtn.disabled = false;
                continueBtn.textContent = `Continuar com ${getMethodName(selectedMethod)}`;
            });
        });

        continueBtn.addEventListener('click', () => {
            if (!selectedMethod) return;
            
            // Redirecionar para o gateway apropriado
            if (selectedMethod === 'pix') {
                window.location.href = '{{ route('mercadopago.checkout', $plan) }}';
            } else {
                // Cartão e Boleto vão para Stripe
                window.location.href = '{{ route('subscription.checkout', $plan) }}';
            }
        });

        function getMethodName(method) {
            const names = {
                'pix': 'PIX',
                'card': 'Cartão de Crédito',
                'boleto': 'Boleto'
            };
            return names[method] || method;
        }
    </script>
</body>
</html>
