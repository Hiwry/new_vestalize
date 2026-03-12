<?php

namespace App\Http\Controllers;

use App\Models\PersonalizationPrice;
use App\Models\PersonalizationSetting;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\SublimationLocation;
use App\Services\GeminiVoiceQuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoiceQuoteController extends Controller
{
    private const KNOWN_SIZES = [
        'PP',
        'P',
        'M',
        'G',
        'GG',
        'EXG',
        'G1',
        'G2',
        'G3',
        'ESPECIAL',
        '1',
        '2',
        '4',
        '6',
        '8',
        '10',
        '12',
        '14',
        '16',
        'RN',
        'P BABY',
        'M BABY',
        'G BABY',
    ];

    private const STANDARD_SIZE_GROUPS = [
        [
            'key' => 'adulto',
            'label' => 'Adulto / Plus',
            'sizes' => ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'],
        ],
        [
            'key' => 'infantil',
            'label' => 'Infantil',
            'sizes' => ['1', '2', '4', '6', '8', '10', '12', '14', '16'],
        ],
        [
            'key' => 'baby',
            'label' => 'Baby',
            'sizes' => ['RN', 'P BABY', 'M BABY', 'G BABY'],
        ],
    ];

    public function index()
    {
        $resources = $this->loadVoiceResources(Auth::user()?->tenant_id);

        return view('quotes.voice', [
            'products' => $resources['products'],
            'cutTypes' => $resources['cutTypes'],
            'personalizationTypes' => $resources['personalizationTypes'],
            'personalizationSettings' => $resources['personalizationSettings'],
            'locations' => $resources['locations'],
            'personalizationSizes' => $resources['personalizationSizes'],
            'knownSizes' => self::KNOWN_SIZES,
            'sizeTable' => self::STANDARD_SIZE_GROUPS,
        ]);
    }

    public function match(Request $request, GeminiVoiceQuoteService $gemini)
    {
        $request->validate(
            [
                'text' => 'nullable|string|max:4000|required_without:audio',
                'audio' => 'nullable|file|max:10240|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/wave,audio/aac,audio/ogg,audio/flac,audio/x-flac|required_without:text',
            ],
            [
                'text.required_without' => 'Informe um texto ou envie um áudio.',
                'audio.required_without' => 'Envie um áudio ou informe um texto.',
                'audio.mimetypes' => 'Use um áudio MP3, WAV, AAC, OGG ou FLAC.',
                'audio.max' => 'O áudio deve ter no máximo 10MB.',
            ]
        );

        $text = trim((string) $request->input('text', ''));
        $audio = $request->file('audio');
        $resources = $this->loadVoiceResources(Auth::user()?->tenant_id);

        $heuristicResult = filled($text)
            ? $this->buildHeuristicMatch($text, $resources)
            : $this->emptyMatchResult();

        $provider = 'fallback';
        $result = $heuristicResult;

        $aiPayload = $gemini->extractQuote(
            catalogContext: $this->buildGeminiContext($resources),
            text: filled($text) ? $text : null,
            audio: $audio,
        );

        if (is_array($aiPayload)) {
            $provider = 'gemini';
            $aiResult = $this->mapGeminiPayload($aiPayload, $resources, $text);
            $result = $this->mergeMatchResults($heuristicResult, $aiResult);
        }

        $result = $this->finalizeMatchResult($result, $resources['products']);

        return response()->json([
            'success' => true,
            'data' => $result,
            'meta' => [
                'provider' => $provider,
                'used_ai' => $provider === 'gemini',
            ],
        ]);
    }

    private function loadVoiceResources(?int $tenantId): array
    {
        $products = Product::where('active', true)
            ->when($tenantId, fn($query) => $query->where('tenant_id', $tenantId))
            ->select('id', 'title', 'price', 'wholesale_price', 'wholesale_min_qty', 'available_sizes', 'cut_type_id')
            ->orderBy('title')
            ->get()
            ->map(function (Product $product) {
                $product->available_sizes = $this->normalizeAvailableSizes($product->available_sizes);

                return $product;
            });

        $cutTypes = ProductOption::where('type', 'tipo_corte')
            ->where('active', true)
            ->orderBy('order')
            ->get(['id', 'name']);

        $personalizationTypes = ProductOption::where('type', 'personalizacao')
            ->where('active', true)
            ->orderBy('order')
            ->get(['id', 'name']);

        $locations = SublimationLocation::where('active', true)
            ->orderBy('order')
            ->get(['id', 'name']);

        $personalizationSizes = PersonalizationPrice::where('active', true)
            ->select('size_name', 'size_dimensions')
            ->distinct()
            ->get();

        $personalizationSettings = PersonalizationSetting::query()
            ->where('active', true)
            ->get(['personalization_type', 'charge_by_color', 'color_price_per_unit', 'min_colors', 'max_colors'])
            ->mapWithKeys(function (PersonalizationSetting $setting) {
                $type = mb_strtoupper(trim((string) $setting->personalization_type));

                return [
                    $type => [
                        'personalization_type' => $type,
                        'charge_by_color' => (bool) $setting->charge_by_color,
                        'color_price_per_unit' => (float) ($setting->color_price_per_unit ?? 0),
                        'min_colors' => (int) ($setting->min_colors ?? 1),
                        'max_colors' => $setting->max_colors !== null ? (int) $setting->max_colors : null,
                    ],
                ];
            })
            ->all();

        return compact('products', 'cutTypes', 'personalizationTypes', 'personalizationSettings', 'locations', 'personalizationSizes');
    }

    private function buildHeuristicMatch(string $text, array $resources): array
    {
        $normalized = $this->normalize($text);
        $normalizedForParsing = $this->replaceNumberWords($normalized);
        $tokens = preg_split('/\s+/', $normalizedForParsing, -1, PREG_SPLIT_NO_EMPTY);
        $result = $this->emptyMatchResult();
        $result['raw_text'] = $text;

        $bestProduct = null;
        $bestScore = 0.0;

        foreach ($resources['products'] as $product) {
            $score = $this->fuzzyScore($normalized, $this->normalize($product->title));

            if ($score > $bestScore && $score >= 0.4) {
                $bestScore = $score;
                $bestProduct = $product;
            }
        }

        if ($bestProduct) {
            $result['product'] = $this->formatProductForResponse($bestProduct);
        }

        foreach ($this->extractSizeQuantities($tokens) as $size => $quantity) {
            $result['sizes'][$size] = ($result['sizes'][$size] ?? 0) + $quantity;
        }

        foreach ($this->extractStandaloneSizes($tokens) as $size) {
            if (!isset($result['sizes'][$size])) {
                $result['sizes'][$size] = max(1, (int) ($result['sizes'][$size] ?? 0));
            }
        }

        foreach ($tokens as $index => $token) {
            if (!is_numeric($token) || isset($result['sizes'][strtoupper($token)])) {
                continue;
            }

            $number = (int) $token;

            if ($number < 1 || $number > 9999) {
                continue;
            }

            $nextToken = $tokens[$index + 1] ?? '';

            if (in_array($nextToken, ['unidade', 'unidades', 'peca', 'pecas', 'peça', 'peças', 'un'], true)) {
                $result['quantity'] = $number;
                continue;
            }

            if (!$result['quantity'] && $number > 16) {
                $result['quantity'] = $number;
            }
        }

        if (!$result['quantity'] && !empty($result['sizes'])) {
            $result['quantity'] = array_sum($result['sizes']);
        }

        if (count($result['sizes']) === 1 && $result['quantity'] && array_sum($result['sizes']) < $result['quantity']) {
            $result['sizes'][array_key_first($result['sizes'])] = $result['quantity'];
        }

        $detectedColorCount = $this->extractColorCount($normalizedForParsing);
        $detectedNeon = str_contains($normalizedForParsing, 'neon');

        $matchedTypes = [];

        foreach ($resources['personalizationTypes'] as $personalizationType) {
            $normalizedType = $this->normalize($personalizationType->name);

            if (str_contains($normalizedForParsing, $normalizedType) || $this->fuzzyScore($normalizedForParsing, $normalizedType) >= 0.6) {
                $matchedTypes[] = $personalizationType;
            }
        }

        $matchedPersonalizationSizes = [];

        foreach ($resources['personalizationSizes'] as $personalizationSize) {
            $normalizedSize = $this->normalize($personalizationSize->size_name);
            $normalizedDimensions = $this->normalize((string) ($personalizationSize->size_dimensions ?? ''));

            foreach ($tokens as $token) {
                if ($token === $normalizedSize
                    || str_contains($token, $normalizedSize)
                    || ($normalizedDimensions !== '' && ($token === $normalizedDimensions || str_contains($token, $normalizedDimensions)))
                ) {
                    $matchedPersonalizationSizes[] = $personalizationSize;
                    break;
                }
            }
        }

        $matchedLocations = [];

        foreach ($resources['locations'] as $location) {
            $normalizedLocation = $this->normalize($location->name);

            if (str_contains($normalizedForParsing, $normalizedLocation)) {
                $matchedLocations[] = $location;
            }
        }

        if (!empty($matchedLocations)) {
            $defaultType = $matchedTypes[0] ?? null;
            $defaultSize = $matchedPersonalizationSizes[0] ?? null;

            foreach ($matchedLocations as $location) {
                $colorData = $this->buildPersonalizationColorData(
                    $defaultType?->name,
                    $detectedColorCount,
                    $detectedNeon,
                    null,
                    $resources['personalizationSettings'] ?? []
                );

                $result['personalizations'][] = [
                    'location_id' => $location->id,
                    'location_name' => $location->name,
                    'type_id' => $defaultType?->id,
                    'type_name' => $defaultType?->name,
                    'size_name' => $defaultSize?->size_name,
                    'size_dimensions' => $defaultSize?->size_dimensions,
                    'unit_price' => $this->resolvePersonalizationUnitPrice(
                        $defaultType?->name,
                        $defaultSize?->size_name,
                        $this->effectiveQuantity($result),
                        $colorData['color_count']
                    ),
                    'color_count' => $colorData['color_count'],
                    'color_details' => $colorData['color_details'],
                    'has_neon' => $colorData['has_neon'],
                ];
            }
        } elseif (!empty($matchedTypes)) {
            foreach ($matchedTypes as $matchedType) {
                $defaultSize = $matchedPersonalizationSizes[0] ?? null;
                $colorData = $this->buildPersonalizationColorData(
                    $matchedType->name,
                    $detectedColorCount,
                    $detectedNeon,
                    null,
                    $resources['personalizationSettings'] ?? []
                );

                $result['personalizations'][] = [
                    'location_id' => null,
                    'location_name' => null,
                    'type_id' => $matchedType->id,
                    'type_name' => $matchedType->name,
                    'size_name' => $defaultSize?->size_name,
                    'size_dimensions' => $defaultSize?->size_dimensions,
                    'unit_price' => $this->resolvePersonalizationUnitPrice(
                        $matchedType->name,
                        $defaultSize?->size_name,
                        $this->effectiveQuantity($result),
                        $colorData['color_count']
                    ),
                    'color_count' => $colorData['color_count'],
                    'color_details' => $colorData['color_details'],
                    'has_neon' => $colorData['has_neon'],
                ];
            }
        }

        return $result;
    }

    private function buildGeminiContext(array $resources): array
    {
        $cutTypeNames = $resources['cutTypes']->pluck('name', 'id');

        return [
            'known_sizes' => self::KNOWN_SIZES,
            'size_groups' => self::STANDARD_SIZE_GROUPS,
            'products' => $resources['products']->map(fn(Product $product) => [
                'id' => $product->id,
                'title' => $product->title,
                'cut_type_id' => $product->cut_type_id,
                'cut_type_name' => $cutTypeNames[$product->cut_type_id] ?? null,
                'available_sizes' => $this->normalizeAvailableSizes($product->available_sizes),
            ])->values()->all(),
            'personalization_types' => $resources['personalizationTypes']->map(fn($type) => [
                'id' => $type->id,
                'name' => $type->name,
            ])->values()->all(),
            'personalization_settings' => array_values($resources['personalizationSettings'] ?? []),
            'locations' => $resources['locations']->map(fn($location) => [
                'id' => $location->id,
                'name' => $location->name,
            ])->values()->all(),
            'personalization_sizes' => $resources['personalizationSizes']->map(fn($size) => [
                'size_name' => $size->size_name,
                'size_dimensions' => $size->size_dimensions,
            ])->values()->all(),
        ];
    }

    private function mapGeminiPayload(array $payload, array $resources, string $fallbackText = ''): array
    {
        $result = $this->emptyMatchResult();
        $result['raw_text'] = trim((string) ($payload['transcript'] ?? '')) ?: $fallbackText;
        $result['confidence'] = $this->normalizeFloat($payload['confidence'] ?? null);
        $result['needs_review'] = (bool) ($payload['needs_review'] ?? false);
        $result['summary'] = $this->normalizeNullableString($payload['summary'] ?? null);

        $product = $this->resolveProduct(
            id: $payload['product_id'] ?? null,
            title: $payload['product_title'] ?? null,
            products: $resources['products'],
        );

        if ($product) {
            $result['product'] = $this->formatProductForResponse($product);
        }

        $result['quantity'] = $this->normalizePositiveInteger($payload['quantity'] ?? null);
        $result['sizes'] = $this->normalizeSizeEntries(
            entries: is_array($payload['sizes'] ?? null) ? $payload['sizes'] : [],
            quantity: $result['quantity'],
            availableSizes: $product?->available_sizes ?? [],
        );

        $effectiveQuantity = $this->effectiveQuantity($result);

        foreach ((array) ($payload['personalizations'] ?? []) as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $type = $this->resolveOptionByIdOrName(
                id: $entry['type_id'] ?? null,
                name: $entry['type_name'] ?? null,
                options: $resources['personalizationTypes'],
            );

            $location = $this->resolveOptionByIdOrName(
                id: $entry['location_id'] ?? null,
                name: $entry['location_name'] ?? null,
                options: $resources['locations'],
            );

            $size = $this->resolvePersonalizationSize(
                name: $entry['size_name'] ?? null,
                sizes: $resources['personalizationSizes'],
            );

            if (!$type && !$location && !$size) {
                continue;
            }

            $colorData = $this->buildPersonalizationColorData(
                $type?->name,
                $entry['color_count'] ?? null,
                $entry['has_neon'] ?? false,
                $entry['color_details'] ?? null,
                $resources['personalizationSettings'] ?? []
            );

            $result['personalizations'][] = [
                'location_id' => $location?->id,
                'location_name' => $location?->name,
                'type_id' => $type?->id,
                'type_name' => $type?->name,
                'size_name' => $size?->size_name ?? $this->normalizeNullableString($entry['size_name'] ?? null),
                'size_dimensions' => $size?->size_dimensions,
                'color_count' => $colorData['color_count'],
                'color_details' => $colorData['color_details'],
                'has_neon' => $colorData['has_neon'],
                'unit_price' => $this->resolvePersonalizationUnitPrice(
                    $type?->name,
                    $size?->size_name ?? $this->normalizeNullableString($entry['size_name'] ?? null),
                    $effectiveQuantity,
                    $colorData['color_count']
                ),
                'notes' => $this->normalizeNullableString($entry['notes'] ?? null),
            ];
        }

        return $result;
    }

    private function mergeMatchResults(array $heuristicResult, array $aiResult): array
    {
        return [
            'product' => $aiResult['product'] ?? $heuristicResult['product'],
            'sizes' => !empty($aiResult['sizes']) ? $aiResult['sizes'] : $heuristicResult['sizes'],
            'personalizations' => !empty($aiResult['personalizations']) ? $aiResult['personalizations'] : $heuristicResult['personalizations'],
            'quantity' => $aiResult['quantity'] ?? $heuristicResult['quantity'],
            'raw_text' => $aiResult['raw_text'] ?: $heuristicResult['raw_text'],
            'confidence' => $aiResult['confidence'] ?? null,
            'needs_review' => $aiResult['needs_review'] ?? false,
            'summary' => $aiResult['summary'] ?? null,
        ];
    }

    private function finalizeMatchResult(array $result, Collection $products): array
    {
        $quantity = $this->effectiveQuantity($result);

        if (!empty($result['product']['id'])) {
            $product = $products->firstWhere('id', (int) $result['product']['id']);

            if ($product instanceof Product) {
                $result['product'] = $this->formatProductForResponse($product, $quantity);
            }
        }

        foreach ($result['personalizations'] as $index => $entry) {
            $result['personalizations'][$index]['unit_price'] = $this->resolvePersonalizationUnitPrice(
                $entry['type_name'] ?? null,
                $entry['size_name'] ?? null,
                $quantity,
                $entry['color_count'] ?? null
            );
        }

        return $result;
    }

    private function resolveProduct(mixed $id, mixed $title, Collection $products): ?Product
    {
        if (is_numeric($id)) {
            $product = $products->firstWhere('id', (int) $id);

            if ($product instanceof Product) {
                return $product;
            }
        }

        $normalizedTitle = $this->normalizeNullableString($title);

        if (!$normalizedTitle) {
            return null;
        }

        $normalizedNeedle = $this->normalize($normalizedTitle);
        $bestProduct = null;
        $bestScore = 0.0;

        foreach ($products as $product) {
            $normalizedProductTitle = $this->normalize($product->title);
            $score = max(
                $this->fuzzyScore($normalizedNeedle, $normalizedProductTitle),
                $this->fuzzyScore($normalizedProductTitle, $normalizedNeedle)
            );

            if ($score > $bestScore && $score >= 0.5) {
                $bestScore = $score;
                $bestProduct = $product;
            }
        }

        return $bestProduct instanceof Product ? $bestProduct : null;
    }

    private function resolveOptionByIdOrName(mixed $id, mixed $name, Collection $options): mixed
    {
        if (is_numeric($id)) {
            $option = $options->firstWhere('id', (int) $id);

            if ($option) {
                return $option;
            }
        }

        $normalizedName = $this->normalizeNullableString($name);

        if (!$normalizedName) {
            return null;
        }

        $needle = $this->normalize($normalizedName);
        $bestOption = null;
        $bestScore = 0.0;

        foreach ($options as $option) {
            $normalizedOptionName = $this->normalize((string) $option->name);
            $score = max(
                $this->fuzzyScore($needle, $normalizedOptionName),
                $this->fuzzyScore($normalizedOptionName, $needle)
            );

            if ($score > $bestScore && $score >= 0.5) {
                $bestScore = $score;
                $bestOption = $option;
            }
        }

        return $bestOption;
    }

    private function resolvePersonalizationSize(mixed $name, Collection $sizes): ?object
    {
        $normalizedName = $this->normalizeNullableString($name);

        if (!$normalizedName) {
            return null;
        }

        $needle = $this->normalize($normalizedName);

        foreach ($sizes as $size) {
            $normalizedSizeName = $this->normalize((string) $size->size_name);
            $normalizedDimensions = $this->normalize((string) ($size->size_dimensions ?? ''));

            if ($normalizedSizeName === $needle || ($normalizedDimensions !== '' && $normalizedDimensions === $needle)) {
                return $size;
            }
        }

        foreach ($sizes as $size) {
            $normalizedSizeName = $this->normalize((string) $size->size_name);
            $normalizedDimensions = $this->normalize((string) ($size->size_dimensions ?? ''));

            if (str_contains($needle, $normalizedSizeName)
                || str_contains($normalizedSizeName, $needle)
                || ($normalizedDimensions !== '' && str_contains($needle, $normalizedDimensions))
                || ($normalizedDimensions !== '' && str_contains($normalizedDimensions, $needle))
            ) {
                return $size;
            }
        }

        return null;
    }

    private function formatProductForResponse(Product $product, ?int $quantity = null): array
    {
        $effectiveQuantity = max(1, $quantity ?? 1);

        return [
            'id' => $product->id,
            'title' => $product->title,
            'price' => (float) $product->getPriceForQuantity($effectiveQuantity),
            'base_price' => (float) $product->price,
            'wholesale_price' => $product->wholesale_price ? (float) $product->wholesale_price : null,
            'wholesale_min_qty' => $product->wholesale_min_qty ? (int) $product->wholesale_min_qty : null,
            'available_sizes' => $this->normalizeAvailableSizes($product->available_sizes),
            'cut_type_id' => $product->cut_type_id,
        ];
    }

    private function resolvePersonalizationUnitPrice(?string $typeName, ?string $sizeName, int $quantity, ?int $colorCount = null): float
    {
        if (blank($typeName) || blank($sizeName)) {
            return 0.0;
        }

        $lookupQuantity = max(1, $quantity);
        $lookupType = mb_strtoupper(trim($typeName));
        $lookupSize = trim($sizeName);

        $priceRecord = PersonalizationPrice::getPriceForPersonalization($lookupType, $lookupSize, $lookupQuantity)
            ?? PersonalizationPrice::getLowestPriceRange($lookupType, $lookupSize);

        $basePrice = $priceRecord ? (float) $priceRecord->price : 0.0;

        return $basePrice + $this->resolveColorSurcharge($lookupType, $colorCount, $lookupQuantity);
    }

    private function normalizeSizeEntries(array $entries, ?int $quantity = null, array $availableSizes = []): array
    {
        $sizes = [];

        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $size = $this->normalizeSizeLabel($entry['size'] ?? null);
            $sizeQuantity = $this->normalizePositiveInteger($entry['quantity'] ?? null);

            if (!$size || !$sizeQuantity) {
                continue;
            }

            $sizes[$size] = ($sizes[$size] ?? 0) + $sizeQuantity;
        }

        if (count($sizes) === 1 && $quantity && array_sum($sizes) < $quantity) {
            $sizes[array_key_first($sizes)] = $quantity;
        }

        $normalizedAvailableSizes = $this->normalizeAvailableSizes($availableSizes);

        if (empty($sizes) && $quantity && count($normalizedAvailableSizes) === 1) {
            $onlySize = $this->normalizeSizeLabel($normalizedAvailableSizes[0] ?? null);

            if ($onlySize) {
                $sizes[$onlySize] = $quantity;
            }
        }

        return $sizes;
    }

    private function normalizeSizeLabel(mixed $value): ?string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }

        $normalized = $this->normalize((string) $value);

        if ($normalized === '') {
            return null;
        }

        if (isset($this->sizeAliases()[$normalized])) {
            return $this->sizeAliases()[$normalized];
        }

        $canonical = strtoupper($normalized);

        return in_array($canonical, self::KNOWN_SIZES, true) ? $canonical : null;
    }

    private function sizeAliases(): array
    {
        return [
            'pequeno' => 'P',
            'medio' => 'M',
            'media' => 'M',
            'grande' => 'G',
            'extra grande' => 'EXG',
            'extra' => 'EXG',
            'plus size' => 'G1',
            'gg' => 'GG',
            'xg' => 'EXG',
            'esp' => 'ESPECIAL',
            'espec' => 'ESPECIAL',
            'especial' => 'ESPECIAL',
            'p baby' => 'P BABY',
            'm baby' => 'M BABY',
            'g baby' => 'G BABY',
            'pbaby' => 'P BABY',
            'mbaby' => 'M BABY',
            'gbaby' => 'G BABY',
        ];
    }

    private function normalizeAvailableSizes(mixed $sizes): array
    {
        $normalizedSizes = [];

        foreach ((array) $sizes as $size) {
            $normalizedSize = $this->normalizeSizeLabel($size);

            if ($normalizedSize && !in_array($normalizedSize, $normalizedSizes, true)) {
                $normalizedSizes[] = $normalizedSize;
            }
        }

        return $normalizedSizes;
    }

    private function replaceNumberWords(string $text): string
    {
        $map = [
            'zero' => '0',
            'um' => '1',
            'uma' => '1',
            'dois' => '2',
            'duas' => '2',
            'tres' => '3',
            'quatro' => '4',
            'cinco' => '5',
            'seis' => '6',
            'sete' => '7',
            'oito' => '8',
            'nove' => '9',
            'dez' => '10',
            'onze' => '11',
            'doze' => '12',
            'treze' => '13',
            'catorze' => '14',
            'quatorze' => '14',
            'quinze' => '15',
            'dezesseis' => '16',
        ];

        return preg_replace_callback(
            '/\b(' . implode('|', array_map(static fn(string $word) => preg_quote($word, '/'), array_keys($map))) . ')\b/u',
            static fn(array $matches) => $map[$matches[1]] ?? $matches[0],
            $text
        ) ?? $text;
    }

    private function extractSizeQuantities(array $tokens): array
    {
        $sizes = [];
        $tokenCount = count($tokens);

        for ($index = 0; $index < $tokenCount; $index++) {
            $match = $this->matchSizeFromTokens($tokens, $index);

            if (!$match) {
                continue;
            }

            $quantity = null;
            $previousToken = $tokens[$index - 1] ?? null;
            $nextToken = $tokens[$index + $match['length']] ?? null;

            if ($this->isNumericTokenInRange($previousToken)) {
                $quantity = (int) $previousToken;
            } elseif ($this->isNumericTokenInRange($nextToken)) {
                $quantity = (int) $nextToken;
            }

            if ($quantity === null) {
                continue;
            }

            $sizes[$match['size']] = ($sizes[$match['size']] ?? 0) + $quantity;
            $index += $match['length'] - 1;
        }

        return $sizes;
    }

    private function extractStandaloneSizes(array $tokens): array
    {
        $sizes = [];
        $tokenCount = count($tokens);

        for ($index = 0; $index < $tokenCount; $index++) {
            $match = $this->matchSizeFromTokens($tokens, $index);

            if (!$match) {
                continue;
            }

            $previousToken = $tokens[$index - 1] ?? null;
            $nextToken = $tokens[$index + $match['length']] ?? null;

            if ($this->isNumericTokenInRange($previousToken) || $this->isNumericTokenInRange($nextToken)) {
                $index += $match['length'] - 1;
                continue;
            }

            if (ctype_digit($match['size']) && !in_array($previousToken, ['tam', 'tamanho', 'numero', 'n'], true)) {
                $index += $match['length'] - 1;
                continue;
            }

            $sizes[] = $match['size'];
            $index += $match['length'] - 1;
        }

        return array_values(array_unique($sizes));
    }

    private function matchSizeFromTokens(array $tokens, int $index): ?array
    {
        $remaining = count($tokens) - $index;

        for ($length = min(2, $remaining); $length >= 1; $length--) {
            $candidate = implode(' ', array_slice($tokens, $index, $length));
            $size = $this->normalizeSizeLabel($candidate);

            if ($size) {
                return [
                    'size' => $size,
                    'length' => $length,
                ];
            }
        }

        return null;
    }

    private function isNumericTokenInRange(mixed $value, int $min = 1, int $max = 9999): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $number = (int) $value;

        return $number >= $min && $number <= $max;
    }

    private function extractColorCount(string $text): ?int
    {
        if (preg_match('/\b(\d+)\s+cor(?:es)?\b/u', $text, $matches)) {
            return $this->normalizeColorCount($matches[1] ?? null);
        }

        if (preg_match('/\bcor(?:es)?\s+(\d+)\b/u', $text, $matches)) {
            return $this->normalizeColorCount($matches[1] ?? null);
        }

        return null;
    }

    private function buildPersonalizationColorData(
        ?string $typeName,
        mixed $colorCount,
        mixed $hasNeon = false,
        mixed $colorDetails = null,
        array $settings = []
    ): array {
        $supportsColorCount = $this->typeUsesColorCount($typeName, $settings);
        $normalizedColorCount = $this->normalizeColorCount($colorCount);

        return [
            'color_count' => $supportsColorCount ? ($normalizedColorCount ?? 1) : null,
            'color_details' => $supportsColorCount ? $this->normalizeNullableString(is_scalar($colorDetails) ? (string) $colorDetails : null) : null,
            'has_neon' => $supportsColorCount ? $this->normalizeBoolean($hasNeon) : false,
        ];
    }

    private function typeUsesColorCount(?string $typeName, array $settings = []): bool
    {
        if (blank($typeName)) {
            return false;
        }

        $lookupType = mb_strtoupper(trim((string) $typeName));

        if (isset($settings[$lookupType])) {
            return (bool) ($settings[$lookupType]['charge_by_color'] ?? false);
        }

        static $cache = [];

        if (array_key_exists($lookupType, $cache)) {
            return $cache[$lookupType];
        }

        $setting = PersonalizationSetting::findByType($lookupType);

        return $cache[$lookupType] = $setting
            ? (bool) $setting->charge_by_color
            : in_array($lookupType, ['SERIGRAFIA', 'EMBORRACHADO'], true);
    }

    private function resolveColorSurcharge(?string $typeName, ?int $colorCount, int $quantity): float
    {
        if (!$this->typeUsesColorCount($typeName) || !$colorCount || $colorCount <= 1) {
            return 0.0;
        }

        $lookupType = mb_strtoupper(trim((string) $typeName));
        $lookupQuantity = max(1, $quantity);
        $extraColors = max(0, $colorCount - 1);

        $colorPriceRecord = PersonalizationPrice::getPriceForPersonalization($lookupType, 'COR', $lookupQuantity)
            ?? PersonalizationPrice::getLowestPriceRange($lookupType, 'COR');

        if ($colorPriceRecord) {
            return (float) $colorPriceRecord->price * $extraColors;
        }

        $setting = PersonalizationSetting::findByType($lookupType);

        return $setting ? (float) $setting->calculateColorSurcharge($colorCount) : 0.0;
    }

    private function emptyMatchResult(): array
    {
        return [
            'product' => null,
            'sizes' => [],
            'personalizations' => [],
            'quantity' => null,
            'raw_text' => '',
            'confidence' => null,
            'needs_review' => false,
            'summary' => null,
        ];
    }

    private function effectiveQuantity(array $result): int
    {
        $sizeQuantity = array_sum(array_map(
            fn($value) => max(0, (int) $value),
            $result['sizes'] ?? []
        ));

        if ($sizeQuantity > 0) {
            return $sizeQuantity;
        }

        return max(1, (int) ($result['quantity'] ?? 0));
    }

    private function normalizePositiveInteger(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $number = (int) $value;

        return $number > 0 ? $number : null;
    }

    private function normalizeColorCount(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $number = (int) $value;

        return $number >= 1 ? $number : null;
    }

    private function normalizeBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            return in_array(mb_strtolower(trim($value)), ['1', 'true', 'sim', 'yes', 'on'], true);
        }

        return false;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private function normalizeFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = Str::ascii($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function fuzzyScore(string $haystack, string $needle): float
    {
        if ($needle === '') {
            return 0.0;
        }

        if (str_contains($haystack, $needle)) {
            return 1.0;
        }

        $needleTokens = explode(' ', $needle);
        $matchedTokens = 0;

        foreach ($needleTokens as $needleToken) {
            if (strlen($needleToken) < 2) {
                continue;
            }

            if (str_contains($haystack, $needleToken)) {
                $matchedTokens++;
            }
        }

        $totalTokens = count(array_filter($needleTokens, fn(string $token) => strlen($token) >= 2));

        return $totalTokens === 0 ? 0.0 : $matchedTokens / $totalTokens;
    }
}
