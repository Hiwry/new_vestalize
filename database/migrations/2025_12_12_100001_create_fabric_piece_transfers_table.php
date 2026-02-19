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
        Schema::create('fabric_piece_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabric_piece_id')->constrained('fabric_pieces')->onDelete('cascade');
            $table->foreignId('from_store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('status', ['pendente', 'aprovada', 'em_transito', 'recebida', 'cancelada'])->default('pendente');
            
            $table->datetime('requested_at')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->datetime('shipped_at')->nullable();
            $table->datetime('received_at')->nullable();
            
            $table->text('request_notes')->nullable();
            $table->text('approval_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['fabric_piece_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabric_piece_transfers');
    }
};
