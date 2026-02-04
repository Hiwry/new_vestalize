<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class BudgetObservationHelper
{
    public const DEFAULT_OPTIONS = [
        'Acréscimo de R$ 2,00 para GG e R$ 35,00 para EXG',
        'Arte cobrada à parte',
        'Pagamento: 50% entrada + 50% retirada',
        'Prazo sujeito à confirmação',
    ];

    public static function getOptions(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?? (Auth::check() ? Auth::user()->tenant_id : null);

        $keys = [];
        if ($tenantId !== null) {
            $keys[] = self::getSettingKey($tenantId);
        }
        $keys[] = self::getSettingKey();

        foreach ($keys as $key) {
            $setting = Setting::where('key', $key)->first();
            $options = self::decodeOptions($setting?->value);
            if (!empty($options)) {
                return $options;
            }
        }

        return self::DEFAULT_OPTIONS;
    }

    public static function saveOptions(array $options, ?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? (Auth::check() ? Auth::user()->tenant_id : null);
        $normalized = self::normalizeOptions($options);

        Setting::updateOrCreate(
            ['key' => self::getSettingKey($tenantId)],
            [
                'value' => json_encode($normalized, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
            ]
        );
    }

    public static function parseOptionsFromText(?string $text): array
    {
        if ($text === null) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);
        return self::normalizeOptions($lines ?: []);
    }

    public static function normalizeOptions(array $options): array
    {
        $normalized = [];
        foreach ($options as $option) {
            $option = trim((string) $option);
            if ($option === '') {
                continue;
            }
            $normalized[] = $option;
        }

        return array_values(array_unique($normalized));
    }

    public static function getSettingKey(?int $tenantId = null): string
    {
        if ($tenantId !== null) {
            return 'budget_observation_options_tenant_' . $tenantId;
        }

        return 'budget_observation_options';
    }

    private static function decodeOptions(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return self::normalizeOptions($decoded);
    }
}
