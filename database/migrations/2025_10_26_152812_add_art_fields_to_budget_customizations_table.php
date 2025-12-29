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
        Schema::table('budget_customizations', function (Blueprint $table) {
            $table->foreignId('budget_id')->nullable()->after('id')->constrained('budgets')->onDelete('cascade');
            $table->string('art_name')->nullable()->after('budget_item_id');
            $table->string('location')->nullable()->after('art_name');
            $table->text('art_files')->nullable()->after('image'); // JSON com os arquivos
            $table->text('notes')->nullable()->after('art_files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_customizations', function (Blueprint $table) {
            $table->dropForeign(['budget_id']);
            $table->dropColumn(['budget_id', 'art_name', 'location', 'art_files', 'notes']);
        });
    }
};
