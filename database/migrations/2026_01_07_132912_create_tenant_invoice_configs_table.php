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
        Schema::create('tenant_invoice_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->onDelete('cascade');
            
            // Provedor de NF-e
            $table->string('provider')->default('focusnfe'); // focusnfe, nfeio, webmania
            $table->text('api_token')->nullable(); // Token criptografado
            $table->enum('environment', ['homologacao', 'producao'])->default('homologacao');
            
            // Certificado Digital A1
            $table->string('certificate_path')->nullable();
            $table->text('certificate_password')->nullable(); // Criptografado
            $table->timestamp('certificate_expires_at')->nullable();
            
            // Dados do Emitente
            $table->string('razao_social')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj', 14)->nullable();
            $table->string('inscricao_estadual', 20)->nullable();
            $table->string('inscricao_municipal', 20)->nullable();
            $table->tinyInteger('regime_tributario')->default(1); // 1=Simples, 2=Excesso, 3=Normal
            
            // Endereço do Emitente
            $table->string('logradouro')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('codigo_municipio', 7)->nullable();
            
            // Configurações Padrão
            $table->string('default_cfop', 4)->default('5102');
            $table->string('default_ncm', 8)->default('61091000');
            $table->string('natureza_operacao', 100)->default('VENDA DE MERCADORIA');
            
            // Série e número atual
            $table->integer('serie_nfe')->default(1);
            $table->integer('numero_nfe_atual')->default(0);
            
            // Status
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_invoice_configs');
    }
};
