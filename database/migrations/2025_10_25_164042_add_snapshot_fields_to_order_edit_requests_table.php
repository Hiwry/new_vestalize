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
        Schema::table('order_edit_requests', function (Blueprint $table) {
            $table->json('order_snapshot_before')->nullable()->after('changes');
            $table->json('order_snapshot_after')->nullable()->after('order_snapshot_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_edit_requests', function (Blueprint $table) {
            $table->dropColumn(['order_snapshot_before', 'order_snapshot_after']);
        });
    }
};
