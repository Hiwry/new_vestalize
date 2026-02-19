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
            // Remover a foreign key constraint existente
            $table->dropForeign(['client_id']);
            
            // Tornar client_id nullable
            $table->foreignId('client_id')->nullable()->change();
            
            // Recriar a foreign key constraint com nullable
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remover a foreign key constraint
            $table->dropForeign(['client_id']);
            
            // Tornar client_id obrigatório novamente
            $table->foreignId('client_id')->nullable(false)->change();
            
            // Recriar a foreign key constraint
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }
};
