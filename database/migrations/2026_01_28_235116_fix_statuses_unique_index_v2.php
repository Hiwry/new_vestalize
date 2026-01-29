<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop indexes one by one and ignore errors
        try {
            Schema::table('statuses', function (Blueprint $table) {
                $table->dropUnique('statuses_tenant_name_unique');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('statuses', function (Blueprint $table) {
                $table->dropUnique('statuses_name_tenant_unique');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('statuses', function (Blueprint $table) {
                $table->dropUnique('statuses_name_tenant_type_unique');
            });
        } catch (\Exception $e) {}

        // Clear any orphaned data if necessary (optional, but good for clean start)
        // DB::table('statuses')->where('type', 'personalized')->delete();

        Schema::table('statuses', function (Blueprint $table) {
            $table->unique(['name', 'tenant_id', 'type'], 'statuses_name_tenant_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropUnique('statuses_name_tenant_type_unique');
        });
    }
};
