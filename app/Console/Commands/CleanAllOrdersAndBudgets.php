<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Budget;
use App\Models\OrderItem;
use App\Models\OrderSublimation;
use App\Models\OrderSublimationFile;
use App\Models\OrderFile;
use App\Models\OrderComment;
use App\Models\OrderLog;
use App\Models\OrderEditHistory;
use App\Models\OrderEditRequest;
use App\Models\OrderCancellation;
use App\Models\Payment;
use App\Models\CashTransaction;
use App\Models\DeliveryRequest;
use App\Models\BudgetItem;
use App\Models\BudgetCustomization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanAllOrdersAndBudgets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:all-orders-budgets {--confirm : Confirma a remoção sem pedir confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove TODOS os pedidos e orçamentos do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('  ATENÇÃO: Esta operação irá remover TODOS os pedidos e orçamentos do sistema!');
        $this->warn('  Esta ação NÃO pode ser desfeita!');
        $this->newLine();

        // Contar registros
        $orderCount = Order::count();
        $budgetCount = Budget::count();
        $orderItemCount = OrderItem::count();
        $budgetItemCount = BudgetItem::count();
        $paymentCount = Payment::count();
        $commentCount = OrderComment::count();
        $logCount = OrderLog::count();

        $this->info(' Resumo dos dados a serem removidos:');
        $this->table(
            ['Tipo', 'Quantidade'],
            [
                ['Pedidos', $orderCount],
                ['Itens de Pedidos', $orderItemCount],
                ['Orçamentos', $budgetCount],
                ['Itens de Orçamentos', $budgetItemCount],
                ['Pagamentos', $paymentCount],
                ['Comentários', $commentCount],
                ['Logs', $logCount],
            ]
        );

        $this->newLine();

        if (!$this->option('confirm')) {
            if (!$this->confirm(' Deseja realmente continuar e remover TODOS estes dados?', false)) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        $this->info('  Iniciando limpeza...');
        $this->newLine();

        // Desabilitar verificação de foreign keys temporariamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // 1. Remover dados relacionados aos pedidos (na ordem correta devido às foreign keys)
            $this->info('1⃣  Removendo arquivos de sublimações...');
            OrderSublimationFile::truncate();
            $this->info('    Arquivos de sublimações removidos');

            $this->info('2⃣  Removendo sublimações...');
            OrderSublimation::truncate();
            $this->info('    Sublimações removidas');

            $this->info('3⃣  Removendo arquivos de pedidos...');
            OrderFile::truncate();
            $this->info('    Arquivos de pedidos removidos');

            $this->info('4⃣  Removendo cancelamentos...');
            OrderCancellation::truncate();
            $this->info('    Cancelamentos removidos');

            $this->info('5⃣  Removendo solicitações de edição...');
            OrderEditRequest::truncate();
            $this->info('    Solicitações de edição removidas');

            $this->info('6⃣  Removendo histórico de edições...');
            OrderEditHistory::truncate();
            $this->info('    Histórico de edições removido');

            $this->info('7⃣  Removendo logs de pedidos...');
            OrderLog::truncate();
            $this->info('    Logs removidos');

            $this->info('8⃣  Removendo comentários de pedidos...');
            OrderComment::truncate();
            $this->info('    Comentários removidos');

            $this->info('9⃣  Removendo transações de caixa relacionadas a pedidos...');
            CashTransaction::whereNotNull('order_id')->delete();
            $this->info('    Transações de caixa removidas');

            $this->info(' Removendo solicitações de entrega...');
            DeliveryRequest::truncate();
            $this->info('    Solicitações de entrega removidas');

            $this->info('1⃣1⃣  Removendo pagamentos...');
            Payment::truncate();
            $this->info('    Pagamentos removidos');

            $this->info('1⃣2⃣  Removendo itens de pedidos...');
            OrderItem::truncate();
            $this->info('    Itens de pedidos removidos');

            $this->info('1⃣3⃣  Removendo pedidos...');
            Order::truncate();
            $this->info('    Pedidos removidos');

            // 2. Remover dados relacionados aos orçamentos
            $this->info('1⃣4⃣  Removendo customizações de orçamentos...');
            BudgetCustomization::truncate();
            $this->info('    Customizações de orçamentos removidas');

            $this->info('1⃣5⃣  Removendo itens de orçamentos...');
            BudgetItem::truncate();
            $this->info('    Itens de orçamentos removidos');

            $this->info('1⃣6⃣  Removendo orçamentos...');
            Budget::truncate();
            $this->info('    Orçamentos removidos');

            // Reabilitar verificação de foreign keys
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->newLine();
            $this->info(' Limpeza concluída com sucesso!');
            $this->info(' Todos os pedidos e orçamentos foram removidos do banco de dados.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error(' Erro durante a limpeza: ' . $e->getMessage());
            $this->error(' Linha: ' . $e->getLine());
            $this->error(' Arquivo: ' . $e->getFile());
            return 1;
        }

        return 0;
    }
}

