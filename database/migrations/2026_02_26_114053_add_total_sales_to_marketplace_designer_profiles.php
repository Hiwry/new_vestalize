<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_designer_profiles', function (Blueprint $table) {
            $table->unsignedInteger('total_sales')->default(0)->after('rating_count');
            $table->unsignedInteger('total_earnings')->default(0)->after('total_sales');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_designer_profiles', function (Blueprint $table) {
            $table->dropColumn(['total_sales', 'total_earnings']);
        });
    }
};
