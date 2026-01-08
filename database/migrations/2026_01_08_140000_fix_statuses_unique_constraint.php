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
            // Remove old unique constraint on name only
            $table->dropUnique(['name']);
            
            // Add composite unique on name + tenant_id
            $table->unique(['name', 'tenant_id'], 'statuses_name_tenant_unique');
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
