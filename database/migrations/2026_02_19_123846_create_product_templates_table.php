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
        Schema::create('product_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->string('tecido_keyword')->nullable(); // Para busca flexível
            $table->string('personalizacao_keyword')->nullable();
            $table->string('modelo_keyword')->nullable();
            $table->decimal('default_price', 10, 2)->default(0);
            $table->string('icon')->default('fa-shirt');
            $table->json('compatible_cuts')->nullable();
            $table->boolean('allow_application')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_templates');
    }
};
