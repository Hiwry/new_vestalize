<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\PersonalizationPrice;
use App\Models\SublimationLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoiceQuoteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $products = Product::where('active', true)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->select('id', 'title', 'price', 'wholesale_price', 'wholesale_min_qty', 'available_sizes', 'cut_type_id')
            ->orderBy('title')
            ->get();

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

        $knownSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL',
                        '1', '2', '4', '6', '8', '10', '12', '14', '16',
                        'RN', 'P BABY', 'M BABY', 'G BABY'];

        return view('quotes.voice', compact(
            'products',
            'cutTypes',
            'personalizationTypes',
            'locations',
            'personalizationSizes',
            'knownSizes'
        ));
    }

    public function match(Request $request)
    {
        $request->validate(['text' => 'required|string|max:500']);
        $text = $request->input('text');
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $normalized = $this->normalize($text);
        $tokens = preg_split('/\s+/', $normalized);

        $result = [
            'product' => null,
            'sizes' => [],
            'personalizations' => [],
            'quantity' => null,
            'raw_text' => $text,
        ];

        // 1. Match Product
        $products = Product::where('active', true)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();

        $bestProduct = null;
        $bestScore = 0;

        foreach ($products as $product) {
            $score = $this->fuzzyScore($normalized, $this->normalize($product->title));
            if ($score > $bestScore && $score >= 0.4) {
                $bestScore = $score;
                $bestProduct = $product;
            }
        }

        if ($bestProduct) {
            $result['product'] = [
                'id' => $bestProduct->id,
                'title' => $bestProduct->title,
                'price' => (float) $bestProduct->price,
                'wholesale_price' => $bestProduct->wholesale_price ? (float) $bestProduct->wholesale_price : null,
                'available_sizes' => $bestProduct->available_sizes,
            ];
        }

        // 2. Match Sizes
        $knownSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL',
                        '1', '2', '4', '6', '8', '10', '12', '14', '16'];
        $sizeAliases = [
            'pequeno' => 'P', 'medio' => 'M', 'grande' => 'G',
            'extra grande' => 'EXG', 'extra' => 'EXG',
            'infantil' => null,
        ];

        foreach ($tokens as $token) {
            $upper = strtoupper($token);
            if (in_array($upper, $knownSizes)) {
                $result['sizes'][$upper] = 1;
            }
        }

        foreach ($sizeAliases as $alias => $size) {
            if ($size && str_contains($normalized, $alias)) {
                $result['sizes'][$size] = 1;
            }
        }

        // 3. Match Quantity (numbers not matched as sizes)
        foreach ($tokens as $i => $token) {
            if (is_numeric($token) && !isset($result['sizes'][strtoupper($token)])) {
                $num = (int) $token;
                if ($num >= 1 && $num <= 9999) {
                    // Check if next token hints at quantity
                    $nextToken = $tokens[$i + 1] ?? '';
                    if (in_array($nextToken, ['unidade', 'unidades', 'peca', 'pecas', 'peça', 'peças', 'un'])) {
                        $result['quantity'] = $num;
                    } elseif (!$result['quantity'] && $num > 16) {
                        // Standalone number > 16 likely a quantity
                        $result['quantity'] = $num;
                    }
                }
            }
        }

        // 4. Match Personalization Types
        $personalizationTypes = ProductOption::where('type', 'personalizacao')
            ->where('active', true)
            ->get();

        $matchedTypes = [];
        foreach ($personalizationTypes as $pType) {
            $pNorm = $this->normalize($pType->name);
            if (str_contains($normalized, $pNorm) || $this->fuzzyScore($normalized, $pNorm) >= 0.6) {
                $matchedTypes[] = $pType;
            }
        }

        // 5. Match Personalization Sizes (A4, A3, 10x15, etc.)
        $pSizes = PersonalizationPrice::where('active', true)
            ->select('size_name', 'size_dimensions')
            ->distinct()
            ->get();

        $matchedPSizes = [];
        foreach ($pSizes as $ps) {
            $psNorm = $this->normalize($ps->size_name);
            foreach ($tokens as $token) {
                if ($token === $psNorm || str_contains($token, $psNorm)) {
                    $matchedPSizes[] = $ps;
                    break;
                }
            }
        }

        // 6. Match Locations
        $locations = SublimationLocation::where('active', true)->get();
        $matchedLocations = [];

        foreach ($locations as $loc) {
            $locNorm = $this->normalize($loc->name);
            if (str_contains($normalized, $locNorm)) {
                $matchedLocations[] = $loc;
            }
        }

        // 7. Build Personalization Entries
        // Pair each location with best matching type+size, or create individual entries
        if (!empty($matchedLocations)) {
            $defaultType = $matchedTypes[0] ?? null;
            $defaultSize = $matchedPSizes[0] ?? null;

            foreach ($matchedLocations as $loc) {
                $entry = [
                    'location_id' => $loc->id,
                    'location_name' => $loc->name,
                    'type_id' => $defaultType?->id,
                    'type_name' => $defaultType?->name,
                    'size_name' => $defaultSize?->size_name ?? null,
                    'size_dimensions' => $defaultSize?->size_dimensions ?? null,
                    'unit_price' => 0,
                ];

                // Try to get price
                if ($defaultType && $defaultSize) {
                    $qty = $result['quantity'] ?? max(1, array_sum($result['sizes'] ?: [1]));
                    $priceRecord = PersonalizationPrice::getPriceForPersonalization(
                        strtoupper($defaultType->name),
                        $defaultSize->size_name,
                        $qty
                    );
                    if ($priceRecord) {
                        $entry['unit_price'] = (float) $priceRecord->price;
                    }
                }

                $result['personalizations'][] = $entry;
            }
        } elseif (!empty($matchedTypes)) {
            // Types but no locations - still add entries
            foreach ($matchedTypes as $mType) {
                $entry = [
                    'location_id' => null,
                    'location_name' => null,
                    'type_id' => $mType->id,
                    'type_name' => $mType->name,
                    'size_name' => $matchedPSizes[0]->size_name ?? null,
                    'size_dimensions' => $matchedPSizes[0]->size_dimensions ?? null,
                    'unit_price' => 0,
                ];

                if (!empty($matchedPSizes)) {
                    $qty = $result['quantity'] ?? max(1, array_sum($result['sizes'] ?: [1]));
                    $priceRecord = PersonalizationPrice::getPriceForPersonalization(
                        strtoupper($mType->name),
                        $matchedPSizes[0]->size_name,
                        $qty
                    );
                    if ($priceRecord) {
                        $entry['unit_price'] = (float) $priceRecord->price;
                    }
                }

                $result['personalizations'][] = $entry;
            }
        }

        return response()->json(['success' => true, 'data' => $result]);
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
        if (empty($needle)) return 0;

        // Exact substring match
        if (str_contains($haystack, $needle)) return 1.0;

        // Token-based matching
        $needleTokens = explode(' ', $needle);
        $matchedTokens = 0;

        foreach ($needleTokens as $nt) {
            if (strlen($nt) < 2) continue;
            if (str_contains($haystack, $nt)) {
                $matchedTokens++;
            }
        }

        $totalTokens = count(array_filter($needleTokens, fn($t) => strlen($t) >= 2));
        if ($totalTokens === 0) return 0;

        return $matchedTokens / $totalTokens;
    }
}
