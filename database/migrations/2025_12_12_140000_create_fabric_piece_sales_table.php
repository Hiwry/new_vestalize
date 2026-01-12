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
        Schema::create('fabric_piece_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_piece_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sold_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->decimal('quantity', 10, 3); // Quantidade vendida (kg ou metros)
            $table->string('unit', 10)->default('kg'); // 'kg' ou 'metros'
            $table->decimal('unit_price', 10, 2)->nullable(); // Preço por unidade
            $table->decimal('total_price', 10, 2)->nullable(); // Preço total da venda
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_piece_sales');
    }
};
