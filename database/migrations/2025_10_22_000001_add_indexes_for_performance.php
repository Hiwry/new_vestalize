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
        // Índices para tabela orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('client_id', 'idx_orders_client_id');
            $table->index('user_id', 'idx_orders_user_id');
            $table->index('status_id', 'idx_orders_status_id');
            $table->index('is_draft', 'idx_orders_is_draft');
            $table->index('is_cancelled', 'idx_orders_is_cancelled');
            $table->index('client_token', 'idx_orders_client_token');
            $table->index('created_at', 'idx_orders_created_at');
            $table->index('order_date', 'idx_orders_order_date');
            $table->index('delivery_date', 'idx_orders_delivery_date');
            $table->index(['status_id', 'is_draft'], 'idx_orders_status_draft');
        });

        // Índices para tabela clients
        Schema::table('clients', function (Blueprint $table) {
            $table->index('name', 'idx_clients_name');
            $table->index('phone_primary', 'idx_clients_phone_primary');
            $table->index('email', 'idx_clients_email');
            $table->index('cpf_cnpj', 'idx_clients_cpf_cnpj');
        });

        // Índices para tabela order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_items_order_id');
            $table->index('art_name', 'idx_order_items_art_name');
        });

        // Índices para tabela payments
        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id', 'idx_payments_order_id');
            $table->index('status', 'idx_payments_status');
            $table->index('payment_date', 'idx_payments_payment_date');
        });

        // Índices para tabela cash_transactions
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->index('order_id', 'idx_cash_transactions_order_id');
            $table->index('user_id', 'idx_cash_transactions_user_id');
            $table->index('type', 'idx_cash_transactions_type');
            $table->index('status', 'idx_cash_transactions_status');
            $table->index('transaction_date', 'idx_cash_transactions_transaction_date');
            $table->index(['type', 'status'], 'idx_cash_transactions_type_status');
        });

        // Índices para tabela order_comments
        Schema::table('order_comments', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_comments_order_id');
            $table->index('user_id', 'idx_order_comments_user_id');
        });

        // Índices para tabela order_logs
        Schema::table('order_logs', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_logs_order_id');
            $table->index('user_id', 'idx_order_logs_user_id');
            $table->index('action', 'idx_order_logs_action');
        });

        // Índices para tabela order_sublimations
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->index('order_item_id', 'idx_order_sublimations_order_item_id');
            $table->index('size_id', 'idx_order_sublimations_size_id');
            $table->index('location_id', 'idx_order_sublimations_location_id');
        });

        // Índices para tabela order_cancellations
        Schema::table('order_cancellations', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_cancellations_order_id');
            $table->index('status', 'idx_order_cancellations_status');
        });

        // Índices para tabela order_edit_requests
        Schema::table('order_edit_requests', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_edit_requests_order_id');
            $table->index('user_id', 'idx_order_edit_requests_user_id');
            $table->index('status', 'idx_order_edit_requests_status');
        });

        // Índices para tabela delivery_requests
        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->index('order_id', 'idx_delivery_requests_order_id');
            $table->index('status', 'idx_delivery_requests_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices da tabela orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_client_id');
            $table->dropIndex('idx_orders_user_id');
            $table->dropIndex('idx_orders_status_id');
            $table->dropIndex('idx_orders_is_draft');
            $table->dropIndex('idx_orders_is_cancelled');
            $table->dropIndex('idx_orders_client_token');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_order_date');
            $table->dropIndex('idx_orders_delivery_date');
            $table->dropIndex('idx_orders_status_draft');
        });

        // Remover índices da tabela clients
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('idx_clients_name');
            $table->dropIndex('idx_clients_phone_primary');
            $table->dropIndex('idx_clients_email');
            $table->dropIndex('idx_clients_cpf_cnpj');
        });

        // Remover índices da tabela order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_art_name');
        });

        // Remover índices da tabela payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_order_id');
            $table->dropIndex('idx_payments_status');
            $table->dropIndex('idx_payments_payment_date');
        });

        // Remover índices da tabela cash_transactions
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_cash_transactions_order_id');
            $table->dropIndex('idx_cash_transactions_user_id');
            $table->dropIndex('idx_cash_transactions_type');
            $table->dropIndex('idx_cash_transactions_status');
            $table->dropIndex('idx_cash_transactions_transaction_date');
            $table->dropIndex('idx_cash_transactions_type_status');
        });

        // Remover índices da tabela order_comments
        Schema::table('order_comments', function (Blueprint $table) {
            $table->dropIndex('idx_order_comments_order_id');
            $table->dropIndex('idx_order_comments_user_id');
        });

        // Remover índices da tabela order_logs
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropIndex('idx_order_logs_order_id');
            $table->dropIndex('idx_order_logs_user_id');
            $table->dropIndex('idx_order_logs_action');
        });

        // Remover índices da tabela order_sublimations
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->dropIndex('idx_order_sublimations_order_item_id');
            $table->dropIndex('idx_order_sublimations_size_id');
            $table->dropIndex('idx_order_sublimations_location_id');
        });

        // Remover índices da tabela order_cancellations
        Schema::table('order_cancellations', function (Blueprint $table) {
            $table->dropIndex('idx_order_cancellations_order_id');
            $table->dropIndex('idx_order_cancellations_status');
        });

        // Remover índices da tabela order_edit_requests
        Schema::table('order_edit_requests', function (Blueprint $table) {
            $table->dropIndex('idx_order_edit_requests_order_id');
            $table->dropIndex('idx_order_edit_requests_user_id');
            $table->dropIndex('idx_order_edit_requests_status');
        });

        // Remover índices da tabela delivery_requests
        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->dropIndex('idx_delivery_requests_order_id');
            $table->dropIndex('idx_delivery_requests_status');
        });
    }
};

