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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('set null');
            
            // Produto
            $table->string('codigo', 60)->nullable();
            $table->string('descricao', 120);
            $table->string('ncm', 8);
            $table->string('cfop', 4);
            $table->string('unidade', 6)->default('UN');
            
            // Valores
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario', 12, 4);
            $table->decimal('valor_total', 12, 2);
            
            // Impostos (Simples Nacional)
            $table->char('origem', 1)->default('0'); // 0=Nacional
            $table->string('csosn', 3)->nullable(); // Ex: 102, 103, 500
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
