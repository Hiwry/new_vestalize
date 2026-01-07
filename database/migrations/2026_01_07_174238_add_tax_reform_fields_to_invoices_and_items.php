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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('ibs_cbs_base_calculo', 12, 2)->default(0)->after('valor_total');
            $table->decimal('ibs_valor_total', 12, 2)->default(0)->after('ibs_cbs_base_calculo');
            $table->decimal('cbs_valor_total', 12, 2)->default(0)->after('ibs_valor_total');
            $table->decimal('is_valor_total', 12, 2)->default(0)->after('cbs_valor_total');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('ibs_cbs_base_calculo', 12, 2)->default(0)->after('valor_total');
            $table->decimal('ibs_valor', 12, 2)->default(0)->after('ibs_cbs_base_calculo');
            $table->decimal('cbs_valor', 12, 2)->default(0)->after('ibs_valor');
            $table->decimal('pIBS', 5, 2)->default(0)->after('cbs_valor');
            $table->decimal('pCBS', 5, 2)->default(0)->after('pIBS');
            $table->string('cst_ibs_cbs', 3)->nullable()->after('pCBS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['ibs_cbs_base_calculo', 'ibs_valor_total', 'cbs_valor_total', 'is_valor_total']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['ibs_cbs_base_calculo', 'ibs_valor', 'cbs_valor', 'pIBS', 'pCBS', 'cst_ibs_cbs']);
        });
    }
};
