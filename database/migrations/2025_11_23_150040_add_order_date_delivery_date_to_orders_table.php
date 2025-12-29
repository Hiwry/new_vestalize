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
        Schema::table('orders', function (Blueprint $table) {
            // Adicionar order_date se não existir
            if (!Schema::hasColumn('orders', 'order_date')) {
                $table->date('order_date')->nullable()->after('user_id');
            }
            
            // Adicionar delivery_date se não existir
            if (!Schema::hasColumn('orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('order_date');
            }
            
            // Adicionar contract_type se não existir
            if (!Schema::hasColumn('orders', 'contract_type')) {
                $table->string('contract_type')->default('INICIAL')->after('delivery_date');
            }
            
            // Adicionar seller se não existir
            if (!Schema::hasColumn('orders', 'seller')) {
                $table->string('seller')->nullable()->after('contract_type');
            }
            
            // Adicionar nt se não existir
            if (!Schema::hasColumn('orders', 'nt')) {
                $table->string('nt')->nullable()->after('seller');
            }
            
            // Adicionar total_items se não existir
            if (!Schema::hasColumn('orders', 'total_items')) {
                $table->integer('total_items')->default(0)->after('nt');
            }
            
            // Adicionar subtotal se não existir
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('total_items');
            }
            
            // Adicionar discount se não existir
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            }
            
            // Adicionar delivery_fee se não existir
            if (!Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 12, 2)->default(0)->after('discount');
            }
            
            // Adicionar notes se não existir
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('delivery_fee');
            }
        });
        
        // Adicionar índices se não existirem
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('order_date', 'idx_orders_order_date');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('delivery_date', 'idx_orders_delivery_date');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover colunas em produção
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->dropColumn(['order_date', 'delivery_date', 'contract_type', 'seller', 'nt', 'total_items', 'subtotal', 'discount', 'delivery_fee', 'notes']);
        // });
    }
};
