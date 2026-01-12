<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeliveryRequestController extends Controller
{
    // Listar solicitações (para admin)
    public function index()
    {
        $requests = DeliveryRequest::with(['order.client', 'requestedByUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery-requests.index', compact('requests'));
    }

    // Criar solicitação de antecipação
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'requested_delivery_date' => 'required|date',
                'current_delivery_date' => 'required|date',
                'reason' => 'required|string|max:500',
            ]);

            // Validação manual da data
            if (strtotime($validated['requested_delivery_date']) >= strtotime($validated['current_delivery_date'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A data solicitada deve ser anterior à data de entrega atual.'
                ], 400);
            }

            $order = Order::findOrFail($validated['order_id']);
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa estar autenticado.'
                ], 401);
            }

            // Verificar se já existe uma solicitação pendente
            $existingRequest = DeliveryRequest::where('order_id', $order->id)
                ->where('status', 'pendente')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma solicitação pendente para este pedido.'
                ], 400);
            }

            $deliveryRequest = DeliveryRequest::create([
                'order_id' => $order->id,
                'current_delivery_date' => $validated['current_delivery_date'],
                'requested_delivery_date' => $validated['requested_delivery_date'],
                'reason' => $validated['reason'],
                'status' => 'pendente',
                'requested_by' => $user->id,
                'requested_by_name' => $user->name,
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'delivery_request_created',
                'description' => "Solicitação de antecipação de entrega criada. Nova data: " . Carbon::parse($validated['requested_delivery_date'])->format('d/m/Y'),
            ]);

            // Notificar todos os admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::createDeliveryRequestCreated(
                    $admin->id,
                    $order->id,
                    str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    $user->name
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Solicitação enviada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    // Aprovar solicitação
    public function approve(Request $request, DeliveryRequest $deliveryRequest)
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $deliveryRequest->update([
            'status' => 'aprovado',
            'reviewed_by' => $user->id,
            'reviewed_by_name' => $user->name,
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        // Atualizar data de entrega do pedido
        $deliveryRequest->order->update([
            'delivery_date' => $deliveryRequest->requested_delivery_date,
        ]);

        // Criar log
        OrderLog::create([
            'order_id' => $deliveryRequest->order_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'delivery_request_approved',
            'description' => "Solicitação de antecipação APROVADA. Nova data de entrega: " . $deliveryRequest->requested_delivery_date->format('d/m/Y'),
        ]);

        // Notificar o usuário que solicitou a antecipação
        Notification::createDeliveryRequestApproved(
            $deliveryRequest->requested_by,
            $deliveryRequest->order_id,
            str_pad($deliveryRequest->order_id, 6, '0', STR_PAD_LEFT),
            $deliveryRequest->requested_delivery_date->format('d/m/Y'),
            $user->name
        );

        return redirect()->back()->with('success', 'Solicitação aprovada com sucesso!');
    }

    // Rejeitar solicitação
    public function reject(Request $request, DeliveryRequest $deliveryRequest)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        $deliveryRequest->update([
            'status' => 'rejeitado',
            'reviewed_by' => $user->id,
            'reviewed_by_name' => $user->name,
            'review_notes' => $validated['review_notes'],
            'reviewed_at' => now(),
        ]);

        // Criar log
        OrderLog::create([
            'order_id' => $deliveryRequest->order_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'delivery_request_rejected',
            'description' => "Solicitação de antecipação REJEITADA. Motivo: " . $validated['review_notes'],
        ]);

        // Notificar o usuário que solicitou a antecipação
        Notification::createDeliveryRequestRejected(
            $deliveryRequest->requested_by,
            $deliveryRequest->order_id,
            str_pad($deliveryRequest->order_id, 6, '0', STR_PAD_LEFT),
            $user->name,
            $validated['review_notes']
        );

        return redirect()->back()->with('success', 'Solicitação rejeitada.');
    }
}
