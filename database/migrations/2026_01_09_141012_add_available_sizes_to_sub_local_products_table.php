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
        Schema::table('sub_local_products', function (Blueprint $table) {
            // Campo para armazenar os tamanhos disponíveis (PP, P, M, G, GG, XGG, etc)
            $table->json('available_sizes')->nullable()->after('requires_customization');
            // Flag para indicar se o produto requer seleção de tamanho
            $table->boolean('requires_size')->default(false)->after('available_sizes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_local_products', function (Blueprint $table) {
            $table->dropColumn(['available_sizes', 'requires_size']);
        });
    }
};
