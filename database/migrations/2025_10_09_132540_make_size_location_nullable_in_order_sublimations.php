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
        Schema::table('order_sublimations', function (Blueprint $table) {
            // Remover foreign keys antigas
            $table->dropForeign(['size_id']);
            $table->dropForeign(['location_id']);
            
            // Tornar campos nullable
            $table->unsignedBigInteger('size_id')->nullable()->change();
            $table->unsignedBigInteger('location_id')->nullable()->change();
            
            // Adicionar campos de texto para armazenar nomes (quando não houver ID)
            $table->string('size_name')->nullable()->after('size_id');
            $table->string('location_name')->nullable()->after('location_id');
            $table->string('application_type')->nullable()->after('order_item_id'); // sublimation, dtf, embroidery, serigraphy
            
            // Recriar foreign keys como nullable
            $table->foreign('size_id')->references('id')->on('sublimation_sizes')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('sublimation_locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sublimations', function (Blueprint $table) {
            // Remover foreign keys
            $table->dropForeign(['size_id']);
            $table->dropForeign(['location_id']);
            
            // Remover campos de texto
            $table->dropColumn(['size_name', 'location_name', 'application_type']);
            
            // Tornar campos NOT NULL novamente
            $table->unsignedBigInteger('size_id')->nullable(false)->change();
            $table->unsignedBigInteger('location_id')->nullable(false)->change();
            
            // Recriar foreign keys como NOT NULL
            $table->foreign('size_id')->references('id')->on('sublimation_sizes')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('sublimation_locations')->onDelete('cascade');
        });
    }
};
