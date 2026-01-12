<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos para itens SUB. TOTAL nos order_items
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Flag para identificar itens SUB. TOTAL
            $table->boolean('is_sublimation_total')->default(false)->after('is_pinned');
            
            // Tipo de produto SUB. TOTAL (camisa, bandeira, etc.)
            $table->string('sublimation_type', 50)->nullable()->after('is_sublimation_total');
            
            // Adicionais selecionados (JSON array de IDs)
            $table->json('sublimation_addons')->nullable()->after('sublimation_type');
            
            // Arquivo Corel
            $table->string('corel_file_path')->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_sublimation_total',
                'sublimation_type',
                'sublimation_addons',
                'corel_file_path',
            ]);
        });
    }
};
