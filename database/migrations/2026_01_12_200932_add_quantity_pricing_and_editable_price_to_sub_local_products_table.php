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
            // Tabela de preços por quantidade (JSON)
            // Formato: [{"min_quantity": 1, "max_quantity": 10, "price": 25.00}, {"min_quantity": 11, "max_quantity": 50, "price": 22.00}]
            $table->json('quantity_pricing')->nullable()->after('cost');
            
            // Flag para habilitar tabela de preços por quantidade
            $table->boolean('has_quantity_pricing')->default(false)->after('quantity_pricing');
            
            // Flag para permitir edição do preço no pedido
            $table->boolean('allow_price_edit')->default(false)->after('has_quantity_pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_local_products', function (Blueprint $table) {
            $table->dropColumn(['quantity_pricing', 'has_quantity_pricing', 'allow_price_edit']);
        });
    }
};
