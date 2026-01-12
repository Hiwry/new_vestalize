<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sublimation_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // A4, ESCUDO, A3, MEIA FOLHA
            $table->string('dimensions'); // 28X21, 10X10, etc
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sublimation_sizes');
    }
};
