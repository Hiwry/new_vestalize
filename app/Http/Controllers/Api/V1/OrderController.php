<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Listagem de pedidos do tenant autenticado.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $orders = Order::where('tenant_id', $tenantId)
            ->with(['client', 'status'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($orders);
    }

    /**
     * Detalhes de um pedido específico.
     */
    public function show($id)
    {
        $tenantId = Auth::user()->tenant_id;
        
        $order = Order::where('tenant_id', $tenantId)
            ->with(['client', 'status', 'items', 'payments'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Atualizar status do pedido (útil para apps de produção).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id'
        ]);

        $tenantId = Auth::user()->tenant_id;
        $order = Order::where('tenant_id', $tenantId)->findOrFail($id);

        $order->update([
            'status_id' => $request->status_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status do pedido atualizado com sucesso.',
            'order' => $order
        ]);
    }
}
