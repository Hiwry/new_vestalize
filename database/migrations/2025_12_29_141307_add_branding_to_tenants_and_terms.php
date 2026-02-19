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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('email');
            $table->string('primary_color')->default('#4f46e5')->after('logo_path');
            $table->string('secondary_color')->default('#7c3aed')->after('primary_color');
        });

        Schema::table('terms_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('terms_conditions', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'primary_color', 'secondary_color']);
        });
    }
};
