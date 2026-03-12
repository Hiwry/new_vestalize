<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiVoiceQuoteService
{
    public function isEnabled(): bool
    {
        return (bool) config('services.gemini.voice_quote_enabled')
            && filled(config('services.gemini.api_key'));
    }

    public function extractQuote(array $catalogContext, ?string $text = null, ?UploadedFile $audio = null): ?array
    {
        if (!$this->isEnabled() || (blank($text) && !$audio)) {
            return null;
        }

        $response = Http::baseUrl(rtrim((string) config('services.gemini.endpoint'), '/'))
            ->withHeaders([
                'x-goog-api-key' => (string) config('services.gemini.api_key'),
            ])
            ->acceptJson()
            ->timeout(60)
            ->retry(1, 300)
            ->post('/models/' . config('services.gemini.model') . ':generateContent', $this->buildPayload(
                catalogContext: $catalogContext,
                text: $text,
                audio: $audio,
            ));

        if (!$response->successful()) {
            Log::warning('Gemini voice quote request failed.', [
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);

            return null;
        }

        $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (!is_string($rawText) || trim($rawText) === '') {
            Log::warning('Gemini voice quote returned an empty payload.', [
                'response' => $response->json(),
            ]);

            return null;
        }

        $decoded = $this->decodeJsonPayload($rawText);

        if (!is_array($decoded)) {
            Log::warning('Gemini voice quote returned invalid JSON.', [
                'payload' => $rawText,
            ]);

            return null;
        }

        return $decoded;
    }

    private function buildPayload(array $catalogContext, ?string $text = null, ?UploadedFile $audio = null): array
    {
        $parts = [];

        if ($audio) {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $this->normalizeMimeType((string) $audio->getMimeType()),
                    'data' => base64_encode((string) file_get_contents($audio->getRealPath())),
                ],
            ];
        }

        $parts[] = [
            'text' => $this->buildUserPrompt($catalogContext, $text, $audio !== null),
        ];

        return [
            'systemInstruction' => [
                'parts' => [[
                    'text' => $this->buildSystemInstruction(),
                ]],
            ],
            'contents' => [[
                'role' => 'user',
                'parts' => $parts,
            ]],
            'generationConfig' => [
                'temperature' => 0.1,
                'topP' => 0.9,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
                'responseJsonSchema' => $this->responseSchema(),
            ],
        ];
    }

    private function buildSystemInstruction(): string
    {
        return implode("\n", [
            'Você extrai dados de orçamento para produtos personalizados.',
            'Analise o áudio e/ou o texto do usuário e devolva apenas JSON válido.',
            'Use somente IDs, nomes e tamanhos existentes no catálogo enviado.',
            'Nunca invente produto, tipo de personalização, local ou tamanho.',
            'Quando houver ambiguidade, deixe o campo nulo e marque needs_review como true.',
            'Se houver um único tamanho citado junto com uma quantidade total, atribua essa quantidade a esse tamanho.',
            'Quando houver grade como "20 P 30 M", devolva sizes com as quantidades exatas por tamanho.',
            'Para personalizações que cobram por cor, devolva color_count, color_details e has_neon quando a fala indicar isso.',
            'Se o áudio e a transcrição preliminar divergirem, priorize o conteúdo do áudio.',
        ]);
    }

    private function buildUserPrompt(array $catalogContext, ?string $text = null, bool $hasAudio = false): string
    {
        $instructions = [
            'Extraia um orçamento de áudio/texto para o sistema Vestalize.',
            'Retorne transcript com a fala/transcrição final em pt-BR.',
            'Retorne quantity com a quantidade total de peças quando estiver clara.',
            'Retorne sizes como uma lista de objetos {size, quantity}.',
            'Retorne personalizations como uma lista de objetos com type_id, type_name, size_name, location_id, location_name, color_count, color_details, has_neon e notes.',
            'Se o usuário disser algo como "3 cores", associe color_count à personalização correta.',
            'Se um campo não estiver claro, use null.',
        ];

        if ($hasAudio) {
            $instructions[] = 'Há um áudio anexado para ser interpretado.';
        }

        if (filled($text)) {
            $instructions[] = 'Também há uma transcrição preliminar do navegador: "' . trim($text) . '"';
        }

        $instructions[] = 'Catálogo disponível em JSON:';
        $instructions[] = json_encode($catalogContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return implode("\n\n", $instructions);
    }

    private function responseSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'transcript' => ['type' => ['string', 'null']],
                'product_id' => ['type' => ['integer', 'null']],
                'product_title' => ['type' => ['string', 'null']],
                'quantity' => ['type' => ['integer', 'null']],
                'sizes' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'size' => ['type' => 'string'],
                            'quantity' => ['type' => 'integer'],
                        ],
                        'required' => ['size', 'quantity'],
                    ],
                ],
                'personalizations' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'type_id' => ['type' => ['integer', 'null']],
                            'type_name' => ['type' => ['string', 'null']],
                            'size_name' => ['type' => ['string', 'null']],
                            'location_id' => ['type' => ['integer', 'null']],
                            'location_name' => ['type' => ['string', 'null']],
                            'color_count' => ['type' => ['integer', 'null']],
                            'color_details' => ['type' => ['string', 'null']],
                            'has_neon' => ['type' => 'boolean'],
                            'notes' => ['type' => ['string', 'null']],
                        ],
                        'required' => ['type_id', 'type_name', 'size_name', 'location_id', 'location_name', 'color_count', 'color_details', 'has_neon', 'notes'],
                    ],
                ],
                'confidence' => ['type' => ['number', 'null']],
                'needs_review' => ['type' => 'boolean'],
                'summary' => ['type' => ['string', 'null']],
            ],
            'required' => [
                'transcript',
                'product_id',
                'product_title',
                'quantity',
                'sizes',
                'personalizations',
                'confidence',
                'needs_review',
                'summary',
            ],
        ];
    }

    private function decodeJsonPayload(string $payload): ?array
    {
        $payload = trim($payload);

        if (str_starts_with($payload, '```')) {
            $payload = preg_replace('/^```(?:json)?\s*/', '', $payload) ?? $payload;
            $payload = preg_replace('/\s*```$/', '', $payload) ?? $payload;
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function normalizeMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'audio/x-wav', 'audio/wave' => 'audio/wav',
            'audio/x-flac' => 'audio/flac',
            'audio/mp3' => 'audio/mpeg',
            default => $mimeType,
        };
    }
}
