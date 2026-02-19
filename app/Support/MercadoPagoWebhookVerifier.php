<?php

namespace App\Support;

use Illuminate\Http\Request;

class MercadoPagoWebhookVerifier
{
    public static function isValid(Request $request, ?string $secret, bool $enforceSignature = true): bool
    {
        $secret = is_string($secret) ? trim($secret) : '';

        if ($secret === '') {
            return $enforceSignature === false;
        }

        $signatureHeader = (string) $request->header('x-signature', '');
        $requestId = (string) $request->header('x-request-id', '');

        if ($signatureHeader === '' || $requestId === '') {
            return false;
        }

        $signatureMap = self::parseSignatureHeader($signatureHeader);
        $timestamp = $signatureMap['ts'] ?? '';
        $hashV1 = strtolower((string) ($signatureMap['v1'] ?? ''));

        if ($timestamp === '' || $hashV1 === '') {
            return false;
        }

        $dataId = self::extractDataId($request);
        if ($dataId === null || $dataId === '') {
            return false;
        }

        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$timestamp};";
        $expected = strtolower(hash_hmac('sha256', $manifest, $secret));

        return hash_equals($expected, $hashV1);
    }

    public static function extractDataId(Request $request): ?string
    {
        $candidates = [
            $request->input('data.id'),
            $request->query('data.id'),
            $request->input('resource.id'),
            $request->query('resource.id'),
            $request->input('id'),
            $request->query('id'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== null && trim((string) $candidate) !== '') {
                return trim((string) $candidate);
            }
        }

        return null;
    }

    /**
     * Header format example: ts=1700000000,v1=abc123...
     */
    private static function parseSignatureHeader(string $header): array
    {
        $result = [];

        foreach (explode(',', $header) as $fragment) {
            $parts = explode('=', trim($fragment), 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = strtolower(trim($parts[0]));
            $value = trim($parts[1]);

            if ($key !== '' && $value !== '') {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
