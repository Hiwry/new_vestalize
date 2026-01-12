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
            $table->text('seller_notes')->nullable()->after('application_image');
            $table->text('color_details')->nullable()->after('seller_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->dropColumn(['seller_notes', 'color_details']);
        });
    }
};
