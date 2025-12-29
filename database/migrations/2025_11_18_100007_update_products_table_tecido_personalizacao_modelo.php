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
        Schema::table('products', function (Blueprint $table) {
            // Remover foreign keys antigas
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropIndex(['category_id', 'active']);
            $table->dropIndex(['subcategory_id', 'active']);
            
            // Remover colunas antigas
            $table->dropColumn(['category_id', 'subcategory_id']);
            
            // Adicionar novas colunas
            $table->foreignId('tecido_id')->nullable()->after('description')->constrained('tecidos')->onDelete('set null');
            $table->foreignId('personalizacao_id')->nullable()->after('tecido_id')->constrained('personalizacoes')->onDelete('set null');
            $table->foreignId('modelo_id')->nullable()->after('personalizacao_id')->constrained('modelos')->onDelete('set null');
            
            // Adicionar índices
            $table->index(['tecido_id', 'active']);
            $table->index(['personalizacao_id', 'active']);
            $table->index(['modelo_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remover novas foreign keys
            $table->dropForeign(['tecido_id']);
            $table->dropForeign(['personalizacao_id']);
            $table->dropForeign(['modelo_id']);
            $table->dropIndex(['tecido_id', 'active']);
            $table->dropIndex(['personalizacao_id', 'active']);
            $table->dropIndex(['modelo_id', 'active']);
            
            // Remover novas colunas
            $table->dropColumn(['tecido_id', 'personalizacao_id', 'modelo_id']);
            
            // Restaurar colunas antigas
            $table->foreignId('category_id')->nullable()->after('description')->constrained('categories')->onDelete('set null');
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained('subcategories')->onDelete('set null');
            
            // Restaurar índices
            $table->index(['category_id', 'active']);
            $table->index(['subcategory_id', 'active']);
        });
    }
};

