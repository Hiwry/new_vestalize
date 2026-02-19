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
        Schema::create('personalization_special_options', function (Blueprint $table) {
            $table->id();
            $table->string('personalization_type', 50);
            $table->string('name', 100);
            $table->enum('charge_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('charge_value', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['personalization_type', 'active'], 'pers_special_opts_type_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalization_special_options');
    }
};
