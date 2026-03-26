<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class CashApprovalSyncService
{
    public function syncApprovedPayment(Order $order, Payment $payment, string $actionLabel, ?string $actorName = null): void
    {
        $approvalTimestamp = now('America/Sao_Paulo');
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

        $signatureOccurrences = collect($paymentMethods)
            ->map(fn(array $method) => $this->buildMethodSignature($method))
            ->countBy()
            ->all();

        foreach ($paymentMethods as $method) {
            $normalizedMethod = $this->normalizePaymentMethodData($method, $payment);

            if (!$normalizedMethod) {
                continue;
            }

            $matchingTransaction = $this->findMatchingCashTransaction(
                $existingTransactions,
                $normalizedMethod,
                $usedTransactionIds,
                (int) ($signatureOccurrences[$this->buildMethodSignature($method)] ?? 1)
            );

            if ($matchingTransaction) {
                $usedTransactionIds[] = $matchingTransaction->id;

                $updates = [];

                if ($matchingTransaction->status !== 'confirmado') {
                    $updates['status'] = 'confirmado';
                    $updates['transaction_date'] = $approvalTimestamp;
                    $updates['notes'] = $this->appendApprovalNote($matchingTransaction->notes, $actionLabel, $actorName);
                }

                if ($this->transactionNeedsPaymentMethodSync($matchingTransaction, $normalizedMethod)) {
                    $updates['payment_methods'] = [$this->buildTransactionPaymentMethodPayload($normalizedMethod)];
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
                'payment_methods' => [$this->buildTransactionPaymentMethodPayload($normalizedMethod)],
                'status' => 'confirmado',
                'transaction_date' => $approvalTimestamp,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'user_name' => $order->user?->name ?? $order->seller ?? $actorName ?? 'Sistema',
                'notes' => $this->appendApprovalNote('Transacao gerada automaticamente na aprovacao do caixa.', $actionLabel, $actorName),
            ]);

            $usedTransactionIds[] = $created->id;
        }

        CashTransaction::where('order_id', $order->id)
            ->where('type', 'entrada')
            ->where('status', 'pendente')
            ->update([
                'status' => 'confirmado',
                'transaction_date' => $approvalTimestamp,
                'notes' => $this->appendApprovalNote(null, $actionLabel, $actorName),
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
            'id' => isset($method['id']) ? (string) $method['id'] : null,
            'method' => $methodName !== '' ? $methodName : 'pix',
            'amount' => $amount,
            'date' => Carbon::parse($dateValue),
        ];
    }

    private function findMatchingCashTransaction($transactions, array $method, array $usedTransactionIds, int $signatureOccurrences): ?CashTransaction
    {
        if (!empty($method['id'])) {
            $idMatch = $transactions->first(function (CashTransaction $transaction) use ($method, $usedTransactionIds) {
                if (in_array($transaction->id, $usedTransactionIds, true)) {
                    return false;
                }

                $transactionMethodIds = collect($transaction->payment_methods ?? [])
                    ->pluck('id')
                    ->filter()
                    ->map(fn($id) => (string) $id)
                    ->all();

                return in_array((string) $method['id'], $transactionMethodIds, true);
            });

            if ($idMatch) {
                return $idMatch;
            }
        }

        $sameDayMatch = $transactions->first(function (CashTransaction $transaction) use ($method, $usedTransactionIds) {
            return !in_array($transaction->id, $usedTransactionIds, true)
                && strtolower((string) $transaction->payment_method) === $method['method']
                && abs((float) $transaction->amount - $method['amount']) < 0.01
                && optional($transaction->transaction_date)?->isSameDay($method['date']);
        });

        if ($sameDayMatch) {
            return $sameDayMatch;
        }

        if ($signatureOccurrences === 1) {
            return $transactions->first(function (CashTransaction $transaction) use ($method, $usedTransactionIds) {
                return !in_array($transaction->id, $usedTransactionIds, true)
                    && strtolower((string) $transaction->payment_method) === $method['method']
                    && abs((float) $transaction->amount - $method['amount']) < 0.01;
            });
        }

        return null;
    }

    private function transactionNeedsPaymentMethodSync(CashTransaction $transaction, array $method): bool
    {
        $paymentMethods = is_array($transaction->payment_methods) ? $transaction->payment_methods : [];

        if (empty($paymentMethods)) {
            return true;
        }

        $current = $paymentMethods[0] ?? [];
        if (($current['id'] ?? null) !== ($method['id'] ?? null)) {
            return true;
        }

        if (strtolower((string) ($current['method'] ?? '')) !== $method['method']) {
            return true;
        }

        return abs((float) ($current['amount'] ?? 0) - (float) $method['amount']) >= 0.01;
    }

    private function buildTransactionPaymentMethodPayload(array $method): array
    {
        return [
            'id' => $method['id'],
            'method' => $method['method'],
            'amount' => $method['amount'],
            'date' => $method['date']->format('Y-m-d H:i:s'),
        ];
    }

    private function appendApprovalNote(?string $currentNotes, string $actionLabel, ?string $actorName = null): string
    {
        $note = $actionLabel . ': ' . ($actorName ?? auth()->user()?->name ?? 'Sistema') . ' em ' . now()->format('d/m/Y H:i');

        return trim(($currentNotes ? ($currentNotes . PHP_EOL) : '') . $note);
    }

    private function buildMethodSignature(array $method): string
    {
        $methodName = strtolower(trim((string) ($method['method'] ?? '')));
        $amount = number_format((float) ($method['amount'] ?? 0), 2, '.', '');

        return $methodName . '|' . $amount;
    }
}
