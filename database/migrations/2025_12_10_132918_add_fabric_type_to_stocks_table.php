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
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('fabric_type_id')
                ->nullable()
                ->after('fabric_id')
                ->constrained('product_options')
                ->onDelete('set null')
                ->comment('Tipo de tecido específico (ex: Cacharrel, Brim, etc)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['fabric_type_id']);
            $table->dropColumn('fabric_type_id');
        });
    }
};
