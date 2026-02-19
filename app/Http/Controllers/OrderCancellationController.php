<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderCancellationController extends Controller
{
    public function request(Request $request, Order $order)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            // Verificar se já existe uma solicitação pendente
            if ($order->pendingCancellation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma solicitação de cancelamento pendente para este pedido.'
                ], 400);
            }

            // Verificar se o pedido pode ser cancelado
            if ($order->is_cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido já foi cancelado.'
                ], 400);
            }

            DB::beginTransaction();
            
            // Criar solicitação de cancelamento
            $cancellation = OrderCancellation::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'status' => 'pending'
            ]);

            // Atualizar pedido
            $order->update([
                'has_pending_cancellation' => true
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'cancellation_requested',
                'description' => 'Solicitação de cancelamento enviada',
                'old_value' => null,
                'new_value' => [
                    'reason' => $request->reason,
                    'status' => 'pending'
                ]
            ]);

            // Notificar todos os admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::createCancellationRequest(
                    $admin->id,
                    $order->id,
                    str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    Auth::user()->name
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de cancelamento enviada com sucesso.',
                'cancellation' => $cancellation
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao processar cancelamento: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação de cancelamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, OrderCancellation $cancellation)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($cancellation->status !== 'pending') {
            return redirect()->route('admin.cancellations.index')
                ->with('error', 'Esta solicitação já foi processada.');
        }

        DB::beginTransaction();
        try {
            // Aprovar cancelamento
            $cancellation->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Cancelar pedido
            $cancellation->order->update([
                'is_cancelled' => true,
                'cancelled_at' => now(),
                'cancellation_reason' => $cancellation->reason,
                'has_pending_cancellation' => false,
                'last_updated_at' => now()
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $cancellation->order_id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'cancellation_approved',
                'description' => 'Cancelamento aprovado',
                'old_value' => ['status' => 'pending'],
                'new_value' => [
                    'status' => 'approved',
                    'reason' => $cancellation->reason,
                    'admin_notes' => $request->admin_notes
                ]
            ]);

            // Notificar o usuário que solicitou o cancelamento
            Notification::createCancellationApproved(
                $cancellation->user_id,
                $cancellation->order_id,
                str_pad($cancellation->order_id, 6, '0', STR_PAD_LEFT),
                Auth::user()->name
            );

            DB::commit();

            return redirect()->route('admin.cancellations.index')
                ->with('success', 'Cancelamento aprovado com sucesso! O pedido #' . str_pad($cancellation->order_id, 6, '0', STR_PAD_LEFT) . ' foi cancelado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.cancellations.index')
                ->with('error', 'Erro ao aprovar cancelamento: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, OrderCancellation $cancellation)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        if ($cancellation->status !== 'pending') {
            return redirect()->route('admin.cancellations.index')
                ->with('error', 'Esta solicitação já foi processada.');
        }

        DB::beginTransaction();
        try {
            // Rejeitar cancelamento
            $cancellation->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Atualizar pedido
            $cancellation->order->update([
                'has_pending_cancellation' => false
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $cancellation->order_id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'cancellation_rejected',
                'description' => 'Cancelamento rejeitado',
                'old_value' => ['status' => 'pending'],
                'new_value' => [
                    'status' => 'rejected',
                    'admin_notes' => $request->admin_notes
                ]
            ]);

            // Notificar o usuário que solicitou o cancelamento
            Notification::createCancellationRejected(
                $cancellation->user_id,
                $cancellation->order_id,
                str_pad($cancellation->order_id, 6, '0', STR_PAD_LEFT),
                Auth::user()->name,
                $request->admin_notes
            );

            DB::commit();

            return redirect()->route('admin.cancellations.index')
                ->with('success', 'Cancelamento rejeitado. O pedido #' . str_pad($cancellation->order_id, 6, '0', STR_PAD_LEFT) . ' permanece ativo.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.cancellations.index')
                ->with('error', 'Erro ao rejeitar cancelamento: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $cancellations = OrderCancellation::with(['order.client', 'user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.cancellations.index', compact('cancellations'));
    }
}
