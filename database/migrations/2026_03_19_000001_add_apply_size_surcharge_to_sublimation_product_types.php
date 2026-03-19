<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->boolean('apply_size_surcharge')->default(true)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_product_types', function (Blueprint $table) {
            $table->dropColumn('apply_size_surcharge');
        });
    }
};
