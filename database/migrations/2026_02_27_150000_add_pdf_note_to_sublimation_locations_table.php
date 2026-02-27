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
        Schema::table('sublimation_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('sublimation_locations', 'pdf_note')) {
                $table->string('pdf_note')->nullable()->after('show_in_pdf')
                    ->comment('Custom note template for the sewing sheet. Example: {LOCAL} aberta para {TIPO}');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sublimation_locations', function (Blueprint $table) {
            if (Schema::hasColumn('sublimation_locations', 'pdf_note')) {
                $table->dropColumn('pdf_note');
            }
        });
    }
};
