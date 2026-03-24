<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $order = Order::with(['payment', 'client', 'user'])->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->cash_approved) {
            return response()->json(['error' => 'Pagamento ja aprovado'], 400);
        }

        DB::transaction(function () use ($order, $payment) {
            $this->syncCashTransactionsForApprovedMethods($order, $payment, 'Aprovado por');

            $payment->update([
                'cash_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            if (($payment->remaining_amount ?? 0) <= 0) {
                $payment->update(['status' => 'pago']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pagamento aprovado e baixa realizada com sucesso!',
        ]);
    }

    /**
     * Aprovar entrada (1a etapa) de um pedido.
     */
    public function approveEntry(Request $request, $orderId): JsonResponse
    {
        $order = Order::with(['payment', 'client', 'user'])->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->entry_approved) {
            return response()->json(['error' => 'Entrada ja aprovada'], 400);
        }

        DB::transaction(function () use ($order, $payment) {
            $payment->update([
                'entry_approved' => true,
                'entry_approved_by' => Auth::id(),
                'entry_approved_at' => now(),
            ]);

            $this->syncCashTransactionsForApprovedMethods($order, $payment, 'Entrada aprovada por');

            if (($payment->remaining_amount ?? 0) <= 0) {
                $payment->update([
                    'cash_approved' => true,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'status' => 'pago',
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Entrada aprovada com sucesso!',
        ]);
    }

    /**
     * Aprovar restante (2a etapa) de um pedido.
     */
    public function approveRemaining(Request $request, $orderId): JsonResponse
    {
        $order = Order::with(['payment', 'client', 'user'])->findOrFail($orderId);
        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['error' => 'Pagamento nao encontrado'], 404);
        }

        if ($payment->remaining_approved) {
            return response()->json(['error' => 'Restante ja aprovado'], 400);
        }

        DB::transaction(function () use ($order, $payment) {
            $remainingAmount = round((float) ($payment->remaining_amount ?? 0), 2);
            $paymentTotal = round((float) ($payment->amount ?? 0), 2);
            $paymentMethods = is_array($payment->payment_methods) ? $payment->payment_methods : [];
            $methodsTotal = round(collect($paymentMethods)->sum(fn($method) => (float) ($method['amount'] ?? 0)), 2);

            // Se o restante ainda nao foi registrado como metodo, cria uma entrada padrao.
            if ($remainingAmount > 0 && $methodsTotal + 0.01 < $paymentTotal) {
                $paymentMethods[] = [
                    'id' => now()->timestamp . rand(1000, 9999),
                    'method' => $payment->payment_method ?: $payment->method ?: 'pix',
                    'amount' => $remainingAmount,
                    'date' => now()->format('Y-m-d H:i:s'),
                ];
                $methodsTotal = round($methodsTotal + $remainingAmount, 2);
            }

            $payment->update([
                'payment_methods' => $paymentMethods,
                'entry_amount' => min($paymentTotal, max((float) ($payment->entry_amount ?? 0), $methodsTotal)),
                'remaining_amount' => 0,
                'remaining_approved' => true,
                'remaining_approved_by' => Auth::id(),
                'remaining_approved_at' => now(),
                'cash_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'pago',
            ]);

            $this->syncCashTransactionsForApprovedMethods($order, $payment->fresh(), 'Aprovado (restante) por');
        });

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
                $order = Order::with(['payment', 'client', 'user'])->find($orderId);
                if (!$order || !$order->payment) {
                    $errors[] = "Pedido #{$orderId}: Pagamento nao encontrado";
                    continue;
                }

                if ($order->payment->cash_approved) {
                    $errors[] = "Pedido #{$orderId}: Ja aprovado";
                    continue;
                }

                DB::transaction(function () use ($order) {
                    $this->syncCashTransactionsForApprovedMethods($order, $order->payment, 'Aprovado em lote por');

                    $order->payment->update([
                        'cash_approved' => true,
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    if (($order->payment->remaining_amount ?? 0) <= 0) {
                        $order->payment->update(['status' => 'pago']);
                    }
                });

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

    private function syncCashTransactionsForApprovedMethods(Order $order, Payment $payment, string $actionLabel): void
    {
        $existingTransactions = $order->cashTransactions()
            ->where('type', 'entrada')
            ->where('category', 'Venda')
            ->orderBy('id')
            ->get();

        $usedTransactionIds = [];
        $paymentMethods = is_array($payment->payment_methods) ? $payment->payment_methods : [];

        if (empty($paymentMethods) && (float) ($payment->entry_amount ?? 0) > 0) {
            $paymentMethods[] = [
                'id' => 'fallback-entry',
                'method' => $payment->payment_method ?: $payment->method ?: 'pix',
                'amount' => (float) $payment->entry_amount,
                'date' => optional($payment->payment_date ?? $payment->entry_date)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
            ];
        }

        foreach ($paymentMethods as $method) {
            $normalizedMethod = $this->normalizePaymentMethodData($method, $payment);

            if (!$normalizedMethod) {
                continue;
            }

            $matchingTransaction = $this->findMatchingCashTransaction(
                $existingTransactions,
                $normalizedMethod,
                $usedTransactionIds
            );

            if ($matchingTransaction) {
                $usedTransactionIds[] = $matchingTransaction->id;

                $updates = [];

                if ($matchingTransaction->status !== 'confirmado') {
                    $updates['status'] = 'confirmado';
                    $updates['notes'] = $this->appendApprovalNote($matchingTransaction->notes, $actionLabel);
                }

                if (empty($matchingTransaction->payment_methods)) {
                    $updates['payment_methods'] = [[
                        'method' => $normalizedMethod['method'],
                        'amount' => $normalizedMethod['amount'],
                        'date' => $normalizedMethod['date']->format('Y-m-d H:i:s'),
                    ]];
                }

                if (!empty($updates)) {
                    $matchingTransaction->update($updates);
                }

                continue;
            }

            $created = CashTransaction::create([
                'store_id' => $order->store_id,
                'type' => 'entrada',
                'category' => 'Venda',
                'description' => "Pagamento do Pedido #" . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . ($order->client->name ?? 'N/A'),
                'amount' => $normalizedMethod['amount'],
                'payment_method' => $normalizedMethod['method'],
                'payment_methods' => [[
                    'method' => $normalizedMethod['method'],
                    'amount' => $normalizedMethod['amount'],
                    'date' => $normalizedMethod['date']->format('Y-m-d H:i:s'),
                ]],
                'status' => 'confirmado',
                'transaction_date' => $normalizedMethod['date'],
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'user_name' => $order->user?->name ?? $order->seller ?? Auth::user()?->name ?? 'Sistema',
                'notes' => $this->appendApprovalNote('Transacao gerada automaticamente na aprovacao do caixa.', $actionLabel),
            ]);

            $usedTransactionIds[] = $created->id;
        }

        CashTransaction::where('order_id', $order->id)
            ->where('type', 'entrada')
            ->where('status', 'pendente')
            ->update([
                'status' => 'confirmado',
                'notes' => $this->appendApprovalNote(null, $actionLabel),
            ]);
    }

    private function normalizePaymentMethodData(array $method, Payment $payment): ?array
    {
        $amount = round((float) ($method['amount'] ?? 0), 2);
        if ($amount <= 0) {
            return null;
        }

        $methodName = strtolower(trim((string) ($method['method'] ?? $payment->payment_method ?? $payment->method ?? 'pix')));
        $dateValue = $method['date'] ?? $payment->payment_date ?? $payment->entry_date ?? now();

        return [
            'method' => $methodName !== '' ? $methodName : 'pix',
            'amount' => $amount,
            'date' => Carbon::parse($dateValue),
        ];
    }

    private function findMatchingCashTransaction($transactions, array $method, array $usedTransactionIds): ?CashTransaction
    {
        $sameDayMatch = $transactions->first(function (CashTransaction $transaction) use ($method, $usedTransactionIds) {
            return !in_array($transaction->id, $usedTransactionIds, true)
                && strtolower((string) $transaction->payment_method) === $method['method']
                && abs((float) $transaction->amount - $method['amount']) < 0.01
                && optional($transaction->transaction_date)?->isSameDay($method['date']);
        });

        if ($sameDayMatch) {
            return $sameDayMatch;
        }

        return $transactions->first(function (CashTransaction $transaction) use ($method, $usedTransactionIds) {
            return !in_array($transaction->id, $usedTransactionIds, true)
                && strtolower((string) $transaction->payment_method) === $method['method']
                && abs((float) $transaction->amount - $method['amount']) < 0.01;
        });
    }

    private function appendApprovalNote(?string $currentNotes, string $actionLabel): string
    {
        $note = $actionLabel . ': ' . (Auth::user()->name ?? 'Sistema') . ' em ' . now()->format('d/m/Y H:i');

        return trim(($currentNotes ? ($currentNotes . PHP_EOL) : '') . $note);
    }
}
