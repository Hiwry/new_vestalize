<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Se a coluna 'order' existir, renomear para 'position'
        if (Schema::hasColumn('statuses', 'order') && !Schema::hasColumn('statuses', 'position')) {
            DB::statement('ALTER TABLE `statuses` CHANGE `order` `position` SMALLINT(5) UNSIGNED DEFAULT 0');
        }
        
        // Se não existir nem 'order' nem 'position', adicionar 'position'
        if (!Schema::hasColumn('statuses', 'position') && !Schema::hasColumn('statuses', 'order')) {
            Schema::table('statuses', function (Blueprint $table) {
                $table->unsignedSmallInteger('position')->default(0)->after('color');
            });
        }
        
        // Adicionar índice único em 'name' se não existir
        try {
            Schema::table('statuses', function (Blueprint $table) {
                $table->unique('name', 'statuses_name_unique');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não remover a coluna position em produção
        // Schema::table('statuses', function (Blueprint $table) {
        //     $table->dropColumn('position');
        // });
    }
};
