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
        Schema::table('statuses', function (Blueprint $table) {
            $table->string('type')->default('production')->after('tenant_id');
            
            // Drop old unique constraint
            try {
                $table->dropUnique('statuses_name_tenant_unique');
            } catch (\Exception $e) {}
            
            // Add new unique constraint including type
            $table->unique(['name', 'tenant_id', 'type'], 'statuses_name_tenant_type_unique');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('origin')->nullable()->default('general')->after('store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            try {
                $table->dropUnique('statuses_name_tenant_type_unique');
            } catch (\Exception $e) {}
            
            $table->dropColumn('type');
            
            // Restore old unique constraint
            try {
                $table->unique(['name', 'tenant_id'], 'statuses_name_tenant_unique');
            } catch (\Exception $e) {}
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('origin');
        });
    }
};
