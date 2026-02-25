<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutorial_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('icone')->default('fa-folder'); // Font Awesome icon
            $table->string('cor')->default('purple');       // purple, amber, emerald, etc.
            $table->string('perfil')->default('admin');     // admin, vendedor, producao
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutorial_category_id')->constrained('tutorial_categories')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('youtube_id');
            $table->string('duracao')->nullable();
            $table->string('capa_url')->nullable(); // custom cover, fallback to YouTube thumbnail
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutorials');
        Schema::dropIfExists('tutorial_categories');
    }
};
