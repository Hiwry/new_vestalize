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
        Schema::create('sales_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            
            // Dados da venda
            $table->decimal('total', 10, 2);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->integer('items_count')->default(0);
            $table->boolean('is_pdv')->default(false);
            $table->boolean('is_cancelled')->default(false);
            
            // Status
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('set null');
            
            // Informações adicionais
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Dados extras em JSON
            
            // Timestamps
            $table->timestamp('sale_date');
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('user_id');
            $table->index('store_id');
            $table->index('sale_date');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_history');
    }
};
