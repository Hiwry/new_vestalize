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
        // Use a separate schema call or raw SQL to be safer, or just use try/catch block
        try {
            Schema::table('statuses', function (Blueprint $table) {
                 $table->dropUnique(['name']);
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }

        Schema::table('statuses', function (Blueprint $table) {
            // Add composite unique on name + tenant_id
             // Check if already exists first to avoid dup error
            try {
                $table->unique(['name', 'tenant_id'], 'statuses_name_tenant_unique');
            } catch (\Exception $e) {
                // Ignore if already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropUnique('statuses_name_tenant_unique');
            $table->unique('name');
        });
    }
};
