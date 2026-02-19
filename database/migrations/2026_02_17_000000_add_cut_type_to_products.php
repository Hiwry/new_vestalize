<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('cut_type_id')->nullable()->after('tecido_id');
            $table->foreign('cut_type_id')->references('id')->on('product_options')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['cut_type_id']);
            $table->dropColumn('cut_type_id');
        });
    }
};
