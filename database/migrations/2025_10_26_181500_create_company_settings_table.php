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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_zip')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_cnpj')->nullable();
            $table->string('logo_path')->nullable(); // Caminho para o logo
            
            // Informações bancárias
            $table->string('bank_name')->nullable();
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('pix_key')->nullable();
            
            // Outras configurações
            $table->text('footer_text')->nullable(); // Texto do rodapé para PDFs
            $table->text('terms_conditions')->nullable(); // Termos e condições
            
            $table->timestamps();
        });
        
        // Criar um registro padrão
        DB::table('company_settings')->insert([
            'company_name' => 'Sua Empresa',
            'company_phone' => '(00) 00000-0000',
            'company_email' => 'contato@suaempresa.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};

