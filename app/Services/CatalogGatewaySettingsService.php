<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class CatalogGatewaySettingsService
{
    public const PROVIDER_MERCADO_PAGO = 'mercadopago';
    public const PROVIDER_MANUAL = 'manual';

    public static function defaults(): array
    {
        return [
            'provider' => self::PROVIDER_MERCADO_PAGO,
            'require_paid_before_order' => true,
            'mark_failed_payments' => true,
        ];
    }

    public static function forTenant(int $tenantId): array
    {
        $defaults = self::defaults();
        $provider = self::getString($tenantId, 'provider', $defaults['provider']);
        $mercadoPagoAccessToken = self::getSecret($tenantId, 'mercadopago_access_token');
        $mercadoPagoPublicKey = self::getSecret($tenantId, 'mercadopago_public_key');

        if (!in_array($provider, [self::PROVIDER_MERCADO_PAGO, self::PROVIDER_MANUAL], true)) {
            $provider = $defaults['provider'];
        }

        return [
            'provider' => $provider,
            'require_paid_before_order' => self::getBool($tenantId, 'require_paid_before_order', $defaults['require_paid_before_order']),
            'mark_failed_payments' => self::getBool($tenantId, 'mark_failed_payments', $defaults['mark_failed_payments']),
            'mercadopago_access_token' => $mercadoPagoAccessToken,
            'mercadopago_public_key' => $mercadoPagoPublicKey,
            'has_mercadopago_access_token' => !empty($mercadoPagoAccessToken),
            'has_mercadopago_public_key' => !empty($mercadoPagoPublicKey),
        ];
    }

    public static function updateForTenant(int $tenantId, array $settings): void
    {
        $provider = $settings['provider'] ?? self::PROVIDER_MERCADO_PAGO;
        $requirePaid = (bool) ($settings['require_paid_before_order'] ?? true);
        $markFailed = (bool) ($settings['mark_failed_payments'] ?? true);

        self::set($tenantId, 'provider', $provider, 'string');
        self::set($tenantId, 'require_paid_before_order', $requirePaid ? '1' : '0', 'boolean');
        self::set($tenantId, 'mark_failed_payments', $markFailed ? '1' : '0', 'boolean');

        if (($settings['update_mercadopago_access_token'] ?? false) === true) {
            self::setSecret($tenantId, 'mercadopago_access_token', $settings['mercadopago_access_token'] ?? null);
        }

        if (($settings['update_mercadopago_public_key'] ?? false) === true) {
            self::setSecret($tenantId, 'mercadopago_public_key', $settings['mercadopago_public_key'] ?? null);
        }
    }

    public static function resolveMercadoPagoCredentialsForTenant(int $tenantId): array
    {
        $tenantSettings = self::forTenant($tenantId);
        $tenantAccessToken = $tenantSettings['mercadopago_access_token'] ?? null;
        $tenantPublicKey = $tenantSettings['mercadopago_public_key'] ?? null;

        if (!empty($tenantAccessToken)) {
            return [
                'access_token' => $tenantAccessToken,
                'public_key' => $tenantPublicKey,
                'source' => 'tenant',
            ];
        }

        return [
            'access_token' => config('services.mercadopago.access_token'),
            'public_key' => config('services.mercadopago.public_key'),
            'source' => 'global',
        ];
    }

    private static function getString(int $tenantId, string $name, string $default): string
    {
        $value = Setting::where('key', self::key($tenantId, $name))->value('value');

        return is_string($value) && $value !== '' ? $value : $default;
    }

    private static function getBool(int $tenantId, string $name, bool $default): bool
    {
        $value = Setting::where('key', self::key($tenantId, $name))->value('value');

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    private static function set(int $tenantId, string $name, string $value, string $type): void
    {
        Setting::updateOrCreate(
            ['key' => self::key($tenantId, $name)],
            ['value' => $value, 'type' => $type]
        );
    }

    private static function getSecret(int $tenantId, string $name): ?string
    {
        $value = Setting::where('key', self::key($tenantId, $name))->value('value');
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Backward compatibility with non-encrypted values.
            return $value;
        }
    }

    private static function setSecret(int $tenantId, string $name, ?string $value): void
    {
        if (!is_string($value) || trim($value) === '') {
            Setting::where('key', self::key($tenantId, $name))->delete();
            return;
        }

        Setting::updateOrCreate(
            ['key' => self::key($tenantId, $name)],
            ['value' => Crypt::encryptString(trim($value)), 'type' => 'secret']
        );
    }

    private static function key(int $tenantId, string $name): string
    {
        return "catalog_gateway.tenant_{$tenantId}.{$name}";
    }
}
