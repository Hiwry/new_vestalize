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
            $table->json('addons')->nullable()->after('color_details');
            $table->boolean('regata_discount')->default(false)->after('addons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->dropColumn(['addons', 'regata_discount']);
        });
    }
};
