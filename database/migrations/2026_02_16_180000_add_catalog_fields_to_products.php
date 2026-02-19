<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_in_catalog')->default(false)->after('active');
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('price');
            $table->integer('wholesale_min_qty')->default(6)->after('wholesale_price');
            $table->text('catalog_description')->nullable()->after('description');
            $table->string('sku')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'show_in_catalog',
                'wholesale_price',
                'wholesale_min_qty',
                'catalog_description',
                'sku',
            ]);
        });
    }
};
