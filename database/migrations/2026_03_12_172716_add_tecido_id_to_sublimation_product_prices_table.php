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
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('tecido_id')->nullable()->after('product_type');
            $table->foreign('tecido_id')->references('id')->on('tecidos')->onDelete('cascade');
        });

        // Popular dados existentes baseado no tecido atual do tipo de produto
        $prices = DB::table('sublimation_product_prices')->get();
        foreach ($prices as $price) {
            $type = DB::table('sublimation_product_types')
                ->where('slug', $price->product_type)
                ->where('tenant_id', $price->tenant_id)
                ->first();

            if ($type && $type->tecido_id) {
                DB::table('sublimation_product_prices')
                    ->where('id', $price->id)
                    ->update(['tecido_id' => $type->tecido_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sublimation_product_prices', function (Blueprint $table) {
            $table->dropForeign(['tecido_id']);
            $table->dropColumn('tecido_id');
        });
    }
};
