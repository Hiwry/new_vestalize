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
        Schema::table('fabric_pieces', function (Blueprint $table) {
            // Adicionar fabric_type_id e renomear fabric_id
            $table->foreignId('fabric_type_id')->nullable()->after('color_id')->constrained('product_options')->nullOnDelete();
            
            // Campos para transferência entre lojas
            $table->foreignId('origin_store_id')->nullable()->after('destination')->constrained('stores')->nullOnDelete();
            $table->foreignId('destination_store_id')->nullable()->after('origin_store_id')->constrained('stores')->nullOnDelete();
            $table->boolean('between_stores')->default(false)->after('destination_store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabric_pieces', function (Blueprint $table) {
            $table->dropForeign(['fabric_type_id']);
            $table->dropColumn('fabric_type_id');
            $table->dropForeign(['origin_store_id']);
            $table->dropColumn('origin_store_id');
            $table->dropForeign(['destination_store_id']);
            $table->dropColumn('destination_store_id');
            $table->dropColumn('between_stores');
        });
    }
};
