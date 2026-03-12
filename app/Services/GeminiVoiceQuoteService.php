<?php

namespace App\Services;

use RuntimeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiVoiceQuoteService
{
    private const DIRECT_AUDIO_MIME_TYPES = [
        'audio/wav',
        'audio/mp3',
        'audio/mpeg',
        'audio/aiff',
        'audio/aac',
        'audio/ogg',
        'audio/flac',
    ];

    private const CONVERTIBLE_AUDIO_MIME_TYPES = [
        'audio/mp4',
        'audio/m4a',
        'audio/x-m4a',
        'audio/mp4a-latm',
        'audio/webm',
        'video/webm',
        'video/mp4',
        'audio/3gpp',
        'video/3gpp',
    ];

    private const CONVERTIBLE_AUDIO_EXTENSIONS = [
        'm4a',
        'mp4',
        'webm',
        '3gp',
        '3gpp',
    ];

    private const DIRECT_AUDIO_EXTENSION_MAP = [
        'wav' => 'audio/wav',
        'mp3' => 'audio/mp3',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'aif' => 'audio/aiff',
        'aiff' => 'audio/aiff',
    ];

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

        $audioPart = $audio ? $this->prepareAudioPart($audio) : null;

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
                audioPart: $audioPart,
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

    private function buildPayload(array $catalogContext, ?string $text = null, ?array $audioPart = null): array
    {
        $parts = [];

        if ($audioPart) {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $audioPart['mimeType'],
                    'data' => $audioPart['data'],
                ],
            ];
        }

        $parts[] = [
            'text' => $this->buildUserPrompt($catalogContext, $text, $audioPart !== null),
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

    private function prepareAudioPart(UploadedFile $audio): array
    {
        $mimeType = $this->normalizeMimeType((string) $audio->getMimeType());
        $clientMimeType = $this->normalizeMimeType((string) $audio->getClientMimeType());
        $extension = strtolower((string) $audio->getClientOriginalExtension());

        Log::info('Gemini voice quote received audio upload.', [
            'original_name' => $audio->getClientOriginalName(),
            'size_bytes' => $audio->getSize(),
            'detected_mime_type' => $mimeType,
            'client_mime_type' => $clientMimeType,
            'extension' => $extension,
        ]);

        if (in_array($mimeType, self::DIRECT_AUDIO_MIME_TYPES, true)) {
            return [
                'mimeType' => $mimeType,
                'data' => base64_encode((string) file_get_contents($audio->getRealPath())),
            ];
        }

        if (isset(self::DIRECT_AUDIO_EXTENSION_MAP[$extension])) {
            return [
                'mimeType' => self::DIRECT_AUDIO_EXTENSION_MAP[$extension],
                'data' => base64_encode((string) file_get_contents($audio->getRealPath())),
            ];
        }

        if (in_array($mimeType, self::CONVERTIBLE_AUDIO_MIME_TYPES, true)
            || in_array($clientMimeType, self::CONVERTIBLE_AUDIO_MIME_TYPES, true)
            || in_array($extension, self::CONVERTIBLE_AUDIO_EXTENSIONS, true)
        ) {
            return $this->convertAudioToWav($audio, $mimeType, $clientMimeType, $extension);
        }

        throw new RuntimeException(
            'Formato de audio nao suportado pelo servidor. Formato recebido: '
            . ($mimeType ?: 'desconhecido')
            . '. Envie MP3, WAV, AAC, OGG, FLAC ou use M4A/MP4/WEBM/3GP com FFmpeg habilitado no servidor.'
        );
    }

    private function normalizeMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'audio/x-wav', 'audio/wave' => 'audio/wav',
            'audio/x-flac' => 'audio/flac',
            'audio/mp3', 'audio/mpeg' => 'audio/mp3',
            'audio/x-m4a', 'audio/m4a', 'audio/mp4a-latm' => 'audio/mp4',
            'audio/3gp' => 'audio/3gpp',
            'video/3gp' => 'video/3gpp',
            default => $mimeType,
        };
    }

    private function convertAudioToWav(UploadedFile $audio, string $mimeType, string $clientMimeType, string $extension): array
    {
        if (!$this->canUseExec()) {
            throw new RuntimeException(
                'O servidor recebeu um audio em formato movel (' . ($mimeType ?: 'desconhecido')
                . '), mas nao pode converter porque a funcao exec esta indisponivel.'
            );
        }

        $ffmpegBinary = (string) config('services.gemini.ffmpeg_binary', 'ffmpeg');
        $tempBase = tempnam(sys_get_temp_dir(), 'gemini-audio-');

        if ($tempBase === false) {
            throw new RuntimeException('Nao foi possivel preparar um arquivo temporario para converter o audio.');
        }

        $outputPath = $tempBase . '.wav';
        @unlink($tempBase);

        $command = sprintf(
            '%s -y -i %s -vn -ac 1 -ar 16000 -c:a pcm_s16le %s 2>&1',
            escapeshellarg($ffmpegBinary),
            escapeshellarg((string) $audio->getRealPath()),
            escapeshellarg($outputPath)
        );

        exec($command, $outputLines, $exitCode);

        if ($exitCode !== 0 || !is_file($outputPath)) {
            @unlink($outputPath);

            Log::warning('Gemini voice quote audio conversion failed.', [
                'original_name' => $audio->getClientOriginalName(),
                'detected_mime_type' => $mimeType,
                'client_mime_type' => $clientMimeType,
                'extension' => $extension,
                'ffmpeg_binary' => $ffmpegBinary,
                'exit_code' => $exitCode,
                'ffmpeg_output' => implode("\n", $outputLines),
            ]);

            throw new RuntimeException(
                'O servidor recebeu o audio em formato ' . ($mimeType ?: 'desconhecido')
                . ', mas nao conseguiu converter para um formato aceito pela Gemini. '
                . 'Verifique se o FFmpeg esta instalado e acessivel no servidor.'
            );
        }

        try {
            return [
                'mimeType' => 'audio/wav',
                'data' => base64_encode((string) file_get_contents($outputPath)),
            ];
        } finally {
            @unlink($outputPath);
        }
    }

    private function canUseExec(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $disabled = array_filter(array_map('trim', explode(',', (string) ini_get('disable_functions'))));

        return !in_array('exec', $disabled, true);
    }
}
