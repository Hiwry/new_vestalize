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
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->unsignedBigInteger('tecido_id')->nullable()->after('tenant_id');
            $table->foreign('tecido_id')->references('id')->on('tecidos')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->dropForeign(['tecido_id']);
            $table->dropColumn('tecido_id');
        });

    }
};
