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
        Schema::create('personalization_settings', function (Blueprint $table) {
            $table->id();
            $table->string('personalization_type', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            
            // Cobrança por cor
            $table->boolean('charge_by_color')->default(false);
            $table->decimal('color_price_per_unit', 10, 2)->default(0);
            $table->integer('min_colors')->default(1);
            $table->integer('max_colors')->nullable();
            
            // Descontos por múltiplas aplicações (em percentual)
            $table->decimal('discount_2nd_application', 5, 2)->default(0);
            $table->decimal('discount_3rd_application', 5, 2)->default(0);
            $table->decimal('discount_4th_plus_application', 5, 2)->default(0);
            
            // Configurações gerais
            $table->boolean('has_sizes')->default(true);
            $table->boolean('has_locations')->default(true);
            $table->boolean('has_special_options')->default(false);
            
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalization_settings');
    }
};
