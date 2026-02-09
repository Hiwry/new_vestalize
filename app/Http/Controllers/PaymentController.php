<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\SubscriptionPayment;

class PaymentController extends Controller
{
    /**
     * Show Stripe checkout page
     */
    public function checkout(Plan $plan)
    {
        return view('subscription.checkout', compact('plan'));
    }

    /**
     * Create Stripe Payment Intent
     */
    public function createIntent(Plan $plan, Request $request)
    {
        try {
            $stripeSecret = config('services.stripe.secret');
            
            if (empty($stripeSecret)) {
                return response()->json([
                    'error' => 'Stripe não está configurado. Por favor, configure as chaves do Stripe no arquivo .env'
                ], 500);
            }
            
            Stripe::setApiKey($stripeSecret);

            $method = $request->input('method');
            $allowedMethods = ['pix', 'card'];
            if ($method && !in_array($method, $allowedMethods, true)) {
                return response()->json([
                    'error' => 'Método de pagamento inválido.'
                ], 422);
            }

            $amount = (float) $plan->price;
            $couponCode = strtoupper((string) $request->input('coupon_code', ''));
            $discountAmount = 0;

            if ($couponCode === 'VESTASTART' && $plan->slug === 'start') {
                $amount = 79.90;
                $discountAmount = 20.00;
            }

            // Amount in cents
            $amount = (int) round($amount * 100);

            $paymentIntentData = [
                'amount' => $amount,
                'currency' => 'brl',
                'description' => "Assinatura Plano {$plan->name}" . ($couponCode ? " (Cupom: $couponCode)" : ""),
                'metadata' => [
                    'tenant_id' => auth()->user()->tenant_id,
                    'plan_id' => $plan->id,
                    'plan_slug' => $plan->slug,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discountAmount,
                ],
                // Cartão ou PIX
                'payment_method_types' => $method ? [$method] : ['card', 'pix'],
            ];

            $paymentIntent = PaymentIntent::create($paymentIntentData);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'publishableKey' => config('services.stripe.key'),
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro na API do Stripe: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Stripe Intent Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao criar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Stripe Webhooks
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Webhook Error: Invalid payload');
            return response('Invalid payload', 400);
        } catch(SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Webhook Error: Invalid signature');
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSucceeded($paymentIntent);
                break;
            default:
                // Unexpected event type
                // Log::info('Received unknown event type ' . $event->type);
        }

        return response('Success', 200);
    }

    protected function handlePaymentSucceeded($paymentIntent)
    {
        $metadata = $paymentIntent->metadata;
        if (!isset($metadata->tenant_id) || !isset($metadata->plan_id)) {
            Log::error('Webhook Error: Metadata missing tenant_id or plan_id', ['metadata' => $metadata]);
            return;
        }

        $tenant = \App\Models\Tenant::find($metadata->tenant_id);
        if ($tenant) {
            // Update tenant plan
            $tenant->update([
                'plan_id' => $metadata->plan_id,
                'status' => 'active',
                'subscription_ends_at' => now()->addMonth(), // Assuming monthly subscription
                'stripe_id' => $paymentIntent->customer ?? null, // Can be refined to store customer ID properly
            ]);

            // Log payment for audit (super admin history)
            $subscriptionPayment = null;
            try {
                $subscriptionPayment = SubscriptionPayment::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $metadata->plan_id,
                    'amount' => $paymentIntent->amount_received / 100,
                    'currency' => $paymentIntent->currency,
                    'payment_intent_id' => $paymentIntent->id,
                    'payment_method' => $paymentIntent->payment_method_types[0] ?? null,
                    'status' => $paymentIntent->status,
                    'paid_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('SubscriptionPayment log failed: ' . $e->getMessage());
            }

            // Registrar comissão de afiliado se o tenant tiver um afiliado vinculado
            if ($tenant->affiliate_id && $subscriptionPayment) {
                try {
                    $affiliate = \App\Models\Affiliate::find($tenant->affiliate_id);
                    if ($affiliate && $affiliate->isActive()) {
                        $affiliate->registerCommission($tenant, $subscriptionPayment);
                        Log::info("Comissão registrada para afiliado {$affiliate->name} - Tenant: {$tenant->name}");
                    }
                } catch (\Exception $e) {
                    Log::error('Afiliado commission failed: ' . $e->getMessage());
                }
            }

            Log::info("Tenant {$tenant->name} upgraded to plan ID {$metadata->plan_id}");
        } else {
            Log::error("Tenant not found for ID {$metadata->tenant_id}");
        }
    }

    /**
     * Handle return_url after Stripe redirect (local/dev fallback without webhook tunneling)
     */
    public function handleReturn(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        $redirectStatus = $request->query('redirect_status');

        if (!$paymentIntentId) {
            return redirect()->route('subscription.index')->with('error', 'Pagamento não encontrado.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent && $paymentIntent->status === 'succeeded') {
                // Aplicar atualização de plano usando a mesma lógica do webhook
                $this->handlePaymentSucceeded($paymentIntent);
                return redirect()->route('subscription.index')->with('success', 'Pagamento aprovado e plano atualizado!');
            }

            // Outros status (requires_payment_method, requires_action, etc.)
            return redirect()->route('subscription.index')->with('error', 'Pagamento não foi confirmado. Status: ' . ($paymentIntent->status ?? $redirectStatus ?? 'desconhecido'));
        } catch (\Exception $e) {
            Log::error('Stripe Return Error: ' . $e->getMessage());
            return redirect()->route('subscription.index')->with('error', 'Erro ao validar pagamento: ' . $e->getMessage());
        }
    }
}
