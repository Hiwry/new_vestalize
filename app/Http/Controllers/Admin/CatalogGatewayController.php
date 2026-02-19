<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogGatewaySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogGatewayController extends Controller
{
    public function edit(): View
    {
        $tenantId = (int) Auth::user()->tenant_id;
        abort_if($tenantId <= 0, 403, 'Usuário sem tenant associado.');

        $settings = CatalogGatewaySettingsService::forTenant($tenantId);
        $credentials = CatalogGatewaySettingsService::resolveMercadoPagoCredentialsForTenant($tenantId);

        $providers = [
            CatalogGatewaySettingsService::PROVIDER_MERCADO_PAGO => 'Mercado Pago (PIX)',
            CatalogGatewaySettingsService::PROVIDER_MANUAL => 'Manual (sem integração automática)',
        ];

        $mercadoPagoConfigured = !empty($credentials['access_token']);
        $credentialsSource = $credentials['source'] ?? 'global';
        $hasTenantAccessToken = !empty($settings['has_mercadopago_access_token']);
        $hasTenantPublicKey = !empty($settings['has_mercadopago_public_key']);

        $webhookUrl = route('catalog.payment.webhook');

        return view('admin.catalog.gateway', compact(
            'settings',
            'providers',
            'mercadoPagoConfigured',
            'webhookUrl',
            'credentialsSource',
            'hasTenantAccessToken',
            'hasTenantPublicKey'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $tenantId = (int) Auth::user()->tenant_id;
        abort_if($tenantId <= 0, 403, 'Usuário sem tenant associado.');

        $validated = $request->validate([
            'provider' => 'required|in:mercadopago,manual',
            'require_paid_before_order' => 'nullable|boolean',
            'mark_failed_payments' => 'nullable|boolean',
            'mercadopago_access_token' => 'nullable|string|max:600',
            'mercadopago_public_key' => 'nullable|string|max:300',
            'clear_mercadopago_access_token' => 'nullable|boolean',
            'clear_mercadopago_public_key' => 'nullable|boolean',
        ]);

        $accessToken = trim((string) ($validated['mercadopago_access_token'] ?? ''));
        $publicKey = trim((string) ($validated['mercadopago_public_key'] ?? ''));
        $clearAccessToken = $request->boolean('clear_mercadopago_access_token');
        $clearPublicKey = $request->boolean('clear_mercadopago_public_key');

        CatalogGatewaySettingsService::updateForTenant($tenantId, [
            'provider' => $validated['provider'],
            'require_paid_before_order' => $request->boolean('require_paid_before_order'),
            'mark_failed_payments' => $request->boolean('mark_failed_payments'),
            'mercadopago_access_token' => $clearAccessToken ? null : $accessToken,
            'mercadopago_public_key' => $clearPublicKey ? null : $publicKey,
            'update_mercadopago_access_token' => $accessToken !== '' || $clearAccessToken,
            'update_mercadopago_public_key' => $publicKey !== '' || $clearPublicKey,
        ]);

        return redirect()
            ->route('admin.catalog-gateway.edit')
            ->with('success', 'Configuração do gateway de pagamento atualizada com sucesso.');
    }
}
