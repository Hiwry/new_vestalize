<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('shelf', 50)->nullable()->after('size')->comment('Prateleira/Estante onde o produto está armazenado');
            $table->index('shelf');
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex(['shelf']);
            $table->dropColumn('shelf');
        });
    }
};
