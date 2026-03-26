<?php

namespace App\Console\Commands;

use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillCashApprovalDates extends Command
{
    protected $signature = 'cash:backfill-approval-dates
                            {--order_id= : Corrige apenas um pedido especifico}
                            {--dry-run : Apenas exibe o que seria alterado}';

    protected $description = 'Alinha transaction_date de vendas antigas com a data real de aprovacao no caixa';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $orderId = $this->option('order_id');

        $query = CashTransaction::with(['order.payment'])
            ->where('type', 'entrada')
            ->where('category', 'Venda')
            ->where('status', 'confirmado')
            ->whereNotNull('order_id');

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $transactions = $query->orderBy('id')->get();

        if ($transactions->isEmpty()) {
            $this->info('Nenhuma transacao elegivel encontrada.');
            return self::SUCCESS;
        }

        $checked = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($transactions as $transaction) {
            $checked++;

            $payment = $transaction->order?->payment;
            if (!$payment) {
                $skipped++;
                $this->line("Pulado: transacao {$transaction->id} sem payment associado.");
                continue;
            }

            $targetDate = $this->resolveApprovalDate($transaction->notes, $payment);
            if (!$targetDate) {
                $skipped++;
                $this->line("Pulado: transacao {$transaction->id} sem data de aprovacao disponivel.");
                continue;
            }

            $currentDate = $transaction->transaction_date
                ? Carbon::parse($transaction->transaction_date)
                : null;

            if ($currentDate && $currentDate->equalTo($targetDate)) {
                continue;
            }

            $message = sprintf(
                'Transacao %d | Pedido #%06d | %s -> %s',
                $transaction->id,
                $transaction->order_id,
                $currentDate?->format('d/m/Y H:i:s') ?? 'null',
                $targetDate->format('d/m/Y H:i:s')
            );

            if ($dryRun) {
                $this->warn('[dry-run] ' . $message);
                $updated++;
                continue;
            }

            $transaction->update([
                'transaction_date' => $targetDate,
            ]);

            $this->info($message);
            $updated++;
        }

        $this->newLine();
        $this->info("Verificadas: {$checked}");
        $this->info($dryRun ? "Ajustes simulados: {$updated}" : "Ajustes aplicados: {$updated}");
        $this->info("Puladas: {$skipped}");

        return self::SUCCESS;
    }

    private function resolveApprovalDate(?string $notes, $payment): ?Carbon
    {
        $normalizedNotes = strtolower((string) $notes);

        if (str_contains($normalizedNotes, 'aprovado (restante) por') && $payment->remaining_approved_at) {
            return Carbon::parse($payment->remaining_approved_at);
        }

        if (str_contains($normalizedNotes, 'entrada aprovada por') && $payment->entry_approved_at) {
            return Carbon::parse($payment->entry_approved_at);
        }

        if (
            (str_contains($normalizedNotes, 'aprovado por') || str_contains($normalizedNotes, 'aprovado em lote por'))
            && $payment->approved_at
        ) {
            return Carbon::parse($payment->approved_at);
        }

        if ($payment->approved_at) {
            return Carbon::parse($payment->approved_at);
        }

        if ($payment->remaining_approved_at) {
            return Carbon::parse($payment->remaining_approved_at);
        }

        if ($payment->entry_approved_at) {
            return Carbon::parse($payment->entry_approved_at);
        }

        return null;
    }
}
