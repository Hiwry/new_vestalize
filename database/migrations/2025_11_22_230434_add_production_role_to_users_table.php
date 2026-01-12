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
        // A role 'producao' já pode ser adicionada diretamente na coluna role
        // Esta migration apenas documenta e garante que o sistema suporte essa role
        Schema::table('users', function (Blueprint $table) {
            // A coluna role já existe, não precisa adicionar
            // Apenas documentamos que agora suportamos 'producao'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há nada para reverter
    }
};
