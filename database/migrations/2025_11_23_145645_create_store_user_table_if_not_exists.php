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
        // Criar tabela store_user apenas se não existir
        if (!Schema::hasTable('store_user')) {
            Schema::create('store_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('role')->default('admin_loja');
                $table->timestamps();
                
                // Índices
                $table->unique(['store_id', 'user_id']);
                $table->index('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover a tabela em produção
        // Schema::dropIfExists('store_user');
    }
};
