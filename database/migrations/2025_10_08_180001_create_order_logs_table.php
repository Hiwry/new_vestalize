<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name'); // Nome de quem fez a ação
            $table->string('action'); // Tipo de ação (status_changed, comment_added, etc)
            $table->text('description'); // Descrição da ação
            $table->json('old_value')->nullable(); // Valor antigo (para mudanças)
            $table->json('new_value')->nullable(); // Valor novo (para mudanças)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
