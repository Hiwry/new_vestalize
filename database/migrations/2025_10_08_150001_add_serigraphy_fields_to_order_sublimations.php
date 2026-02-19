<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->integer('color_count')->default(0)->after('quantity');
            $table->boolean('has_neon')->default(false)->after('color_count');
            $table->decimal('neon_surcharge', 10, 2)->default(0)->after('has_neon');
        });
    }

    public function down(): void
    {
        Schema::table('order_sublimations', function (Blueprint $table) {
            $table->dropColumn(['color_count', 'has_neon', 'neon_surcharge']);
        });
    }
};
