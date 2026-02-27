<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\MarketplaceCreditPackage;
use App\Models\Marketplace\MarketplaceCreditWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    /**
     * Página de compra de créditos
     */
    public function index()
    {
        $user    = Auth::user();
        $wallet  = MarketplaceCreditWallet::getOrCreate($user->id);
        $packages = MarketplaceCreditPackage::active()->get();

        // Verifica se o usuário é assinante ativo do Vestalize
        $isSubscriber = $this->isActiveSubscriber($user);

        return view('marketplace.credits.index', compact('wallet', 'packages', 'isSubscriber'));
    }

    /**
     * Inicia compra de créditos via MercadoPago PIX
     */
    public function purchase(Request $request, int $packageId)
    {
        $user    = Auth::user();
        $package = MarketplaceCreditPackage::active()->findOrFail($packageId);
        $isSubscriber = $this->isActiveSubscriber($user);

        // Aplica desconto se for assinante
        $price = $isSubscriber ? $package->subscriber_price : $package->price;

        // Cria preferência de pagamento via MercadoPago
        $mercadoPagoController = app(\App\Http\Controllers\MercadoPagoController::class);

        // Armazena dados da compra na sessão para recuperar no callback
        session([
            'marketplace_credit_purchase' => [
                'package_id'       => $package->id,
                'credits'          => $package->credits,
                'price'            => $price,
                'user_id'          => $user->id,
                'subscriber_discount' => $isSubscriber,
            ]
        ]);

        // Gera PIX via MercadoPago
        $mpService = app(\MercadoPago\Client\Payment\PaymentClient::class ?? null);

        // Fallback: usar o controller do MercadoPago existente
        return response()->json([
            'success'      => true,
            'package'      => $package->name,
            'credits'      => $package->credits,
            'price'        => number_format($price, 2, ',', '.'),
            'discount'     => $isSubscriber,
            'redirect_url' => route('marketplace.credits.pix', $package->id),
        ]);
    }

    /**
     * Gera PIX para compra de créditos
     */
    public function generatePix(Request $request, int $packageId)
    {
        $user    = Auth::user();
        $package = MarketplaceCreditPackage::active()->findOrFail($packageId);
        $isSubscriber = $this->isActiveSubscriber($user);
        $price = $isSubscriber ? $package->subscriber_price : $package->price;

        // Salva dados da sessão
        session([
            'marketplace_credit_purchase' => [
                'package_id'          => $package->id,
                'credits'             => $package->credits,
                'price'               => $price,
                'user_id'             => $user->id,
                'subscriber_discount' => $isSubscriber,
            ]
        ]);

        return view('marketplace.credits.checkout', compact('package', 'price', 'isSubscriber'));
    }

    /**
     * Callback do MercadoPago - confirma pagamento e credita carteira
     */
    public function handleReturn(Request $request)
    {
        $purchase = session('marketplace_credit_purchase');

        if (!$purchase || $request->status !== 'approved') {
            return redirect()->route('marketplace.credits.index')
                ->with('error', 'Pagamento não confirmado. Tente novamente.');
        }

        $this->creditWallet($purchase, $request->payment_id);
        session()->forget('marketplace_credit_purchase');

        return redirect()->route('marketplace.credits.index')
            ->with('success', "✅ {$purchase['credits']} créditos adicionados à sua carteira!");
    }

    /**
     * Webhook do MercadoPago para créditos do marketplace
     */
    public function webhook(Request $request)
    {
        Log::info('Marketplace Credits Webhook', $request->all());

        if ($request->type !== 'payment') {
            return response()->json(['ok' => true]);
        }

        // Aqui você integraria com o SDK do MercadoPago para verificar o pagamento
        // Por ora, apenas registra o log
        return response()->json(['ok' => true]);
    }

    /**
     * Adiciona créditos manualmente (admin)
     */
    public function addCreditsManually(int $userId, int $credits, string $description): void
    {
        $wallet = MarketplaceCreditWallet::getOrCreate($userId);
        $wallet->credit($credits, 'bonus', $description);
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function isActiveSubscriber($user): bool
    {
        if (!$user->tenant_id) return false;
        $user->loadMissing('tenant');
        return $user->tenant && $user->tenant->isActive();
    }

    private function creditWallet(array $purchase, ?string $paymentId = null): void
    {
        $wallet = MarketplaceCreditWallet::getOrCreate($purchase['user_id']);
        $wallet->credit($purchase['credits'], 'purchase', "Compra de {$purchase['credits']} créditos", [
            'payment_id'                  => $paymentId,
            'payment_method'              => 'pix',
            'payment_amount'              => $purchase['price'],
            'subscriber_discount_applied' => $purchase['subscriber_discount'] ?? false,
            'reference_type'              => 'credit_package',
            'reference_id'                => $purchase['package_id'],
        ]);
    }
}
