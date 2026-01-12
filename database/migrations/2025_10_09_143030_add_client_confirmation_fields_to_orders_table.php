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
            $table->string('client_token')->nullable()->unique()->after('is_draft');
            $table->boolean('client_confirmed')->default(false)->after('client_token');
            $table->timestamp('client_confirmed_at')->nullable()->after('client_confirmed');
            $table->text('client_confirmation_notes')->nullable()->after('client_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'client_token',
                'client_confirmed',
                'client_confirmed_at',
                'client_confirmation_notes'
            ]);
        });
    }
};
