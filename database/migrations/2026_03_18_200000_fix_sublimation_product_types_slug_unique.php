<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->dropUnique('sublimation_product_types_slug_unique');
            $table->unique(['slug', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->dropUnique(['slug', 'tenant_id']);
            $table->unique('slug');
        });
    }
};
