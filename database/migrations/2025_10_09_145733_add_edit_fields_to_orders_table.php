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
            $table->boolean('is_editing')->default(false)->after('client_confirmation_notes');
            $table->timestamp('edit_requested_at')->nullable()->after('is_editing');
            $table->text('edit_notes')->nullable()->after('edit_requested_at');
            $table->timestamp('edit_completed_at')->nullable()->after('edit_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_editing',
                'edit_requested_at',
                'edit_notes',
                'edit_completed_at'
            ]);
        });
    }
};
