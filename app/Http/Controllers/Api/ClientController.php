<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PersonalizationPrice;
use App\Models\ProductOption;
use App\Services\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private readonly ImageProcessor $imageProcessor)
    {
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $clients = Client::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone_primary', 'LIKE', "%{$query}%")
            ->orWhere('cpf_cnpj', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($clients);
    }

    public function getProductOptions(): JsonResponse
    {
        $options = ProductOption::where('active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('type');

        return response()->json($options);
    }

    public function getProductOptionsWithParents(): JsonResponse
    {
        $mapOption = function($item) {
            $parentIds = $item->parents->pluck('id')->toArray();
            if ($item->parent_id) {
                $parentIds[] = $item->parent_id;
            }
            // Remove duplicates and re-index
            $parentIds = array_values(array_unique($parentIds));

            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'cost' => $item->cost,
                'parent_id' => $item->parent_id, // Keep for backward compatibility
                'parent_ids' => $parentIds,
            ];
        };

        $options = [
            'personalizacao' => ProductOption::where('type', 'personalizacao')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            }),
            'tecido' => ProductOption::with('parents')->where('type', 'tecido')->where('active', true)->orderBy('order')->get()->map($mapOption),
            'tipo_tecido' => ProductOption::with('parents')->where('type', 'tipo_tecido')->where('active', true)->orderBy('order')->get()->map($mapOption),
            'cor' => ProductOption::with('parents')->where('type', 'cor')->where('active', true)->orderBy('order')->get()->map($mapOption),
            'tipo_corte' => ProductOption::with('parents')->where('type', 'tipo_corte')->where('active', true)->orderBy('order')->get()->map($mapOption),
            'detalhe' => ProductOption::with('parents')->where('type', 'detalhe')->where('active', true)->orderBy('order')->get()->map($mapOption),
            'gola' => ProductOption::with('parents')->where('type', 'gola')->where('active', true)->orderBy('order')->get()->map($mapOption),
        ];

        return response()->json($options);
    }

    public function updateItemCoverImage(Request $request, $id): JsonResponse
    {
        try {
            \Log::info('=== UPLOAD IMAGEM DE CAPA ===');
            \Log::info('Item ID: ' . $id);
            \Log::info('Request data: ' . json_encode($request->all()));
            \Log::info('Files: ' . json_encode($request->files->all()));
            
            $request->validate([
                'cover_image' => 'required|image|max:10240', // Máximo 10MB
            ]);

            $item = \App\Models\OrderItem::findOrFail($id);
            \Log::info('Item encontrado: ' . json_encode($item->toArray()));
            
            // Processar e salvar a imagem
            $coverImage = $request->file('cover_image');
            $coverImagePath = $this->imageProcessor->processAndStore(
                $coverImage,
                'orders/items/covers',
                [
                    'max_width' => 1200,
                    'max_height' => 1200,
                    'quality' => 85,
                ]
            );
            
            if (!$coverImagePath) {
                throw new \RuntimeException('Falha ao processar imagem de capa.');
            }

            \Log::info('Imagem salva em: ' . $coverImagePath);
            
            // Remover imagem anterior se existir
            $this->imageProcessor->delete($item->cover_image);
            
            // Atualizar o item com a nova imagem de capa
            $item->update(['cover_image' => $coverImagePath]);
            
            \Log::info('Item atualizado com sucesso');
            
            // Recarregar o item para obter a URL da imagem
            $item->refresh();
            
            // Verificar se o arquivo existe no novo caminho (public/images)
            $fullPath = public_path('images/' . $coverImagePath);
            $fileExists = file_exists($fullPath);
            $fileSize = $fileExists ? filesize($fullPath) : 0;
            
            \Log::info('API: Resposta de upload de imagem', [
                'item_id' => $item->id,
                'order_id' => $item->order_id,
                'cover_image_path' => $coverImagePath,
                'cover_image_url' => $item->cover_image_url,
                'file_exists' => $fileExists,
                'file_size' => $fileSize,
                'full_path' => $fullPath,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem de capa atualizada com sucesso!',
                'cover_image_path' => $coverImagePath,
                'cover_image_url' => $item->cover_image_url,
                'order_id' => $item->order_id,
                'debug' => [
                    'file_exists' => $fileExists,
                    'file_size' => $fileSize,
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', $e->errors()['cover_image'] ?? ['Arquivo inválido'])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar imagem de capa do item: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateArtName(Request $request, $id): JsonResponse
    {
        try {
            \Log::info('=== ATUALIZAR NOME DA ARTE ===');
            \Log::info('Item ID: ' . $id);
            \Log::info('Request data: ' . json_encode($request->all()));
            
            $request->validate([
                'art_name' => 'required|string|max:255',
            ]);

            $item = \App\Models\OrderItem::findOrFail($id);
            \Log::info('Item encontrado: ID ' . $item->id);
            
            // Atualizar o nome da arte
            $item->update(['art_name' => $request->art_name]);
            
            \Log::info('Nome da arte atualizado com sucesso: ' . $request->art_name);
            
            return response()->json([
                'success' => true,
                'message' => 'Nome da arte atualizado com sucesso!',
                'art_name' => $request->art_name
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', $e->errors()['art_name'] ?? ['Nome inválido'])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar nome da arte: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSublimationSizes(): JsonResponse
    {
        $sizes = \App\Models\SublimationSize::where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json($sizes);
    }

    public function getSublimationLocations(): JsonResponse
    {
        $locations = \App\Models\SublimationLocation::where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json($locations);
    }

    public function getSublimationPrice($sizeId, $quantity): JsonResponse
    {
        $size = \App\Models\SublimationSize::findOrFail($sizeId);
        $priceData = $size->getPriceForQuantity($quantity);

        if (!$priceData) {
            return response()->json(['price' => 0], 404);
        }

        return response()->json([
            'price' => $priceData->price,
            'quantity_from' => $priceData->quantity_from,
            'quantity_to' => $priceData->quantity_to,
        ]);
    }

    public function getSerigraphyColors(): JsonResponse
    {
        // Buscar preços de COR da tabela personalization_prices para EMBORRACHADO
        // Remover duplicatas pegando o registro mais recente para cada faixa
        $allColors = \App\Models\PersonalizationPrice::where('personalization_type', 'EMBORRACHADO')
            ->where('size_name', 'COR')
            ->where('active', true)
            ->orderBy('quantity_from')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'quantity_from', 'quantity_to', 'price', 'updated_at']);

        // Agrupar por faixa e pegar apenas o mais recente de cada faixa
        $emborrachadoColors = $allColors->groupBy(function ($item) {
            return $item->quantity_from . '-' . ($item->quantity_to ?? '999999');
        })->map(function ($group) {
            // Pegar o primeiro (mais recente devido ao orderBy)
            return $group->first();
        })->values()->map(function ($item) {
                return [
                    'name' => "COR + ({$item->quantity_from}-{$item->quantity_to})",
                'price' => (float)$item->price,
                    'quantity_from' => $item->quantity_from,
                    'quantity_to' => $item->quantity_to,
                ];
        })->sortBy('quantity_from')->values();

        \Log::info('API getSerigraphyColors chamada, cores encontradas: ' . json_encode($emborrachadoColors->toArray()));

        return response()->json($emborrachadoColors);
    }

    public function getSizeSurcharge(Request $request, $size): JsonResponse
    {
        $totalPrice = $request->input('price', 0);
        $surcharge = \App\Models\SizeSurcharge::getSurchargeForSize($size, $totalPrice);

        if (!$surcharge) {
            return response()->json(['surcharge' => 0]);
        }

        return response()->json([
            'surcharge' => $surcharge->surcharge,
            'price_from' => $surcharge->price_from,
            'price_to' => $surcharge->price_to,
        ]);
    }

    public function getOrderItem($id): JsonResponse
    {
        try {
            \Log::info(' Buscando item para edição', ['item_id' => $id]);
            
            $item = \App\Models\OrderItem::with('sublimations')->findOrFail($id);
            
            \Log::info(' Item encontrado', [
                'item_id' => $item->id,
                'fabric' => $item->fabric,
                'color' => $item->color,
                'print_type' => $item->print_type
            ]);
            
            // Processar sizes
            $sizes = $item->sizes;
            if (is_string($sizes)) {
                $sizes = json_decode($sizes, true) ?? [];
            }
            if (!is_array($sizes)) {
                $sizes = [];
            }
            
            // Buscar IDs das opções de produto baseado nos nomes
            $getOptionId = function($name, $type) {
                if (empty($name)) return null;
                $option = ProductOption::where('type', $type)
                    ->where('name', $name)
                    ->first();
                return $option ? $option->id : null;
            };
            
            // Processar personalização
            $personalizationIds = [];
            if ($item->print_type) {
                $personalizationNames = explode(', ', $item->print_type);
                foreach ($personalizationNames as $name) {
                    $id = $getOptionId(trim($name), 'personalizacao');
                    if ($id) {
                        $personalizationIds[] = $id;
                    }
                }
            }
            
            $data = [
                'id' => $item->id,
                'item_number' => $item->item_number,
                'fabric' => $item->fabric,
                'fabric_id' => $getOptionId($item->fabric, 'tecido'),
                'color' => $item->color,
                'color_id' => $getOptionId($item->color, 'cor'),
                'collar' => $item->collar,
                'collar_id' => $getOptionId($item->collar, 'gola'),
                'model' => $item->model,
                'model_id' => $getOptionId($item->model, 'tipo_corte'),
                'detail' => $item->detail,
                'detail_id' => $getOptionId($item->detail, 'detalhe'),
                'print_type' => $item->print_type,
                'print_type_ids' => $personalizationIds,
                'print_desc' => $item->print_desc,
                'art_name' => $item->art_name,
                'sizes' => $sizes,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'art_notes' => $item->art_notes,
                'cover_image' => $item->cover_image,
                'cover_image_url' => $item->cover_image ? asset('storage/' . $item->cover_image) : null,
            ];
            
            \Log::info(' Retornando dados do item', ['data' => $data]);
            
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error(' Erro ao buscar item: ' . $e->getMessage(), [
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Item não encontrado: ' . $e->getMessage()], 404);
        }
    }
}
