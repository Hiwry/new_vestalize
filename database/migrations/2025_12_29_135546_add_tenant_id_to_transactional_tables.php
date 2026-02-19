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
        $tables = ['orders', 'budgets', 'clients'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'tenant_id')) {
                    $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['orders', 'budgets', 'clients'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'tenant_id')) {
                    $table->dropForeign([$tableName . '_tenant_id_foreign']); // Some MySQL versions may need this
                    $table->dropColumn('tenant_id');
                }
            });
        }
    }
};
