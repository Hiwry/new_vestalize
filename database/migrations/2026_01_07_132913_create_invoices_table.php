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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Identificação
            $table->string('ref', 50)->unique(); // Referência interna (ex: T1-O123)
            $table->integer('numero')->nullable();
            $table->integer('serie')->default(1);
            
            // Dados da NF-e
            $table->string('chave_nfe', 44)->nullable();
            $table->string('protocolo', 50)->nullable();
            $table->timestamp('data_emissao')->nullable();
            
            // Valores
            $table->decimal('valor_produtos', 12, 2)->default(0);
            $table->decimal('valor_frete', 12, 2)->default(0);
            $table->decimal('valor_desconto', 12, 2)->default(0);
            $table->decimal('valor_total', 12, 2)->default(0);
            
            // Status
            $table->enum('status', ['pending', 'processing', 'authorized', 'cancelled', 'denied', 'error'])->default('pending');
            $table->string('status_sefaz', 20)->nullable();
            $table->text('motivo_sefaz')->nullable();
            
            // Arquivos
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            
            // Cancelamento
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_protocol', 50)->nullable();
            $table->text('cancel_reason')->nullable();
            
            // Controle de tentativas
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->json('error_log')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['tenant_id', 'status']);
            $table->index('chave_nfe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
