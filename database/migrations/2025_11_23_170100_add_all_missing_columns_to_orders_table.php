<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Campos de termos e condições
            if (!Schema::hasColumn('orders', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('terms_accepted');
            }
            if (!Schema::hasColumn('orders', 'terms_version')) {
                $table->string('terms_version')->nullable()->after('terms_accepted_at');
            }
            
            // Campos de confirmação do cliente
            if (!Schema::hasColumn('orders', 'client_token')) {
                $table->string('client_token')->nullable()->unique()->after('is_draft');
            }
            if (!Schema::hasColumn('orders', 'client_confirmation_notes')) {
                $table->text('client_confirmation_notes')->nullable()->after('client_confirmed_at');
            }
            
            // Campos de edição
            if (!Schema::hasColumn('orders', 'is_editing')) {
                $table->boolean('is_editing')->default(false)->after('client_confirmation_notes');
            }
            if (!Schema::hasColumn('orders', 'edit_requested_at')) {
                $table->timestamp('edit_requested_at')->nullable()->after('is_editing');
            }
            if (!Schema::hasColumn('orders', 'edit_notes')) {
                $table->text('edit_notes')->nullable()->after('edit_requested_at');
            }
            if (!Schema::hasColumn('orders', 'edit_completed_at')) {
                $table->timestamp('edit_completed_at')->nullable()->after('edit_notes');
            }
            if (!Schema::hasColumn('orders', 'edit_status')) {
                $table->enum('edit_status', ['none', 'requested', 'approved', 'rejected', 'completed'])->default('none')->after('edit_completed_at');
            }
            if (!Schema::hasColumn('orders', 'edit_approved_at')) {
                $table->timestamp('edit_approved_at')->nullable()->after('edit_status');
            }
            if (!Schema::hasColumn('orders', 'edit_rejected_at')) {
                $table->timestamp('edit_rejected_at')->nullable()->after('edit_approved_at');
            }
            if (!Schema::hasColumn('orders', 'edit_rejection_reason')) {
                $table->text('edit_rejection_reason')->nullable()->after('edit_rejected_at');
            }
            if (!Schema::hasColumn('orders', 'edit_approved_by')) {
                $table->foreignId('edit_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('edit_rejection_reason');
            }
            if (!Schema::hasColumn('orders', 'is_modified')) {
                $table->boolean('is_modified')->default(false)->after('edit_approved_by');
            }
            if (!Schema::hasColumn('orders', 'last_modified_at')) {
                $table->timestamp('last_modified_at')->nullable()->after('is_modified');
            }
            
            // Campos de cancelamento
            if (!Schema::hasColumn('orders', 'is_cancelled')) {
                $table->boolean('is_cancelled')->default(false)->after('last_modified_at');
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('is_cancelled');
            }
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('orders', 'has_pending_edit')) {
                $table->boolean('has_pending_edit')->default(false)->after('cancellation_reason');
            }
            if (!Schema::hasColumn('orders', 'has_pending_cancellation')) {
                $table->boolean('has_pending_cancellation')->default(false)->after('has_pending_edit');
            }
            if (!Schema::hasColumn('orders', 'last_updated_at')) {
                $table->timestamp('last_updated_at')->nullable()->after('has_pending_cancellation');
            }
        });
        
        // Adicionar índice único para client_token se não existir
        try {
            Schema::table('orders', function (Blueprint $table) {
                if (!DB::getSchemaBuilder()->hasIndex('orders', 'orders_client_token_unique')) {
                    $table->unique('client_token', 'orders_client_token_unique');
                }
            });
        } catch (\Exception $e) {
            // Índice já existe ou erro, ignorar
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columnsToDrop = [
                'terms_accepted_at',
                'terms_version',
                'client_token',
                'client_confirmation_notes',
                'is_editing',
                'edit_requested_at',
                'edit_notes',
                'edit_completed_at',
                'edit_status',
                'edit_approved_at',
                'edit_rejected_at',
                'edit_rejection_reason',
                'edit_approved_by',
                'is_modified',
                'last_modified_at',
                'is_cancelled',
                'cancelled_at',
                'cancellation_reason',
                'has_pending_edit',
                'has_pending_cancellation',
                'last_updated_at'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    try {
                        $table->dropColumn($column);
                    } catch (\Exception $e) {
                        // Ignorar se houver erro ao remover
                    }
                }
            }
        });
    }
};

