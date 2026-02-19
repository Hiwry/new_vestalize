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
        Schema::table('budgets', function (Blueprint $table) {
            // Verificar se a coluna não existe antes de adicionar
            if (!Schema::hasColumn('budgets', 'order_number')) {
                $table->string('order_number')->nullable()->after('budget_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            if (Schema::hasColumn('budgets', 'order_number')) {
                $table->dropColumn('order_number');
            }
        });
    }
};
