<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // Configurar o SDK do Mercado Pago
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Exibir página de checkout
     */
    public function checkout(Plan $plan)
    {
        return view('subscription.mercadopago-checkout', compact('plan'));
    }

    /**
     * Criar preferência de pagamento
     */
    /**
     * Gerar pagamento imediato via PIX (QR Code e Copia e Cola)
     */
    public function generatePixPayment(Plan $plan)
    {
        try {
            $client = new PaymentClient();
            $user = auth()->user();

            $payment = $client->create([
                'transaction_amount' => (float) $plan->price,
                'description' => "Assinatura Plano {$plan->name} - Vestalize",
                'payment_method_id' => 'pix',
                'installments' => 1,
                'payer' => [
                    'email' => $user->email,
                    'first_name' => explode(' ', $user->name)[0] ?? 'Cliente',
                    'last_name' => explode(' ', $user->name)[1] ?? 'Teste',
                    'identification' => [
                        'type' => 'CPF',
                        'number' => '19100000000' // Test CPF
                    ]
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'plan_id' => $plan->id,
                    'env' => config('app.env')
                ],
            ]);

            $transactionData = $payment->point_of_interaction->transaction_data;

            return response()->json([
                'qr_code' => $transactionData->qr_code,
                'qr_code_base64' => $transactionData->qr_code_base64,
                'ticket_url' => $transactionData->ticket_url,
                'id' => $payment->id
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $content = $apiResponse ? (method_exists($apiResponse, 'getContent') ? $apiResponse->getContent() : (string)$apiResponse) : 'N/A';
            Log::error('MercadoPago PIX API Error:', [
                'message' => $e->getMessage(),
                'status_code' => $e->getStatusCode(),
                'content' => $content
            ]);
            return response()->json([
                'error' => 'Erro na API do Mercado Pago: ' . ($content['message'] ?? $e->getMessage()),
                'details' => $content
            ], 422);
        } catch (\Exception $e) {
            Log::error('MercadoPago PIX General Error: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno ao gerar PIX: ' . $e->getMessage()], 500);
        }
    }

    public function createPreference(Plan $plan)
    {
        try {
            $client = new PreferenceClient();

            $user = auth()->user();
            $fullName = trim($user->name ?: 'Cliente Silva');
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Silva';

            $preference = $client->create([
                'items' => [
                    [
                        'id' => $plan->id,
                        'title' => "Assinatura Plano {$plan->name}",
                        'description' => "Pagamento mensal - {$plan->name}",
                        'quantity' => 1,
                        'unit_price' => (float) $plan->price,
                        'currency_id' => 'BRL',
                    ]
                ],
                'payer' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                ],
                'payment_methods' => [
                    'included_payment_methods' => [
                        ['id' => 'pix'],
                        ['id' => 'bolbradesco'],
                        ['id' => 'master'],
                        ['id' => 'visa'],
                        ['id' => 'amex'],
                        ['id' => 'elo'],
                        ['id' => 'hipercard'],
                    ],
                ],
                'back_urls' => [
                    'success' => route('mercadopago.success'),
                    'failure' => route('mercadopago.failure'),
                    'pending' => route('mercadopago.pending'),
                ],
                // 'auto_return' => 'approved',
                'notification_url' => route('mercadopago.webhook'),
                'metadata' => [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'plan_id' => $plan->id,
                    'plan_slug' => $plan->slug,
                ],
            ]);

            return response()->json([
                'id' => $preference->id,
                'init_point' => $preference->init_point,  // Para desktop
                'sandbox_init_point' => $preference->sandbox_init_point,  // Para sandbox
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $content = $apiResponse ? (method_exists($apiResponse, 'getContent') ? $apiResponse->getContent() : (string)$apiResponse) : 'N/A';
            
            Log::error('MercadoPago API Error Details:', [
                'message' => $e->getMessage(),
                'status_code' => $e->getStatusCode(),
                'content' => $content
            ]);

            // Return a 422 if it's a validation error from MP, otherwise 500
            return response()->json([
                'error' => 'Erro na API do Mercado Pago: ' . ($content['message'] ?? $e->getMessage()),
                'details' => $content
            ], $e->getStatusCode() == 400 ? 422 : 500);
        } catch (\Exception $e) {
            Log::error('MercadoPago Preference Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erro interno ao criar preferência: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para receber notificações de pagamento
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('MercadoPago Webhook recebido:', $request->all());

            // Mercado Pago envia o tipo de notificação
            $type = $request->input('type');

            if ($type === 'payment') {
                $paymentId = $request->input('data.id');

                if ($paymentId) {
                    $client = new PaymentClient();
                    $payment = $client->get($paymentId);

                    Log::info('Payment details:', (array) $payment);

                    // Processar pagamento aprovado
                    if ($payment->status === 'approved') {
                        $metadata = $payment->metadata;
                        
                        $tenantId = $metadata['tenant_id'] ?? null;
                        $planId = $metadata['plan_id'] ?? null;

                        if ($tenantId && $planId) {
                            $tenant = Tenant::find($tenantId);
                            $plan = Plan::find($planId);

                            if ($tenant && $plan) {
                                // Atualizar plano do tenant
                                $tenant->plan_id = $plan->id;
                                $tenant->plan_expires_at = now()->addMonth();
                                $tenant->save();

                                // Registrar pagamento
                                SubscriptionPayment::create([
                                    'tenant_id' => $tenant->id,
                                    'plan_id' => $plan->id,
                                    'amount' => $payment->transaction_amount,
                                    'payment_method' => $payment->payment_type_id,
                                    'payment_id' => $payment->id,
                                    'status' => 'approved',
                                    'paid_at' => now(),
                                ]);

                                Log::info("Assinatura atualizada para tenant {$tenant->id}");
                            }
                        }
                    }
                }
            }

            return response()->json(['status' => 'received'], 200);

        } catch (\Exception $e) {
            Log::error('MercadoPago Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Página de sucesso
     */
    public function success(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $status = $request->input('status');

        return view('subscription.mercadopago-success', compact('paymentId', 'status'));
    }

    /**
     * Página de pagamento pendente
     */
    public function pending(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        return view('subscription.mercadopago-pending', compact('paymentId'));
    }

    /**
     * Página de falha
     */
    public function failure(Request $request)
    {
        return view('subscription.mercadopago-failure');
    }
}
