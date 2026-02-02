<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_customizations', function (Blueprint $table) {
            if (!Schema::hasColumn('budget_customizations', 'addons')) {
                $table->json('addons')->nullable()->after('art_files');
            }
            if (!Schema::hasColumn('budget_customizations', 'regata_discount')) {
                $table->boolean('regata_discount')->default(false)->after('addons');
            }
        });
    }

    public function down(): void
    {
        Schema::table('budget_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('budget_customizations', 'regata_discount')) {
                $table->dropColumn('regata_discount');
            }
            if (Schema::hasColumn('budget_customizations', 'addons')) {
                $table->dropColumn('addons');
            }
        });
    }
};
