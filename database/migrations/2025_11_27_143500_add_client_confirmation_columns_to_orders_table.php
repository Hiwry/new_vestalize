<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'client_token')) {
                $table->string('client_token', 64)->nullable()->after('terms_accepted_at');
            }
            if (!Schema::hasColumn('orders', 'client_confirmed')) {
                $table->boolean('client_confirmed')->default(false)->after('client_token');
            }
            if (!Schema::hasColumn('orders', 'client_confirmed_at')) {
                $table->timestamp('client_confirmed_at')->nullable()->after('client_confirmed');
            }
            if (!Schema::hasColumn('orders', 'client_confirmation_notes')) {
                $table->text('client_confirmation_notes')->nullable()->after('client_confirmed_at');
            }
        });
    }

    public function down()
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
