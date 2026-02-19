<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('available_sizes')->nullable()->after('application_types');
            $table->json('available_colors')->nullable()->after('available_sizes');
            $table->boolean('track_stock')->default(false)->after('available_colors');
            $table->integer('stock_quantity')->nullable()->after('track_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['available_sizes', 'available_colors', 'track_stock', 'stock_quantity']);
        });
    }
};
