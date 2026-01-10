<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CashApprovalController extends Controller
{
    /**
     * Lista de pedidos e vendas pendentes de aprovação
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            return view('cash.approvals', [
                'orders' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'status' => 'pendente',
                'type' => 'todos',
                'search' => null,
                'stats' => [
                    'pendentes' => 0,
                    'aprovados' => 0,
                    'total_pendente' => 0,
                ],
                'isSuperAdmin' => true
            ]);
        }

        $status = $request->get('status', 'pendente'); // pendente, aprovado, todos
        $type = $request->get('type', 'todos'); // pedidos, vendas, todos
        $search = $request->get('search');

        $query = Order::with(['client', 'user', 'store', 'payment.approvedBy', 'status'])
            ->where('is_draft', false)
            ->where('is_cancelled', false)
            ->has('payment');

        // Filtrar por tipo
        if ($type === 'pedidos') {
            $query->where('is_pdv', false);
        } elseif ($type === 'vendas') {
            $query->where('is_pdv', true);
        }

        // Filtrar por status de aprovação
        if ($status === 'pendente') {
            $query->whereHas('payment', function($q) {
                $q->where('cash_approved', false);
            });
        } elseif ($status === 'aprovado') {
            $query->whereHas('payment', function($q) {
                $q->where('cash_approved', true);
            });
        }

        // Busca
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estatísticas
        $stats = [
            'pendentes' => Order::where('is_draft', false)
                ->where('is_cancelled', false)
                ->whereHas('payment', function($q) {
                    $q->where('cash_approved', false);
                })
                ->count(),
            'aprovados' => Order::where('is_draft', false)
                ->where('is_cancelled', false)
                ->whereHas('payment', function($q) {
                    $q->where('cash_approved', true);
                })
                ->count(),
            'total_pendente' => Payment::where('cash_approved', false)
                ->sum('entry_amount'),
        ];

        return view('cash.approvals', compact('orders', 'status', 'type', 'search', 'stats'));
    }

    /**
     * Aprovar pagamento e dar baixa no pedido
     */
    public function approve(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento não encontrado'], 404);
        }

        if ($payment->cash_approved) {
            return response()->json(['error' => 'Pagamento já aprovado'], 400);
        }

        // Aprovar pagamento
        $payment->update([
            'cash_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Atualizar status do pagamento para pago se necessário
        if ($payment->remaining_amount <= 0) {
            $payment->update(['status' => 'pago']);
        }

        // Confirmar transações de caixa pendentes relacionadas a este pedido
        CashTransaction::where('order_id', $order->id)
            ->where('status', 'pendente')
            ->update([
                'status' => 'confirmado',
                'notes' => 'Aprovado por: ' . Auth::user()->name . ' em ' . now()->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento aprovado e baixa realizada com sucesso!'
        ]);
    }

    /**
     * Anexar comprovante de pagamento
     */
    public function attachReceipt(Request $request, $orderId): JsonResponse
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB
        ]);

        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento não encontrado'], 404);
        }

        // Deletar comprovante anterior se existir
        if ($payment->receipt_attachment) {
            Storage::disk('public')->delete($payment->receipt_attachment);
        }

        // Salvar novo comprovante
        $file = $request->file('receipt');
        $path = $file->store('receipts', 'public');

        $payment->update([
            'receipt_attachment' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comprovante anexado com sucesso!',
            'receipt_url' => Storage::url($path),
        ]);
    }

    /**
     * Visualizar comprovante
     */
    public function viewReceipt($orderId)
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment || !$payment->receipt_attachment) {
            abort(404, 'Comprovante não encontrado');
        }

        $filePath = Storage::disk('public')->path($payment->receipt_attachment);
        
        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado');
        }

        $mimeType = File::mimeType($filePath);
        if (!$mimeType) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }

    /**
     * Remover comprovante
     */
    public function removeReceipt($orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment || !$payment->receipt_attachment) {
            return response()->json(['error' => 'Comprovante não encontrado'], 404);
        }

        Storage::disk('public')->delete($payment->receipt_attachment);

        $payment->update([
            'receipt_attachment' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comprovante removido com sucesso!'
        ]);
    }

    /**
     * Aprovar múltiplos pedidos
     */
    public function approveMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $approved = 0;
        $errors = [];

        foreach ($request->order_ids as $orderId) {
            try {
                $order = Order::with('payment')->find($orderId);
                if (!$order || !$order->payment) {
                    $errors[] = "Pedido #{$orderId}: Pagamento não encontrado";
                    continue;
                }

                if ($order->payment->cash_approved) {
                    $errors[] = "Pedido #{$orderId}: Já aprovado";
                    continue;
                }

                $order->payment->update([
                    'cash_approved' => true,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);

                if ($order->payment->remaining_amount <= 0) {
                    $order->payment->update(['status' => 'pago']);
                }

                CashTransaction::where('order_id', $order->id)
                    ->where('status', 'pendente')
                    ->update([
                        'status' => 'confirmado',
                        'notes' => 'Aprovado em lote por: ' . Auth::user()->name . ' em ' . now()->format('d/m/Y H:i'),
                    ]);

                $approved++;
            } catch (\Exception $e) {
                $errors[] = "Pedido #{$orderId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'approved' => $approved,
            'errors' => $errors,
            'message' => "{$approved} pedido(s) aprovado(s) com sucesso!"
        ]);
    }
}
