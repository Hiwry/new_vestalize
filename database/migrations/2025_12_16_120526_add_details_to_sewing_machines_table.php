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
        Schema::table('sewing_machines', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('model');
            $table->string('worker_name')->nullable()->after('store_id');
            $table->string('internal_code')->nullable()->after('id');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sewing_machines', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'worker_name', 'internal_code', 'purchase_price']);
        });
    }
};
