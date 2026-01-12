@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Checkout - ' . $plan->name) }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Order Summary -->
                <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resumo do Pedido</h3>
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-300">
                        <span>Assinatura Plano {{ $plan->name }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">R$ {{ number_format($plan->price, 2, ',', '.') }} / mês</span>
                    </div>
                </div>

                <!-- Stripe Elements Placeholder -->
                <form id="payment-form">
                    <div id="payment-element">
                        <!-- Stripe.js will inject the Payment Element here -->
                    </div>
                    
                    <div id="error-message" class="mt-4 text-red-600 dark:text-red-400 text-sm hidden">
                        <!-- Display error message to your customers here -->
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('subscription.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            Voltar
                        </a>
                        <button id="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <span id="button-text">Pagar R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                            <div class="hidden ml-3" id="spinner">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const form = document.getElementById('payment-form');
        const submitBtn = document.getElementById('submit');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');
        const errorMessage = document.getElementById('error-message');

        let stripe;
        let elements;

        try {
            // Fetch the PaymentIntent client secret
            const response = await fetch("{{ route('subscription.create-intent', $plan) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
            });

            if (!response.ok) {
                throw new Error(`Erro na API: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            if (!data.clientSecret || !data.publishableKey) {
                throw new Error('Resposta inválida da API: clientSecret ou publishableKey ausente');
            }

            // Initialize Stripe
            stripe = Stripe(data.publishableKey);

            const options = {
                clientSecret: data.clientSecret,
                appearance: {
                    theme: document.documentElement.classList.contains('dark') ? 'night' : 'stripe',
                },
            };

            // Set up Stripe.js and Elements to use in checkout form
            elements = stripe.elements(options);
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

        } catch (error) {
            console.error('Erro ao carregar checkout:', error);
            showMessage('Erro ao carregar checkout: ' + error.message);
            submitBtn.disabled = true;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!stripe || !elements) {
                showMessage('Formulário de pagamento não está pronto. Por favor, recarregue a página.');
                return;
            }

            setLoading(true);

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    // Retorna para rota que valida o PaymentIntent e aplica o plano
                    return_url: "{{ route('subscription.return') }}",
                },
            });

            if (error) {
                // This point will only be reached if there is an immediate error when
                // confirming the payment. Show error to your customer (e.g., payment
                // details incomplete)
                showMessage(error.message);
                setLoading(false);
            } else {
                // Your customer will be redirected to your `return_url`. For some payment
                // methods like iDEAL, your customer will be redirected to an intermediate
                // site first to authorize the payment, then redirected to the `return_url`.
            }
        });

        function showMessage(messageText) {
            errorMessage.classList.remove('hidden');
            errorMessage.textContent = messageText;
            setTimeout(() => {
                errorMessage.classList.add('hidden');
                errorMessage.textContent = '';
            }, 8000);
        }

        function setLoading(isLoading) {
            if (isLoading) {
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.classList.add('hidden');
            } else {
                submitBtn.disabled = false;
                spinner.classList.add('hidden');
                buttonText.classList.remove('hidden');
            }
        }
    });
</script>
@endpush
@endsection
