<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Produtos da Sublimação Total (camisa básica, conjunto, bandeira, etc.)
        Schema::create('sublimation_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // Ex: Camisa Básica, Conjunto Esportivo, Bandeira
            $table->text('description')->nullable();
            $table->string('type')->default('custom'); // camisa, conjunto, bandeira, winderbanner, custom
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('fabric_always_white')->default(true);
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Opções por produto (modelo, tamanho, acabamento)
        Schema::create('sublimation_product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('sublimation_products')->onDelete('cascade');
            $table->string('option_group'); // modelo, tamanho, acabamento, entrada_mastro
            $table->string('name'); // Tradicional, Babylook, P, M, G, Com mastro
            $table->decimal('price_modifier', 10, 2)->default(0); // Adicional ou desconto
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Faixas de preço por quantidade
        Schema::create('sublimation_product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('sublimation_products')->onDelete('cascade');
            $table->integer('quantity_from')->default(1);
            $table->integer('quantity_to')->nullable(); // null = sem limite
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
        });

        // Adicionais (gola v, nome/número, bordado)
        Schema::create('sublimation_product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('sublimation_products')->onDelete('cascade'); // null = disponível para todos
            $table->string('name'); // Gola V, Nome, Número, Bordado
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Adicionar campo para habilitar/desabilitar SUB. TOTAL no tenant
        if (!Schema::hasColumn('tenants', 'sublimation_total_enabled')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->boolean('sublimation_total_enabled')->default(false)->after('secondary_color');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sublimation_product_addons');
        Schema::dropIfExists('sublimation_product_prices');
        Schema::dropIfExists('sublimation_product_options');
        Schema::dropIfExists('sublimation_products');

        if (Schema::hasColumn('tenants', 'sublimation_total_enabled')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('sublimation_total_enabled');
            });
        }
    }
};
