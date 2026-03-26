<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('budget_items') || !Schema::hasColumn('budget_items', 'personalization_types')) {
            return;
        }

        Schema::table('budget_items', function (Blueprint $table) {
            $table->text('personalization_types')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('budget_items') || !Schema::hasColumn('budget_items', 'personalization_types')) {
            return;
        }

        Schema::table('budget_items', function (Blueprint $table) {
            $table->string('personalization_types')->nullable()->change();
        });
    }
};
