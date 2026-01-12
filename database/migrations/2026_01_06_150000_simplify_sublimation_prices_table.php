<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Simplifica a estrutura para: preços por tipo de produto (modo cards)
     */
    public function up(): void
    {
        // Adicionar coluna product_type à tabela de preços
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            // Remover FK de product_id (vamos usar product_type agora)
            if (Schema::hasColumn('sublimation_product_prices', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            
            // Adicionar tenant_id e product_type
            if (!Schema::hasColumn('sublimation_product_prices', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sublimation_product_prices', 'product_type')) {
                $table->string('product_type', 50)->after('tenant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            if (Schema::hasColumn('sublimation_product_prices', 'product_type')) {
                $table->dropColumn('product_type');
            }
            if (Schema::hasColumn('sublimation_product_prices', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('sublimation_products')->onDelete('cascade');
        });
    }
};
