<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Adicionar novas colunas
            $table->string('payment_method')->nullable()->after('method'); // Método principal ou "multiplo"
            $table->text('payment_methods')->nullable()->after('payment_method'); // JSON com múltiplos métodos
            $table->date('payment_date')->nullable()->after('due_date');
            $table->date('entry_date')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_methods', 'payment_date', 'entry_date']);
        });
    }
};
