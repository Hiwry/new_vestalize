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
                // Verificar e adicionar coluna sale_type se não existir
                if (!Schema::hasColumn('products', 'sale_type')) {
                    $table->enum('sale_type', ['unidade', 'kg', 'metro'])->default('unidade')->after('price');
                }
                
                // Verificar e adicionar coluna allow_application se não existir
                if (!Schema::hasColumn('products', 'allow_application')) {
                    $table->boolean('allow_application')->default(false)->after('sale_type');
                }
                
                // Verificar e adicionar coluna application_types se não existir
                if (!Schema::hasColumn('products', 'application_types')) {
                    $table->json('application_types')->nullable()->after('allow_application');
                }
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
                if (Schema::hasColumn('products', 'application_types')) {
                    $table->dropColumn('application_types');
                }
                if (Schema::hasColumn('products', 'allow_application')) {
                    $table->dropColumn('allow_application');
                }
                if (Schema::hasColumn('products', 'sale_type')) {
                    $table->dropColumn('sale_type');
                }
            });
        }
    }
};

