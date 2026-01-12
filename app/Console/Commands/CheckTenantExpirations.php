<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTenantExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tenant-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica assinaturas e trials vencidos e notifica o administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminEmail = config('mail.admin_email', 'hiwry@hotmail.com');
        $today = now()->startOfDay();

        // 1. Verificar Trials que vencem hoje
        $expiredTrials = \App\Models\Tenant::whereDate('trial_ends_at', $today)
            ->where('status', 'active')
            ->get();

        foreach ($expiredTrials as $tenant) {
            $this->info("Trial vencido: {$tenant->name}");
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\TenantExpiredNotification($tenant, 'trial'));
        }

        // 2. Verificar Assinaturas que vencem hoje
        $expiredSubs = \App\Models\Tenant::whereDate('subscription_ends_at', $today)
            ->where('status', 'active')
            ->get();

        foreach ($expiredSubs as $tenant) {
            $this->info("Assinatura vencida: {$tenant->name}");
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\TenantExpiredNotification($tenant, 'assinatura'));
        }

        $this->info('Verificação concluída.');
    }
}
