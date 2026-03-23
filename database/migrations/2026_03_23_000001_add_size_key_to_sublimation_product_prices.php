<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            $table->string('size_key', 100)->nullable()->after('product_type');
        });
    }

    public function down(): void
    {
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            $table->dropColumn('size_key');
        });
    }
};
