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
        // Criar product_options se não existir
        if (!Schema::hasTable('product_options')) {
            Schema::create('product_options', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // personalizacao, tecido, tipo_tecido, cor, tipo_corte, detalhe, gola
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0);
                $table->string('parent_type')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['type', 'active']);
            });
        }
        
        // Criar settings se não existir
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->timestamps();
            });
        }
        
        // Criar product_option_relations se não existir
        if (!Schema::hasTable('product_option_relations')) {
            Schema::create('product_option_relations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('option_id')->constrained('product_options')->onDelete('cascade');
                $table->unsignedBigInteger('parent_id');
                $table->string('parent_type')->nullable();
                $table->timestamps();
                
                $table->unique(['option_id', 'parent_id']);
                $table->foreign('parent_id')->references('id')->on('product_options')->onDelete('cascade');
            });
        } else {
            // Se a tabela existe mas parent_type não é nullable, alterar
            if (Schema::hasColumn('product_option_relations', 'parent_type')) {
                Schema::table('product_option_relations', function (Blueprint $table) {
                    $table->string('parent_type')->nullable()->change();
                });
            }
        }
        
        // Criar personalization_prices se não existir
        if (!Schema::hasTable('personalization_prices')) {
            Schema::create('personalization_prices', function (Blueprint $table) {
                $table->id();
                $table->string('personalization_type'); // DTF, SERIGRAFIA, BORDADO, SUBLIMACAO
                $table->string('size_name'); // A4, A3, 10x15cm, etc.
                $table->string('size_dimensions')->nullable(); // 21x29.7cm, etc.
                $table->integer('quantity_from'); // Quantidade mínima
                $table->integer('quantity_to')->nullable(); // Quantidade máxima (null = infinito)
                $table->decimal('price', 10, 2); // Preço unitário
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0); // Para ordenação
                $table->timestamps();
                
                $table->index(['personalization_type', 'active']);
                $table->index(['personalization_type', 'size_name']);
            });
        }
        
        // Criar sublimation_sizes se não existir
        if (!Schema::hasTable('sublimation_sizes')) {
            Schema::create('sublimation_sizes', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // A4, ESCUDO, A3, MEIA FOLHA
                $table->string('dimensions'); // 28X21, 10X10, etc
                $table->integer('order')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
        
        // Criar sublimation_prices se não existir
        if (!Schema::hasTable('sublimation_prices')) {
            Schema::create('sublimation_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('size_id')->constrained('sublimation_sizes')->onDelete('cascade');
                $table->integer('quantity_from'); // Quantidade mínima
                $table->integer('quantity_to')->nullable(); // Quantidade máxima (null = infinito)
                $table->decimal('price', 10, 2);
                $table->timestamps();
            });
        }
        
        // Criar sublimation_locations se não existir
        if (!Schema::hasTable('sublimation_locations')) {
            Schema::create('sublimation_locations', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Manga Direita, Manga Esquerda, Frente, Costas, etc
                $table->integer('order')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover tabelas em produção
    }
};
