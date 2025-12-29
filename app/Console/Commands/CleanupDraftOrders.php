<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupDraftOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup-drafts {--days=7 : Número de dias para considerar um rascunho como órfão} {--dry-run : Apenas mostrar o que seria removido}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove pedidos rascunho órfãos (não finalizados há mais de X dias)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Procurando pedidos rascunho criados antes de {$cutoffDate->format('d/m/Y H:i:s')}...");
        
        $draftOrders = Order::where('is_draft', true)
            ->where('created_at', '<', $cutoffDate)
            ->with(['client', 'items'])
            ->get();
        
        if ($draftOrders->isEmpty()) {
            $this->info('Nenhum pedido rascunho órfão encontrado.');
            return 0;
        }
        
        $this->info("Encontrados {$draftOrders->count()} pedidos rascunho órfãos:");
        
        foreach ($draftOrders as $order) {
            $itemCount = $order->items->sum('quantity');
            $this->line("- Pedido #{$order->id} | Cliente: {$order->client->name} | Peças: {$itemCount} | Criado: {$order->created_at->format('d/m/Y H:i')}");
        }
        
        if ($dryRun) {
            $this->warn('Modo dry-run ativado. Nenhum pedido foi removido.');
            return 0;
        }
        
        if ($this->confirm('Deseja realmente remover estes pedidos rascunho órfãos?')) {
            $deletedCount = 0;
            
            foreach ($draftOrders as $order) {
                // Remover itens relacionados primeiro
                $order->items()->delete();
                
                // Remover logs relacionados
                $order->logs()->delete();
                
                // Remover comentários relacionados
                $order->comments()->delete();
                
                // Remover pagamentos relacionados
                $order->payments()->delete();
                
                // Remover transações de caixa relacionadas
                $order->cashTransactions()->delete();
                
                // Remover solicitações de entrega relacionadas
                $order->deliveryRequests()->delete();
                
                // Remover histórico de edições relacionadas
                $order->editHistory()->delete();
                
                // Finalmente, remover o pedido
                $order->delete();
                $deletedCount++;
            }
            
            $this->info("✅ {$deletedCount} pedidos rascunho órfãos foram removidos com sucesso!");
        } else {
            $this->info('Operação cancelada.');
        }
        
        return 0;
    }
}
