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
     * Usa Mercado Pago como primário, PixService como fallback
     */
    public function generatePixPayment(Plan $plan, Request $request)
    {
        try {
            $client = new PaymentClient();
            $user = auth()->user();

            $amount = (float) $plan->price;
            $couponCode = strtoupper($request->input('coupon_code'));
            $discountAmount = 0;

            if ($couponCode === 'VESTASTART' && $plan->id == 3) {
                $amount = 79.90;
                $discountAmount = 20.00;
            }

            $payment = $client->create([
                'transaction_amount' => $amount,
                'description' => "Assinatura Plano {$plan->name} - Vestalize" . ($couponCode ? " (Cupom: $couponCode)" : ""),
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
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
                    'env' => config('app.env')
                ],
            ]);

            $transactionData = $payment->point_of_interaction->transaction_data;

            return response()->json([
                'qr_code' => $transactionData->qr_code,
                'qr_code_base64' => $transactionData->qr_code_base64,
                'ticket_url' => $transactionData->ticket_url,
                'id' => $payment->id,
                'source' => 'mercadopago'
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            // Mercado Pago falhou - usar nosso PixService como fallback
            Log::warning('MercadoPago PIX API Error - usando fallback PixService', [
                'message' => $e->getMessage(),
                'plan_id' => $plan->id
            ]);
            
            return $this->generatePixFallback($plan, $request);
            
        } catch (\Exception $e) {
            Log::error('MercadoPago PIX General Error: ' . $e->getMessage());
            
            // Tentar fallback
            return $this->generatePixFallback($plan, $request);
        }
    }
    
    /**
     * Gera PIX usando nosso serviço interno (fallback)
     */
    private function generatePixFallback(Plan $plan, Request $request)
    {
        try {
            $pixService = app(\App\Services\PixService::class);
            $txId = 'PLANO' . $plan->id . time();
            
            $amount = (float) $plan->price;
            $couponCode = strtoupper($request->input('coupon_code'));

            if ($couponCode === 'VESTASTART' && $plan->id == 3) {
                $amount = 79.90;
            }

            $pix = $pixService->generate($amount, $txId);
            
            // Extrair base64 sem o prefixo data:image/png;base64,
            $qrCodeBase64 = str_replace('data:image/png;base64,', '', $pix['qrcode']);
            
            return response()->json([
                'qr_code' => $pix['payload'],
                'qr_code_base64' => $qrCodeBase64,
                'ticket_url' => '#',
                'id' => $txId,
                'source' => 'pixservice',
                'pix_key' => $pix['pix_key'],
                'merchant_name' => $pix['merchant_name'],
                'note' => 'PIX gerado via chave alternativa. Após o pagamento, envie o comprovante para ativar seu plano.'
            ]);
        } catch (\Exception $e) {
            Log::error('PixService fallback failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Não foi possível gerar o PIX. Entre em contato com o suporte.',
            ], 500);
        }
    }

    public function createPreference(Plan $plan, Request $request)
    {
        try {
            $client = new PreferenceClient();

            $user = auth()->user();
            $fullName = trim($user->name ?: 'Cliente Silva');
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Silva';

            $amount = (float) $plan->price;
            $couponCode = strtoupper($request->input('coupon_code'));
            $discountAmount = 0;

            if ($couponCode === 'VESTASTART' && $plan->id == 3) {
                $amount = 79.90;
                $discountAmount = 20.00;
            }

            $preference = $client->create([
                'items' => [
                    [
                        'id' => $plan->id,
                        'title' => "Assinatura Plano {$plan->name}",
                        'description' => "Pagamento mensal - {$plan->name}" . ($couponCode ? " (Cupom: $couponCode)" : ""),
                        'quantity' => 1,
                        'unit_price' => $amount,
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
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
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
                                if (isset($metadata['coupon_code'])) {
                                    $tenant->applied_coupon = $metadata['coupon_code'];
                                }
                                $tenant->save();

                                // Registrar pagamento
                                SubscriptionPayment::create([
                                    'tenant_id' => $tenant->id,
                                    'plan_id' => $plan->id,
                                    'amount' => $payment->transaction_amount,
                                    'coupon_code' => $metadata['coupon_code'] ?? null,
                                    'discount_amount' => $metadata['discount_amount'] ?? 0,
                                    'payment_method' => $payment->payment_type_id,
                                    'payment_intent_id' => $payment->id, // payment_id -> payment_intent_id matches model
                                    'status' => 'succeeded', // approved -> succeeded to match system status
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
