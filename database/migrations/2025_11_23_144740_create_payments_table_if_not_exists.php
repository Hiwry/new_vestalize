<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar tabela payments apenas se não existir
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->enum('method', ['pix','dinheiro','cartao','boleto','transferencia']);
                $table->string('payment_method')->nullable();
                $table->text('payment_methods')->nullable();
                $table->decimal('amount', 12, 2);
                $table->decimal('entry_amount', 12, 2)->default(0);
                $table->decimal('remaining_amount', 12, 2)->default(0);
                $table->date('due_date')->nullable();
                $table->date('payment_date')->nullable();
                $table->date('entry_date')->nullable();
                $table->string('status')->default('pendente');
                $table->text('notes')->nullable();
                $table->string('receipt_attachment')->nullable();
                $table->boolean('cash_approved')->default(false);
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('approved_by');
            });
        } else {
            // Se a tabela já existe, apenas adicionar colunas faltantes
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('method');
                }
                if (!Schema::hasColumn('payments', 'payment_methods')) {
                    $table->text('payment_methods')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('payments', 'payment_date')) {
                    $table->date('payment_date')->nullable()->after('due_date');
                }
                if (!Schema::hasColumn('payments', 'entry_date')) {
                    $table->date('entry_date')->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('payments', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                if (!Schema::hasColumn('payments', 'receipt_attachment')) {
                    $table->string('receipt_attachment')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('payments', 'cash_approved')) {
                    $table->boolean('cash_approved')->default(false)->after('receipt_attachment');
                }
                if (!Schema::hasColumn('payments', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->after('cash_approved')->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('payments', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover a tabela em produção
        // Schema::dropIfExists('payments');
    }
};
