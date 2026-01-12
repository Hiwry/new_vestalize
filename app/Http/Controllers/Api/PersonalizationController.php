<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderSublimation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PersonalizationController extends Controller
{
    /**
     * Retorna uma personalização específica
     */
    public function show($id): JsonResponse
    {
        try {
            $personalization = OrderSublimation::with(['files', 'size', 'location'])->find($id);
            
            if (!$personalization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personalização não encontrada'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'personalization' => $personalization
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar personalização:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar personalização: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove a personalização do banco de dados
     */
    public function destroy($id): JsonResponse
    {
        try {
            \Log::info('=== REMOVING PERSONALIZATION ===', [
                'id' => $id,
                'request_method' => request()->method(),
                'headers' => request()->headers->all()
            ]);
            
            // Carregar com relacionamentos para evitar consultas adicionais
            $personalization = OrderSublimation::with(['orderItem.order', 'files'])->find($id);
            
            if (!$personalization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personalização não encontrada'
                ], 404);
            }
            
            \Log::info('Personalization found:', [
                'id' => $personalization->id,
                'art_name' => $personalization->art_name,
                'final_price' => $personalization->final_price
            ]);
            
            // Buscar o item do pedido para atualizar o preço
            $orderItem = $personalization->orderItem;
            $personalizationPrice = $personalization->final_price ?? 0;
            
            // Deletar arquivos associados (usar a coleção carregada)
            $filesCount = $personalization->files->count();
            if ($filesCount > 0) {
                $personalization->files()->delete();
            }
            
            \Log::info('Files deleted:', ['count' => $filesCount]);
            
            // Deletar a personalização
            $personalization->delete();
            
            // Atualizar preço do item (subtrair o preço da personalização removida)
            if ($orderItem) {
                $newItemPrice = max(0, $orderItem->total_price - $personalizationPrice);
                $orderItem->update(['total_price' => $newItemPrice]);
                
                // Atualizar subtotal do pedido
                $order = $orderItem->order;
                if ($order) {
                    $newSubtotal = $order->items()->sum('total_price');
                    $order->update(['subtotal' => $newSubtotal]);
                    
                    \Log::info('Order totals updated:', [
                        'order_id' => $order->id,
                        'old_subtotal' => $order->subtotal,
                        'new_subtotal' => $newSubtotal
                    ]);
                }
            }
            
            \Log::info('Personalization deleted successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Personalização removida com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao remover personalização:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover personalização: ' . $e->getMessage()
            ], 500);
        }
    }
}