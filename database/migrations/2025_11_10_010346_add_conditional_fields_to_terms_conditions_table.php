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
        Schema::table('terms_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('terms_conditions', 'personalization_type')) {
                $table->string('personalization_type')->nullable()->after('version');
            }
            if (!Schema::hasColumn('terms_conditions', 'fabric_type_id')) {
                $table->unsignedBigInteger('fabric_type_id')->nullable()->after('personalization_type');
            }
            if (!Schema::hasColumn('terms_conditions', 'title')) {
                $table->string('title')->nullable()->after('fabric_type_id');
            }
        });
        
        // Adicionar índices e foreign key separadamente (com try-catch para ignorar erros se já existirem)
        try {
            Schema::table('terms_conditions', function (Blueprint $table) {
                $table->index(['personalization_type', 'fabric_type_id', 'active'], 'terms_cond_type_fabric_active_idx');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            Schema::table('terms_conditions', function (Blueprint $table) {
                if (Schema::hasColumn('terms_conditions', 'fabric_type_id')) {
                    $table->foreign('fabric_type_id')
                          ->references('id')
                          ->on('product_options')
                          ->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign key já existe, ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->dropForeign(['fabric_type_id']);
            $table->dropIndex('terms_cond_type_fabric_active_idx');
            $table->dropColumn(['personalization_type', 'fabric_type_id', 'title']);
        });
    }
};
