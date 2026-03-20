<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CashApprovalController extends Controller
{
    /**
     * Lista de pedidos e vendas pendentes de aprovacao.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pendente');
        $type = $request->get('type', 'todos');
        $search = $request->get('search');

        $query = Order::with(['client', 'user', 'store', 'payment.approvedBy', 'payment.entryApprovedBy', 'payment.remainingApprovedBy', 'status'])
            ->where('is_draft', false)
            ->where('is_cancelled', false)
            ->has('payment');

        \App\Helpers\StoreHelper::applyStoreFilter($query);

        if ($type === 'pedidos') {
            $query->where('is_pdv', false);
        } elseif ($type === 'vendas') {
            $query->where('is_pdv', true);
        }

        if ($status === 'pendente') {
            $query->whereHas('payment', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('cash_approved', false)
                        ->orWhereNull('cash_approved');
                });
            });
        } elseif ($status === 'aprovado') {
            $query->whereHas('payment', function ($q) {
                $q->where('cash_approved', true);
            });
        }

        if ($search) {
            $query->search($search);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        $statsQuery = Order::where('is_draft', false)->where('is_cancelled', false);
        \App\Helpers\StoreHelper::applyStoreFilter($statsQuery);

        $stats = [
            'pendentes' => (clone $statsQuery)
                ->whereHas('payment', function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('cash_approved', false)
                            ->orWhereNull('cash_approved');
                    });
                })
                ->count(),
            'aprovados' => (clone $statsQuery)
                ->whereHas('payment', function ($q) {
                    $q->where('cash_approved', true);
                })
                ->count(),
            'total_pendente' => Payment::whereIn('order_id', (clone $statsQuery)->pluck('id'))
                ->where(function ($q) {
                    $q->where('cash_approved', false)
                        ->orWhereNull('cash_approved');
                })
                ->sum('entry_amount'),
        ];

        return view('cash.approvals', compact('orders', 'status', 'type', 'search', 'stats'));
    }

    /**
     * Aprovar pagamento e dar baixa no pedido.
     */
    public function approve(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->cash_approved) {
            return response()->json(['error' => 'Pagamento ja aprovado'], 400);
        }

        $payment->update([
            'cash_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($payment->remaining_amount <= 0) {
            $payment->update(['status' => 'pago']);
        }

        CashTransaction::where('order_id', $order->id)
            ->where('status', 'pendente')
            ->update([
                'status' => 'confirmado',
                'notes' => 'Aprovado por: ' . Auth::user()->name . ' em ' . now()->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento aprovado e baixa realizada com sucesso!',
        ]);
    }

    /**
     * Aprovar entrada (1ª etapa) de um pedido.
     */
    public function approveEntry(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->entry_approved) {
            return response()->json(['error' => 'Entrada ja aprovada'], 400);
        }

        $payment->update([
            'entry_approved' => true,
            'entry_approved_by' => Auth::id(),
            'entry_approved_at' => now(),
        ]);

        // Se não há restante, marca como totalmente aprovado também
        if (($payment->remaining_amount ?? 0) <= 0) {
            $payment->update([
                'cash_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'pago',
            ]);
            CashTransaction::where('order_id', $order->id)
                ->where('status', 'pendente')
                ->update([
                    'status' => 'confirmado',
                    'notes' => 'Aprovado por: ' . Auth::user()->name . ' em ' . now()->format('d/m/Y H:i'),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrada aprovada com sucesso!',
        ]);
    }

    /**
     * Aprovar restante (2ª etapa) de um pedido.
     */
    public function approveRemaining(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->remaining_approved) {
            return response()->json(['error' => 'Restante ja aprovado'], 400);
        }

        $payment->update([
            'remaining_approved' => true,
            'remaining_approved_by' => Auth::id(),
            'remaining_approved_at' => now(),
        ]);

        // Quando o restante é aprovado, marca o pagamento como totalmente aprovado
        $payment->update([
            'cash_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'status' => 'pago',
        ]);

        CashTransaction::where('order_id', $order->id)
            ->where('status', 'pendente')
            ->update([
                'status' => 'confirmado',
                'notes' => 'Aprovado (restante) por: ' . Auth::user()->name . ' em ' . now()->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Restante aprovado! Pedido totalmente quitado.',
        ]);
    }

    /**
     * Anexar um ou mais comprovantes de pagamento.
     */
    public function attachReceipt(Request $request, $orderId): JsonResponse
    {
        $request->validate([
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'receipts' => 'nullable|array|min:1',
            'receipts.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        $files = [];
        if ($request->hasFile('receipts')) {
            $files = $request->file('receipts');
        } elseif ($request->hasFile('receipt')) {
            $files = [$request->file('receipt')];
        }

        if (empty($files)) {
            return response()->json(['error' => 'Envie ao menos um comprovante valido.'], 422);
        }

        $attachments = $payment->receipt_attachments_list;

        foreach ($files as $file) {
            $path = $file->store('receipts', 'public');
            $attachments[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'uploaded_at' => now()->toISOString(),
            ];
        }

        $this->syncReceiptAttachments($payment, $attachments);

        return response()->json([
            'success' => true,
            'message' => count($files) > 1
                ? 'Comprovantes anexados com sucesso!'
                : 'Comprovante anexado com sucesso!',
            'receipt_count' => count($payment->fresh()->receipt_attachments_list),
        ]);
    }

    /**
     * Visualizar um comprovante especifico.
     */
    public function viewReceipt(Request $request, $orderId)
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;
        $receiptIndex = $request->query('receipt');
        $attachment = $this->resolveReceiptAttachment(
            $payment,
            $receiptIndex !== null ? (int) $receiptIndex : 0
        );

        if (!$payment || !$attachment) {
            abort(404, 'Comprovante nao encontrado');
        }

        $filePath = Storage::disk('public')->path($attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo nao encontrado');
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
     * Remover um comprovante especifico.
     */
    public function removeReceipt(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;
        $receiptIndex = (int) $request->input('receipt_index', 0);
        $attachments = $payment?->receipt_attachments_list ?? [];

        if (!$payment || empty($attachments) || !isset($attachments[$receiptIndex])) {
            return response()->json(['error' => 'Comprovante nao encontrado'], 404);
        }

        Storage::disk('public')->delete($attachments[$receiptIndex]['path']);
        unset($attachments[$receiptIndex]);

        $this->syncReceiptAttachments($payment, array_values($attachments));

        return response()->json([
            'success' => true,
            'message' => 'Comprovante removido com sucesso!',
            'receipt_count' => count($payment->fresh()->receipt_attachments_list),
        ]);
    }

    /**
     * Aprovar multiplos pedidos.
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
                    $errors[] = "Pedido #{$orderId}: Pagamento nao encontrado";
                    continue;
                }

                if ($order->payment->cash_approved) {
                    $errors[] = "Pedido #{$orderId}: Ja aprovado";
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
            'message' => "{$approved} pedido(s) aprovado(s) com sucesso!",
        ]);
    }

    private function resolveReceiptAttachment(?Payment $payment, int $receiptIndex = 0): ?array
    {
        if (!$payment) {
            return null;
        }

        $attachments = $payment->receipt_attachments_list;

        return $attachments[$receiptIndex] ?? null;
    }

    private function syncReceiptAttachments(Payment $payment, array $attachments): void
    {
        $payment->update([
            'receipt_attachment' => $attachments[0]['path'] ?? null,
            'receipt_attachments' => empty($attachments) ? null : array_values($attachments),
        ]);
    }
}
