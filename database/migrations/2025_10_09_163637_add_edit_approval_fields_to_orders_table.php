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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('edit_status', ['none', 'requested', 'approved', 'rejected', 'completed'])->default('none')->after('edit_completed_at');
            $table->timestamp('edit_approved_at')->nullable()->after('edit_status');
            $table->timestamp('edit_rejected_at')->nullable()->after('edit_approved_at');
            $table->text('edit_rejection_reason')->nullable()->after('edit_rejected_at');
            $table->foreignId('edit_approved_by')->nullable()->constrained('users')->onDelete('set null')->after('edit_rejection_reason');
            $table->boolean('is_modified')->default(false)->after('edit_approved_by');
            $table->timestamp('last_modified_at')->nullable()->after('is_modified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['edit_approved_by']);
            $table->dropColumn([
                'edit_status',
                'edit_approved_at',
                'edit_rejected_at',
                'edit_rejection_reason',
                'edit_approved_by',
                'is_modified',
                'last_modified_at'
            ]);
        });
    }
};
