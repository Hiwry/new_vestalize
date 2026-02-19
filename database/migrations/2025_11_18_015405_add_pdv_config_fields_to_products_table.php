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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // Tipo de venda: unidade, kg, metro (para tecido/malha)
                $table->enum('sale_type', ['unidade', 'kg', 'metro'])->default('unidade')->after('price');
                
                // Permitir aplicação (para camisa/calça)
                $table->boolean('allow_application')->default(false)->after('sale_type');
                
                // Tipos de aplicação permitidos (JSON: sublimacao_local, dtf)
                $table->json('application_types')->nullable()->after('allow_application');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['sale_type', 'allow_application', 'application_types']);
            });
        }
    }
};
