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
        // Criar tabela order_items apenas se não existir
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->unsignedTinyInteger('item_number')->default(1);
                $table->string('fabric')->nullable();
                $table->string('color')->nullable();
                $table->string('collar')->nullable();
                $table->string('model')->nullable();
                $table->string('detail')->nullable();
                $table->string('print_type')->nullable();
                $table->string('print_desc')->nullable();
                $table->string('art_name')->nullable();
                $table->string('cover_image')->nullable();
                $table->json('sizes')->nullable();
                $table->integer('quantity')->default(0);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('total_price', 12, 2)->default(0);
                $table->text('art_notes')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('art_name');
            });
        } else {
            // Se a tabela já existe, apenas adicionar colunas faltantes
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'art_name')) {
                    $table->string('art_name')->nullable()->after('print_type');
                }
                if (!Schema::hasColumn('order_items', 'cover_image')) {
                    $table->string('cover_image')->nullable()->after('art_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover a tabela em produção
        // Schema::dropIfExists('order_items');
    }
};
