<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona product_type aos adicionais para vincular ao tipo de produto
     */
    public function up(): void
    {
        Schema::table('sublimation_product_addons', function (Blueprint $table) {
            // Adicionar product_type para vincular adicional a um tipo específico
            if (!Schema::hasColumn('sublimation_product_addons', 'product_type')) {
                $table->string('product_type', 50)->nullable()->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sublimation_product_addons', function (Blueprint $table) {
            if (Schema::hasColumn('sublimation_product_addons', 'product_type')) {
                $table->dropColumn('product_type');
            }
        });
    }
};
