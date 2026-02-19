<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Verificar e adicionar colunas se não existirem
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
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('payments', 'payment_methods')) {
                $table->dropColumn('payment_methods');
            }
            if (Schema::hasColumn('payments', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
            if (Schema::hasColumn('payments', 'entry_date')) {
                $table->dropColumn('entry_date');
            }
        });
    }
};
