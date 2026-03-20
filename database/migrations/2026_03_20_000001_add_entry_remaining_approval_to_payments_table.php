<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('entry_approved')->nullable()->default(null)->after('cash_approved');
            $table->unsignedBigInteger('entry_approved_by')->nullable()->after('entry_approved');
            $table->timestamp('entry_approved_at')->nullable()->after('entry_approved_by');
            $table->boolean('remaining_approved')->nullable()->default(null)->after('entry_approved_at');
            $table->unsignedBigInteger('remaining_approved_by')->nullable()->after('remaining_approved');
            $table->timestamp('remaining_approved_at')->nullable()->after('remaining_approved_by');

            $table->foreign('entry_approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('remaining_approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['entry_approved_by']);
            $table->dropForeign(['remaining_approved_by']);
            $table->dropColumn([
                'entry_approved', 'entry_approved_by', 'entry_approved_at',
                'remaining_approved', 'remaining_approved_by', 'remaining_approved_at',
            ]);
        });
    }
};
