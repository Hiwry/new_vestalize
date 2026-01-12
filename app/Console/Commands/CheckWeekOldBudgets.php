<?php

namespace App\Console\Commands;

use App\Models\Budget;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckWeekOldBudgets extends Command
{
    protected $signature = 'budgets:check-week-old';
    protected $description = 'Verifica orçamentos com 1 semana e cria notificações';

    public function handle()
    {
        $weekAgo = Carbon::now()->subWeek();
        
        // Buscar orçamentos pendentes criados há exatamente 7 dias
        $budgets = Budget::with(['client', 'user'])
            ->where('status', 'pending')
            ->whereDate('created_at', $weekAgo->toDateString())
            ->get();

        $notificationsCreated = 0;

        foreach ($budgets as $budget) {
            // Verificar se já não existe uma notificação para este orçamento
            $existingNotification = Notification::where('user_id', $budget->user_id)
                ->where('type', 'budget_week_old')
                ->where('data->budget_id', $budget->id)
                ->exists();

            if (!$existingNotification) {
                Notification::createBudgetWeekOld(
                    $budget->user_id,
                    $budget->id,
                    $budget->budget_number,
                    $budget->client->name
                );
                $notificationsCreated++;
            }
        }

        $this->info("Verificação concluída. {$notificationsCreated} notificações criadas para {$budgets->count()} orçamentos.");

        return 0;
    }
}
